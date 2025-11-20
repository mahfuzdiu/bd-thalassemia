<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * @param $product
     * @return mixed
     */
    public function insertProduct(&$product)
    {
        $uuid = Str::uuid()->toString();
        $product['uuid'] = $uuid;
        return Product::create([
            'uuid' => $uuid,
            'product_sku' => strtoupper(trim($product['product_sku'])),
            'name' => strtolower($product['name']),
            'description' => $product['description']]
        );
    }

    /**
     * @param $productData
     * @param $product
     * @return array
     */
    public function insertVariants(&$product)
    {
        $productsFromDb = Product::where('uuid', $product['uuid'])->pluck('id', 'uuid')->toArray();
        $productVariants = [];

        foreach ($product['variants'] as &$variant){
            //variants preparation
            $variantUid = Str::uuid()->toString();
            $variant['uuid'] = $variantUid;

            $productVariants[] = [
                'product_id' => $productsFromDb[$product['uuid']],
                'uuid' => $variantUid,
                'variant_sku' => strtoupper(trim($variant['variant_sku'])),
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'created_at' => now()
            ];
        }

        ProductVariant::insert($productVariants);
    }

    public function insertAttributes($product)
    {
        //attribute insertion
        $attributes = [];
        foreach ($product['variants'] as $variant) {
            foreach ($variant['attributes'] as $key => $value) {
                $attributes[] = strtolower($key);
            }
        }

        $attributes = array_unique($attributes);
        $attributes = array_map(fn($name) => ['name' => $name], $attributes);
        Attribute::insertOrIgnore($attributes);
    }

    public function insertAttributeValues($product)
    {
        $attributes = Attribute::pluck('id', 'name')->toArray();

        $seen = [];
        $attributeValues = [];

        foreach ($product['variants'] as $variant) {
            foreach ($variant['attributes'] as $key => $value) {
                $attrId = $attributes[strtolower($key)];
                $attrValue = strtolower($value);
                $uniqueKey = $attrId . ':' . $attrValue;
                if (!isset($seen[$uniqueKey])) {
                    $seen[$uniqueKey] = true;
                    $attributeValues[] = [
                        'attribute_id' => $attrId,
                        'value' => $attrValue,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        AttributeValue::insertOrIgnore($attributeValues);
    }

    public function insertVariantAttributeValuesPivot($product){
        $productVariantsFromDb = ProductVariant::whereIn('uuid', array_column($product['variants'], 'uuid'))->pluck('id', 'uuid')->toArray();

        //variant and attribute value pivot connection
        $attributeNameValueCombination = DB::table('attributes')
            ->join('attribute_values', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->select(
                DB::raw("CONCAT(LOWER(attributes.name), '_', LOWER(attribute_values.value)) as key_name"),
                'attribute_values.id as value_id'
            )->get()
            ->pluck('value_id', 'key_name');


        $productVariantAttributeValuePivots = [];
        foreach ($product['variants'] as $variant){
            foreach ($variant['attributes'] as $key => $value){
                $keyToFindAttributeValueId = strtolower($key) . '_' . strtolower($value);
                $productVariantAttributeValuePivots[] = [
                    'attribute_value_id' => $attributeNameValueCombination[$keyToFindAttributeValueId],
                    'product_variant_id' => $productVariantsFromDb[$variant['uuid']],
                ];
            }
        }

        DB::table('attribute_value_product_variant')->insert($productVariantAttributeValuePivots);
    }

    /**
     * @param $productData
     * @return array
     */
    public function getProductMapper($productData)
    {
        $dataMapper = [];
        foreach ($productData['variants'] as $variant){
            $dataMapper[strtoupper(trim($variant['variant_sku']))] = [
                'price' => $variant['price'],
                'stock' => $variant['stock'],
            ];
        }

        return $dataMapper;
    }

    /**
     * @param $product
     * @param $dataMapper
     */
    public function update($product, $dataMapper)
    {
        $updatedVariants = [];
        foreach ($product->variants as $variant) {
            $updatedVariants[] = [
                'product_id' => $variant->product_id,
                'uuid' => $variant->uuid,
                'variant_sku' => strtoupper($variant->variant_sku),
                'price' => $dataMapper[$variant->variant_sku]['price'],
                'stock' => $dataMapper[$variant->variant_sku]['stock'],
                'updated_at' => now(),
            ];
        }

        ProductVariant::upsert($updatedVariants, ['variant_sku'], ['price', 'stock', 'updated_at']);
    }
}

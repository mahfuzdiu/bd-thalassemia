<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportService
{
    public function insertProducts(&$products)
    {
        $productsFromDb = Product::whereIn('product_sku', array_column($products, 'product_sku'))->pluck('uuid', 'product_sku')->toArray();
        $productsToBeInserted = [];
        foreach ($products as &$product) {
            $uuid = Str::uuid()->toString();
            if (!array_key_exists($product['product_sku'], $productsFromDb)) {
                $product['uuid'] = $uuid;
                $productsToBeInserted[] = [
                    'uuid' => $uuid,
                    'product_sku' => strtoupper($product['product_sku']),
                    'name' => strtolower($product['name']),
                    'description' => $product['description'],
                    'created_by' => null
                ];
            } else {
                $product['uuid'] = $productsFromDb[strtoupper($product['product_sku'])];
            }
        }

        Product::insert($productsToBeInserted);
    }

    public function insertVariants(&$products)
    {
        $productsFromDb = Product::whereIn('uuid', array_column($products, 'uuid'))->pluck('id', 'uuid');

        $productVariants = [];
        foreach ($products as &$product) {
            foreach ($product['variants'] as &$variant) {
                $variantUid = Str::uuid()->toString();
                $variant['uuid'] = $variantUid;
                $productVariants[] = [
                    'product_id' => $productsFromDb[$product['uuid']],
                    'uuid' => $variantUid,
                    'variant_sku' => strtoupper($variant['variant_sku']),
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'created_at' => now()
                ];
            }
        }

        ProductVariant::insert($productVariants);
    }

    public function insertAttributes($products)
    {
        $attributes = [];
        foreach ($products as $product) {
            foreach ($product['variants'] as $variant) {
                foreach ($variant['attributes'] as $key => $value) {
                    if (!in_array(strtolower($key), $attributes)) {
                        $attributes[] = strtolower($key);
                    }
                }
            }
        }

        $attributes = array_map(fn ($name) => ['name' => $name], $attributes);
        Attribute::insertOrIgnore($attributes);
    }

    public function insertAttributeValues($products)
    {
        $attributes = Attribute::pluck('id', 'name')->toArray();
        $attributeValues = [];
        foreach ($products as $product) {
            foreach ($product['variants'] as $variant) {
                //dd($variant['attributes']);
                foreach ($variant['attributes'] as $key => $value) {
                    $attrName = strtolower($key);
                    $attrValue = strtolower($value);
                    $uniqueKey = $attrName . ':' . $attrValue;

                    if (!isset($seen[$uniqueKey])) {
                        $seen[$uniqueKey] = true;
                        $attributeValues[] = [
                            'attribute_id' => $attributes[$attrName],
                            'value' => $attrValue,
                            'created_at' => now()->toDate()
                        ];
                    }
                }
            }
        }

        AttributeValue::insertOrIgnore($attributeValues);
    }

    public function insertVariantAttributeValuesPivot(&$products)
    {
        $variants = [];
        foreach ($products as $product) {
            foreach ($product['variants'] as $variant) {
                $variants[] = $variant;
            }
        }

        $productVariantsFromDb = ProductVariant::whereIn('uuid', array_column($variants, 'uuid'))->pluck('id', 'uuid')->toArray();

        //variant and attribute value pivot connection
        $attributeNameValueCombination = DB::table('attributes')
            ->join('attribute_values', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->select(
                DB::raw("CONCAT(LOWER(attributes.name), '_', LOWER(attribute_values.value)) as key_name"),
                'attribute_values.id as value_id'
            )->get()
            ->pluck('value_id', 'key_name');


        $productVariantAttributeValuePivots = [];
        foreach ($products as $product) {
            foreach ($product['variants'] as $variant) {
                foreach ($variant['attributes'] as $key => $value) {
                    $keyToFindAttributeValueId = strtolower($key) . '_' . strtolower($value);
                    $productVariantAttributeValuePivots[] = [
                        'attribute_value_id' => $attributeNameValueCombination[$keyToFindAttributeValueId],
                        'product_variant_id' => $productVariantsFromDb[$variant['uuid']],
                    ];
                }
            }
        }

        DB::table('attribute_value_product_variant')->insert($productVariantAttributeValuePivots);
    }
}

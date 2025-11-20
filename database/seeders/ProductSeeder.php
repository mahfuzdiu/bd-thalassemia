<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Test data
     */
    public function run(): void
    {
        // Make sure attributes exist
        $colorAttr = Attribute::firstOrCreate(['name' => 'color']);
        $sizeAttr  = Attribute::firstOrCreate(['name' => 'size']);

        // Create attribute values if not exist
        $colors = ['red', 'black'];
        $sizes  = ['m', 'l'];

        foreach ($colors as $color) {
            AttributeValue::firstOrCreate([
                'attribute_id' => $colorAttr->id,
                'value' => $color
            ]);
        }

        foreach ($sizes as $size) {
            AttributeValue::firstOrCreate([
                'attribute_id' => $sizeAttr->id,
                'value' => $size
            ]);
        }

        $colorValues = $colorAttr->values()->get(); // all color values
        $sizeValues  = $sizeAttr->values()->get();  // all size values

        // Create 4 products
        $productsData = [
            [
                'name' => 'Cotton T-Shirt',
                'description' => 'A comfortable cotton T-Shirt perfect for everyday wear',
            ],
            [
                'name' => 'Hoodie',
                'description' => 'Warm polyester hoodie for winter',
            ],
            [
                'name' => 'Shirt',
                'description' => 'Cotton shirt is available',
            ],
            [
                'name' => 'Pant',
                'description' => 'Blue pant with discount',
            ],
        ];

        foreach ($productsData as $data) {
            $product = Product::create($data);
            // create all combinations of variants
            foreach ($colorValues as $color) {
                foreach ($sizeValues as $size) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => strtoupper("{$product->id}-{$color->value}-{$size->value}"),
                        'price' => rand(100, 500),
                        'stock' => rand(0, 100),
                    ]);

                    // attach pivot table (variant ↔ attribute_values)
                    $variant->values()->sync([$color->id, $size->id]);
                }
            }
        }
    }
}

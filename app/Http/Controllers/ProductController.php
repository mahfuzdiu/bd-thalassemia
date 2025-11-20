<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public ProductService $ps;

    public function __construct(ProductService $ps)
    {
        $this->ps = $ps;
    }

    public function index()
    {
        return Product::with('variants')->paginate(1);
    }

    /**
     * Store a newly created resource in storage.
     * @param ProductStoreRequest $request
     * @return
     */
    public function store(ProductStoreRequest $request)
    {
        $product = $request->validated();
        DB::transaction(function () use ($product) {
            $this->ps->insertProduct($product);
            $this->ps->insertVariants($product);
            $this->ps->insertAttributes($product);
            $this->ps->insertAttributeValues($product);
            $this->ps->insertVariantAttributeValuesPivot($product);
        });

        return response()->json(['message' => __('messages.product.created')]);
    }

    /**
     * Update the specified resource in storage.
     * @param ProductStoreRequest $request
     * @param $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductStoreRequest $request, $productId)
    {
        $productData = $request->validated();
        $dataMapper = $this->ps->getProductMapper($productData);
        $product = Product::with(['variants' => function($query) use ($dataMapper) {
            $query->whereIn('variant_sku', array_keys($dataMapper));
        }])->findOrFail($productId);

        DB::transaction(function () use ($product, $productData, $dataMapper){
            $product->update(['name' => strtolower($productData['name']), 'description' => $productData['description'],]);
            $this->ps->update($product, $dataMapper);
        });
        return response()->json(['message' => __('messages.product.updated')]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Product $product
     * @return bool|null
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }
}

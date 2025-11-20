<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductSearchRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use App\Services\ElasticService;

class ProductController extends Controller
{
    public ProductService $ps;
    /**
     * @var ElasticService
     */
    public ElasticService $es;

    public function __construct(ProductService $ps, ElasticService $es)
    {
        $this->ps = $ps;
        $this->es = $es;
    }

    public function search(ProductSearchRequest $request)
    {
        $name = isset($request->validated()['name']) ? $request->validated()['name'] : null;
        $description = isset($request->validated()['description']) ? $request->validated()['description'] : null;

        // Build ES query dynamically
        $must = [];

        if ($name) {
            $must[] = [
                'match' => [
                    'name' => [
                        'query' => $name,
                        'fuzziness' => 'AUTO'
                    ]
                ]
            ];
        }

        if ($description) {
            $must[] = [
                'match' => [
                    'description' => [
                        'query' => $description,
                        'fuzziness' => 'AUTO'
                    ]
                ]
            ];
        }

        // If no search input, return empty result
        if (empty($must)) {
            return response()->json([]);
        }

        $params = [
            'index' => 'products',
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => $must,
                        'minimum_should_match' => 1
                    ]
                ]
            ]
        ];

        $response = $this->es->getClient()->search($params);
        $results = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        return response()->json($results);
    }

    public function index()
    {
        return $this->ps->getProducts();
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
        $product = Product::with(['variants' => function ($query) use ($dataMapper) {
            $query->whereIn('variant_sku', array_keys($dataMapper));
        }])->findOrFail($productId);

        DB::transaction(function () use ($product, $productData, $dataMapper) {
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

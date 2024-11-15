<?php

namespace App\Http\Controllers\Api\V1\Products;

use Validator;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class ProductsController extends Controller
{
    public function __construct()
    {
        // Apply custom middleware for store and update methods
        $this->middleware('admin.role')->only(['store', 'update']);
    }


    public function index(Request $request)
    {

        $perPage = $request->get('per_page', 15); // Default to 15 if 'per_page' is not provided

        // Define a unique cache key based on the current page and per_page parameter
        $cacheKey = 'products_page_' . $request->get('page', 1) . '_per_page_' . $perPage;

        // Cache for 10 minutes (600 seconds), modify this duration as needed
        $products = Cache::remember($cacheKey, 600, function () use ($perPage) {
            return Product::where('status', 'active')->paginate($perPage);
        });

        return $this->ResponseJson('success', 'Product lists', $products, 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:1',
            'slug' => 'required|string|max:50|unique:products,slug',
            'description' => 'nullable|string',
        ]);


        if ($validator->fails()) {
            return $this->ResponseJson(true, 'Validation Error.', $validator->messages()->all(), 422);
        } else {

            try {

                $product = Product::create($request->all());
                return $this->ResponseJson('success', 'Product lists', $product, 201);
            } catch (\Throwable $th) {
                return $this->ResponseJson('error', 'Product Store Faild', $th->getMessage(), 500);
            }
        }
    }

    public function update(Request $request, $id)
    {

        $product = Product::where('id', $id)->first();

        if (empty($product)) {
            return $this->ResponseJson("error", 'Product Id not found!', null, 422);
        }
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        try {
            // Return success response
            // Update the product with the validated data
            $product->update($request->all());

            return $this->ResponseJson('success', 'Product updated successfully', $product, 200);
        } catch (\Throwable $th) {
            // Handle any errors and return a failure response
            return $this->ResponseJson('error', 'Product update failed', $th->getMessage(), 500);
        }
    }
}

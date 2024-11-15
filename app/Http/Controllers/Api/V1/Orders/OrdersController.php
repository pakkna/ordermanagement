<?php

namespace App\Http\Controllers\Api\V1\Orders;

use DB;
use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    public function placeOrder(Request $request)
    {

        // Validate incoming request
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Start DB transaction
        DB::beginTransaction();

        try {
            // Get the authenticated user
            $user = auth('api')->user();;
            $totalAmount = 0;
            $orderItems = [];

            // Process each product in the order
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);

                // Check if the requested quantity is available in stock
                if ($product->stock < $productData['quantity']) {
                    // Rollback the transaction if there's insufficient stock
                    DB::rollBack();

                    return $this->ResponseJson('success', "Product {$product->name} is out of stock.", null, 422);
                }

                // Calculate the total for this product
                $priceAtOrder = $product->price;
                $totalAmount += $priceAtOrder * $productData['quantity'];

                // Prepare order item data
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $priceAtOrder,
                    'total' => $priceAtOrder * $productData['quantity'],
                ];

                // Decrease the stock of the product
                $product->decrement('stock', $productData['quantity']);
            }

            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // Create the order items associated with this order
            $order->items()->createMany($orderItems);
            $order->total_item = $order->items()->count();

            // Commit the transaction
            DB::commit();

            // Return a success response
            return $this->ResponseJson('success', 'Order placed successfully', $order, 201);
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Return an error response
            return $this->ResponseJson('error', 'An error occurred while placing the order', null, 500);
        }
    }

    public function orderHistory(Request $request)
    {
        // Get the authenticated user
        $user = auth('api')->user();
        $page = $request->get('page', 1);  // Get the current page from the request, defaulting to 1

        // Generate a unique cache key for the user and page
        $cacheKey = "order_history_user_{$user->id}_page_{$page}";

        // Set cache duration (e.g., 10 minutes)
        $cacheDuration = 600; // seconds

        try {
            // Attempt to retrieve from cache, if not found, execute query and cache the result
            $orderHistoryData = Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
                // Retrieve the paginated user's orders with related order items and product details
                $orders = Order::with(['items.product'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

                // Format the order data with pagination preserved
                $orderHistory = $orders->getCollection()->map(function ($order) {
                    return [
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'total_amount' => $order->total_amount,
                        'created_at' => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'), // Ensure date format
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'total' => $item->total,
                            ];
                        }),
                    ];
                });

                return [
                    'orders' => $orderHistory,
                    'pagination' => [
                        'total' => $orders->total(),
                        'per_page' => $orders->perPage(),
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                        'from' => $orders->firstItem(),
                        'to' => $orders->lastItem(),
                    ],
                ];
            });

            // Return the cached or fresh response
            return $this->ResponseJson('success', 'Order history', $orderHistoryData, 200);
        } catch (\Throwable $th) {
            return $this->ResponseJson('error', 'Order history retrieval failed!', $th->getMessage(), 500);
        }
    }
}

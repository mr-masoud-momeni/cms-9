<?php

namespace App\Http\Controllers\front;

use App\Helpers\ShopHelper;
use App\Models\Order;
use App\Models\Product;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $buyer = auth('buyer')->user();
        $currentShop = ShopHelper::getShop();

        if (!$buyer) {
            $cart = ShopHelper::getGuestCart();

            if (empty($cart)) {
                return view('Frontend.Store.Pages.cart', [
                    'products' => collect(),
                    'totalAmount' => 0,
                    'shippingAmount' => 0,
                ]);
            }

            if ($currentShop->buyer_login_required === true) {
                return redirect()->route('buyer.login')
                    ->with('warning', 'برای ادامه خرید باید ثبت‌نام کنید.');
            }

            $products = Product::whereIn('id', array_keys($cart))
                ->where('shop_id', $currentShop->id)
                ->get();

            $totalAmount = 0;
            foreach ($products as $product) {
                $product->cart_quantity = $cart[$product->id] ?? 0;
                $product->cart_price = $product->price;
                $totalAmount += $product->cart_price * $product->cart_quantity;
            }

            $shippingAmount = (int) ($currentShop->shipping_cost ?? 0);

            return view('Frontend.Store.Pages.cart', compact('products', 'totalAmount', 'shippingAmount'));
        }

        $order = $buyer->orders()
            ->where('status', Order::STATUS_PENDING)
            ->where('shop_id', $currentShop->id)
            ->with('products')
            ->first();

        if (!$order) {
            return view('Frontend.Store.Pages.cart', [
                'products' => collect(),
                'totalAmount' => 0,
                'shippingAmount' => 0,
            ]);
        }

        $products = $order->products;
        $totalAmount = 0;

        foreach ($products as $product) {
            $product->cart_quantity = $product->pivot->quantity;
            $product->cart_price = $product->pivot->price;
            $totalAmount += $product->cart_price * $product->cart_quantity;
        }

        $shippingAmount = (int) ($order->shipping_amount ?? 0);

        if ($shippingAmount === 0) {
            $shippingAmount = (int) ($currentShop->shipping_cost ?? 0);
        }

        return view('Frontend.Store.Pages.cart', compact('products', 'totalAmount', 'shippingAmount'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) {
            return null;
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'count_product' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $shop = ShopHelper::getShop();
        $product = Product::where('id', $request->product_id)
            ->where('shop_id', $shop->id)
            ->first();

        if (!$product) {
            return response()->json([
                'error' => ['این محصول متعلق به این فروشگاه نیست.']
            ], 404);
        }

        $quantity = (int) $request->count_product;

        $availableStock = $product->available_stock;

        if ($availableStock <= 0) {
            return response()->json([
                'error' => [Order::MESSAGE_OUT_OF_STOCK]
            ]);
        }

        if (auth('buyer')->check()) {
            $buyer = auth('buyer')->user();
            $order = $buyer->orders()
                ->where('status', Order::STATUS_PENDING)
                ->where('shop_id', $shop->id)
                ->first();

            $existingProduct = $order
                ? $order->products()->where('product_id', $product->id)->first()
                : null;

            $currentQuantity = $existingProduct ? (int) $existingProduct->pivot->quantity : 0;
            $requestedQuantity = $currentQuantity + $quantity;

            if ($requestedQuantity > $availableStock) {
                return response()->json([
                    'error' => [
                        str_replace(
                            [':available', ':unit'],
                            [$availableStock, $product->unit],
                            Order::MESSAGE_STOCK_CONFLICT
                        )
                    ]
                ]);
            }

            if (!$order) {
                $order = Order::create([
                    'buyer_id' => $buyer->id,
                    'shop_id' => $shop->id,
                    'status' => Order::STATUS_PENDING,
                    'created_at' => now(),
                ]);
            }

            if ($existingProduct) {
                $order->products()->updateExistingPivot($product->id, [
                    'quantity' => $requestedQuantity,
                    'price' => $product->price,
                ]);
                $addToCart = 0;
            } else {
                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
                $addToCart = 1;
            }

            return response()->json([
                'success' => $addToCart,
                'message' => 'به سبد خرید شما اضافه شد.'
            ]);
        }

        if (auth('shop_admin')->check()) {
            return response()->json([
                'message' => 'ادمین نمی‌تواند محصول به سبد خرید اضافه کند.'
            ]);
        }

        $cart = ShopHelper::getGuestCart();
        $currentQuantity = isset($cart[$product->id]) ? (int) $cart[$product->id] : 0;
        $requestedQuantity = $currentQuantity + $quantity;

        if ($requestedQuantity > $availableStock) {
            return response()->json([
                'error' => [
                    str_replace(
                        [':available', ':unit'],
                        [$availableStock, $product->unit],
                        Order::MESSAGE_STOCK_CONFLICT
                    )
                ]
            ]);
        }

        if ($currentQuantity > 0) {
            $cart[$product->id] = $requestedQuantity;
            $addToCart = 0;
        } else {
            $cart[$product->id] = $quantity;
            $addToCart = 1;
        }

        ShopHelper::putGuestCart($cart);

        return response()->json([
            'success' => $addToCart,
            'message' => 'محصول به سبد خرید مهمان اضافه شد.'
        ]);
    }

    public function completedOrders()
    {
        $buyer = auth('buyer')->user();
        $currentShop = ShopHelper::getShop();

        if (!$buyer) {
            return redirect()->route('buyer.show.register')
                ->with('message', 'برای مشاهده سفارش‌ها باید وارد شوید.');
        }

        $orders = $buyer->orders()
            ->where('status', Order::STATUS_PAID)
            ->where('shop_id', $currentShop->id)
            ->with('products')
            ->get();

        return view('Frontend.Shop.Orders.completeOrders', compact('orders'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $currentShop = ShopHelper::getShop();
        $buyer = auth('buyer')->user();

        if (!$buyer) {
            if ($currentShop->buyer_login_required === true) {
                return redirect()->route('buyer.login')
                    ->with('warning', 'برای ادامه خرید باید وارد حساب کاربری شوید.');
            }

            $cart = session('cart', []);

            if (isset($cart[$id])) {
                unset($cart[$id]);
            }

            session()->put('cart', $cart);

            return redirect()->back()
                ->with('success', 'محصول از سبد خرید حذف شد.');
        }

        $order = $buyer->orders()
            ->where('status', Order::STATUS_PENDING)
            ->where('shop_id', $currentShop->id)
            ->first();

        if (!$order) {
            return redirect()->back()
                ->with('error', 'سبد خرید پیدا نشد.');
        }

        $order->products()
            ->where('products.id', $id)
            ->detach($id);

        return redirect()->back()
            ->with('success', 'محصول از سبد خرید حذف شد.');
    }
}

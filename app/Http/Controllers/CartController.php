<?php

namespace App\Http\Controllers;

use App\Events\NewOrderPlaced;
use App\Models\Commission;
use App\Models\CommissionUser;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // public function addToCart(Request $request, $productId)
    // {
    //     // return $variantIndex = $request->input('variant_index');
    //     $product = Product::find($productId);

    //     if (!$product) {
    //         return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
    //     }

    //     // Lấy giỏ hàng từ session (nếu có), nếu không thì tạo mới
    //     $cart = session()->get('cart', []);

    //     // Kiểm tra nếu sản phẩm đã có trong giỏ hàng, thì tăng số lượng
    //     if (isset($cart[$productId])) {
    //         if ($cart[$productId]['quantity'] >= $product->stock) {
    //             return redirect()->back()->with('error', 'Không đủ hàng trong kho!');
    //         }

    //         $cart[$productId]['quantity']++;
    //     } else {
    //         if ($product->stock < 1) {
    //             return redirect()->back()->with('error', 'Sản phẩm đã hết hàng!');
    //         }

    //         // Thêm mới vào giỏ
    //         $cart[$productId] = [
    //             'name' => $product->name,
    //             'price' => $product->sale_price,
    //             'quantity' => 1,
    //             'image' => $product->thumbnail,
    //         ];
    //     }

    //     // Cập nhật giỏ hàng vào session
    //     session()->put('cart', $cart);

    //     return redirect()->route('cart.view')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    // }

    public function addToCart(Request $request, $productId)
    {
        $product = Product::with('variants.attributeValues.attribute')->find($productId);

        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $cart = session()->get('cart', []);
        $variantIndex = $request->input('variant_index');
        $variant = null;

        if ($product->variants->count() > 0 && is_null($variantIndex)) {
            return redirect()
                ->route('product.show', ['slug' => $product->slug])
                ->with('error', 'Vui lòng chọn biến thể trước khi thêm vào giỏ hàng!');
        }

        if (!is_null($variantIndex) && isset($product->variants[$variantIndex])) {
            $variant = $product->variants[$variantIndex];
        }

        // Xác định số lượng hiện tại đã có trong giỏ (nếu có)
        $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        // Nếu có biến thể
        if ($variant) {
            if ($currentQty + 1 > $variant->stock) {
                return redirect()->back()->with('error', 'Không đủ hàng trong kho cho biến thể đã chọn!');
            }
            // Kiểm tra tồn kho
            $alreadyExists = collect($cart)->firstWhere('variant_id', $variant->id);
            if ($alreadyExists && $alreadyExists['quantity'] >= $variant->stock) {
                return redirect()->back()->with('error', 'Không đủ hàng trong kho!');
            }

            $attributeText = collect($variant->attributeValues)
                ->map(fn($val) => $val->attribute->name . ': ' . $val->value)
                ->implode(', ');

            $cart[$product->id] = [
                'variant_id' => $variant->id,
                'name'       => $product->name . " ({$attributeText})",
                'price'      => $variant->sale_price,
                'quantity'   => $currentQty + 1,
                'image'      => $variant->image ?? $product->thumbnail,
            ];
        } else {
            if ($currentQty + 1 > $product->stock) {
                return redirect()->back()->with('error', 'Không đủ hàng trong kho!');
            }
            // Sản phẩm không có biến thể
            $alreadyExists = collect($cart)->filter(function ($item) use ($product) {
                return $item['variant_id'] === null && str_starts_with($item['name'], $product->name);
            })->first();

            if ($alreadyExists && $alreadyExists['quantity'] >= $product->stock) {
                return redirect()->back()->with('error', 'Không đủ hàng trong kho!');
            }

            $cart[$product->id] = [
                'variant_id' => null,
                'name'       => $product->name,
                'price'      => $product->sale_price,
                'quantity'   => $currentQty + 1,
                'image'      => $product->thumbnail,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.view')->with('success', 'Đã thêm vào giỏ hàng!');
    }


    // CartController.php (thêm phương thức để hiển thị giỏ hàng)
    public function viewCart()
    {
        $carts = session()->get('cart', []);
        $total = 0;

        foreach ($carts as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('cart', compact('carts', 'total'));
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }
        session()->put('cart', $cart);
        return redirect()->route('cart.view')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    // public function increaseQuantity($productId)
    // {
    //     $cart = session()->get('cart', []);

    //     if (isset($cart[$productId])) {
    //         $product = Product::find($productId);

    //         if (!$product) {
    //             return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
    //         }

    //         if ($cart[$productId]['quantity'] >= $product->stock) {
    //             return redirect()->back()->with('error', 'Không đủ hàng trong kho!');
    //         }

    //         $cart[$productId]['quantity']++;
    //         session()->put('cart', $cart);

    //         return redirect()->route('cart.view')->with('success', 'Số lượng sản phẩm đã được cập nhật!');
    //     }

    //     return redirect()->back()->with('error', 'Sản phẩm không có trong giỏ hàng!');
    // }

    public function increaseQuantity($itemKey)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$itemKey])) {
            return redirect()->back()->with('error', 'Sản phẩm không có trong giỏ hàng!');
        }

        $item = $cart[$itemKey];
        // return $item;

        // Nếu có biến thể
        if (!empty($item['variant_id'])) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant) {
                return redirect()->back()->with('error', 'Biến thể không tồn tại!');
            }

            if ($item['quantity'] >= $variant->stock) {
                return redirect()->back()->with('error', 'Không đủ hàng trong kho cho biến thể này!');
            }
        } else {
            // Nếu không có variant_id thì phải mặc định stock = ∞ hoặc cấu hình sẵn từ cart
            return redirect()->back()->with('error', 'Không thể kiểm tra tồn kho vì thiếu thông tin sản phẩm!');
        }

        $cart[$itemKey]['quantity']++;
        session()->put('cart', $cart);

        return redirect()->route('cart.view')->with('success', 'Tăng số lượng thành công!');
    }



    // public function decreaseQuantity($productId)
    // {
    //     $cart = session()->get('cart', []);

    //     if (isset($cart[$productId])) {
    //         if ($cart[$productId]['quantity'] > 1) {
    //             // Giảm số lượng nếu còn nhiều hơn 1
    //             $cart[$productId]['quantity']--;
    //         } else {
    //             // Xóa sản phẩm nếu số lượng = 1
    //             unset($cart[$productId]);
    //         }
    //     }
    //     session()->put('cart', $cart);

    //     return redirect()->route('cart.view')->with('success', 'Số lượng sản phẩm đã được cập nhật!');
    // }

    public function decreaseQuantity($productId)
    {
        $cart = session()->get('cart', []);

        // Kiểm tra sản phẩm có trong giỏ không
        if (!isset($cart[$productId])) {
            return redirect()->route('cart.view')->with('error', 'Sản phẩm không có trong giỏ hàng!');
        }

        // Giảm số lượng hoặc xoá hẳn
        if ($cart[$productId]['quantity'] > 1) {
            $cart[$productId]['quantity']--;
            $message = 'Đã giảm 1 sản phẩm trong giỏ.';
        } else {
            unset($cart[$productId]);
            $message = 'Đã xoá sản phẩm khỏi giỏ hàng.';
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.view')->with('success', $message);
    }


    public function clientProduct()
    {
        return view('product');
    }

    // public function place(Request $request)
    // {
    //     $total = 0;
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'phone' => 'required|string|max:20',
    //         'address' => 'required|string|max:255',
    //         'payment_method' => 'required|string',
    //     ]);

    //     $cart = session()->get('cart', []);
    //     if (empty($cart)) {
    //         return back()->with('error', 'Giỏ hàng trống!');
    //     }

    //     foreach ($cart as $productId => $item) {
    //         $product = Product::find($productId);
    //         if (!$product) {
    //             return back()->with('error', "Sản phẩm ID {$productId} không tồn tại hoặc đã bị xóa khỏi hệ thống.");
    //         }
    //         $total += $product->sale_price * $item['quantity']; // đảm bảo dùng giá hiện tại
    //     }

    //     // $total = 0;
    //     // foreach (session('cart', []) as $item) {
    //     //     $total += $item['price'] * $item['quantity'];
    //     // }

    //     // Tạo đơn hàng
    //     $order = Order::create([
    //         'user_id' => Auth::id(),
    //         'name' => $request->name,
    //         'phone' => $request->phone,
    //         'address' => $request->address,
    //         'payment_method' => $request->payment_method,
    //         'total' => $total,
    //         'status' => 'pending',
    //         'referrer_id' => session()->get('referrer_id') ?? null,
    //     ]);

    //     foreach ($cart as $productId => $item) {
    //         // return $item;
    //         $product = Product::find($productId);
    //         if (!$product) {
    //             // Bỏ qua hoặc log lỗi nếu sản phẩm không tồn tại
    //             continue;
    //         }

    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $product->id,
    //             'product_name' => $product->name,
    //             'thumbnail' => $product->thumbnail,
    //             'price' => $product->sale_price,
    //             'quantity' => $item['quantity'],
    //         ]);
    //     }

    //     // Xóa giỏ hàng
    //     session()->forget('cart');
    //     session()->forget('referrer_id');
    //     event(new NewOrderPlaced($order));
    //     Log::info('Đã phát event đơn hàng mới');

    //     return redirect('/')->with('success', 'Đặt hàng thành công!');
    // }



    // public function place(Request $request)
    // {
    //     // 1. Validate thông tin đầu vào
    //     $request->validate([
    //         'name'           => 'required|string|max:255',
    //         'phone'          => 'required|string|max:20',
    //         'address'        => 'required|string|max:255',
    //         'payment_method' => 'required|string',
    //     ]);

    //     $cart = session()->get('cart', []);
    //     if (empty($cart)) {
    //         return back()->with('error', 'Giỏ hàng trống!');
    //     }

    //     // Lấy referrer id (nếu có) và xóa khỏi session
    //     $referrerId = session()->pull('referrer_id', null);

    //     // Bọc transaction để rollback nếu có lỗi
    //     DB::beginTransaction();

    //     try {
    //         $total = 0;

    //         // 2. Lặp qua cart để tính tổng và kiểm tra stock
    //         foreach ($cart as $productId => $item) {
    //             $product = Product::find($productId);
    //             if (!$product) {
    //                 throw new \Exception("Sản phẩm ID {$productId} không tồn tại!");
    //             }
    //             if ($item['quantity'] > $product->stock) {
    //                 throw new \Exception("Sản phẩm \"{$product->name}\" chỉ còn {$product->stock} trong kho.");
    //             }
    //             $total += $product->sale_price * $item['quantity'];
    //         }

    //         // 3. Tạo Order chính
    //         $order = Order::create([
    //             'user_id'        => Auth::id(),
    //             'name'           => $request->name,
    //             'phone'          => $request->phone,
    //             'address'        => $request->address,
    //             'payment_method' => $request->payment_method,
    //             'total'          => $total,
    //             'status'         => 'pending',
    //             'referrer_id'    => $referrerId,
    //         ]);

    //         // 4. Tạo chi tiết đơn và giảm stock
    //         foreach ($cart as $productId => $item) {
    //             $product = Product::find($productId);

    //             OrderItem::create([
    //                 'order_id'     => $order->id,
    //                 'product_id'   => $product->id,
    //                 'product_name' => $product->name,
    //                 'thumbnail'    => $product->thumbnail,
    //                 'price'        => $product->sale_price,
    //                 'quantity'     => $item['quantity'],
    //                 'referrer_id'     => $referrerId,
    //                 'commission_amount'     => ($product->sale_price * $item['quantity']) * ($product->commission_rate / 100),
    //             ]);

    //             // Giảm stock
    //             $product->decrement('stock', $item['quantity']);
    //         }

    //         // Nếu là chuyển khoản thì tạo mã QR
    //         if ($request->payment_method === 'BANK') {
    //             $qrResponse = Http::post('https://api.vietqr.io/v2/generate', [
    //                 'accountNo' => '0398623059',
    //                 'accountName' => 'TRAN HUY HUNG',
    //                 'acqId' => '970422', // MB Bank
    //                 'amount' => $order->total,
    //                 'addInfo' => $order->id,
    //                 'template' => 'compact2',
    //             ]);

    //             $qrUrl = $qrResponse->json('data.qrCodeURL');
    //             // $order->update([
    //             //     'qr_code_url' => $qrUrl,
    //             // ]);
    //         }

    //         if ($request->payment_method === 'VNPAY') {
    //             $vnp_TmnCode = '3XN2ER8H';
    //             $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0';
    //             $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    //             $vnp_Returnurl = route('vnpay.return');

    //             $vnp_TxnRef = $order->id;
    //             $vnp_OrderInfo = "Thanh toán đơn hàng #" . $order->id;
    //             $vnp_Amount = $order->total * 100;
    //             $vnp_Locale = 'vn';
    //             $vnp_IpAddr = $request->ip();

    //             $inputData = [
    //                 "vnp_Version" => "2.1.0",
    //                 "vnp_TmnCode" => $vnp_TmnCode,
    //                 "vnp_Amount" => $vnp_Amount,
    //                 "vnp_Command" => "pay",
    //                 "vnp_CreateDate" => now()->format('YmdHis'),
    //                 "vnp_CurrCode" => "VND",
    //                 "vnp_IpAddr" => $vnp_IpAddr,
    //                 "vnp_Locale" => $vnp_Locale,
    //                 "vnp_OrderInfo" => $vnp_OrderInfo,
    //                 "vnp_OrderType" => "billpayment",
    //                 "vnp_ReturnUrl" => $vnp_Returnurl,
    //                 "vnp_TxnRef" => $vnp_TxnRef,
    //             ];

    //             ksort($inputData);
    //             $hashData = collect($inputData)->map(function ($v, $k) {
    //                 return "$k=$v";
    //             })->implode('&');
    //             $query = http_build_query($inputData);

    //             $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    //             $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

    //             return redirect($vnp_Url);
    //         }

    //         // 5. Commit transaction
    //         DB::commit();

    //         // Xóa session cart & referrer
    //         session()->forget(['cart', 'referrer_id']);

    //         // Phát event
    //         event(new NewOrderPlaced($order));
    //         Log::info('Đã phát event đơn hàng mới: Order#' . $order->id);

    //         return redirect('/')->with('success', 'Đặt hàng thành công!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Place Order failed: ' . $e->getMessage());
    //         return back()->with('error', $e->getMessage());
    //     }
    // }

    // public function place(Request $request)
    // {
    //     $request->validate([
    //         'name'           => 'required|string|max:255',
    //         'phone'          => 'required|string|max:20',
    //         'address'        => 'required|string|max:255',
    //         'payment_method' => 'required|string',
    //     ]);

    //     $cart = session()->get('cart', []);
    //     // return $cart;

    //     if (empty($cart)) {
    //         return back()->with('error', 'Giỏ hàng trống!');
    //     }
    //     $referrerId = Auth::check() ? Auth::user()->referrer_id : null;


    //     // $referrerId = session()->pull('referrer_id', null);
    //     DB::beginTransaction();

    //     // try {
    //     $total = 0;
    //     $oldOrder = Order::where('phone', $request->phone)->where('status', 'completed')->exists();
    //     $oldUser = session()->get('oldUser');
    //     $oldOrderById = Order::where('user_id', Auth::user()->id)->where('status', 'completed')->exists();

    //     // dd($oldOrder);
    //     // return $request->phone;

    //     foreach ($cart as $productId => $item) {
    //         $product = Product::find($productId);
    //         if (!$product) {
    //             throw new \Exception("Sản phẩm ID {$productId} không tồn tại!");
    //         }
    //         if ($item['quantity'] > $product->stock) {
    //             throw new \Exception("Sản phẩm \"{$product->name}\" chỉ còn {$product->stock} trong kho.");
    //         }
    //         if (!$oldOrder && !$oldUser && $product->category_id != '2') {
    //             // Nếu là đơn đầu tiên (dựa trên SĐT) và không đăng nhập
    //             $salePrice = $product->sale_price * 0.95; // giảm 5%
    //         } else {
    //             $salePrice = $product->sale_price;
    //         }
    //         $total += $salePrice * $item['quantity'];

    //         // $total += $product->sale_price * $item['quantity'];
    //     }


    //     $order = Order::create([
    //         'user_id'        => Auth::id(),
    //         'name'           => $request->name,
    //         'phone'          => $request->phone,
    //         'address'        => $request->address,
    //         'payment_method' => $request->payment_method,
    //         'total'          => $total,
    //         'status'         => 'pending',
    //         'referrer_id'    => $referrerId,
    //     ]);


    //     foreach ($cart as $productId => $item) {
    //         $product = Product::find($productId);
    //         if (!$oldUser && $product->category_id != '2') {
    //             session()->put('oldUser', true);
    //             $orderItem = OrderItem::create([
    //                 'order_id'     => $order->id,
    //                 'product_id'   => $product->id,
    //                 'product_name' => $product->name,
    //                 'thumbnail'    => $product->thumbnail,
    //                 'price'        => $product->sale_price -  ($product->sale_price * (5 / 100)),
    //                 'quantity'     => $item['quantity'],
    //                 'referrer_id'     => $referrerId,
    //                 'commission_amount'     =>  $product->sale_price -  ($product->sale_price * (5 / 100)) * ($product->commission_rate / 100),
    //             ]);
    //         } else {
    //             $orderItem = OrderItem::create([
    //                 'order_id'     => $order->id,
    //                 'product_id'   => $product->id,
    //                 'product_name' => $product->name,
    //                 'thumbnail'    => $product->thumbnail,
    //                 'price'        => $product->sale_price,
    //                 'quantity'     => $item['quantity'],
    //                 'referrer_id'     => $referrerId,
    //                 'commission_amount'     => ($product->sale_price * $item['quantity']) * ($product->commission_rate / 100),
    //             ]);

    //             if ($oldOrderById) {
    //                 $level = 1;
    //                 $categoryId = $product->category_id;
    //                 $commissions = Commission::where('category_id', $categoryId)->orderByDesc('level')->get();
    //                 $referrer = User::find($order->referrer_id);
    //                 foreach ($commissions as $commission) {
    //                     if (!$referrer) break;
    //                     // ✅ Điều kiện: F1 phải từng mua sản phẩm có category_id == 2
    //                     $hasPurchasedCategory2 = DB::table('order_items')
    //                         ->join('orders', 'order_items.order_id', '=', 'orders.id')
    //                         ->join('products', 'order_items.product_id', '=', 'products.id')
    //                         ->where('orders.user_id', $referrer->id)
    //                         ->where('orders.status', 'completed')
    //                         ->where('products.category_id', 2)
    //                         ->exists();
    //                     // return $hasPurchasedCategory2;
    //                     if (!$hasPurchasedCategory2) {
    //                         // Không đủ điều kiện nhận hoa hồng → dừng
    //                         // $referrer = User::find($referrer->referrer_id); // chuyển lên tuyến trên
    //                         // $level++;
    //                         // continue;
    //                         break;
    //                     }
    //                     $commissionAmount = $product->sale_price * ($commission->percentage / 100);
    //                     CommissionUser::create([
    //                         'user_id' => $referrer->id,
    //                         'order_item_id' => $orderItem->id,
    //                         'level' => $level,
    //                         'amount' => $commissionAmount,
    //                     ]);
    //                     $referrer = User::find($referrer->referrer_id);
    //                     $level++;
    //                 }
    //             }
    //             $product->decrement('stock', $item['quantity']);
    //         }
    //     }

    //     DB::commit();
    //     session()->forget(['cart', 'referrer_id']);
    //     event(new NewOrderPlaced($order));
    //     Log::info('Đã phát event đơn hàng mới: Order#' . $order->id);

    //     //VNPAY
    //     if ($request->payment_method === 'VNPAY') {
    //         $vnp_TmnCode = '3XN2ER8H';
    //         $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0';
    //         $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    //         // $vnp_Url = 'https://pay.vnpay.vn/vpcpay.html';
    //         $vnp_Returnurl = route('vnpay.return');

    //         $vnp_TxnRef = $order->id;
    //         $vnp_OrderInfo = "Thanh toán hóa đơn phí dich vụ";
    //         $vnp_Amount = $order->total * 100; // Tính theo đơn vị VND
    //         $vnp_Locale = 'vn';
    //         $vnp_IpAddr = $request->ip();

    //         $inputData = [
    //             "vnp_Version" => "2.0.0",
    //             "vnp_TmnCode" => $vnp_TmnCode,
    //             "vnp_Amount" => $vnp_Amount,
    //             "vnp_Command" => "pay",
    //             "vnp_CreateDate" => now()->format('YmdHis'),
    //             "vnp_CurrCode" => "VND",
    //             "vnp_IpAddr" => $vnp_IpAddr,
    //             "vnp_Locale" => $vnp_Locale,
    //             "vnp_OrderInfo" => $vnp_OrderInfo,
    //             "vnp_OrderType" => "billpayment",
    //             "vnp_ReturnUrl" => $vnp_Returnurl,
    //             "vnp_TxnRef" => $vnp_TxnRef,

    //         ];
    //         ksort($inputData);
    //         $hashData = '';
    //         foreach ($inputData as $key => $value) {
    //             $hashData .= $key . '=' . $value . '&';
    //         }
    //         $hashData = rtrim($hashData, '&');
    //         $query = http_build_query($inputData);
    //         $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    //         $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;
    //         return redirect($vnp_Url);
    //     }
    //     return redirect('/')->with('success', 'Đặt hàng thành công!');
    //     // } catch (\Exception $e) {
    //     //     DB::rollBack();
    //     //     Log::error('Place Order failed: ' . $e->getMessage());
    //     //     return back()->with('error', $e->getMessage());
    //     // }
    // }

    public function place(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'payment_method' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();

        try {
            $isLoggedIn = Auth::check();
            $userId = $isLoggedIn ? Auth::id() : null;
            $referrerId = $isLoggedIn ? Auth::user()->referrer_id : session()->get('referrer_id');
            $oldUser = session()->get('oldUser');
            $oldOrder = Order::where('phone', $request->phone)->where('status', 'completed')->exists();
            $oldOrderById = $userId ? Order::where('user_id', $userId)->where('status', 'completed')->exists() : false;

            $total = 0;

            foreach ($cart as $item) {
                // dd($cart);
                $variant = $item['variant_id'] ? ProductVariant::find($item['variant_id']) : null;
                $price = $item['price'];
                $stock = $variant ? $variant->stock : ($item['stock'] ?? 0);

                if ($item['quantity'] > $stock) {
                    throw new \Exception("Sản phẩm \"{$item['name']}\" chỉ còn {$stock} trong kho.");
                }

                // Giảm 5% nếu là đơn đầu tiên
                if (!$oldOrder && !$oldUser) {
                    $price *= 0.95;
                }

                $total += $price * $item['quantity'];
            }

            $order = Order::create([
                'user_id'        => $userId,
                'name'           => $request->name,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'payment_method' => $request->payment_method,
                'total'          => $total,
                'status'         => 'pending',
                'referrer_id'    => $referrerId,
            ]);

            foreach ($cart as $item) {
                $variant = $item['variant_id'] ? ProductVariant::find($item['variant_id']) : null;
                $price = $item['price'];
                $stock = $variant ? $variant->stock : ($item['stock'] ?? 0);

                if (!$oldUser) {
                    session()->put('oldUser', true);
                    $price *= 0.95;
                }

                $commissionAmount = $price * $item['quantity'] * 0.1; // mặc định 10% nếu không có commission_rate

                $orderItem = OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $variant ? $variant->product_id : null,
                    'variant_id'       => $variant ? $variant->id : null,
                    'product_name'     => $item['name'],
                    'thumbnail'        => $item['image'],
                    'price'            => $price,
                    'quantity'         => $item['quantity'],
                    'referrer_id'      => $referrerId,
                    'commission_amount' => $commissionAmount,
                ]);

                // Giảm tồn kho
                if ($variant) {
                    $variant->decrement('stock', $item['quantity']);
                }

                // ✅ Nếu đã đăng nhập và từng mua trước đó → xử lý hoa hồng
                if ($isLoggedIn && $oldOrderById && $referrerId) {
                    $level = 1;
                    $referrer = User::find($referrerId);
                    $categoryId = optional($variant)->product->category_id;

                    $commissions = Commission::where('category_id', $categoryId)->orderByDesc('level')->get();
                    foreach ($commissions as $commission) {
                        if (!$referrer) break;

                        $hasPurchasedCategory2 = OrderItem::join('orders', 'order_items.order_id', 'orders.id')
                            ->join('products', 'order_items.product_id', 'products.id')
                            ->where('orders.user_id', $referrer->id)
                            ->where('orders.status', 'completed')
                            ->where('products.category_id', 2)
                            ->exists();

                        if (!$hasPurchasedCategory2) break;

                        CommissionUser::create([
                            'user_id'       => $referrer->id,
                            'order_item_id' => $orderItem->id,
                            'level'         => $level,
                            'amount'        => $price * $item['quantity'] * ($commission->percentage / 100),
                        ]);

                        $referrer = User::find($referrer->referrer_id);
                        $level++;
                    }
                }
            }

            DB::commit();
            session()->forget(['cart', 'referrer_id']);
            event(new NewOrderPlaced($order));
            Log::info('Đã phát event đơn hàng mới: Order#' . $order->id);

            // ✅ Xử lý VNPAY nếu được chọn
            if ($request->payment_method === 'VNPAY') {
                $vnp_TmnCode    = '3XN2ER8H';
                $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0';
                $vnp_Url        = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
                $vnp_Returnurl  = route('vnpay.return');

                $inputData = [
                    "vnp_Version"    => "2.0.0",
                    "vnp_TmnCode"    => $vnp_TmnCode,
                    "vnp_Amount"     => $order->total * 100,
                    "vnp_Command"    => "pay",
                    "vnp_CreateDate" => now()->format('YmdHis'),
                    "vnp_CurrCode"   => "VND",
                    "vnp_IpAddr"     => $request->ip(),
                    "vnp_Locale"     => "vn",
                    "vnp_OrderInfo"  => "Thanh toán hóa đơn phí dịch vụ",
                    "vnp_OrderType"  => "billpayment",
                    "vnp_ReturnUrl"  => $vnp_Returnurl,
                    "vnp_TxnRef"     => $order->id,
                ];

                ksort($inputData);
                $hashData = urldecode(http_build_query($inputData));
                $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
                $vnp_Url = $vnp_Url . '?' . http_build_query($inputData) . '&vnp_SecureHash=' . $vnp_SecureHash;

                return redirect($vnp_Url);
            }

            return redirect('/')->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Place Order failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }



    public function callback(Request $request)
    {
        $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0'; // Replace with your real hash secret
        $inputData = $request->all();

        // Remove the vnp_SecureHash from the input data before calculating the hash
        unset($inputData['vnp_SecureHash']);

        // Sort the input data by key
        ksort($inputData);

        // Prepare the string for hashing
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Remove the last '&' character from the string
        $hashData = rtrim($hashData, '&');

        // Calculate the secure hash using SHA-512
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Compare the calculated hash with the one sent by VNPay
        if ($secureHash === $request->input('vnp_SecureHash')) {
            // Hash matches, verify the response code
            if (trim($request->input('vnp_ResponseCode')) === '00') {
                // return "ok" . $request->input('vnp_ResponseCode');

                $orderId = $request->input('vnp_TxnRef');
                $amount = $request->input('vnp_Amount') / 100;

                $order = Order::find($orderId);
                if (!$order) {
                    return redirect('/')->with('error', 'Không tìm thấy đơn hàng.');
                }

                // Nếu VNPAY trả về amount * 100 thì cần chia trước
                $vnPayAmount = (int) $request->input('vnp_Amount');
                $amount = $vnPayAmount / 100;

                if (!is_numeric($amount)) {
                    return redirect('/')->with('error', 'Dữ liệu thanh toán không hợp lệ.');
                }

                if ($order->total != $amount) {
                    return redirect('/')->with('error', 'Số tiền không khớp.');
                }

                if ($order->status_payment == 'paid') {
                    return redirect('/')->with('error', 'Đơn hàng này đã thanh toán trước đó.');
                }
                if ($order && $order->total == $amount) {
                    $order->update([
                        'status_payment' => 'paid',
                        'payment_method' => 'VNPAY',
                    ]);
                    return redirect('/')->with('success', 'Thanh toán thành công!');
                }
            }

            return redirect('/')->with('error', 'Thanh toán thất bại!');
        } else {
            return redirect('/')->with('error', 'Sai mã xác thực!');
        }
    }



    public function generateQr(Request $request)
    {
        // Giả sử bạn đã validate và tạo đơn hàng
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $request->input('total_amount'),
            'status' => 'pending',
            'payment_method' => 'MB'
        ]);

        // Tạo mã QR chuyển khoản
        $qrResponse = Http::post('https://api.vietqr.io/v2/generate', [
            'accountNo' => '0398623059', //Số tài khoản người nhận
            'accountName' => 'TRAN HUY HUNG',
            'acqId' => '970422',
            'amount' => $order->total_amount,
            'addInfo' =>  $order->id,
            'template' => 'compact2',
        ]);

        $qrUrl = $qrResponse->json('data.qrCodeURL');

        // Lưu URL mã QR vào đơn hàng (giả sử có cột qr_code_url)
        // $order->update([
        //     'qr_code_url' => $qrUrl,
        // ]);

        return redirect()->route('orders.show', $order->id);
    }

    public function checkout(Request $request)
    {
        // return $request->id;
        $vnp_TmnCode = '3XN2ER8H'; // Thay bằng mã của bạn
        $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0'; // Thay bằng mã của bạn
        $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        // $vnp_Url = 'https://pay.vnpay.vn/vpcpay.html';
        $vnp_Returnurl = route('vnpay.return');

        $vnp_TxnRef = $request->id;
        $vnp_OrderInfo = "Thanh toán hóa đơn phí dich vụ";
        $vnp_Amount = $request->total * 100; // Tính theo đơn vị VND
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,

        ];
        // $vnp_BankCode = $request->input('bank_code');
        // if (isset($vnp_BankCode) && $vnp_BankCode != '') {
        //     $inputData['vnp_BankCode'] = $vnp_BankCode;
        // }
        // return $inputData;
        ksort($inputData);

        // 1. Tạo hashData (KHÔNG ENCODE)
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= $key . '=' . $value . '&';
        }
        $hashData = rtrim($hashData, '&');

        // 2. Tạo query (CÓ ENCODE)
        $query = http_build_query($inputData);

        // 3. Tạo mã chữ ký
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // 4. Tạo URL cuối cùng
        $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

        // 5. Redirect
        return redirect($vnp_Url);
    }
}

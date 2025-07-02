<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\appSetting;
use App\Models\article;
use App\Models\banner;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\NewOrderPlaced;
use App\Models\BankAccount;
use App\Models\Commission;
use App\Models\CommissionUser;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductCategory;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $AppSetting = appSetting::first();
        $banners = banner::where('position', 1)->where('status', 1)->get();
        $products = Product::with('category', 'group')->latest()->take(10)->get();
        $articles = article::with('category')->latest()->take(10)->get();
        $menu1 = banner::where('position', 2)->where('status', 1)->get();
        $topProducts = Product::select(
            'products.id',
            'products.name',
            'products.slug',
            'products.thumbnail',
            'products.sale_price',
            'products.stock',
            'products.price',
            DB::raw('SUM(order_items.quantity) as total_sold'),
        )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy(
                'products.id',
                'products.name',
                'products.slug',
                'products.thumbnail',
                'products.sale_price',
                'products.stock',
                'products.price',
            )
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        $finalTop = DB::table('order_items')
            ->select('referrer_id', DB::raw('SUM(price * quantity) as total_sales'))
            ->whereNotNull('referrer_id')
            ->groupBy('referrer_id')
            ->orderByDesc('total_sales')
            ->take(20)
            ->get()
            ->map(function ($item) {
                $user = User::find($item->referrer_id);
                return [
                    'id' => $item->referrer_id,
                    'name' => $user?->full_name ?? 'Không rõ',
                    'avatar_url' => $user?->avatar,
                    'total_sales' => $item->total_sales,
                ];
            });

        return response()->json([
            'appSetting' => $AppSetting,
            'banners' => $banners,
            'products' => $products,
            'topProducts' => $topProducts,
            'articles' => $articles,
            'menu1' => $menu1,
            'finalTop' => $finalTop,
            'user' => $user,
        ]);
    }

    public function checkUser(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $hasHighValueCombo = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items.product', function ($query) {
                $query->where('category_id', 2)
                    ->where('sale_price', '>=', 1000000);
            })
            ->exists();

        return response()->json([
            'user' => $user,
            'hasHighValueCombo' => $hasHighValueCombo,
        ]);
    }

    public function historyAffilate(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $orders = Order::with('items.product')
            ->where('referrer_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json([
            'orders' => $orders
        ]);
        // return view('ordersHistoryAffilate', compact('orders'));
    }

    public function historyOrder(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json([
            'orders' => $orders
        ]);
        // return view('ordersHistoryAffilate', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showProduct($slug, Request $request)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        if ($request->has('ref')) {
            session(['referrer_id' => $request->query('ref')]);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
            'related_products' => $relatedProducts
        ]);
    }

    public function showProductId($id)
    {
        return $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'stock' => $product->stock,
            'price' => $product->sale_price,
            'thumbnail' => $product->thumbnail,
        ]);
    }


    public function showArticle($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->take(3)
            ->get();
        return response()->json([
            'success' => true,
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    // public function addToCart($productId)
    // {
    //     $product = Product::find($productId);

    //     if (!$product) {
    //         return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
    //     }

    //     // Lấy giỏ hàng từ session
    //     $cart = session()->get('cart', []);

    //     // Nếu sản phẩm đã tồn tại trong giỏ hàng
    //     if (isset($cart[$productId])) {
    //         if ($cart[$productId]['quantity'] >= $product->stock) {
    //             return response()->json(['error' => 'Không đủ hàng trong kho!'], 400);
    //         }

    //         $cart[$productId]['quantity']++;
    //     } else {
    //         if ($product->stock < 1) {
    //             return response()->json(['error' => 'Sản phẩm đã hết hàng!'], 400);
    //         }

    //         // Thêm sản phẩm mới vào giỏ hàng
    //         $cart[$productId] = [
    //             'name' => $product->name,
    //             'price' => $product->sale_price,
    //             'quantity' => 1,
    //             'image' => $product->thumbnail,
    //         ];
    //     }

    //     // Cập nhật giỏ hàng vào session
    //     // session()->put('cart', $cart);
    //     // $cart = session()->get('cart');
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
    //         'cart' => $cart
    //     ]);
    // }

    // public function viewCart()
    // {
    //     $carts = session()->get('cart', []);
    //     $total = 0;

    //     foreach ($carts as $item) {
    //         $total += $item['price'] * $item['quantity'];
    //     }
    //     return response()->json([
    //         'success' => true,
    //         'total' => $total,
    //         'carts' => $carts
    //     ]);
    // }

    public function checkStock($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
        }

        if ($product->stock < 1) {
            return response()->json(['error' => 'Sản phẩm đã hết hàng!'], 400);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sale_price' => $product->sale_price,
                'thumbnail' => $product->thumbnail,
                'stock' => $product->stock,
                'category_id' => $product->category_id,
            ]
        ]);
    }

    public function place(Request $request)
    {
        // 1. Validate đầu vàoo
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'payment_method' => 'required|string|in:BANK,VNPAY,COD',
            'cart'           => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.quantity'   => 'required|integer|min:1',
            'referrer_id'    => 'nullable|exists:users,id',
            'oldUser'    => 'nullable',
        ]);

        $cart = $validated['cart'];
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }

        // Lấy referrer_id nếu frontend truyền lên (hoặc có thể bỏ qua)
        $referrerId = $user->referrer_id;
        // $referrerId = $validated['referrer_id'];

        DB::beginTransaction();

        try {
            $total = 0;
            $oldOrder = Order::where('phone', $validated['phone'])->where('status', 'completed')->exists();
            $oldUser = $validated['oldUser'];
            $oldOrderById = Order::where('user_id', $user->id)->where('status', 'completed')->exists();
            $hasHighValueCombo = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.user_id', $user->id)
                ->where('orders.status', 'completed')
                ->where('products.category_id', 2)
                ->where('products.sale_price', '>=', 1000000)
                ->exists();
            // Kiểm tra xem có phải đơn lẻ thứ 2 không
            $hasSingleProductOrder = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.user_id', $user->id)
                ->where('orders.status', 'completed')
                ->where('products.category_id', '!=', 2)
                ->exists();

            // 2. Kiểm tra sản phẩm và tính tổng
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if ($item['quantity'] > $product->stock) {
                    throw new \Exception("Sản phẩm \"{$product->name}\" chỉ còn {$product->stock} trong kho.");
                }
                if (!$oldOrder && !$oldUser && $product->category_id != '2' && !$hasSingleProductOrder) {
                    $salePrice = $product->sale_price * 0.90; // giảm 5%
                } elseif ($hasSingleProductOrder && $product->category_id != '2') {
                    $salePrice = $product->sale_price * 0.80;
                } else {
                    $salePrice = $product->sale_price;
                }
                // $total += $product->sale_price * $item['quantity'];
                $total += $salePrice * $item['quantity'];
            }

            // 3. Tạo đơn hàng
            $order = Order::create([
                'user_id'        => $user->id,
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'address'        => $validated['address'],
                'payment_method' => $validated['payment_method'],
                'total'          => $total,
                'status'         => 'pending',
                'referrer_id'    => $referrerId,
            ]);

            // 4. Tạo chi tiết đơn hàng
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                // if ($product->category_id == '2') {
                //     $referrerId = null;
                // }
                // if (!$oldUser && $product->category_id != '2' && !$hasHighValueCombo) {
                //     if (!$oldUser && $product->category_id != '2') {
                //         $price = $product->sale_price * 0.80;
                //     } else {
                //         $price = $product->sale_price * 0.90;
                //     }
                //     $orderItem = OrderItem::create([
                //         'order_id'     => $order->id,
                //         'product_id'   => $product->id,
                //         'product_name' => $product->name,
                //         'thumbnail'    => $product->thumbnail,
                //         'price'        => $price,
                //         'quantity'     => $item['quantity'],
                //         'referrer_id'     => $referrerId,
                //         'commission_amount'     =>  $price,
                //     ]);
                // } else {
                    if (!$oldUser && $product->category_id != '2' && $hasSingleProductOrder && $hasHighValueCombo) {
                        $salePrice = $product->sale_price * 0.80; // giảm 5%
                    } elseif (!$oldUser && $product->category_id != '2' ) {
                        $salePrice = $product->sale_price * 0.90;
                    } else {
                        $salePrice = $product->sale_price;
                    }
                    $orderItem = OrderItem::create([
                        'order_id'         => $order->id,
                        'product_id'       => $product->id,
                        'product_name'     => $product->name,
                        'thumbnail'        => $product->thumbnail,
                        'price'            => $salePrice,
                        'quantity'         => $item['quantity'],
                        'referrer_id'      => $referrerId,
                        'commission_amount' => ($salePrice * $item['quantity']) * ($product->commission_rate / 100),
                    ]);
                    // if ($oldOrderById) {
                        $level = 1;
                        $categoryId = $product->category_id;
                        $commissions = Commission::where('category_id', $categoryId)->orderBy('level')->get();

                        $referrer = User::find($order->referrer_id);

                        foreach ($commissions as $commission) {
                            if (!$referrer) break;
                            $hasPurchasedCategory2 = DB::table('order_items')
                                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                ->join('products', 'order_items.product_id', '=', 'products.id')
                                ->where('orders.user_id', $referrer->id)
                                ->where('orders.status', 'completed')
                                ->where('products.category_id', "2")
                                ->exists();
                            // return $hasPurchasedCategory2;
                            if (!$hasPurchasedCategory2) {
                                // Không đủ điều kiện nhận hoa hồng → dừng
                                // $referrer = User::find($referrer->referrer_id); // chuyển lên tuyến trên
                                // $level++;
                                continue;
                            }
                            if (!$oldOrder && !$oldUser && $product->category_id != '2') {
                                $salePrice = $product->sale_price * 0.90;
                            } elseif (!$oldUser && $hasSingleProductOrder && $product->category_id != '2') {
                                $salePrice = $product->sale_price * 0.80;
                            } else {
                                $salePrice = $product->sale_price;
                            }
                            $commissionAmount = $salePrice * ($commission->percentage / 100);
                            // return $commissionAmount;
                            CommissionUser::create([
                                'user_id' => $referrer->id,
                                'order_item_id' => $orderItem->id,
                                'level' => $level,
                                'amount' => $commissionAmount,
                            ]);
                            $referrer = User::find($referrer->referrer_id);
                            $level++;
                        }
                    // }
                // }
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Phát event
            event(new NewOrderPlaced($order));

            // 5. Trả về URL thanh toán nếu cần
            if ($validated['payment_method'] === 'VNPAY') {
                $vnp_TmnCode = '3XN2ER8H';
                $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0';
                $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
                $vnp_Returnurl = route('api.vnpay.return');

                $vnp_TxnRef = $order->id;
                $vnp_Amount = $order->total * 100;
                $vnp_IpAddr = $request->ip();

                $inputData = [
                    "vnp_Version" => "2.0.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => now()->format('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => 'vn',
                    "vnp_OrderInfo" => "Thanh toán hóa đơn phí dịch vụ",
                    "vnp_OrderType" => "billpayment",
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef,
                ];

                ksort($inputData);
                $hashData = urldecode(http_build_query($inputData));
                $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
                $paymentUrl = $vnp_Url . '?' . http_build_query($inputData) . '&vnp_SecureHash=' . $vnp_SecureHash;

                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentUrl,
                    'order_id' => $order->id
                ]);
            }

            // Nếu không dùng VNPAY
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Đặt hàng thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $vnp_HashSecret = '2J57J7TFGX8K25CXTREYFWFQGG9OXKR0';
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);

        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $queryParams = [
            'success' => 'false',
            'message' => 'Lỗi xác thực từ VNPay',
        ];

        if ($secureHash === $vnp_SecureHash) {
            if (trim($request->input('vnp_ResponseCode')) === '00') {
                $orderId = $request->input('vnp_TxnRef');
                $vnPayAmount = (int) $request->input('vnp_Amount');
                $amount = $vnPayAmount / 100;

                $order = Order::find($orderId);
                if ($order && $order->total == $amount) {
                    if ($order->status_payment !== 'paid') {
                        $order->update([
                            'status_payment' => 'paid',
                            'payment_method' => 'VNPAY',
                        ]);
                    }
                    $queryParams = [
                        'success' => 'true',
                        'message' => 'Thanh toán thành công!',
                        'order_id' => $order->id,
                    ];
                } else {
                    $queryParams['message'] = 'Số tiền không khớp hoặc đơn hàng không tồn tại.';
                }
            } else {
                $queryParams['message'] = 'Thanh toán thất bại!';
            }
        }
    }

    public function checkout(Request $request)
    {
        try {
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

            // Return the payment URL
            return response()->json([
                'success' => true,
                'payment_url' => $vnp_Url
            ]);
        } catch (\Exception $e) {
            // DB::rollBack();
            // Log::error('Đặt hàng thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getAllProduct()
    {
        $products = Product::with('category', 'group')->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function getAllCategory()
    {
        $categories = ProductCategory::all();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function agencyApi(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }

        $teamSales = Order::whereNotNull('referrer_id')->sum('total');

        // $userSale = Order::where('referrer_id', $user->id)->sum('total');
        $userSale = CommissionUser::where('user_id', $user->id)->sum('amount');

        // $commission_pending = DB::table('order_items')
        //     ->join('orders', 'order_items.order_id', '=', 'orders.id')
        //     ->where('order_items.referrer_id', $user->id)
        //     ->whereNotIn('orders.status', ['completed', 'cancelled'])
        //     ->sum('order_items.commission_amount');

        // $commission_completed = DB::table('order_items')
        //     ->join('orders', 'order_items.order_id', '=', 'orders.id')
        //     ->where('order_items.referrer_id', $user->id)
        //     ->where('orders.status', 'completed')
        //     ->sum('order_items.commission_amount');

        $commission_pending = CommissionUser::where('status', 'pending')->where('user_id', $user->id)->sum('amount');
        $commission_completed = CommissionUser::where('status', 'paid')->where('user_id', $user->id)->sum('amount');

        $count_order_completed = Order::where('referrer_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $count_user_referrer = Order::whereNotNull('referrer_id')
            ->distinct('referrer_id')
            ->count('referrer_id');
        $banners = banner::where('position', 3)->where('status', 1)->get();
        return response()->json([
            'user' => $user,
            'teamSales' => $teamSales,
            'userSale' => $userSale,
            'commission_pending' => $commission_pending,
            'commission_completed' => $commission_completed,
            'count_order_completed' => $count_order_completed,
            'count_user_referrer' => $count_user_referrer,
            'banners' => $banners,
        ]);
    }

    // public function Login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'phone' => 'required|regex:/^0[0-9]{9}$/',
    //         'password' => 'required|string|min:6',
    //     ], [
    //         'phone.required' => 'Vui lòng nhập phone.',
    //         'phone.regex' => 'Nhập sai định dạng số điện thoại',
    //         'password.required' => 'Vui lòng nhập mật khẩu.',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $credentials = $request->only('phone', 'password');
    //     $remember = $request->remember ?? false;

    //     if (Auth::attempt($credentials, $remember)) {
    //         $user = Auth::user();

    //         // Tạo token nếu dùng Sanctum hoặc Passport
    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Đăng nhập thành công!',
    //             'token' => $token,
    //             'user' => $user
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => false,
    //         'message' => 'Số điện thoại hoặc mật khẩu không đúng.'
    //     ], 401);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('phone', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Sai thông tin đăng nhập'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // public function getMembers(Request $request)
    // {
    //     $query = $request->input('q'); // Lấy giá trị tìm kiếm từ URL ?q=xxx

    //     $users = User::query();

    //     if ($query) {
    //         $users->where(function ($q) use ($query) {
    //             $q->where('full_name', 'like', '%' . $query . '%')
    //                 ->orWhere('phone', 'like', '%' . $query . '%')
    //                 ->orWhere('id', $query); // Nếu nhập đúng ID thì tìm theo ID
    //         });
    //     }

    //     $users = $users->get(); // Lấy tất cả người dùng thỏa mãn điều kiện tìm kiếm

    //     $members = $users->map(function ($user) {
    //         // Tính doanh số cá nhân
    //         $userSaleCompleted = Order::where('referrer_id', $user->id)
    //             ->where('status', 'completed') // Chỉ tính đơn đã hoàn thành
    //             ->sum('total');
    //         $userSale = Order::where('referrer_id', $user->id)
    //             ->sum('total');

    //         // Tính hoa hồng đã hoàn thành
    //         $commission_completed = DB::table('order_items')
    //             ->join('orders', 'order_items.order_id', '=', 'orders.id')
    //             ->where('order_items.referrer_id', $user->id)
    //             ->where('orders.status', 'completed')
    //             ->sum('order_items.commission_amount');

    //         return (object) [
    //             'id' => $user->id,
    //             'name' => $user->full_name ?? $user->name,
    //             'phone' => $user->phone,
    //             'avatar' => $user->avatar ?? null,
    //             'joined_at' => $user->created_at,
    //             'personal_sales_completed' => $userSaleCompleted,
    //             'personal_sales' => $userSale,
    //             'commission' => $commission_completed,
    //             'total_members' => 0,
    //             'branch_count' => 0,
    //         ];
    //     });

    //     return response()->json([
    //         'members' => $members,
    //         'query' => $query,
    //     ]);
    // }

    public function getMembers(Request $request)
        {
            $user = null;
            $authHeader = $request->header('Authorization'); // lấy header Authorization
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];

                // kiểm tra token có hợp lệ không
                $accessToken = PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                }
            }

            $query = $request->input('q');

            // Những người bạn giới thiệu trực tiếp (F1)
            $directReferrals = User::where('referrer_id', $user->id)->get();

            // Những người được F1 giới thiệu (F2)
            $indirectReferrals = collect();
            foreach ($directReferrals as $ref) {
                $indirectReferrals = $indirectReferrals->merge(
                    User::where('referrer_id', $ref->id)->get()
                );
            }

            // Gộp toàn bộ F2
            $targetUsers = $indirectReferrals;

            // Nếu có query tìm kiếm
            if ($query) {
                $targetUsers = $targetUsers->filter(function ($u) use ($query) {
                    return str_contains(strtolower($u->full_name), strtolower($query)) ||
                        str_contains($u->phone, $query) ||
                        (string)$u->id === $query;
                });
            }

            // Xử lý doanh số và hoa hồng
            $members = $targetUsers->map(function ($user) {
                $userSaleCompleted = Order::where('referrer_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('total');

                $userSale = Order::where('referrer_id', $user->id)
                    ->sum('total');

                $commission_completed = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('order_items.referrer_id', $user->id)
                    ->where('orders.status', 'completed')
                    ->sum('order_items.commission_amount');

                return (object)[
                    'id' => $user->id,
                    'name' => $user->full_name ?? $user->name,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar ?? null,
                    'joined_at' => $user->created_at,
                    'personal_sales_completed' => $userSaleCompleted,
                    'personal_sales' => $userSale,
                    'commission' => $commission_completed,
                    'total_members' => 0,
                    'branch_count' => 0,
                ];
            });

            return response()->json([
                'members' => $members->values(),
                'query' => $query,
            ]);
        }


    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng đã hoàn thành hoặc đã bị hủy.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Trả lại stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            // Cập nhật trạng thái đơn
            $order->status = 'cancelled';

            if ($order->payment_method === 'VNPAY' && $order->status_payment === 'paid') {
                $user = User::find($order->user_id);
                if ($user) {
                    $user->balance += $order->total;
                    $user->save();

                    $order->status_payment = 'refunded';
                }
            }

            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được hủy thành công và trả lại tồn kho.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Hủy đơn không thành công, vui lòng thử lại.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request)
    {
        $user = null;
        // return $request;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            // kiểm tra token có hợp lệ không
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $user = User::where('id', $user->id)->first();

        $validated = $request->validate([
            'name' => 'unique:users,name,' . $user->id,
            'full_name' => 'string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'phone' => 'string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            // return $validated['avatar'];

        }

        $user->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
        // return redirect()->route('account.index')->with('success', 'Cập nhật người dùng thành công');
    }

    public function zaloSignup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id' => 'nullable|string|max:255',
            'avatar' => 'nullable|string',
            'phone' => 'nullable',
            'referrer_id' => 'nullable',
        ]);
        if (isset($validated['name']) && str_starts_with($validated['name'], 'User')) {
            $validated['name'] = 'user name';
        }
        $referrer = User::Find($validated['referrer_id'] ?? null);
        
        $user = User::create([
            'name' => "user" . $validated['id'],
            'full_name' => $validated['name'],
            'phone' => $validated['phone'],
            'avatar' => $validated['avatar'], // có thể lưu URL ảnh
            'balance' => appSetting::first()->donated,
            'referrer_id' => $referrer?->id,
            'role' => 2, // giả sử 2 là role mặc định
            'password' => Hash::make('123456'), // password ngẫu nhiên
        ]);

        // Đăng nhập và trả về token
        $token = $user->createToken('zalo-login')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Đăng ký và đăng nhập thành công',
        ]);
    }

    public function BankAccount(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        // return response()->json($authHeader);

        $accounts = BankAccount::where('user_id', $user->id)->first();
        return response()->json($accounts);
    }

    public function createOrUpdate(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $request->validate([
            'id' => 'nullable|integer|exists:bank_accounts,id',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
        ]);

        if ($request->filled('id')) {
            // Cập nhật
            $account = BankAccount::where('user_id', $user->id)->findOrFail($request->id);
            $account->update($request->only(['account_name', 'account_number', 'bank_name']));
            return response()->json(['message' => 'Cập nhật tài khoản thành công', 'data' => $account]);
        } else {
            // Tạo mới
            $account = BankAccount::create([
                'user_id' => $user->id,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
            ]);
            return response()->json(['message' => 'Tạo tài khoản thành công', 'data' => $account]);
        }
    }

    public function indexWithdrawRequest(Request $request)
    {
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $requests = WithdrawRequest::where('user_id', $user->id)->orderByDesc('created_at')->get();
        return response()->json($requests);
    }

    public function storeWithdrawRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'note' => 'nullable|string',
        ]);
        $user = null;
        $authHeader = $request->header('Authorization'); // lấy header Authorization
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }
        $accounts = BankAccount::where('user_id', $user->id)->first();
        if ($accounts) {
            $withdraw = WithdrawRequest::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'note' => $request->note,
                'status' => 'pending',
            ]);
            $user->decrement('balance', $request->amount);
            return response()->json([
                'message' => 'Yêu cầu rút tiền đã được tạo.',
                'data' => $withdraw,
            ]);
        } else {
            return response()->json([
                'message' => 'Bạn chưa điền thông tin thẻ thanh toán.',
                'error' => true,
            ], 401);
        }
    }

    public function bankTransferNotify(Request $request)
    {
        // Giả sử bên Zalo hoặc đối tác gửi về những thông tin sau:
        $orderId = $request->input('order_id');
        $amount = $request->input('amount');
        $status = $request->input('status'); // expected: 'success'
        $transactionId = $request->input('trans_id'); // Mã giao dịch ngân hàng (nếu có)

        // Kiểm tra đơn hàng tồn tại
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại.',
            ], 404);
        }

        // Kiểm tra số tiền và trạng thái thanh toán
        if ($status === 'success' && $order->total == $amount) {
            if ($order->status_payment !== 'paid') {
                $order->update([
                    'status_payment' => 'paid',
                    'payment_method' => 'BANK_TRANSFER',
                    'transaction_id' => $transactionId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán chuyển khoản thành công.',
                'order_id' => $order->id,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Thanh toán không hợp lệ hoặc số tiền không khớp.',
            ]);
        }
    }
    public function handleZaloCallback(Request $request)
    {
        Log::info('Zalo Bank Transfer Notify:', $request->all());
        $privateKey = env('ZALO_PRIVATE_KEY'); // bạn nên đặt khóa này trong file .env

        $data = $request->input('data');
        $mac = $request->input('mac');
        $overallMac = $request->input('overallMac');

        // Bước 1: Kiểm tra MAC
        $dataForMac = "appId={$data['appId']}&amount={$data['amount']}&description={$data['description']}&orderId={$data['orderId']}&message={$data['message']}&resultCode={$data['resultCode']}&transId={$data['transId']}";
        $reqMac = hash_hmac('sha256', $dataForMac, $privateKey);

        if ($reqMac !== $mac) {
            Log::warning('Sai MAC trong callback');
            return response()->json([
                'returnCode' => -1,
                'returnMessage' => 'Sai MAC, dữ liệu không hợp lệ',
            ]);
        }

        // Bước 2: Kiểm tra overallMac
        ksort($data); // sắp xếp theo thứ tự từ điển
        $dataOverallMac = collect($data)->map(function ($v, $k) {
            return "$k=$v";
        })->implode('&');

        $reqOverallMac = hash_hmac('sha256', $dataOverallMac, $privateKey);
        if ($reqOverallMac !== $overallMac) {
            Log::warning('Sai overallMac trong callback');
            return response()->json([
                'returnCode' => -2,
                'returnMessage' => 'Sai overallMac, dữ liệu không hợp lệ',
            ]);
        }

        // Bước 3: Kiểm tra và cập nhật đơn hàng
        $order = Order::where('order_id', $data['orderId'])->first();
        if (!$order) {
            return response()->json([
                'returnCode' => -3,
                'returnMessage' => 'Không tìm thấy đơn hàng',
            ]);
        }

        // Kiểm tra xem đơn đã được xử lý chưa
        if ($order->status_payment === 'paid') {
            return response()->json([
                'returnCode' => 2,
                'returnMessage' => 'Đơn hàng đã được thanh toán',
            ]);
        }

        if ($data['resultCode'] == 1) {
            $order->status_payment = 'paid';
            $order->save();

            return response()->json([
                'returnCode' => 1,
                'returnMessage' => 'Thanh toán thành công',
            ]);
        } else {
            return response()->json([
                'returnCode' => -4,
                'returnMessage' => 'Thanh toán thất bại',
            ]);
        }
    }
    public function getZaloUserInfo(Request $request)
    {
        $accessToken = $request->input('access_token');
        $code = $request->input('code');

        if (!$accessToken || !$code) {
            return response()->json(['message' => 'Thiếu access_token hoặc code'], 400);
        }

        $response = Http::withHeaders([
            'access_token' => $accessToken,
            'code' => $code,
            'secret_key' => env('ZALO_SECRET_KEY'),
        ])->get('https://graph.zalo.me/v2.0/me/info');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'message' => 'Lỗi khi gọi Zalo API',
            'error' => $response->body(),
        ], $response->status());
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Commission;
use App\Models\CommissionUser;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // $orders = Order::with('items.product', 'referrer')->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->orderby('status')->paginate(7);
    //     // return view('admin.orders.index', compact('orders'));
    //     $query = Order::with('items.product', 'referrer');
    //     if ($request->has('search') && $request->search != '') {
    //         $keyword = $request->search;
    //         $query->where(function ($q) use ($keyword) {
    //             $q->where('name', 'like', "%{$keyword}%")
    //                 ->orWhere('id', 'like', "%{$keyword}%")
    //                 ->orWhere('phone', 'like', "%{$keyword}%");
    //         });
    //     }

    //     if ($request->status && $request->status !== 'all') {
    //         $query->where('status', $request->status);
    //     }

    //     $orders = $query
    //         ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
    //         ->orderBy('status')
    //         ->orderByDesc('created_at')
    //         ->paginate(10)
    //         ->withQueryString(); // giữ lại query khi chuyển trang

    //     return view('admin.orders.index', compact('orders'));
    // }

    public function index(Request $request)
    {
        $query = Order::with('items.product', 'referrer');
        // ->whereDate('created_at', Carbon::today()); // Chỉ lấy đơn hôm nay

        // Tìm kiếm theo tên, ID, hoặc số điện thoại
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('id', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $from = $request->from_date
                ? Carbon::parse($request->from_date)->startOfDay()
                : Carbon::minValue();

            $to   = $request->to_date
                ? Carbon::parse($request->to_date)->endOfDay()
                : Carbon::now()->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }
        // Lọc theo trạng thái nếu có
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('items.product'); // Load các sản phẩm trong đơn
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function updateStatusOld(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            $statusFlow = [
                'pending' => 'approved',
                'approved' => 'packed',
                'packed' => 'shipped',
                'shipped' => 'completed',
                'completed' => 'completed', // đã hoàn thành thì không nâng cấp nữa
                'cancelled' => 'cancelled', // hủy rồi thì giữ nguyên
            ];
            $status = $order->status;

            if ($order->status == 'completed' || $order->status == 'cancelled') {
                return redirect()->back()->with('success', 'Không thể cập nhật trạng thái!');
            }

            if ($request->status != $order->status) {
                $status = $request->status;
            }
            // return $status;
            if ($request->status === 'cancelled') {

                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                    $commission_user = CommissionUser::where('order_item_id', $item->id)->get();
                    foreach ($commission_user as $commission) {
                        $commission->status = 'cancel';
                        $commission->save();
                    }
                    // return $product;
                }
                if ($order->payment_method == 'VNPAY' && $order->status_payment == 'paid') {
                    $user = User::find($order->user_id);
                    if ($user) {
                        $user->balance += $order->total;
                        $user->save();
                        $order->status_payment = 'refunded';
                        $order->save();
                    }
                }
            }
            $currentStatus = $status;
            $nextStatus = $statusFlow[$currentStatus] ?? $currentStatus;

            $order->status = $nextStatus;
            if ($status === 'completed') {
                $order->status_payment = 'paid';
            }
            $order->save();

            if ($status === 'completed' && $order->referrer_id) {
                $totalCommission = 0;
                $orderItems = OrderItem::where('order_id', $order->id)->get();

                foreach ($orderItems as $item) {
                    $product = Product::find($item->product_id);
                    if (!$product) continue;
                    $totalCommission += $item->commission_amount;

                    // $categoryId = $product->category_id;
                    // $commissions = Commission::where('category_id', $categoryId)->orderBy('level')->get();

                    // $referrer = User::find($order->referrer_id);

                    // foreach ($commissions as $commission) {
                    //     if (!$referrer) break; // Nếu không còn người giới thiệu thì dừng

                    //     $commissionAmount = $product->sale_price * ($commission->percentage / 100);
                    //     $referrer->balance += $commissionAmount;
                    //     $referrer->save();
                    //     $referrer = User::find($referrer->referrer_id);
                    //     // return $referrer;

                    // }
                    // return $item->id;
                    $commission_user1 = CommissionUser::where('order_item_id', $item->id)->where('agency_rights', true)->get();
                    foreach ($commission_user1 as $commission) {
                        $referrer = User::find($commission->user_id);
                        if (!$referrer) break;
                        $referrer->balance += $commission->amount;
                        $referrer->save();
                        $commission->status = 'paid';
                        $commission->save();
                    }
                    // if ($product->category_id == 2 && $product->sale_price >= 1000000) {
                    $commission_user2 = CommissionUser::where('user_id', $item->referrer_id)->where('agency_rights', false)->get();
                    foreach ($commission_user2 as $commission) {
                        $user = User::find($commission->user_id);
                        if (!$user) break;
                        $hasHighValueCombo = DB::table('orders')
                            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                            ->join('products', 'order_items.product_id', '=', 'products.id')
                            ->where('orders.user_id', $user->id)
                            ->where('orders.status_payment', 'paid')
                            ->where('products.category_id', 2)
                            ->where('products.sale_price', '>=', 1000000)
                            ->exists();
                        if ($hasHighValueCombo || ($product->category_id == 2 && $product->sale_price >= 1000000)) {
                            $user->balance += $commission->amount;
                            $user->save();
                            $commission->status = 'paid';
                            $commission->agency_rights = true;
                            $commission->save();
                        }
                    }
                    // }

                    // if ($product->category_id == 2) {
                    //     // Hoa hồng cho người giới thiệu cấp 2 (f2)
                    //     $referrerF2 = User::find($order->referrer_id);
                    //     if ($referrerF2 && $totalCommission > 0) {
                    //         $referrerF2->balance += $item->commission_amount;
                    //         $referrerF2->save();
                    //     }

                    //     // Hoa hồng cho người giới thiệu cấp 1 (f1)
                    //     $referrerF1 = User::find($referrerF2->referrer_id); // Tìm người giới thiệu f1 của f2
                    //     if ($referrerF1 && $totalCommission > 0) {
                    //         $commissionForF1 = $item->commission_amount / 8; // F1 nhận 20% hoa hồng của f2
                    //         $referrerF1->balance += $commissionForF1;
                    //         $referrerF1->save();
                    //     }
                    // }
                }
                // if ($product->category_id != 2) {
                // $referrer = User::find($order->referrer_id);

                // if ($referrer && $totalCommission > 0) {
                //     $referrer->balance += $totalCommission;
                //     $referrer->save();
                // }
                // }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            $statusFlow = [
                'pending' => 'approved',
                'approved' => 'packed',
                'packed' => 'shipped',
                'shipped' => 'completed',
                'completed' => 'completed', // đã hoàn thành thì không nâng cấp nữa
                'cancelled' => 'cancelled', // hủy rồi thì giữ nguyên
            ];
            $status = $order->status;

            if ($order->status == 'completed' || $order->status == 'cancelled') {
                return redirect()->back()->with('success', 'Không thể cập nhật trạng thái!');
            }

            if ($request->status != $order->status) {
                $status = $request->status;
            }
            // return $status;
            if ($request->status === 'cancelled') {

                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                    $commission_user = CommissionUser::where('order_item_id', $item->id)->get();
                    foreach ($commission_user as $commission) {
                        $commission->status = 'cancel';
                        $commission->save();
                    }
                    // return $product;
                }
                if ($order->payment_method == 'VNPAY' && $order->status_payment == 'paid') {
                    $user = User::find($order->user_id);
                    if ($user) {
                        $user->balance += $order->total;
                        $user->save();
                        $order->status_payment = 'refunded';
                        $order->save();
                    }
                }
            }
            $currentStatus = $status;
            $nextStatus = $statusFlow[$currentStatus] ?? $currentStatus;

            $order->status = $nextStatus;
            if ($status === 'completed') {
                $order->status_payment = 'paid';
            }
            $order->save();

            if ($status === 'completed' && $order->referrer_id && $order->user_id) {
                $totalCommission = 0;
                $orderItems = OrderItem::where('order_id', $order->id)->get();
                foreach ($orderItems as $item) {
                    $product = Product::find($item->product_id);
                    if (!$product) continue;
                    $totalCommission += $item->commission_amount;
                }
            }


            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function updateStatusPayment(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            $statusFlow = [
                'pending' => 'paid',
                'paid' => 'paid', // đã hoàn thành thì không nâng cấp nữa
                'refunded' => 'refunded', // hủy rồi thì giữ nguyên
                'failed' => 'failed', // hủy rồi thì giữ nguyên
            ];
            $statusPayment = $order->status_payment;
            if ($statusPayment == 'paid' || $statusPayment == 'refunded' || $statusPayment == 'failed') {
                return redirect()->back()->with('success', 'Không thể cập nhật trạng thái thanh toán!');
            }

            if ($request->status_payment != $order->status_payment) {
                $statusPayment = $request->status_payment;
            }

            $currentStatus = $statusPayment;
            $nextStatus = $statusFlow[$currentStatus] ?? $currentStatus;

            $order->status_payment = $nextStatus;
            $order->save();
            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function invoice(Order $order)
    {
        $order->load('items.product'); // Load các sản phẩm trong đơn
        return view('admin.orders.invoice', compact('order'));
    }

    // OrderController.php
    public function history()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('ordersHistory', compact('orders'));
    }

    public function historyAffilate()
    {
        $orders = Order::with('items.product')
            ->where('referrer_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('ordersHistoryAffilate', compact('orders'));
    }

    public function cancel(Order $order)
    {
        // if (in_array($order->status, ['completed', 'cancelled'])) {
        //     return back()->with('error', 'Không thể hủy đơn hàng đã hoàn thành hoặc đã bị hủy.');
        // }

        // $order->status = 'cancelled';
        // $order->save();

        // return back()->with('success', 'Đơn hàng đã được hủy thành công.');
        // Chỉ cho hủy khi status chưa hoàn thành và chưa hủy
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Không thể hủy đơn hàng đã hoàn thành hoặc đã bị hủy.');
        }

        DB::beginTransaction();
        try {
            // 1. Trả lại stock
            // Giả sử Order model có relation 'items' đến OrderItem
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    // Tăng lại stock bằng số lượng đã đặt
                    $product->increment('stock', $item->quantity);
                }
                $commission_user = CommissionUser::where('order_item_id', $item->id)->get();
                foreach ($commission_user as $commission) {
                    $commission->status = 'cancel';
                    $commission->save();
                }
            }

            // 2. Cập nhật trạng thái đơn
            $order->status = 'cancelled';
            if ($order->payment_method == 'VNPAY' && $order->status_payment == 'paid') {
                $user = User::find($order->user_id);
                if ($user) {
                    $user->balance += $order->total;
                    $user->save();
                    $order->status_payment = 'refunded';
                    $order->save();
                }
            }
            $order->save();

            DB::commit();
            return back()->with('success', 'Đơn hàng đã được hủy và trả lại tồn kho thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::error('Cancel order failed: ' . $e->getMessage());
            return back()->with('error', 'Hủy đơn không thành công, vui lòng thử lại.');
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherApiController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $vouchers = Voucher::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->whereColumn('used', '<', 'quantity')
            ->get();

        return response()->json([
            'status' => true,
            'vouchers' => $vouchers
        ]);
    }

    // Kiểm tra voucher theo mã
    public function show($code)
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'status' => false,
                'message' => 'Mã giảm giá không tồn tại.'
            ], 404);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'status' => false,
                'message' => 'Mã giảm giá không còn hiệu lực hoặc đã hết lượt sử dụng.'
            ], 400);
        }

        return response()->json([
            'status' => true,
            'voucher' => $voucher
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.'
            ]);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc đã hết lượt sử dụng.'
            ]);
        }

        if ($request->order_amount < $voucher->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không đủ giá trị tối thiểu để áp dụng mã này.'
            ]);
        }

        // Tính giảm giá
        $discount = $voucher->calculateDiscount($request->order_amount);

        return response()->json([
            'success' => true,
            'voucher' => $voucher,
            'discount' => $discount,
            'message' => "Áp dụng thành công - giảm " . number_format($discount) . "đ"
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'end_date' => 'required|date',
        ]);

        $validated['start_date'] = now();
        $validated['quantity'] = 1;
        $voucher = \App\Models\Voucher::create($validated);
        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ]);
    }
}

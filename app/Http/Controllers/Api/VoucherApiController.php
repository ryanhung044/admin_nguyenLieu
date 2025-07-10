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
}

<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    // Hiển thị danh sách voucher
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(10);
        return view('admin.vocher.index', compact('vouchers'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('admin.vocher.create');
    }

    // Lưu voucher mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);
        try {
            Voucher::create($validated);

            return redirect()->route('admin.vouchers.index')->with('success', 'Tạo voucher thành công.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi thêm mới: ' . $e->getMessage());
        }
    }

    // Hiển thị chi tiết voucher
    public function show(Voucher $voucher)
    {
        return view('admin.vocher.show', compact('voucher'));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Voucher $voucher)
    {
        return view('admin.vocher.edit', compact('voucher'));
    }

    // Cập nhật voucher
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);
        try {

            $voucher->update($validated);

            return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    // Xóa voucher
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Xóa voucher thành công.');
    }
}

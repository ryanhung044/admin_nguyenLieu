<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::with('category')->orderBy('category_id')->orderBy('level')->get();
        return view('admin.commissions.index', compact('commissions'));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        return view('admin.commissions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'level' => 'required|integer|min:1',
            'percentage' => 'required|numeric|min:0',
        ]);

        Commission::create($request->only(['category_id', 'level', 'percentage']));

        return redirect()->route('admin.commissions.index')->with('success', 'Đã thêm cấu hình hoa hồng.');
    }

    public function edit($id)
    {
        $commission = Commission::findOrFail($id);
        $categories = ProductCategory::all();
        return view('admin.commissions.edit', compact('commission', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'level' => 'required|integer|min:1',
            'percentage' => 'required|numeric|min:0',
        ]);

        $commission = Commission::findOrFail($id);
        $commission->update($request->only(['category_id', 'level', 'percentage']));

        return redirect()->route('admin.commissions.index')->with('success', 'Đã cập nhật cấu hình hoa hồng.');
    }

    public function destroy($id)
    {
        $commission = Commission::findOrFail($id);
        $commission->delete();

        return redirect()->route('admin.commissions.index')->with('success', 'Đã xóa cấu hình hoa hồng.');
    }
}

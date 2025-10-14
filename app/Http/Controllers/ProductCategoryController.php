<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::with('parent')->orderBy('sort_order')->get();
        return view('admin.product_categories.index', compact('categories'));
    }

    // Show form tạo mới danh mục
    public function create()
    {
        $categories = ProductCategory::all(); // Lấy tất cả danh mục cha
        return view('admin.product_categories.create', compact('categories'));
    }

    // Lưu danh mục mới
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_categories,slug',
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        // Nếu slug không được nhập, tự động tạo từ tên
        $baseSlug = Str::slug($request->slug ?? $request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (ProductCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        // $slug = $request->slug ?? Str::slug($request->name);

        // Nếu sort_order không được nhập, lấy số thứ tự lớn nhất hiện tại và cộng thêm 1
        $sortOrder = $request->sort_order ?? ProductCategory::max('sort_order') + 1;

        // Xử lý việc upload ảnh nếu có
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_images', 'public');
        } else {
            $imagePath = null;
        }

        // Lưu danh mục vào cơ sở dữ liệu
        ProductCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'image' => $imagePath,
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('admin.product-categories.index')->with('success', 'Danh mục đã được thêm thành công!');
    }



    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        $categories = ProductCategory::all();  // Lấy tất cả danh mục để chọn danh mục cha
        return view('admin.product_categories.edit', compact('category', 'categories'));
    }

    // Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255' . $id,
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $category = ProductCategory::findOrFail($id);
            $baseSlug = Str::slug($request->slug ?? $request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (ProductCategory::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $sortOrder = $request->sort_order ?? ProductCategory::max('sort_order') + 1;

            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($category->image && file_exists(storage_path('app/public/' . $category->image))) {
                    unlink(storage_path('app/public/' . $category->image));
                }
                $imagePath = $request->file('image')->store('category_images', 'public');
            } else {
                $imagePath = $category->image;
            }

            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'parent_id' => $request->parent_id,
                'image' => $imagePath,
                'sort_order' => $sortOrder,
            ]);

            return redirect()->route('admin.product-categories.index')->with('success', 'Danh mục đã được cập nhật thành công!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }


    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->id == 2) {
            return redirect()->route('admin.product-categories.index')->with('error', 'Không thể xóa danh mục này');
        }
        $productCategory->delete();
        return redirect()->route('admin.product-categories.index')->with('success', 'Xóa danh mục thành công');
    }
}

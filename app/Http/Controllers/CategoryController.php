<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('parent')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_category_id')->get(); // Lấy tất cả danh mục cha
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        // Tạo slug nếu chưa nhập
        $slug = $request->slug ?? Str::slug($request->title);

        // Lưu vào CSDL
        Category::create([
            'title' => $request->title,
            'slug' => $slug,
            'parent_category_id' => $request->parent_category_id,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Chuyên mục đã được thêm thành công!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->whereNull('parent_category_id')->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'parent_category_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'description' => 'nullable|string',
        ]);
    
        $slug = $request->slug ?? Str::slug($request->title);
    
        $category->update([
            'title' => $request->title,
            'slug' => $slug,
            'parent_category_id' => $request->parent_category_id,
            'description' => $request->description,
        ]);
    
        return redirect()->route('admin.categories.index')->with('success', 'Chuyên mục đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công');
    }
}

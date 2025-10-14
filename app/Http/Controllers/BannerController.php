<?php

namespace App\Http\Controllers;

use App\Models\banner;
use App\Http\Requests\StorebannerRequest;
use App\Http\Requests\UpdatebannerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::all();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'position' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|boolean',
        ]);
        try {

            $banner = new Banner();
            $banner->title = $request->title;
            $banner->position = $request->position;
            $banner->link = $request->link;
            $banner->start_date = $request->start_date;
            $banner->end_date = $request->end_date;
            $banner->status = $request->status;

            // Xử lý file upload
            if ($request->hasFile('image')) {
                $banner->image = $request->file('image')->store('banners', 'public');
            }
            $banner->save();

            return redirect()->route('admin.banners.index')->with('success', 'Banner đã được thêm thành công.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi thêm mới: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(banner $banner)
    {
        if ($banner) {
            return view('admin.banners.edit', compact('banner'));
        }
        return back()->withInput()->with('error', 'Không thể truy cập');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url',
            'position' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try {

            $banner->title = $request->title;
            $banner->link = $request->link;
            $banner->position = $request->position;
            $banner->start_date = $request->start_date;
            $banner->end_date = $request->end_date;
            $banner->status = $request->status;

            // Nếu có ảnh mới thì lưu và xóa ảnh cũ
            // if ($request->hasFile('image')) {
            //     if ($banner->image) {
            //         Storage::delete('public/' . $banner->image);
            //     }

            //     $imagePath = $request->file('image')->store('banners', 'public');
            //     $banner->image = $imagePath;
            // }

            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($banner->image && Storage::exists('public/' . $banner->image)) {
                    Storage::delete('public/' . $banner->image);
                }
                // Lưu ảnh mới
                $banner->image = $request->file('image')->store('banners', 'public');
            }
            // if ($request->hasFile('image')) {
            //     // Xóa ảnh cũ nếu có
            //     if ($banner->image && file_exists(public_path($banner->image))) {
            //         unlink(public_path($banner->image));
            //     }

            //     // Lưu ảnh mới vào public/images/banners
            //     $imageName = time() . '.' . $request->image->extension();
            //     $request->image->move(public_path('images/banners'), $imageName);
            //     $banner->image = 'images/banners/' . $imageName;
            // }

            $banner->save();

            return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(banner $banner)
    {
        try {
            if ($banner->image && Storage::exists($banner->image)) {
                Storage::delete($banner->image);
            }

            $banner->delete();

            return redirect()->route('admin.banners.index')->with('success', 'Đã xoá banner thành công.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = !$banner->status; // Đảo ngược trạng thái
        $banner->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}

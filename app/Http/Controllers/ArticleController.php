<?php

namespace App\Http\Controllers;

use App\Models\article;
use App\Http\Requests\StorearticleRequest;
use App\Http\Requests\UpdatearticleRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = article::orderby('category_id')->get();
        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.articles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'summary' => 'nullable|string|max:1000',
                'content' => 'nullable|string',
                'slug' => 'nullable|string|unique:articles,slug',
                'image' => 'nullable|image',
                'category_id' => 'nullable|exists:categories,id',
            ]);

            // Tạo slug nếu không có
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

            // Lưu ảnh đại diện
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('articles', 'public');
            }

            // Tạo bài viết
            $article = article::create($data);

            return redirect()->route('admin.articles.index')->with('success', 'Thêm bài viết thành công!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi thêm mới: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->take(3)
            ->get();

        return view('article_detail', compact('article', 'relatedArticles'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(article $article)
    {
        $categories = Category::all();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try {
            // Update the article's attributes
            $article->title = $validated['title'];
            $article->summary = $validated['summary'];
            $article->content = $validated['content'];
            $article->slug = $validated['slug'] ?? $this->generateSlug($validated['title']); // Generate slug if not provided
            $article->category_id = $validated['category_id'];
            $article->published_at = $validated['published_at'];

            // Handle image upload if a new image is provided
            if ($request->hasFile('image')) {
                // Delete the old image if exists
                if ($article->image && Storage::exists('public/' . $article->image)) {
                    Storage::delete('public/' . $article->image);
                }

                // Store the new image
                $article->image = $request->file('image')->store('articles', 'public');
            }

            // Save the updated article
            $article->save();

            // Redirect back to the articles index page with a success message
            return redirect()->route('admin.articles.index')->with('success', 'Bài viết đã được cập nhật thành công.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(article $article)
    {
        try {
            $article->delete();
            return redirect()->route('admin.articles.index')->with('success', 'Xóa bài viết thành công');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }

    public function articleAffilate()
    {
        return view('article_affilate');
    }
}

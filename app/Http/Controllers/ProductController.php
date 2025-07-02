<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductCategory;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'group')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $attributes = Attribute::all();
        $categories = ProductCategory::all();
        $groups = ProductGroup::all();
        return view('admin.products.create', compact('categories', 'groups', 'attributes'));
    }

    public function store(Request $request)
    {
        // return $request;
        // return $request;
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required',
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image',
            'images.*' => 'nullable|image',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'slug' => 'nullable|string|unique:products,slug',
            'sku' => 'nullable|string|unique:products,sku',
            'category_id' => 'nullable|exists:product_categories,id',
            'group_id' => 'nullable|exists:product_groups,id',
            'commission_rate' => 'nullable',
        ]);

        // Tạo slug nếu không nhập
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sku'] = $data['sku'] ?? Str::slug($data['name']);

        // Lưu ảnh đại diện
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        // Lưu sản phẩm trước để lấy id
        $product = Product::create($data);

        // Lưu nhiều ảnh sản phẩm (nếu có)
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('product_images', 'public');
            }
            $product->images = json_encode($paths);
            $product->save();
        }
        // if ($request->has('variants')) {
        //     foreach ($request->variants as $variantData) {
        //         $product->variants()->create($variantData);
        //     }
        // }
        // 2. Lưu các biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                // return $variant;
                $productVariant = $product->variants()->create([
                    // 'sku' => $variant['sku'],
                    'price' => $variant['price'],
                    'sale_price' => $variant['sale_price'] ?? null,
                    'stock' => $variant['stock'],
                    // 'image' => $variant['image'] ?? null, // Xử lý file nếu cần
                ]);
                // dd($variant['image']);
                if (isset($variant['image']) && $variant['image']) {
                    $path = $variant['image']->store('products', 'public');
                    $productVariant->update(['image' => $path]);
                }

                // 3. Gán giá trị thuộc tính cho biến thể
                // return $variant;
                // $attributeValues = explode(',', $variant['attributes']);
                foreach ($variant['attributes'] as $attr) {
                    $attributeId = $attr['attribute_id'];
                    $value = trim($attr['value']);

                    // Tìm hoặc tạo giá trị thuộc tính
                    $attributeValue = AttributeValue::firstOrCreate([
                        'attribute_id' => $attributeId,
                        'value' => $value,
                    ]);

                    // Gán giá trị thuộc tính cho biến thể
                    $productVariant->attributeValues()->attach($attributeValue->id);
                }
            }
        }
        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function show($slug, Request $request)
    {
        $product = Product::with('variants.attributeValues.attribute')->where('slug', $slug)->firstOrFail();

        $variantData = $product->variants->map(function ($variant) {
            return [
                'attributes' => $variant->attributeValues->pluck('value', 'attribute.name')->toArray(),
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'stock' => $variant->stock,
                'image_url' => $variant->image, // nếu có
            ];
        })->toArray();
        if ($request->has('ref')) {
            session(['referrer_id' => $request->query('ref')]);
        }
        return view('product_detail', compact('product', 'variantData'));
    }

    public function edit(Product $product)
    {
        $product->load(['variants.attributeValues.attribute']);

        $variantData = [];
        foreach ($product->variants as $variant) {
            $attributes = [];
            foreach ($variant->attributeValues as $attrValue) {
                if ($attrValue->attribute) { // Kiểm tra nếu attribute không phải null
                    $attributes[$attrValue->attribute->name] = $attrValue->value;
                } else {
                    // Nếu không có attribute, có thể log hoặc gán giá trị mặc định
                    $attributes['unknown_attribute'] = $attrValue->value;
                }
            }

            $variantData[] = [
                'attributes'   => $attributes, // JSON nếu muốn
                // 'sku'          => $variant->sku,
                'price'        => $variant->price,
                'sale_price'   => $variant->sale_price,
                'stock'        => $variant->stock,
                'image_url'    => $variant->image, // hoặc Storage::url(...)
            ];
        }
        $attributes = Attribute::all();

        // return $variantData;
        $categories = ProductCategory::all();
        $groups = ProductGroup::all();
        return view('admin.products.edit', compact('product', 'categories', 'groups', 'variantData', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id); // Tìm sản phẩm theo ID

        // Validate dữ liệu
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required',
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image',
            'images.*' => 'nullable|image',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id, // Không kiểm tra duy nhất với sản phẩm hiện tại
            'sku' => 'nullable|string|unique:products,sku,' . $product->id, // Không kiểm tra duy nhất với sản phẩm hiện tại
            'category_id' => 'nullable|exists:product_categories,id',
            'group_id' => 'nullable|exists:product_groups,id',
            'commission_rate' => 'nullable',
        ]);

        // Tạo slug nếu không nhập
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sku'] = $data['sku'] ?? Str::slug($data['name']);

        // Lưu ảnh đại diện nếu có
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu có
            if ($product->thumbnail && Storage::exists('public/' . $product->thumbnail)) {
                Storage::delete('public/' . $product->thumbnail);
            }
            // Lưu ảnh mới
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        // Cập nhật thông tin sản phẩm
        $product->update($data);

        // Cập nhật ảnh sản phẩm nếu có
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('product_images', 'public');
            }
            // Cập nhật ảnh mới
            $product->images = json_encode($paths);
            $product->save();
        }
        // return $request->variants;
        // 🔥 BẮT ĐẦU XỬ LÝ BIẾN THỂ 🔥
        if ($request->has('variants')) {
            // 1️⃣ Lưu lại ảnh cũ trước khi xoá
            $oldImages = $product->variants()->pluck('image', 'id')->toArray();

            // 2️⃣ Xoá biến thể cũ
            $product->variants()->delete();

            // 3️⃣ Tạo mới biến thể (giữ ảnh cũ nếu không update)
            foreach ($request->variants as $index => $variant) {
                if (isset($variant['delete']) && $variant['delete'] == 1) {
                    continue; // Bỏ qua biến thể này (không tạo mới!)
                }
                $productVariant = $product->variants()->create([
                    'price' => $variant['price'],
                    'sale_price' => $variant['sale_price'] ?? null,
                    'stock' => $variant['stock'] ?? 0,
                ]);

                // ✅ Xử lý ảnh biến thể
                if (isset($variant['image']) && $variant['image'] instanceof \Illuminate\Http\UploadedFile) {
                    // Có ảnh mới upload
                    $path = $variant['image']->store('products', 'public');
                    $productVariant->update(['image' =>  $path]);
                } elseif (isset($variant['image']) && is_string($variant['image']) && $variant['image'] !== '') {
                    // Có path ảnh từ form (nếu có)
                    $productVariant->update(['image' => $variant['image']]);
                } else {
                    // Không có ảnh mới → lấy lại ảnh cũ (theo index)
                    $oldImage = $oldImages[array_keys($oldImages)[$index] ?? null] ?? null;
                    if ($oldImage) {
                        $productVariant->update(['image' => $oldImage]);
                    }
                }

                // ✅ Gán lại attributes cho biến thể
                if (isset($variant['attributes'])) {
                    $attributes = is_string($variant['attributes'])
                        ? json_decode($variant['attributes'], true)
                        : $variant['attributes'];

                    if ($attributes && is_array($attributes)) {
                        $attributeValueIds = [];
                        foreach ($attributes as $name => $value) {
                            $attribute = Attribute::firstOrCreate(['name' => $name]);
                            $attributeValue = AttributeValue::firstOrCreate([
                                'attribute_id' => $attribute->id,
                                'value' => is_array($value) ? ($value['value'] ?? '') : $value,
                            ]);
                            $attributeValueIds[] = $attributeValue->id;
                        }
                        $productVariant->attributeValues()->sync($attributeValueIds);
                    }
                }
            }
        }


        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }


    public function destroy(Product $product)
    {
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        if ($product->images) {
            foreach (json_decode($product->images) as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/ckeditor', $filename, 'public');

            $url = asset('storage/' . $path);

            return response()->json([
                'uploaded' => true,
                'url' => $url
            ]);
        }

        return response()->json(['uploaded' => false]);
    }

    public function getProduct()
    {
        $products = Product::with('category', 'group')->latest()->get();
        return view('index', compact('products'));
    }

    public function getAllProduct()
    {
        $categories = ProductCategory::all();
        $products = Product::with('category', 'group')->latest()->get();
        return view('product', compact('products', 'categories'));
    }

    public function home()
    {
        return view('layout2');
    }

    public function indexStock()
    {
        $products = Product::with('category')->orderby('stock')->paginate(10);
        return view('admin.products.inventory', compact('products'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->stock = $request->stock;
        $product->save();

        return redirect()->back()->with('success', 'Cập nhật tồn kho thành công!');
    }
}

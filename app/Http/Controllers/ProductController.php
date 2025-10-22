<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductCategory;
use App\Models\ProductGroup;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Lọc theo danh mục
        if ($request->category && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Lọc theo từ khóa
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo khoảng giá
        if ($request->min_price) {
            $query->where('sale_price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('sale_price', '<=', $request->max_price);
        }

        // Số lượng sản phẩm mỗi trang
        $perPage = $request->input('per_page', 10);

        $products = $query->orderByDesc('id')->paginate($perPage);
        $categories = ProductCategory::all();

        return view('admin.products.index', compact('products', 'categories'));
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required',
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image',
            'images.*' => 'nullable|image',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'slug' => 'nullable|string',
            'sku' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'group_id' => 'nullable|exists:product_groups,id',
            'commission_rate' => 'nullable',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',

            'summary.string' => 'Tóm tắt phải là chuỗi ký tự.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',

            'content.string' => 'Nội dung chi tiết phải là chuỗi ký tự.',

            'thumbnail.image' => 'Ảnh đại diện phải là định dạng hình ảnh hợp lệ.',
            'images.*.image' => 'Các ảnh sản phẩm phải là hình ảnh hợp lệ.',

            'price.numeric' => 'Giá niêm yết phải là số.',
            'sale_price.numeric' => 'Giá bán phải là số.',

            'slug.string' => 'Slug phải là chuỗi ký tự.',

            'sku.string' => 'Mã sản phẩm (SKU) phải là chuỗi ký tự.',

            'category_id.exists' => 'Danh mục sản phẩm không hợp lệ.',
            'group_id.exists' => 'Nhóm sản phẩm không hợp lệ.',

            'commission_rate.numeric' => 'Tỷ lệ hoa hồng phải là số.',
            'commission_rate.min' => 'Tỷ lệ hoa hồng không được nhỏ hơn 0%.',
            'commission_rate.max' => 'Tỷ lệ hoa hồng không được lớn hơn 100%.',
        ]);
        try {
            // Tạo slug nếu không nhập
            $baseSlug = Str::slug($data['slug'] ?? $data['name']);

            $slug = $baseSlug;
            $counter = 1;

            // Lặp cho đến khi tìm được slug chưa tồn tại
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
            $data['commission_rate'] = $data['commission_rate'] ?? 0;
            $data['sku'] = $data['sku'] ?? strtoupper(Str::substr(Str::slug($data['name']), 0, 3)) . '-' . Str::upper(Str::random(5));

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
        } catch (\Throwable $e) {
            // Ghi log lỗi để debug
            Log::error('Lỗi thêm sản phẩm: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            // Quay lại form và hiển thị lỗi
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm: ' . $e->getMessage());
        }
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
        try {

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
        } catch (\Exception $e) {
            // Ghi log nếu cần
            Log::error('Lỗi khi tải trang chỉnh sửa sản phẩm: ' . $e->getMessage());

            // Chuyển hướng về trang trước với thông báo lỗi
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Đã xảy ra lỗi khi tải trang chỉnh sửa sản phẩm. Vui lòng thử lại!');
        }
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
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:product_categories,id',
            'group_id' => 'nullable|exists:product_groups,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',

            'summary.string' => 'Tóm tắt phải là chuỗi ký tự.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',

            'content.string' => 'Nội dung chi tiết phải là chuỗi ký tự.',

            'thumbnail.image' => 'Ảnh đại diện phải là định dạng hình ảnh hợp lệ.',
            'images.*.image' => 'Các ảnh sản phẩm phải là hình ảnh hợp lệ.',

            'price.numeric' => 'Giá niêm yết phải là số.',
            'sale_price.numeric' => 'Giá bán phải là số.',

            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn slug khác.',
            'slug.string' => 'Slug phải là chuỗi ký tự.',

            'sku.unique' => 'Mã sản phẩm (SKU) đã tồn tại.',
            'sku.string' => 'Mã sản phẩm (SKU) phải là chuỗi ký tự.',

            'category_id.exists' => 'Danh mục sản phẩm không hợp lệ.',
            'group_id.exists' => 'Nhóm sản phẩm không hợp lệ.',

            'commission_rate.numeric' => 'Tỷ lệ hoa hồng phải là số.',
            'commission_rate.min' => 'Tỷ lệ hoa hồng không được nhỏ hơn 0%.',
            'commission_rate.max' => 'Tỷ lệ hoa hồng không được lớn hơn 100%.',
        ]);

        try {
            // 🔹 Gán mặc định commission_rate = 0 nếu không nhập
            $data['commission_rate'] = $data['commission_rate'] ?? 0;
            // 🔹 Tạo slug tự động và tránh trùng
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;

            // 🔹 Tạo SKU tự động đẹp nếu không nhập
            $data['sku'] = $data['sku'] ?? (
                strtoupper(Str::substr(Str::slug($data['name']), 0, 3))
                . '-' . now()->format('ymd')
                . '-' . Str::upper(Str::random(4))
            );


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
        } catch (\Throwable $e) {
            Log::error('Lỗi cập nhật sản phẩm: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
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

    public function importAllFromExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        ini_set('max_execution_time', 300);
        set_time_limit(300);

        try {
            if (!$request->hasFile('file')) {
                return back()->with('error', 'Không tìm thấy file upload!');
            }

            $path = $request->file('file')->getRealPath();
            $rows = Excel::toArray([], $path)[0]; // Lấy sheet đầu tiên
            $header = array_shift($rows); // Bỏ dòng tiêu đề

            $imported = 0;
            $failed = 0;

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                try {
                    // Mapping cột dựa theo cấu trúc bạn đưa
                    // Mapping cột dựa theo thứ tự bạn cung cấp
                    $url             = trim($row[0] ?? '');   // Url
                    $name            = trim($row[1] ?? '');   // Tên
                    $description     = trim($row[2] ?? '');   // Mô tả
                    $excerpt         = trim($row[3] ?? '');   // Trích dẫn
                    $brand           = trim($row[4] ?? '');   // Hãng
                    $categoryName    = trim($row[5] ?? '');   // Loại sản phẩm
                    $attr1Name       = trim($row[6] ?? '');   // Thuộc tính 1
                    $attr1Value      = trim($row[7] ?? '');   // Giá trị thuộc tính 1
                    $attr2Name       = trim($row[8] ?? '');   // Thuộc tính 2
                    $attr2Value      = trim($row[9] ?? '');   // Giá trị thuộc tính 2
                    $attr3Name       = trim($row[10] ?? '');  // Thuộc tính 3
                    $attr3Value      = trim($row[11] ?? '');  // Giá trị thuộc tính 3
                    $variantSku      = trim($row[12] ?? '');  // Mã phiên bản sản phẩm
                    $weight          = (float)($row[13] ?? 0); // Khối lượng
                    $stock           = (int)($row[14] ?? 0);  // Số lượng tồn kho
                    $price           = (int)($row[15] ?? 0); // Giá
                    $comparePrice    = (int)($row[16] ?? 0); // Giá so sánh
                    $isDeliverable   = strtolower(trim($row[17] ?? 'yes')) === 'yes'; // Có giao hàng không?
                    $variantTaxable  = strtolower(trim($row[18] ?? 'no')) === 'yes';  // Variant Taxable
                    $barcode         = trim($row[19] ?? '');  // Barcode
                    $imageUrl        = trim($row[20] ?? '');  // Link hình
                    $imageDesc       = trim($row[21] ?? '');  // Mô tả hình
                    $seoTitle        = trim($row[22] ?? '');  // SEO Title
                    $seoDescription  = trim($row[23] ?? '');  // SEO Description
                    $variantImage    = trim($row[24] ?? '');  // Ảnh biến thể


                    if (empty($name)) continue;
                    if (empty($barcode)) continue;

                    /** -------------------------
                     * 1️⃣ TẠO DANH MỤC (nếu có)
                     * ------------------------*/
                    $category = null;
                    if (!empty($categoryName)) {
                        $category = ProductCategory::firstOrCreate(
                            ['name' => $categoryName],
                            [
                                'slug' => Str::slug($categoryName),
                                'parent_id' => null,
                                'sort_order' => 0,
                            ]
                        );
                    }

                    /** -------------------------
                     * 2️⃣ TẠO SẢN PHẨM CHÍNH
                     * ------------------------*/
                    $slugBase = Str::slug($name);
                    $slug = $slugBase;
                    $count = 1;
                    while (Product::where('slug', $slug)->exists()) {
                        $slug = $slugBase . '-' . $count++;
                    }

                    $product = Product::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'summary' => Str::limit(strip_tags($description), 200),
                            'content' => $description,
                            'thumbnail' => null,
                            'price' => $comparePrice,
                            'sale_price' => $price,
                            'stock' => $stock,
                            'category_id' => $category ? $category->id : null,
                            'sku' => strtoupper('SKU-' . Str::random(6)),
                            // 'seo_title' => $seoTitle,
                            // 'seo_description' => $seoDescription,
                        ]
                    );

                    /** -------------------------
                     * 3️⃣ LƯU ẢNH
                     * ------------------------*/
                    // if ($imageUrl && Str::startsWith($imageUrl, ['http://', 'https://'])) {
                        // try {
                        //     $imageContent = @file_get_contents($imageUrl);
                        //     Log::warning("imageContent: {$imageContent}");

                        //     if ($imageContent !== false) {
                        //         // Lấy tên file gốc (không gồm domain)
                        //         $pathInfo = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
                        //         $extension = strtolower($pathInfo['extension'] ?? 'jpg');

                        //         // Đảm bảo tên file an toàn
                        //         $fileName = 'products/' . Str::slug($name) . '-' . uniqid() . '.' . $extension;
                        //     Log::warning("ảnh: {$fileName}");

                        //         // Lưu file vào storage/public/products/
                        //         Storage::disk('public')->put($fileName, $imageContent);

                        //         // ✅ Chỉ lưu đường dẫn tương đối, không lưu URL đầy đủ
                        //         // if (!$product->thumbnail) {
                        //             $product->thumbnail = $fileName;
                        //             $product->save();
                        //         // }
                        //     }
                        // } catch (\Exception $e) {
                        //     Log::warning("⚠️ Không tải được ảnh: {$e->getMessage()}");
                        // }
                    // }


                    /** -------------------------
                     * 4️⃣ TẠO BIẾN THỂ
                     * ------------------------*/
                    // $variant = ProductVariant::create([
                    //     'product_id' => $product->id,
                    //     'price' => $comparePrice,
                    //     'sale_price' => $price,
                    //     'stock' => $stock,
                    //     // 'weight' => $weight,
                    //     'barcode' => $barcode,
                    // ]);
                    // if ($variantImage) {
                    //     $variant->image = $variantImage;
                    //     $variant->save();
                    // }

                    // /** -------------------------
                    //  * 5️⃣ TẠO / GẮN THUỘC TÍNH
                    //  * ------------------------*/
                    // $attrs = [
                    //     [$attr1Name, $attr1Value],
                    //     [$attr2Name, $attr2Value],
                    //     [$attr3Name, $attr3Value],
                    // ];

                    // foreach ($attrs as [$attrName, $attrValue]) {
                    //     if (!$attrName || !$attrValue) continue;

                    //     $attribute = Attribute::firstOrCreate(['name' => $attrName]);
                    //     $attrVal = AttributeValue::firstOrCreate([
                    //         'attribute_id' => $attribute->id,
                    //         'value' => $attrValue,
                    //     ]);

                    //     // Gắn vào variant
                    //     if (!$variant->attributeValues->contains($attrVal->id)) {
                    //         $variant->attributeValues()->attach($attrVal->id);
                    //     }
                    // }

                    $imported++;
                } catch (\Throwable $e) {
                    Log::error("❌ Lỗi tại dòng {$index}: " . $e->getMessage());
                    $failed++;
                }
            }

            DB::commit();

            return back()->with('success', "✅ Import xong: {$imported} dòng thành công, {$failed} lỗi.");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('❌ Lỗi import tổng hợp: ' . $th->getMessage());
            return back()->with('error', 'Lỗi khi import: ' . $th->getMessage());
        }
    }
}

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Chỉnh sửa sản phẩm</h1>

    <form action="<?php echo e(route('admin.products.update', $product->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?> <!-- This line is important for PUT requests in Laravel -->

        <div class="row">
            <!-- Cột bên trái - Thông tin cơ bản -->
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name"
                        name="name" value="<?php echo e(old('name', $product->name)); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label">Nội dung tóm tắt</label>
                    <textarea class="form-control <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="summary" name="summary"><?php echo e(old('summary', $product->summary)); ?></textarea>
                    <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung chi tiết</label>
                    <textarea class="form-control <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="content" name="content"><?php echo e(old('content', $product->content)); ?></textarea>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="sku" class="form-label">Mã sản phẩm</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="sku"
                        name="sku" value="<?php echo e(old('sku', $product->sku)); ?>">
                    <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Đường dẫn đến sản phẩm:</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="slug"
                        name="slug" value="<?php echo e(old('slug', $product->slug)); ?>">
                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="commission_rate" class="form-label">Hoa hồng cho cộng tác viên:</label>
                    <input type="number" class="form-control <?php $__errorArgs = ['commission_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="commission_rate" name="commission_rate"
                        value="<?php echo e(old('commission_rate', $product->commission_rate)); ?>">
                    <?php $__errorArgs = ['commission_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Danh mục sản phẩm</label>
                    <select class="form-select <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="category_id"
                        name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>"
                                <?php echo e(old('category_id', $product->category_id) == $cat->id ? 'selected' : ''); ?>>
                                <?php echo e($cat->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                

                <div class="mb-3">
                    <label for="stock" class="form-label">Số lượng:</label>
                    <input type="number" class="form-control <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="stock"
                        name="stock" value="<?php echo e(old('stock', $product->stock)); ?>">
                    <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="price" class="form-label">Giá niêm yết</label>
                        <input type="number" class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="price"
                            name="price" value="<?php echo e(old('price', $product->price)); ?>">
                        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="sale_price" class="form-label">Giá bán</label>
                        <input type="number" class="form-control <?php $__errorArgs = ['sale_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="sale_price"
                            name="sale_price" value="<?php echo e(old('sale_price', $product->sale_price)); ?>" required>
                        <?php $__errorArgs = ['sale_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="thumbnail"
                        name="thumbnail">
                    <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if($product->thumbnail): ?>
                        <div class="mt-2">
                            <img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>" alt="Thumbnail" width="100">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">Ảnh sản phẩm (nhiều ảnh)</label>
                    <input type="file" class="form-control <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="images"
                        name="images[]" multiple>
                    <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if($product->images): ?>
                        <div class="mt-2">
                            <?php $__currentLoopData = json_decode($product->images); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img src="<?php echo e(asset('storage/' . $image)); ?>" alt="Product Image" width="100"
                                    class="mr-2">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>


            </div>
        </div>
        <div id="attribute-template" class="d-none">
            <div class="attribute-row">
                <div class="row mb-3">
                    <div class="col-4">
                        <select id="attributeSelect" class="attribute-name form-select" style="height: 100%">
                            <option value="">--Vui Lòng chọn thuộc tính--</option>
                            <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($attribute->id); ?>"><?php echo e($attribute->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <option value="addAttibute">Thuộc tính khác</option>
                        </select>
                    </div>
                    <div class="col-8 d-flex gap-3">
                        <input type="text" name="attribute_value" class="attribute-values form-control"
                            placeholder="Nhập giá trị và enter (vd: S, M, L)">
                        <button type="button" class="btn btn-danger btn-sm remove-attribute">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="attributes-wrapper"></div>
        <button type="button" class="btn btn-success my-2" id="add-attribute">Tạo lại thuộc tính</button>
        <button type="button" class="btn btn-primary my-2" id="generate-combinations">Sinh tổ hợp</button>
        <table class="table table-bordered mt-3" id="variants-table">
            <thead>
                <tr>
                    <th>Thuộc tính</th>
                    <th>Giá niêm yết</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Hình ảnh</th>
                    <th>Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $variantData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <tr>
                        <td>
                            <input type="hidden" name="variants[<?php echo e($index); ?>][attributes]"
                                value='<?php echo json_encode($variant['attributes'], 15, 512) ?>'>
                            <?php echo e(implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($variant['attributes']), $variant['attributes']))); ?>

                        </td>
                        
                        <td><input type="number" min="0" name="variants[<?php echo e($index); ?>][price]"
                                value="<?php echo e($variant['price'] ?? ''); ?>" class="form-control"></td>
                        <td><input type="number" min="0" name="variants[<?php echo e($index); ?>][sale_price]"
                                value="<?php echo e($variant['sale_price'] ?? ''); ?>" class="form-control"></td>
                        <td><input type="number" min="0" name="variants[<?php echo e($index); ?>][stock]"
                                value="<?php echo e($variant['stock'] ?? ''); ?>" class="form-control"></td>
                        <td>
                            <?php if(!empty($variant['image_url'])): ?>
                                <label style="cursor: pointer;">
                                    <img src="<?php echo e(asset('/storage/' . $variant['image_url'])); ?>" width="60"
                                        class="rounded border">
                                    <input type="file" name="variants[<?php echo e($index); ?>][image]" class="d-none"
                                        onchange="previewImage(event, this)">
                                </label>
                            <?php else: ?>
                                <input type="file" name="variants[<?php echo e($index); ?>][image]" class="form-control">
                            <?php endif; ?>
                        </td>

                        <td>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)">
                                <i class="bi bi-trash"></i> Xoá
                            </button>
                            <input type="hidden" name="variants[<?php echo e($index); ?>][delete]" value="0">
                        </td>
                    </tr>
                    
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">Cập nhật sản phẩm</button>
        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-secondary">Quay lại</a>
    </form>
    <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="add-attribute-form" action="<?php echo e(route('admin.attributes.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm thuộc tính mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" name="attribute_name"
                            placeholder="Nhập tên thuộc tính">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Thêm</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        function previewImage(event, input) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Tìm thẻ img gần nhất (cùng label)
                    const img = input.closest('label').querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script>
        function removeVariantRow(button) {
            const row = button.closest('tr');
            row.style.display = 'none'; // Ẩn hàng này
            const inputDelete = row.querySelector('input[name$="[delete]"]');
            if (inputDelete) {
                inputDelete.value = 1; // Đánh dấu xoá
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("attributeSelect").addEventListener("change", function() {
                const selectedValue = this.value;
                if (selectedValue === "addAttibute") {
                    // Mở modal khi chọn "Thuộc tính khác"
                    var myModal = new bootstrap.Modal(document.getElementById('addAttributeModal'));
                    myModal.show();
                }
            });

            // Thêm thuộc tính mới khi nhấn nút Thêm thuộc tính
            document.getElementById('add-attribute').addEventListener('click', function() {
                const template = document.getElementById('attribute-template').firstElementChild;
                const wrapper = document.getElementById('attributes-wrapper');
                const clone = template.cloneNode(true);

                wrapper.appendChild(clone);

                // Cập nhật sự kiện change cho thuộc tính mới được thêm
                const selectElement = clone.querySelector(".attribute-name");
                selectElement.addEventListener("change", function() {
                    const selectedValue = this.value;
                    if (selectedValue === "addAttibute") {
                        // Mở modal khi chọn "Thuộc tính khác"
                        var myModal = new bootstrap.Modal(document.getElementById(
                            'addAttributeModal'));
                        myModal.show();
                    }
                });
            });

            // Xử lý xóa thuộc tính khi nhấn nút xóa
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-attribute')) {
                    const row = e.target.closest('.attribute-row');
                    if (row) {
                        row.remove();
                    }
                }
            });


            // Sự kiện khi đóng modal mà không thêm thuộc tính mới
            document.querySelector('.btn-close').addEventListener('click', function() {
                // Reset dropdown về "Vui lòng chọn thuộc tính"
                const selects = document.querySelectorAll('.attribute-name');
                selects.forEach(function(select) {
                    select.value = '';
                });
            });
        });

        document.getElementById('generate-combinations').addEventListener('click', function() {
            const attributes = [];

            document.querySelectorAll('.attribute-row').forEach(row => {
                const id = row.querySelector('.attribute-name').value; // attribute ID
                const values = row.querySelector('.attribute-values').value.split(',').map(v => v.trim())
                    .filter(v => v);
                if (id && values.length) {
                    attributes.push({
                        id, // Lưu attribute_id
                        values
                    });
                }
            });

            const combinations = generateCombinations(attributes.map(a => a.values)); // tổ hợp giá trị
            const attributeIds = attributes.map(a => a.id); // giữ thứ tự id theo values

            const tableBody = document.querySelector('#variants-table tbody');
            tableBody.innerHTML = ''; // clear old rows

            combinations.forEach((combo, index) => {
                let hiddenInputs = '';
                combo.forEach((val, i) => {
                    hiddenInputs +=
                        `<input type="hidden" name="variants[${index}][attributes][${i}][attribute_id]" value="${attributeIds[i]}">`;
                    hiddenInputs +=
                        `<input type="hidden" name="variants[${index}][attributes][${i}][value]" value="${val}">`;
                });

                const row = document.createElement('tr');
                row.innerHTML = `
                                <td>${combo.join(', ')}${hiddenInputs}</td>
                                <td><input type="number" name="variants[${index}][price]" class="form-control"></td>
                                <td><input type="number" name="variants[${index}][sale_price]" class="form-control"></td>
                                <td><input type="number" name="variants[${index}][stock]" class="form-control"></td>
                                <td><input type="file" name="variants[${index}][image]" class="form-control"></td>
                                <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)">
                                    <i class="bi bi-trash"></i> Xoá
                                </button>
                                <input type="hidden" name="variants[${index }][delete]" value="0">
                            </td>
                            `;
                tableBody.appendChild(row);
            });
        });


        function generateCombinations(arrays) {
            return arrays.reduce((acc, curr) => {
                const result = [];
                acc.forEach(a => {
                    curr.forEach(b => {
                        result.push(a.concat([b]));
                    });
                });
                return result;
            }, [
                []
            ]);
        }
    </script>
    <script>
        ClassicEditor
            .create(document.querySelector('#content'), {
                ckfinder: {
                    uploadUrl: '<?php echo e(route('upload') . '?_token=' . csrf_token()); ?>'
                }
            })
            .then(editor => {
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
                });
            })
            .catch(error => {
                console.error(error);
            });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/products/edit.blade.php ENDPATH**/ ?>
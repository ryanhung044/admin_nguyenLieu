

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Thêm mới sản phẩm</h1>

    <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

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
                        name="name" value="<?php echo e(old('name')); ?>" required>
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
unset($__errorArgs, $__bag); ?>" id="summary" name="summary"><?php echo e(old('summary')); ?></textarea>
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
unset($__errorArgs, $__bag); ?>" id="content" name="content"><?php echo e(old('content')); ?></textarea>
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
                        name="sku" value="<?php echo e(old('sku')); ?>">
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
                        name="slug" value="<?php echo e(old('slug')); ?>">
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
                    <input type="text" class="form-control <?php $__errorArgs = ['commission_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="commission_rate" name="commission_rate" value="<?php echo e(old('commission_rate')); ?>">
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
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id') == $cat->id ? 'selected' : ''); ?>>
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
                    <input type="number" min="0" class="form-control <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="stock"
                        name="stock" value="<?php echo e(old('stock')); ?>">
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
                            name="price" value="<?php echo e(old('price')); ?>">
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
                            name="sale_price" value="<?php echo e(old('sale_price')); ?>" required>
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
                </div>


            </div>
        </div>
        <!-- Template ẩn -->
        

        <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
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
        // document.addEventListener('DOMContentLoaded', function() {
        //     document.getElementById("attributeSelect").addEventListener("change", function() {
        //         const selectedValue = this.value;
        //         if (selectedValue === "addAttibute") {
        //             // Mở modal khi chọn "Thuộc tính khác"
        //             var myModal = new bootstrap.Modal(document.getElementById('addAttributeModal'));
        //             myModal.show();
        //         }
        //     });

        //     // Thêm thuộc tính mới khi nhấn nút Thêm thuộc tính
        //     document.getElementById('add-attribute').addEventListener('click', function() {
        //         const template = document.getElementById('attribute-template').firstElementChild;
        //         const wrapper = document.getElementById('attributes-wrapper');
        //         const clone = template.cloneNode(true);

        //         wrapper.appendChild(clone);

        //         // Cập nhật sự kiện change cho thuộc tính mới được thêm
        //         const selectElement = clone.querySelector(".attribute-name");
        //         selectElement.addEventListener("change", function() {
        //             const selectedValue = this.value;
        //             if (selectedValue === "addAttibute") {
        //                 // Mở modal khi chọn "Thuộc tính khác"
        //                 var myModal = new bootstrap.Modal(document.getElementById(
        //                     'addAttributeModal'));
        //                 myModal.show();
        //             }
        //         });
        //     });

        //     // Xử lý xóa thuộc tính khi nhấn nút xóa
        //     document.addEventListener('click', function(e) {
        //         if (e.target.closest('.remove-attribute')) {
        //             const row = e.target.closest('.attribute-row');
        //             if (row) {
        //                 row.remove();
        //             }
        //         }
        //     });


        //     // Sự kiện khi đóng modal mà không thêm thuộc tính mới
        //     document.querySelector('.btn-close').addEventListener('click', function() {
        //         // Reset dropdown về "Vui lòng chọn thuộc tính"
        //         const selects = document.querySelectorAll('.attribute-name');
        //         selects.forEach(function(select) {
        //             select.value = '';
        //         });
        //     });
        // });

        // document.getElementById('generate-combinations').addEventListener('click', function() {
        //     const attributes = [];

        //     document.querySelectorAll('.attribute-row').forEach(row => {
        //         const id = row.querySelector('.attribute-name').value; // attribute ID
        //         const values = row.querySelector('.attribute-values').value.split(',').map(v => v.trim())
        //             .filter(v => v);
        //         if (id && values.length) {
        //             attributes.push({
        //                 id, // Lưu attribute_id
        //                 values
        //             });
        //         }
        //     });

        //     const combinations = generateCombinations(attributes.map(a => a.values)); // tổ hợp giá trị
        //     const attributeIds = attributes.map(a => a.id); // giữ thứ tự id theo values

        //     const tableBody = document.querySelector('#variants-table tbody');
        //     tableBody.innerHTML = ''; // clear old rows

        //     combinations.forEach((combo, index) => {
        //         let hiddenInputs = '';
        //         combo.forEach((val, i) => {
        //             hiddenInputs +=
        //                 `<input type="hidden" name="variants[${index}][attributes][${i}][attribute_id]" value="${attributeIds[i]}">`;
        //             hiddenInputs +=
        //                 `<input type="hidden" name="variants[${index}][attributes][${i}][value]" value="${val}">`;
        //         });

        //         const row = document.createElement('tr');
        //         row.innerHTML = `
        //     <td>${combo.join(', ')}${hiddenInputs}</td>
        //     <td><input type="number" name="variants[${index}][price]" class="form-control"></td>
        //     <td><input type="number" name="variants[${index}][sale_price]" class="form-control"></td>
        //     <td><input type="number" name="variants[${index}][stock]" class="form-control"></td>
        //     <td><input type="file" name="variants[${index}][image]" class="form-control"></td>
        // `;
        //         tableBody.appendChild(row);
        //     });
        // });


        // function generateCombinations(arrays) {
        //     return arrays.reduce((acc, curr) => {
        //         const result = [];
        //         acc.forEach(a => {
        //             curr.forEach(b => {
        //                 result.push(a.concat([b]));
        //             });
        //         });
        //         return result;
        //     }, [
        //         []
        //     ]);
        // }

        ClassicEditor.create(document.querySelector('#content'), {
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

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/products/create.blade.php ENDPATH**/ ?>
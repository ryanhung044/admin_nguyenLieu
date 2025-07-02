<?php $__env->startSection('content'); ?>
    <div>
        <h1>Cập nhật Thông tin Ứng dụng</h1>

        <form action="<?php echo e(route('admin.app-setting.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-group">
                <label for="app_name">Tên Ứng dụng</label>
                <input type="text" name="app_name" id="app_name" class="form-control"
                    value="<?php echo e(old('app_name', $setting->app_name)); ?>">
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" class="form-control"
                    value="<?php echo e(old('address', $setting->address)); ?>">
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="latitude">Vĩ độ</label>
                    <input type="text" name="latitude" id="latitude" class="form-control"
                        value="<?php echo e(old('latitude', $setting->latitude)); ?>">
                </div>
                <div class="col-md-6">
                    <label for="longitude">Kinh độ</label>
                    <input type="text" name="longitude" id="longitude" class="form-control"
                        value="<?php echo e(old('longitude', $setting->longitude)); ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                        value="<?php echo e(old('phone', $setting->phone)); ?>">
                </div>

                <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="<?php echo e(old('email', $setting->email)); ?>">
                </div>

                <div class="col-md-4">
                    <label for="default_color">Màu mặc định trên app</label>

                    <div class="input-group">
                        <div class="input-group">
                            <input type="text" name="default_color" id="default_color" class="form-control"
                                   placeholder="#000000"
                                   value="<?php echo e(old('default_color', $setting->default_color)); ?>">
                            <input type="color" id="color_picker" value="<?php echo e(old('default_color', $setting->default_color)); ?>"
                                   style="width: 60px;height: 100%; padding: 0; border: none; background: none;">
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label for="description">Thông tin ứng dụng</label>
                <textarea name="description" id="description" class="form-control" rows="4"><?php echo e(old('description', $setting->description)); ?></textarea>
            </div>

            <div class="form-group">
                <label for="donated">Tặng khách mới</label>
                <input type="number" name="donated" id="donated" class="form-control"
                    value="<?php echo e(old('donated', $setting->donated)); ?>">
            </div>

            <div class="form-group">
                <label>Logo Web hiện tại:</label><br>
                <?php if($setting->logo_path): ?>
                    <img src="<?php echo e(asset('storage/' .$setting->logo_path)); ?>" width="100" alt="Logo">
                <?php endif; ?>
                <input type="file" name="logo_path" class="form-control mt-2">
            </div>

            

            <div class="form-group">
                <label>Favicon hiện tại:</label><br>
                <?php if($setting->favicon_path): ?>
                    <img src="<?php echo e(asset('storage/' .$setting->favicon_path)); ?>" width="100" alt="Favicon">
                <?php endif; ?>
                <input type="file" name="favicon_path" class="form-control mt-2">
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            
        </form>
    </div>
    <script>
        const colorInput = document.getElementById('default_color');
        const colorPicker = document.getElementById('color_picker');
    
        // Khi chọn màu từ bảng -> cập nhật ô text
        colorPicker.addEventListener('input', function () {
            colorInput.value = this.value;
        });
    
        // Khi người dùng gõ vào input text -> cập nhật lại bảng màu
        colorInput.addEventListener('input', function () {
            if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(this.value)) {
                colorPicker.value = this.value;
            }
        });
    </script>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_nguyenLieu\resources\views/admin/app_setting/edit.blade.php ENDPATH**/ ?>
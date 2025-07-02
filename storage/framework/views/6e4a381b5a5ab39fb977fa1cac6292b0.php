

<?php $__env->startSection('content'); ?>
    <div>
        <h1>Cập nhật Banner</h1>

        <form action="<?php echo e(route('admin.banners.update', $banner->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?> 

            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" id="title" name="title" class="form-control"
                    value="<?php echo e(old('title', $banner->title)); ?>" required>
            </div>

            <div class="form-group">
                <label>Hình ảnh hiện tại:</label><br>
                <?php if($banner->image): ?>
                    <img src="<?php echo e(asset('storage/' . $banner->image)); ?>" width="100" alt="Banner">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Cập nhật hình ảnh (nếu cần)</label>
                <input type="file" id="image" name="image" class="form-control">
            </div>

            <div class="form-group">
                <label for="link">Đường dẫn</label>
                <input type="url" id="link" name="link" class="form-control"
                    value="<?php echo e(old('link', $banner->link)); ?>" placeholder="https://...">
            </div>

            <div class="form-group">
                <label for="position">Vị trí hiển thị</label>
                
                <select id="position" name="position" class="form-control" required>
                    <option value="1" <?php echo e(old('position', $banner->position) == 1 ? 'selected' : ''); ?>>Banner giữa màn
                        hình trang chủ
                    </option>
                    <option value="2" <?php echo e(old('position', $banner->position) == 2 ? 'selected' : ''); ?>>Menu giữa màn
                        hình trang chủ</option>
                    <option value="3" <?php echo e(old('position', $banner->position) == 3 ? 'selected' : ''); ?>>Banner đại lý
                    </option>
                </select>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="start_date">Ngày bắt đầu</label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control"
                            value="<?php echo e(old('start_date', \Carbon\Carbon::parse($banner->start_date)->format('Y-m-d\TH:i'))); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="end_date">Ngày kết thúc</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control"
                            value="<?php echo e(old('end_date', \Carbon\Carbon::parse($banner->end_date)->format('Y-m-d\TH:i'))); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="1" <?php echo e(old('status', $banner->status) == 1 ? 'selected' : ''); ?>>Kích hoạt</option>
                    <option value="0" <?php echo e(old('status', $banner->status) == 0 ? 'selected' : ''); ?>>Tắt</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật banner</button>
            <a href="<?php echo e(route('admin.banners.index')); ?>" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/banners/edit.blade.php ENDPATH**/ ?>
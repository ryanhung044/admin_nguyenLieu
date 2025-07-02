

<?php $__env->startSection('content'); ?>
<h1 class="mb-4">Chỉnh sửa hoa hồng</h1>

<form action="<?php echo e(route('admin.commissions.update', $commission->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="mb-3">
        <label for="category_id" class="form-label">Danh mục</label>
        <select name="category_id" id="category_id" class="form-select" required>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>" <?php echo e($commission->category_id == $category->id ? 'selected' : ''); ?>>
                    <?php echo e($category->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="level" class="form-label">Cấp độ</label>
        <input type="number" name="level" id="level" class="form-control" value="<?php echo e($commission->level); ?>" min="1" required>
    </div>

    <div class="mb-3">
        <label for="percentage" class="form-label">Phần trăm (%)</label>
        <input type="number" name="percentage" id="percentage" class="form-control" value="<?php echo e($commission->percentage); ?>" step="0.01" min="0" max="100" required>
    </div>

    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="<?php echo e(route('admin.commissions.index')); ?>" class="btn btn-secondary">Quay lại</a>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/commissions/edit.blade.php ENDPATH**/ ?>
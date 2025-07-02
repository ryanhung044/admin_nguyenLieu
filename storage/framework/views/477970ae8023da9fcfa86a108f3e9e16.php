

<?php $__env->startSection('content'); ?>
<h1 class="mb-4">Thêm hoa hồng</h1>

<form action="<?php echo e(route('admin.commissions.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label for="category_id" class="form-label">Danh mục</label>
        <select name="category_id" id="category_id" class="form-select" required>
            <option value="">-- Chọn danh mục --</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="level" class="form-label">Cấp độ (F1, F2, ...)</label>
        <input type="number" name="level" id="level" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
        <label for="percentage" class="form-label">Phần trăm (%)</label>
        <input type="number" name="percentage" id="percentage" class="form-control" min="0" max="100" step="0.01" required>
    </div>

    <button type="submit" class="btn btn-success">Lưu</button>
    <a href="<?php echo e(route('admin.commissions.index')); ?>" class="btn btn-secondary">Quay lại</a>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/commissions/create.blade.php ENDPATH**/ ?>
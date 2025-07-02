

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Thêm mới danh mục</h1>

    <!-- Form thêm mới danh mục -->
    <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="title">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug (nếu không nhập sẽ tự tạo)</label>
            <input type="text" name="slug" class="form-control">
        </div>

        <div class="form-group">
            <label for="parent_category_id">Chuyên mục cha</label>
            <select name="parent_category_id" class="form-control">
                <option value="">-- Không có --</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->title); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Thêm chuyên mục</button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/categories/create.blade.php ENDPATH**/ ?>


<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Chỉnh sửa danh mục</h1>

    <!-- Form chỉnh sửa danh mục -->
    <form action="<?php echo e(route('admin.categories.update', $category->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="form-group">
            <label for="title">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $category->title)); ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" name="slug" class="form-control" value="<?php echo e(old('slug', $category->slug)); ?>">
        </div>

        <div class="form-group">
            <label for="parent_category_id">Chuyên mục cha</label>
            <select name="parent_category_id" class="form-control">
                <option value="">-- Không có --</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($cat->id !== $category->id): ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e($cat->id == old('parent_category_id', $category->parent_category_id) ? 'selected' : ''); ?>>
                            <?php echo e($cat->title); ?>

                        </option>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?php echo e(old('description', $category->description)); ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật chuyên mục</button>
        <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-secondary">Quay lại</a>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/categories/edit.blade.php ENDPATH**/ ?>
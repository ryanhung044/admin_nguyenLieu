

<?php $__env->startSection('content'); ?>
<h1 class="mb-4">Danh sách bài viết</h1>

<!-- Thêm sản phẩm -->
<a href="<?php echo e(route('admin.articles.create')); ?>" class="btn btn-primary mb-3">Thêm bài viết</a>

<!-- Bảng sản phẩm -->
<table id="productTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục</th>
            <th>Ảnh đại diện</th>
            <th>Thời gian đăng</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($article->id); ?></td>
            <td><?php echo e($article->title); ?></td>
            <td><?php echo e($article->slug); ?></td>
            <td><?php echo e($article->category->title ?? '-'); ?></td>
            <td><img src="<?php echo e(asset('storage/' . $article->image)); ?>" width="50" alt="Ảnh sản phẩm"></td>
            <td><?php echo e($article->published_at ?? ''); ?></td>
            <td><?php echo e($article->created_at ?? ''); ?></td>
            <td>
                <a href="<?php echo e(route('admin.articles.edit', $article->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                <form action="<?php echo e(route('admin.articles.destroy', $article->id)); ?>" method="POST" style="display:inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/articles/index.blade.php ENDPATH**/ ?>
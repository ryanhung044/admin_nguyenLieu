



<?php $__env->startSection('content'); ?>
<h1 class="mb-4">Danh sách danh mục</h1>

<!-- Thêm danh mục -->
<a href="<?php echo e(route('admin.product-categories.create')); ?>" class="btn btn-primary mb-3">Thêm danh mục</a>

<!-- Bảng danh mục -->
<table id="categoryTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục cha</th>
            <th>Ảnh</th>
            <th>Thứ tự</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($cat->id); ?></td>
            <td><?php echo e($cat->name); ?></td>
            <td><?php echo e($cat->slug); ?></td>
            <td><?php echo e($cat->parent->name ?? '-'); ?></td>
            <td><img src="<?php echo e(asset('storage/' . $cat->image)); ?>" width="50" alt="Ảnh danh mục"></td>
            <td><?php echo e($cat->sort_order); ?></td>
            <td>
                <a href="<?php echo e(route('admin.product-categories.edit', $cat->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                <form action="<?php echo e(route('admin.product-categories.destroy', $cat->id)); ?>" method="POST" style="display:inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#categoryTable').DataTable({
            "language": {
                "search": "Tìm kiếm:",
                "lengthMenu": "Hiển thị _MENU_ mục",
                "info": "Hiển thị _START_ đến _END_ trong _TOTAL_ mục",
                "paginate": {
                    "first": "Đầu",
                    "last": "Cuối",
                    "next": "Tiếp",
                    "previous": "Trước"
                },
                "zeroRecords": "Không tìm thấy dữ liệu",
                "infoEmpty": "Không có dữ liệu",
                "infoFiltered": "(lọc từ _MAX_ mục)"
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/product_categories/index.blade.php ENDPATH**/ ?>
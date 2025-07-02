

<?php $__env->startSection('content'); ?>
<h1 class="mb-4">Danh sách sản phẩm</h1>

<!-- Thêm sản phẩm -->
<a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary mb-3">Thêm sản phẩm</a>

<!-- Bảng sản phẩm -->
<table id="productTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục</th>
            <th>Ảnh đại diện</th>
            <th>Giá</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($product->id); ?></td>
            <td><?php echo e($product->name); ?></td>
            <td><?php echo e($product->slug); ?></td>
            <td><?php echo e($product->category->name ?? '-'); ?></td>
            <td><img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>" width="50" alt="Ảnh sản phẩm"></td>
            <td><?php echo e(number_format($product->sale_price, 0, ',', '.')); ?> VND</td>
            <td>
                <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" method="POST" style="display:inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php echo e($products->links('pagination::bootstrap-5')); ?>


<script>
    $(document).ready(function () {
        $('#productTable').DataTable({
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

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/products/index.blade.php ENDPATH**/ ?>
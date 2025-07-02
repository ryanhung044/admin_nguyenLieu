<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Quản lý tồn kho của sản phẩm</h1>

    <!-- Thêm sản phẩm -->
    <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary mb-3">Thêm sản phẩm</a>

    <!-- Bảng sản phẩm -->
    <table id="productTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Ảnh đại diện</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo e($product->stock == 0 ? 'table-danger' : ''); ?>">
                    <td><?php echo e($product->id); ?></td>
                    <td><?php echo e($product->name); ?></td>
                    <td><img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>" width="50"></td>
                    <td><?php echo e(number_format($product->sale_price, 0, ',', '.')); ?> VND</td>
                    <td>
                        <form action="<?php echo e(route('admin.inventory.updateStock', $product->id)); ?>" method="POST"
                            class="d-flex gap-1">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="number" name="stock" value="<?php echo e($product->stock); ?>" min="0"
                                class="form-control form-control-sm" style="width: 80px;">
                            <button type="submit" class="btn btn-sm btn-success">Lưu</button>
                        </form>
                    </td>
                    <td>
                        <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" class="btn btn-sm btn-primary">Cập
                            nhật</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </tbody>
    </table>
    <?php echo e($products->links('pagination::bootstrap-5')); ?>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_nguyenLieu\resources\views/admin/products/inventory.blade.php ENDPATH**/ ?>
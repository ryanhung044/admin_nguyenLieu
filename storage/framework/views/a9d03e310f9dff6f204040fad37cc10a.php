<?php
    use Illuminate\Support\Str;
?>

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Danh sách tài khoản</h1>

    <!-- Thêm sản phẩm -->
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary mb-3">Thêm tài khoản</a>

    <!-- Bảng sản phẩm -->
    <table id="productTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Ảnh đại diện</th>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Giới tính</th>
                <th>Số dư tài khoản</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($user->id); ?></td>
                    <td>

                        <?php if($user->avatar): ?>
                           <?php if(Str::startsWith($user->avatar, ['http://', 'https://'])): ?>
                                <img src="<?php echo e($user->avatar); ?>" width="50" alt="">
                            <?php else: ?>
                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" width="50" alt="">
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg" width="50" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($user->full_name); ?></td>
                    <td><?php echo e($user->phone); ?></td>
                    <td>
                        <?php if($user->gender === 'male'): ?>
                            Nam
                        <?php elseif($user->gender === 'female'): ?>
                            Nữ
                        <?php else: ?>
                            Khác
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(number_format($user->balance, 0, ',', '.')); ?> VND</td>
                    <td>
                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST"
                            style="display:inline-block">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
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

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/user/index.blade.php ENDPATH**/ ?>
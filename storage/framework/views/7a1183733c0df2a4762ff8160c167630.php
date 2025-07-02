<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn đơn hàng #<?php echo e($order->id); ?></title>
    <style>
        body { font-family: Arial, sans-serif; width: 80mm; margin: 0 auto; font-size: 13px; }
        h2, h3 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td, th { padding: 4px 0; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .signature { display: flex; justify-content: space-between; margin-top: 30px; }
        .signature div { width: 45%; text-align: center; }
        .print-btn { margin: 10px 0; text-align: center; }
    </style>
</head>
<body>
    <h2>ĐƠN HÀNG #<?php echo e($order->id); ?></h2>
    <div class="center"><?php echo e($order->created_at->format('Y/m/d H:i:s')); ?></div>

    <p><strong>CH: TINOTECH</strong><br>
    (Thôn Lương Sơn, Đông Sơn, Chương Mỹ, Hà Nội)<br>
    Điện thoại: 84398623059</p>

    <p class="bold">Chi tiết đơn hàng:</p>
    <table>
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?>. <?php echo e($item->product->name); ?> (x<?php echo e($item->quantity); ?>)</td>
            </tr>
            <tr>
                <td>Giá: <?php echo e(number_format($item->price, 0, ',', '.')); ?>đ | Số lượng: <?php echo e($item->quantity); ?> | Thành tiền: <?php echo e(number_format($item->price * $item->quantity, 0, ',', '.')); ?>đ</td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

    <div class="divider"></div>
    <p>
        Tạm tính: <span class="bold"><?php echo e(number_format($order->total, 0, ',', '.')); ?>đ</span><br>
        Tổng tiền được giảm: <span class="bold">0đ</span><br>
        Phí vận chuyển: <span class="bold">Chưa tính</span><br>
        Tổng: <span class="bold"><?php echo e(number_format($order->total, 0, ',', '.')); ?>đ</span>
    </p>

    <div class="divider"></div>
    <p>
        Tổng điểm tích lũy: -<br>
        Ví tích điểm: 0 điểm
    </p>

    <p class="bold">Thông tin người nhận hàng:</p>
    <p>
        Họ và tên: <?php echo e($order->name); ?><br>
        Địa chỉ: <?php echo e($order->address ?? '(chưa cập nhật)'); ?><br>
        Điện thoại: <?php echo e($order->phone); ?><br>
        Ngày đặt hàng: <strong><?php echo e($order->created_at->format('H:i d/m/Y')); ?></strong>
    </p>

    <p>
        Phương thức thanh toán: <strong><?php echo e(strtoupper($order->payment_method ?? 'COD')); ?></strong><br>
        Trạng thái thanh toán: 
        <strong>
            <?php echo e($order->status === 'completed' ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN'); ?>

        </strong>
    </p>

    <div class="signature">
        <div>
            NGƯỜI NHẬN<br><i>(Ký và ghi rõ họ tên)</i>
        </div>
        <div>
            NHÂN VIÊN BÁN HÀNG<br><i>(Ký và ghi rõ họ tên)</i>
        </div>
    </div>

    
</body>
</html>

<script>
    window.onload = function() {
        window.print();
    };
</script>

<?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/orders/invoice.blade.php ENDPATH**/ ?>
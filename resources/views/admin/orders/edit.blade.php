@extends('admin.layout')

@section('content')
<div class="container py-4">
    <h3 class="text-center mb-4">CHI TIẾT ĐƠN HÀNG #{{ $order->id }}</h3>

    <div class="row">
        <!-- Box 1: Thông tin người nhận -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header  fw-bold">
                    Thông tin người nhận
                </div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                    <p><strong>Thời gian đặt:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</p>
                    <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>
                    <p><strong>Trạng thái thanh toán:</strong> 
                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'danger' }}">
                            {{ $order->payment_status == 'paid' ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Box 2: Thông tin đơn hàng -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    Thông tin đơn hàng
                </div>
                <div class="card-body">
                    @foreach ($order->items as $item)
                        <p>
                            <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->product_name }}" style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                            
                            <strong>{{ $item->product_name }}</strong><br>
                            Giá: {{ number_format($item->price, 0, ',', '.') }}đ |
                            SL: {{ $item->quantity }} |
                            Thành tiền: {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                        </p>
                        <hr>
                    @endforeach

                    <p><strong>Tạm tính:</strong> {{ number_format($order->subtotal, 0, ',', '.') }}đ</p>
                    <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee, 0, ',', '.') }}đ</p>
                    <p class="fw-bold fs-5"><strong>Tổng tiền:</strong> {{ number_format($order->total, 0, ',', '.') }}đ</p>
                    
                    <p><strong>Trạng thái đơn hàng:</strong>
                        @php
                            $statusLabels = [
                                'pending' => ['label' => 'Khởi tạo', 'class' => 'secondary'],
                                'approved' => ['label' => 'Duyệt', 'class' => 'info'],
                                'packed' => ['label' => 'Đóng gói', 'class' => 'primary'],
                                'shipped' => ['label' => 'Xuất kho', 'class' => 'warning'],
                                'completed' => ['label' => 'Hoàn thành', 'class' => 'success'],
                                'cancelled' => ['label' => 'Hủy đơn', 'class' => 'danger'],
                            ];
                            $status = $statusLabels[$order->status] ?? ['label' => 'Không xác định', 'class' => 'dark'];
                        @endphp
                        <span class="badge bg-{{ $status['class'] }}">{{ $status['label'] }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end">
        {{-- <button onclick="window.print()" class="btn btn-dark mt-3">
            <i class="fas fa-print"></i> In hóa đơn
        </button> --}}
        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-secondary mt-3">
            <i class="fas fa-print"></i> In hóa đơn
        </a>
    </div>
</div>
@endsection

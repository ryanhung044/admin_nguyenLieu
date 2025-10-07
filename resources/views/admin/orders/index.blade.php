@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-4">Danh sách đơn hàng</h1>
        <div class="d-flex justify-content-between gap-2 align-items-center mb-3">
            <a class="btn btn-primary" href="{{ route('admin.orders.create') }}"> <i class="fas fa-plus-square"></i> Thêm mới đơn hàng</a>
            <div>
                <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>

            </div>

        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3" style="overflow: auto">
        <div class="btn-group" role="group" aria-label="Lọc theo trạng thái">
            @php
                $statusFilters = [
                    'all' => 'Tất cả',
                    'pending' => 'Khởi tạo',
                    'approved' => 'Duyệt',
                    'packed' => 'Đóng gói',
                    'shipped' => 'Xuất kho',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Hủy đơn',
                ];
            @endphp
            @foreach ($statusFilters as $key => $label)
                <a href="{{ route('admin.orders.index', ['status' => $key]) }}"
                    class="btn {{ request('status', 'all') == $key ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            {{-- <form method="GET" action="{{ route('admin.orders.index') }}"
                class="d-flex justify-content-between align-items-center ">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm đơn hàng..."
                    class="form-control d-inline-block" style="min-width: 150px">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </form> --}}
            {{-- Thêm sau ô tìm kiếm --}}
            <form method="GET" action="{{ route('admin.orders.index') }}"
                class="d-flex justify-content-between align-items-center gap-2">

                {{-- Giữ lại các tham số hiện có --}}
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">

                {{-- Từ ngày --}}
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control"
                    style="min-width:150px">

                {{-- Đến ngày --}}
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control"
                    style="min-width:150px">

                {{-- Ô tìm kiếm cũ --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm đơn hàng..."
                    class="form-control" style="min-width:150px">

                <button type="submit" class="btn btn-primary d-flex justify-content-between gap-2 align-items-center"><i class="fas fa-search"></i> Lọc</button>
            </form>

        </div>
    </div>


    <div style="overflow-x: auto;">
        <table id="productTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Trạng thái thanh toán</th>
                    <th>Mặt hàng</th>
                    <th>Người giới thiệu</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->phone }}</td>
                        <td>{{ number_format($order->total, 0, ',', '.') }} VND</td>
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
                            $statusPaymentLabels = [
                                'pending' => ['label' => 'Chờ thanh toán', 'class' => 'danger'],
                                'paid' => ['label' => 'Đã thanh toán', 'class' => 'success'],
                                'failed' => ['label' => 'Thất bại', 'class' => 'danger'],
                                'refunded' => ['label' => 'Hoàn tiền', 'class' => 'primary'],
                            ];
                            $statusPayment = $statusPaymentLabels[$order->status_payment];
                        @endphp

                        <td>
                            <span class="badge fs-5 bg-{{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>

                        <td>
                            <span class="badge fs-5 bg-{{ $statusPayment['class'] }}">
                                {{ $statusPayment['label'] }}
                            </span>
                        </td>

                        <td>
                            @if ($order->items)
                                @foreach ($order->items as $item)
                                    @if ($item->product)
                                        <div>- {{ $item->product_name }} (x{{ $item->quantity }})</div>
                                    @else
                                        <div>- Sản phẩm không tồn tại (x{{ $item->quantity }})</div>
                                    @endif
                                @endforeach
                            @endif
                        </td>

                        <td>
                            @if ($order->referrer)
                                <span class="badge fs-5 bg-secondary ">
                                    [#{{ $order->referrer->id }}] - {{ $order->referrer->full_name }}
                                </span>
                            @else
                                <span class="badge fs-5 bg-danger">Không có</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton{{ $order->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $order->id }}">
                                        <!-- Mở popup cập nhật trạng thái -->
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal" data-id="{{ $order->id }}"
                                                data-status="{{ $order->status }}">
                                                Cập nhật trạng thái
                                            </a>
                                        </li>

                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusPaymentModal" data-id="{{ $order->id }}"
                                                data-statusPayment="{{ $order->status_payment }}">
                                                Trạng thái thanh toán
                                            </a>
                                        </li>

                                        <!-- Hủy đơn -->
                                        <li>
                                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                                    Hủy đơn
                                                </button>
                                            </form>
                                        </li>

                                    </ul>
                                </div>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-print"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                @endforeach

            </tbody>


        </table>
    </div>
    {{ $orders->links('pagination::bootstrap-5') }}
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Trạng thái</label>
                            <select class="form-select" id="statusSelect" name="status">
                                <option value="pending">Khởi tạo</option>
                                <option value="approved">Duyệt</option>
                                <option value="packed">Đóng gói</option>
                                <option value="shipped">Xuất kho</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Hủy đơn</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="updateStatusPaymentModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="updateStatusPaymentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cập nhật trạng thái thanh toán</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="statusSelect" class="form-label">Trạng thái</label>
                                <select class="form-select" id="statusSelect" name="status_payment">
                                    <option value="pending">Chờ thanh toán</option>
                                    <option value="paid">Đã thanh toán</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Cập nhật</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        // console.log('window.Echo' . window.Echo); 
        document.addEventListener('DOMContentLoaded', function() {
            const updateStatusModal = document.getElementById('updateStatusModal');
            const updateStatusPaymentModal = document.getElementById('updateStatusPaymentModal');
            updateStatusModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');

                // Gán form action
                const form = document.getElementById('updateStatusForm');
                form.action = `/admin/orders/${orderId}/status`;

                // Gán trạng thái hiện tại
                document.getElementById('statusSelect').value = currentStatus;
            });
            updateStatusPaymentModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-statusPayment');

                // Gán form action
                const form = document.getElementById('updateStatusPaymentForm');
                form.action = `/admin/orders/${orderId}/updateStatusPayment`;

                // Gán trạng thái hiện tại
                document.getElementById('statusSelect').value = currentStatus;
            });
        });
    </script>
@endsection

@push('scripts')
@endpush

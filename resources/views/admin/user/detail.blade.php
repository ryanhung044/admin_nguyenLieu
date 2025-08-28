<!-- Tabs -->
<ul class="nav nav-tabs" id="userDetailTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Thông tin</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="orders-tab" data-toggle="tab" href="#order-history" role="tab">Lịch sử mua hàng</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="care-tab" data-toggle="tab" href="#care" role="tab">Chăm sóc</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="warranty-tab" data-toggle="tab" href="#warranty" role="tab">Bảo hành</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="repair-tab" data-toggle="tab" href="#repair" role="tab">Lịch sử sửa</a>
    </li>
</ul>

<!-- Tab content -->
<div class="tab-content p-3 border border-top-0" id="userDetailTabsContent">
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <div class="row">
            <!-- Thông tin chính -->
            <div class="col-md-8">
                <div class="border p-3 mb-3 rounded">
                    <h5 class="mb-3">Thông tin</h5>
                    <p><i class="fas fa-user me-2 text-secondary"></i><strong>Tên:</strong>
                        {{ $user->full_name ?? 'Không rõ' }}</p>
                    <p><i class="fas fa-phone me-2 text-secondary"></i><strong>SĐT:</strong> <a
                            href="tel:{{ $user->phone }}">{{ $user->phone ?? 'Không rõ' }}</a></p>
                    <p><i class="fas fa-map-marker-alt me-2 text-secondary"></i><strong>Địa chỉ:</strong>
                        {{ $user->address ?? 'Không rõ' }}</p>
                    <p><i class="fas fa-user-tag me-2 text-secondary"></i><strong>Loại:</strong> Khách lẻ</p>
                    {{-- <p><i class="fas fa-tags me-2 text-secondary"></i><strong>Nhóm, Mã thẻ, Cấp độ:</strong> (chưa có)</p> --}}
                </div>

                <div class="border p-3 mb-3 rounded">
                    <h5 class="mb-3"><i class="fas fa-history me-2 text-secondary"></i>Lịch sử giao dịch</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tổng số hóa đơn:</strong> {{ $totalOrders }}</p>
                            <p><strong>Tổng số ngày mua:</strong> {{ $totalDays }}</p>
                            <p><strong>Tổng sản phẩm đã mua:</strong> {{ $totalProducts }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Điểm tích lũy:</strong> <span
                                    class="text-primary font-weight-bold">{{ number_format($totalPoints, 0) }}</span>
                            </p>
                            <p><strong>Tổng tiền tích lũy:</strong> {{ number_format($totalAmount, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-12">
                            @if ($firstOrder)
                                <p>
                                    <strong>Ngày bắt đầu mua hàng:</strong>
                                    {{ $firstOrder->created_at->format('d/m/Y') }}
                                </p>
                            @endif

                            @if ($lastOrder)
                                <p>
                                    <strong>Ngày mua cuối cùng:</strong> {{ $lastOrder->created_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phụ -->
            <div class="col-md-4">
                <div class="border p-3 mb-3 rounded">
                    <h5 class="mb-3"><i class="fas fa-credit-card me-2 text-secondary"></i>Thanh toán</h5>
                    <p><strong>Tên công ty:</strong></p>
                    <p><strong>Mã số thuế:</strong></p>
                </div>

                {{-- <div class="border p-3 rounded">
                    <h5 class="mb-3 d-flex justify-content-between">
                        <span><i class="fas fa-tags me-2 text-secondary"></i>Nhãn</span>
                        <button class="btn btn-outline-success btn-sm"><i class="fas fa-plus"></i></button>
                    </h5>
                    <p class="text-muted">Chưa có nhãn</p>
                </div> --}}

                <div class="border p-3 rounded">
                    <h5 class="mb-3"><i class="fas fa-paperclip me-2 text-secondary"></i>File đính kèm</h5>
                    <button class="btn btn-success btn-sm"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </div>
            {{-- <div class="col-md-12">
                <div class="border p-3 rounded">
                    <h5 class="mb-3"><i class="fas fa-paperclip me-2 text-secondary"></i>Gợi ý</h5>
                    <ul class="mt-3 small text-muted">
                        <li><i class="fas fa-info-circle mr-1"></i> Tổng tiền tích lũy = Tổng tiền khách đã mua – trả
                            hàng – điểm sử dụng</li>
                        <li><i class="fas fa-info-circle mr-1"></i> Hóa đơn gồm cả mua và trả hàng</li>
                        <li><i class="fas fa-info-circle mr-1"></i> Sản phẩm = mua – trả</li>
                        <li><i class="fas fa-info-circle mr-1"></i> Ngày đầu/cuối chỉ tính đơn mua</li>
                    </ul>
                </div>
            </div> --}}
        </div>
    </div>

    <!-- Lịch sử mua hàng -->
    <div class="tab-pane fade" id="order-history" role="tabpanel">

        @if ($orders->isEmpty())
            <p class="text-muted">Không có đơn hàng nào.</p>
        @else
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Ngày mua</th>
                        <th>Sản phẩm</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái đơn</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <ul>
                                    @foreach ($order->items as $item)
                                        <li>{{ $item->product_name ?? 'SP' }} × {{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}đ</td>
                            <td>{{ ucfirst($order->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <!-- Chăm sóc -->
    <div class="tab-pane fade" id="care" role="tabpanel">
        <p>Chưa có lịch sử chăm sóc khách hàng...</p>
    </div>

    <!-- Bảo hành -->
    <div class="tab-pane fade" id="warranty" role="tabpanel">
        <p>Chưa có thông tin bảo hành...</p>
    </div>

    <!-- Lịch sử sửa -->
    <div class="tab-pane fade" id="repair" role="tabpanel">
        <p>Chưa có lịch sử sửa chữa...</p>
    </div>
</div>
<script>
    $(function() {
        $('#userDetailTabs a').on('click', function(e) {
            e.preventDefault()
            $(this).tab('show')
        });
    });
</script>

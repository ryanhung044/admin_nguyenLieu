{{-- @extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card" style="height: 80vh">
                <div class="card-header">
                    <h5 class="mb-0"><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}"
                            alt="avatar" class="rounded-circle" width="50" height="50">
                        {{ $conversation?->user?->full_name }}</h5>
                </div>
                <div id="chat-box" class="card-body" style="height: 500px; overflow-y: auto; background: #f9f9f9;">
                    @foreach ($messages as $msg)
                        <div
                            class="d-flex mb-3 
                            @if ($msg->sender_type == 'admin') justify-content-end 
                            @elseif ($msg->sender_type == 'system') justify-content-center 
                            @else justify-content-start @endif">

                            <div class="p-2 rounded 
                                @if ($msg->sender_type == 'admin') bg-primary text-white 
                                @elseif ($msg->sender_type == 'system') bg-light text-muted fst-italic 
                                @else bg-light border @endif"
                                style="max-width: 70%;">

                                @if ($msg->message_type === 'text')
                                    <div>{{ $msg->message_text }}</div>
                                @elseif ($msg->message_type === 'image')
                                    <div><img style="max-width: 200px" src="{{ $msg->message_text }}" alt="image"
                                            class="img-fluid rounded"></div>
                                @elseif ($msg->message_type === 'sticker')
                                    <div>[Sticker ID: {{ $msg->message_text }}]</div>
                                @elseif ($msg->message_type === 'event')
                                    <div class="text-muted fst-italic">{{ $msg->message_text }}</div>
                                @else
                                    <div>[Không xác định]</div>
                                @endif

                                <small class="text-muted d-block mt-1" style="font-size: 12px;">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i d/m/Y ') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    <form action="{{ route('admin.conversations.send', $conversation->id) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="Nhập tin nhắn..."
                                required>
                            <button type="submit" class="btn btn-primary">Gửi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 border-end">
            <h4 class="mb-3">Thông tin hội thoại</h4>
            <p><strong>Platform:</strong>
                <span class="badge bg-{{ $conversation->platform == 'zalo' ? 'info' : 'primary' }}">
                    {{ ucfirst($conversation->platform) }}
                </span>
            </p>

            <h4><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}" alt="avatar"
                    class="rounded-circle" width="50" height="50"> {{ $conversation?->user?->name }}</h4>
            <p><strong>Thời gian:</strong>
                {{ $conversation->last_time ? \Carbon\Carbon::parse($conversation->last_time)->format('d/m/Y H:i') : '' }}
            </p>

            <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary btn-sm">
                ← Quay lại danh sách
            </a>
        </div>
    </div>


    <script>
        document.getElementById('sendBtn').addEventListener('click', async () => {
            const type = 'text'; // hoặc 'image', 'file', ...
            const content = document.getElementById('messageInput').value;

            const conversationId = @json($conversation->id);

            const res = await fetch(`/api/conversations/${conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type,
                    content
                })
            });

            const data = await res.json();
            if (data.success) {
                document.getElementById('messageInput').value = '';
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            let chatBox = document.getElementById("chat-box");
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

            const conversation = @json($conversation); // Laravel helper

            window.Echo.channel(`conversation.${conversation.id}`)
                .listen('.MessageCreated', (e) => {
                    console.log('MessageCreated', e);

                    let msg = e.message;

                    // Tạo div chứa message
                    let div = document.createElement("div");
                    div.className = "d-flex mb-3 " +
                        (msg.sender_type === 'admin' ? "justify-content-end" :
                            msg.sender_type === 'system' ? "justify-content-center" : "justify-content-start");

                    // Xử lý nội dung theo loại message
                    let contentHtml = '';
                    switch (msg.message_type) {
                        case 'text':
                            contentHtml = `<div>${msg.message_text}</div>`;
                            break;
                        case 'image':
                            contentHtml =
                                `<div><img style="max-width: 200px" src="${msg.message_text}" alt="image" class="img-fluid rounded"></div>`;
                            break;
                        case 'sticker':
                            contentHtml = `<div>[Sticker ID: ${msg.message_text}]</div>`;
                            break;
                        case 'event':
                            contentHtml = `<div class="text-muted fst-italic">${msg.message_text}</div>`;
                            break;
                        default:
                            contentHtml = `<div>[Không xác định]</div>`;
                    }

                    // Format thời gian
                    let formattedTime = new Date(msg.sent_at).toLocaleString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Tạo innerHTML
                    div.innerHTML = `
                <div class="p-2 rounded ${msg.sender_type === 'admin' ? 'bg-primary text-white' :
                                        msg.sender_type === 'system' ? 'bg-light text-muted fst-italic' :
                                        'bg-light border'}" style="max-width:70%">
                    ${contentHtml}
                    <small class="text-muted d-block mt-1" style="font-size:12px;">
                        ${formattedTime}
                    </small>
                </div>
            `;

                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        });
    </script>
@endsection --}}


@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-lg-7 col-md-12">
            <div class="card chat-card h-100">
                <div class="card-header d-flex align-items-center">
                    <img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}" alt="avatar"
                        class="rounded-circle me-2" width="50" height="50">
                    <h5 class="mb-0">{{ $conversation?->user?->full_name ?? 'Khách hàng' }}</h5>
                </div>

                <div id="chat-box" class="card-body flex-grow-1" style="overflow-y:auto; background:#f9f9f9;">
                    @foreach ($messages as $msg)
                        <div
                            class="d-flex mb-3 @if ($msg->sender_type == 'admin') justify-content-end
                                             @elseif($msg->sender_type == 'system') justify-content-center
                                             @else justify-content-start @endif">
                            <div class="p-2 rounded @if ($msg->sender_type == 'admin') bg-primary text-white
                                                   @elseif($msg->sender_type == 'system') bg-light text-muted fst-italic
                                                   @else bg-light border @endif"
                                style="max-width:70%;">
                                @switch($msg->message_type)
                                    @case('text')
                                        <div>{{ $msg->message_text }}</div>
                                    @break

                                    @case('image')
                                        <div><img src="{{ $msg->message_text }}" style="max-width:200px" class="img-fluid rounded">
                                        </div>
                                    @break

                                    @case('video')
                                        <div><video src="{{ $msg->message_text }}" controls style="max-width:200px"></video></div>
                                    @break

                                    @case('file')
                                        <div><a href="{{ $msg->message_text }}" target="_blank">Tệp đính kèm</a></div>
                                    @break

                                    @case('sticker')
                                        <div>[Sticker ID: {{ $msg->message_text }}]</div>
                                    @break

                                    @case('event')
                                        <div class="text-muted fst-italic">{{ $msg->message_text }}</div>
                                    @break

                                    @default
                                        <div>[Không xác định]</div>
                                @endswitch
                                <small class="text-muted d-block mt-1" style="font-size:12px;">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer d-flex align-items-center">
                    <input type="text" id="messageInput" class="form-control me-2" placeholder="Nhập tin nhắn...">
                    {{-- <input type="file" id="fileInput" class="me-2" style="width:200px"> --}}
                    <button class="btn btn-primary" id="sendBtn">Gửi</button>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-12 border-end">
            <h4 class="mb-3">Thông tin hội thoại</h4>
            <p><strong>Platform:</strong>
                <span
                    class="badge bg-{{ $conversation->platform == 'zalo' ? 'info' : 'primary' }}">{{ ucfirst($conversation->platform) }}</span>
            </p>
            <h4><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}"
                    class="rounded-circle me-2" width="50" height="50">
                {{ $conversation?->user?->name ?? 'Khách hàng' }}
            </h4>
            <p><strong>Thời gian:</strong>
                {{ $conversation->last_time ? \Carbon\Carbon::parse($conversation->last_time)->format('d/m/Y H:i') : '' }}
            </p>
            <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary btn-sm"><i
                    class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>

            <button id="toggleOrderForm" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Lên đơn</button>

            <div class="card-footer mt-2 d-none" id="orderSection">
                <h6>Đặt hàng cho khách</h6>
                <form id="orderForm">
                    <div class="mb-2">
                        <input type="text" id="name" class="form-control" placeholder="Tên khách" required
                            value="{{ $conversation->user->full_name ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <input type="text" id="phone" class="form-control" placeholder="Số điện thoại" required
                            value="{{ $conversation->user->phone ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <input type="text" id="address" class="form-control" placeholder="Địa chỉ" required
                            value="{{ $conversation->user->address ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <select id="payment_method" class="form-control" required>
                            <option value="cod">Thanh toán khi nhận hàng</option>
                            <option value="bank_transfer">Chuyển khoản</option>
                            <option value="zalo_pay">ZaloPay</option>
                        </select>
                    </div>
                    <input type="hidden" id="user_id" value="{{ $conversation->user->id }}">

                    <div class="card mt-3">
                        <div class="card-header">Giỏ hàng</div>
                        <div class="card-body">
                            <div id="cart-items">
                                <p class="text-muted">Chưa có sản phẩm nào trong giỏ.</p>
                            </div>
                            <div class="mt-3 d-flex justify-content-between">
                                <strong>Tổng cộng: <span id="cart-total">0 đ</span></strong>
                                <button class="btn btn-success btn-sm" id="checkout-btn">Đặt hàng</button>
                            </div>
                        </div>
                    </div>


                    {{-- <button type="submit" class="btn btn-success">Đặt hàng</button> --}}
                </form>
                <div id="orderResult" class="mt-2"></div>
            </div>
        </div>
        <div class="mb-2 d-none" id="productSection">
            <div class="card mt-3">
                <div class="card-header">Danh sách sản phẩm</div>
                <div class="card-body" style="overflow-y:auto;">
                    <div class="row g-3">
                        @foreach ($products as $product)
                            <div class="col-md-3">
                                <div class="card h-100 shadow-sm">
                                    <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('images/no-image.png') }}"
                                        class="card-img-top" alt="{{ $product->name }}"
                                        style="height:200px; object-fit:cover;">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">{{ $product->name }}</h6>

                                        {{-- Nếu có biến thể thì hiển thị select --}}
                                        {{-- Nếu có biến thể thì hiển thị select --}}
                                        @if ($product->variants->count())
                                            <select class="form-select form-select-sm mb-2 variant-select"
                                                data-product-id="{{ $product->id }}">
                                                <option value="">-- Chọn biến thể --</option>
                                                @foreach ($product->variants as $variant)
                                                    <option value="{{ $variant->id }}"
                                                        data-price="{{ $variant->sale_price }}"
                                                        data-stock="{{ $variant->stock }}"
                                                        data-attrs="{{ implode(' - ', $variant->attributeValues->pluck('value')->toArray()) }}">
                                                        {{ implode(' - ', $variant->attributeValues->pluck('value')->toArray()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="hidden" class="variant-select"
                                                data-product-id="{{ $product->id }}" value="">
                                            <p class="text-muted small">Sản phẩm không có biến thể</p>
                                        @endif

                                        {{-- Giá + kho --}}
                                        <p class="fw-bold mb-0 price-label">Giá: {{ number_format($product->sale_price) }}
                                            đ</p>
                                        <p class="text-secondary small stock-label mb-0">Kho: {{ $product->stock }}</p>

                                        <div class="mt-auto d-flex gap-2">
                                            <input type="number" min="0"
                                                class="form-control form-control-sm qty-input" placeholder="Số lượng"
                                                data-product-id="{{ $product->id }}">

                                            <button class="btn btn-primary btn-sm add-to-cart"
                                                data-product-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                {{-- ⚡ chỉ thêm data-price nếu không có biến thể --}}
                                                @if (!$product->variants->count()) data-price="{{ $product->sale_price }}" @endif>
                                                Thêm
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>

        <div class="mb-2 d-none" id="orderHistorySection">
            <div class="card mt-3">
                <div class="card-header">Lịch sử đơn hàng</div>
                <div class="card-body" style="overflow-y:auto; max-height:800px;">
                    <ul id="orderHistoryList" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadOrderHistory(userId, status = 'all') {
            if (!userId) {
                console.error("Chưa có user_id");
                return;
            }

            try {
                const response = await fetch(`/api/orders?user_id=${userId}&status=${status}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error("Lỗi tải đơn hàng");
                }

                const result = await response.json();

                const list = document.getElementById("orderHistoryList");
                list.innerHTML = "";

                if (result.length === 0) {
                    // list.innerHTML = `<li class="list-group-item text-muted">Chưa có đơn hàng</li>`;
                } else {
                    result.forEach(order => {
                        const createdAt = new Date(order.created_at).toLocaleString('vi-VN');

                        const li = document.createElement("li");
                        li.classList.add("list-group-item");

                        let itemsHtml = order.items.map(item => `
        <tr>
            <td style="width:50px">
                <img src="/storage/${item.thumbnail}" 
                     alt="${item.product_name}" 
                     style="width:40px;height:40px;object-fit:cover;">
            </td>
            <td>${item.product_name}</td>
            <td class="text-center">x${item.quantity}</td>
            <td class="text-end">${Number(item.price).toLocaleString('vi-VN')}đ</td>
        </tr>
    `).join("");

                        li.innerHTML = `
        <div class="mb-2">
            <strong>Mã đơn:</strong> #${order.id} |
            <strong>Khách:</strong> ${order.name} (${order.phone}) |
            <strong>Ngày tạo:</strong> ${createdAt}
        </div>
        <div class="mb-2">
            <strong>Địa chỉ:</strong> ${order.address}<br>
            <strong>Thanh toán:</strong> ${order.payment_method.toUpperCase()} |
            <strong>Trạng thái:</strong> 
                <span class="badge ${order.status === 'pending' ? 'bg-warning' : 'bg-success'}">
                    ${order.status}
                </span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px"></th>
                        <th>Sản phẩm</th>
                        <th class="text-center">SL</th>
                        <th class="text-end">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
        </div>
        <div class="text-end fw-bold">Tổng: ${Number(order.total).toLocaleString('vi-VN')}đ</div>
    `;

                        list.appendChild(li);
                    });


                }

                document.getElementById("orderHistorySection").classList.remove("d-none");

            } catch (error) {
                console.error(error);
            }
        }

        // Ví dụ: user_id = 5
        document.addEventListener("DOMContentLoaded", () => {
            // Lấy user_id từ Blade
            const userId = @json($conversation->user->id);

            // Gọi hàm loadOrderHistory
            loadOrderHistory(userId);
        });
    </script>

    <script>
        document.getElementById('toggleOrderForm').addEventListener('click', () => {
            const orderSection = document.getElementById('orderSection');
            const productSection = document.getElementById('productSection');

            orderSection.classList.toggle('d-none');
            productSection.classList.toggle('d-none');
        });
    </script>
    <script>
        let cart = [];

        // Hàm render giỏ hàng
        function renderCart() {
            const container = document.getElementById('cart-items');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = '<p class="text-muted">Chưa có sản phẩm nào trong giỏ.</p>';
                document.getElementById('cart-total').textContent = '0 đ';
                return;
            }

            const table = document.createElement('table');
            table.className = 'table table-sm align-middle';
            table.innerHTML = `
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th style="width:90px">SL</th>
                <th>Thành tiền</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            ${cart.map((item, i) => `
                                                                                                                    <tr>
                                                                                                                        <td>${item.name}</td>
                                                                                                                        <td>${item.price.toLocaleString()} đ</td>
                                                                                                                        <td>
                                                                                                                            <input type="number" class="form-control form-control-sm update-qty" 
                                                                                                                                   data-index="${i}" min="1" value="${item.quantity}">
                                                                                                                        </td>
                                                                                                                        <td>${(item.price * item.quantity).toLocaleString()} đ</td>
                                                                                                                        <td>
                                                                                                                            <button class="btn btn-danger btn-sm remove-item" data-index="${i}">
                                                                                                                                Xóa
                                                                                                                            </button>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                `).join('')}
        </tbody>
    `;
            container.appendChild(table);

            // Cập nhật tổng
            const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
            document.getElementById('cart-total').textContent = total.toLocaleString() + ' đ';

            // Gán event update & remove
            document.querySelectorAll('.update-qty').forEach(input => {
                input.addEventListener('change', () => {
                    const index = input.dataset.index;
                    const newQty = parseInt(input.value) || 1;
                    cart[index].quantity = newQty;
                    renderCart();
                });
            });

            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    const index = btn.dataset.index;
                    cart.splice(index, 1);
                    renderCart();
                });
            });
        }

        // Sự kiện thêm vào giỏ
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', () => {
                const productId = btn.dataset.productId;
                const card = btn.closest('.card-body');
                const select = card.querySelector('.variant-select');
                const qtyInput = card.querySelector('.qty-input');
                const quantity = parseInt(qtyInput.value) || 1;
                if (quantity <= 0) return alert('Vui lòng nhập số lượng');

                let variantId = null;
                let attrs = '';
                let price = 0;

                if (select && select.value) {
                    variantId = select.value;
                    const option = select.options[select.selectedIndex];
                    attrs = option.dataset.attrs;
                    price = parseFloat(option.dataset.price);
                } else {
                    price = parseFloat(btn.dataset.price || 0);
                }

                // Kiểm tra có trong giỏ chưa
                const existing = cart.find(c => c.id == productId && c.variant_id == variantId);
                if (existing) {
                    existing.quantity += quantity;
                } else {
                    cart.push({
                        id: productId,
                        variant_id: variantId,
                        name: btn.dataset.name + (attrs ? ` (${attrs})` : ''),
                        price,
                        quantity
                    });
                }

                console.log(cart);

                qtyInput.value = '';
                renderCart();
            });
        });

        // Sự kiện checkout
        document.getElementById('checkout-btn').addEventListener('click', () => {
            event.preventDefault(); // ❌ Ngăn form submit mặc định

            if (cart.length === 0) return alert('Giỏ hàng trống!');

            const orderData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                payment_method: document.getElementById('payment_method').value,
                user_id: document.getElementById('user_id').value,
                cart: cart.map(item => ({
                    id: item.id,
                    variant_id: item.variantId || null,
                    quantity: item.quantity,
                    name: item.name
                }))
            };

            console.log("Checkout data:", orderData);

            fetch('/api/order/place', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // Nếu bạn dùng Sanctum/Passport cho user đăng nhập thì thêm token:
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                    body: JSON.stringify(orderData)
                })
                .then(res => res.json())
                .then(data => {
                    // return console.log(data);

                    if (data.success) {
                        alert("Đặt hàng thành công! Mã đơn: " + data.order_id);
                        cart = []; // clear giỏ hàng
                        renderCart();
                        const userId = @json($conversation->user->id);

                        // Gọi hàm loadOrderHistory
                        loadOrderHistory(userId);

                    } else {
                        alert("Lỗi: " + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Có lỗi khi đặt hàng.");
                });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatBox = document.getElementById("chat-box");
            const messageInput = document.getElementById("messageInput");
            const sendBtn = document.getElementById("sendBtn");
            const fileInput = document.getElementById("fileInput");
            const conversationId = @json($conversation->id);

            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

            sendBtn.addEventListener('click', async () => {
                let type = 'text';
                let content = messageInput.value.trim();

                const formData = new FormData();
                formData.append('type', type);
                formData.append('content', content);
                if (fileInput) {
                    const file = fileInput.files[0];
                    if (file) {
    
                        const mime = file.type;
                        if (mime.startsWith('image/')) type = 'image';
                        else if (mime.startsWith('video/')) type = 'video';
                        else type = 'file';
                        formData.set('type', type);
                        formData.set('content', file);
                    }
                }


                const res = await fetch(`/api/conversations/${conversationId}/messages`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    messageInput.value = '';
                    fileInput.value = '';
                }
            });

            // Realtime
            window.Echo.channel(`conversation.${conversationId}`)
                .listen('.MessageCreated', (e) => {
                    const msg = e.message;
                    if (!msg) return;

                    const div = document.createElement("div");
                    div.className = "d-flex mb-3 " +
                        (msg.sender_type === 'admin' ? "justify-content-end" : msg.sender_type === 'system' ?
                            "justify-content-center" : "justify-content-start");

                    let contentHtml = '';
                    switch (msg.message_type) {
                        case 'text':
                            contentHtml = `<div>${msg.message_text}</div>`;
                            break;
                        case 'image':
                            contentHtml =
                                `<div><img src="${msg.message_text}" style="max-width:200px" class="img-fluid rounded"></div>`;
                            break;
                        case 'video':
                            contentHtml =
                                `<div><video src="${msg.message_text}" controls style="max-width:200px"></video></div>`;
                            break;
                        case 'file':
                            contentHtml =
                                `<div><a href="${msg.message_text}" target="_blank">Tệp đính kèm</a></div>`;
                            break;
                        case 'sticker':
                            contentHtml = `<div>[Sticker ID: ${msg.message_text}]</div>`;
                            break;
                        case 'event':
                            contentHtml = `<div class="text-muted fst-italic">${msg.message_text}</div>`;
                            break;
                        default:
                            contentHtml = `<div>[Không xác định]</div>`;
                    }

                    const formattedTime = new Date(msg.sent_at).toLocaleString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    div.innerHTML = `<div class="p-2 rounded ${msg.sender_type==='admin'?'bg-primary text-white':msg.sender_type==='system'?'bg-light text-muted fst-italic':'bg-light border'}" style="max-width:70%">
                ${contentHtml}
                <small class="text-muted d-block mt-1" style="font-size:12px;">${formattedTime}</small>
            </div>`;

                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        });
    </script>
    <style>
        .card:hover {
            transform: translateY(-2px);
            transition: 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .add-to-cart {
            white-space: nowrap;
        }

        #chat-box {
            overflow-y: auto;
            background: #f9f9f9;
            min-height: 500px;
            max-height: 500px;
            /* hoặc 400px */
        }

        .list-group-item {
            flex-direction: column;
        }
    </style>
@endsection

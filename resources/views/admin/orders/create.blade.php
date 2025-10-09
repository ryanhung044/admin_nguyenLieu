@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-12 border-end">
            <div class="card-footer mt-2" id="orderSection">
                <h6>Đặt hàng cho khách</h6>
                <form id="orderForm">
                    <div class="mb-2">
                        <input type="text" id="name" class="form-control" placeholder="Tên khách" required
                            value="{{ '' }}">
                    </div>
                    <div class="mb-2">
                        <input type="text" id="phone" class="form-control" placeholder="Số điện thoại" required
                            value="{{ '' }}">
                    </div>
                    <div class="mb-2">
                        <input type="text" id="address" class="form-control" placeholder="Địa chỉ" required
                            value="{{ '' }}">
                    </div>
                    <div class="mb-2">
                        <select id="payment_method" class="form-control" required>
                            <option value="cod">Thanh toán khi nhận hàng</option>
                            <option value="bank_transfer">Chuyển khoản</option>
                        </select>
                    </div>

                    <div class="mb-2 d-flex gap-2">
                        <input type="text" id="voucher_code" class="form-control" placeholder="Nhập mã giảm giá">
                        <button type="button" id="apply-voucher" class="btn btn-outline-primary btn-sm">Áp dụng</button>
                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#createVoucherModal">
                            + Tạo mã
                        </button>
                    </div>
                    <div id="voucher-info" class="text-success small mb-2"></div>

                    <div id="voucher-info" class="text-success small mb-2"></div>

                    {{-- <input type="hidden" id="user_id" value="{{ $conversation->user->id }}"> --}}

                    <div class="card mt-3">
                        <div class="card-header">Giỏ hàng</div>
                        <div class="card-body">
                            <div id="cart-items" style="overflow: auto">
                                <p class="text-muted">Chưa có sản phẩm nào trong giỏ.</p>
                            </div>
                            {{-- <div class="mt-3 d-flex justify-content-between">
                                <strong>Tổng cộng: <span id="cart-total">0 đ</span></strong>
                                <button class="btn btn-success btn-sm" id="checkout-btn">Đặt hàng</button>
                            </div> --}}
                            <div class="d-flex justify-content-between">
                                <span>Tạm tính:</span>
                                <strong><span id="subtotal">0 đ</span></strong>
                            </div>
                            <div class="d-flex justify-content-between text-success">
                                <span>Giảm giá:</span>
                                <strong><span id="discount">0 đ</span></strong>
                            </div>
                            <div class="d-flex justify-content-between border-top mt-1 pt-1">
                                <strong>Tổng cộng:</strong>
                                <strong><span id="cart-total">0 đ</span></strong>
                            </div>
                            <button class="btn btn-success btn-sm mt-2 w-100" id="checkout-btn">Đặt hàng</button>
                        </div>
                    </div>
                </form>
                <div id="orderResult" class="mt-2"></div>
            </div>
        </div>
        <div class=" col-lg-8 mb-2" id="productSection">
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
    </div>

    <script>
        let cart = [];
        let appliedVoucher = null;
        let discountAmount = 0;

        // Gọi API kiểm tra mã giảm giá
        document.getElementById('apply-voucher').addEventListener('click', () => {
            const code = document.getElementById('voucher_code').value.trim();
            if (!code) return alert('Vui lòng nhập mã giảm giá');

            const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

            fetch(`/api/vouchers/check`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code,
                        order_amount: subtotal
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        appliedVoucher = data.voucher;
                        discountAmount = Number(data.discount);

                        let discountText = appliedVoucher.type === 'percentage' ?
                            `Giảm ${Number(appliedVoucher.discount_value)}%` :
                            `Giảm ${Number(discountAmount).toLocaleString()} đ`;

                        document.getElementById('voucher-info').textContent =
                            `Mã "${code}" được áp dụng - ${discountText}`;
                        renderCart();
                    }

                })
                .catch(() => alert('Không thể kiểm tra mã giảm giá'));
        });

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
                                    ${cart.map((item, i) =>
                                    `<tr>
                                                                                                                <td>${item.name}</td>
                                                                                                                <td>${item.price.toLocaleString()} đ</td>
                                                                                                                <td>
                                                                                                                    <input type="number" class="form-control form-control-sm update-qty" 
                                                                                                                            data-index="${i}" min="1" value="${item.quantity}" style="min-width: 50px;">
                                                                                                                </td>
                                                                                                                <td>${(item.price * item.quantity).toLocaleString()} đ</td>
                                                                                                                <td>
                                                                                                                    <button class="btn btn-danger btn-sm remove-item" data-index="${i}">
                                                                                                                        Xóa
                                                                                                                    </button>
                                                                                                                </td>
                                                                                                            </tr>`).join('')}
                                </tbody>
                                `;
            container.appendChild(table);

            // Cập nhật tổng
            // const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
            // document.getElementById('cart-total').textContent = total.toLocaleString() + ' đ';
            const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

            // --- TÍNH GIẢM GIÁ ---
            let discount = 0;

            if (appliedVoucher) {
                if (appliedVoucher.type === 'percentage') {
                    discount = Math.floor(subtotal * (appliedVoucher.discount_value / 100));

                    if (appliedVoucher.max_discount && discount > appliedVoucher.max_discount) {
                        discount = appliedVoucher.max_discount;
                    }

                    // Nếu có đơn hàng tối thiểu
                    if (appliedVoucher.min_order_amount && subtotal < appliedVoucher.min_order_amount) {
                        discount = 0;
                        document.getElementById('voucher-info').textContent =
                            `Đơn hàng chưa đạt mức tối thiểu (${Number(appliedVoucher.min_order_amount).toLocaleString()} đ) để áp dụng mã.`;
                    }
                } else if (appliedVoucher.type === 'fixed') {
                    discount = Number(appliedVoucher.discount_value);
                }

                // Không cho giảm vượt quá tổng tiền
                if (discount > subtotal) discount = subtotal;
            }

            const total = subtotal - discount;

            // --- CẬP NHẬT HIỂN THỊ ---
            document.getElementById('subtotal').textContent = Number(subtotal).toLocaleString() + ' đ';
            document.getElementById('discount').textContent = Number(discount).toLocaleString() + ' đ';
            document.getElementById('cart-total').textContent = Number(total).toLocaleString() + ' đ';


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
            console.log(cart.length);

            if (cart.length === 0) return alert('Giỏ hàng trống!');

            const orderData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                payment_method: document.getElementById('payment_method').value,
                // user_id: document.getElementById('user_id').value,
                voucher: appliedVoucher ? appliedVoucher.code : null, // thêm dòng này

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
                        appliedVoucher = null;
                        discountAmount = 0;
                        renderCart();
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

    <!-- Modal tạo mã giảm giá -->
    <div class="modal fade" id="createVoucherModal" tabindex="-1" aria-labelledby="createVoucherLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createVoucherLabel">Tạo mã giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createVoucherForm">
                        <div class="mb-3">
                            <label for="code" class="form-label">Mã giảm giá</label>
                            <input type="text" id="code" name="code" class="form-control"
                                placeholder="VD: SALE10" required>
                        </div>
                        <div class="mb-3">
                            <label for="discount_type" class="form-label">Loại giảm giá</label>
                            <select id="discount_type" name="discount_type" class="form-select" required>
                                <option value="percentage">Phần trăm (%)</option>
                                <option value="fixed">Số tiền cố định</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="discount_value" class="form-label">Giá trị giảm</label>
                            <input type="number" id="discount_value" name="discount_value" class="form-control"
                                placeholder="Nhập giá trị" required>
                        </div>
                        <div class="mb-3">
                            <label for="min_order_amount" class="form-label">Giá trị đơn tối thiểu</label>
                            <input type="number" id="min_order_amount" name="min_order_amount" class="form-control"
                                placeholder="VD: 100000">
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Ngày hết hạn</label>
                            <input type="date" id="expiry_date" name="expiry_date" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-success w-100" id="createVoucherBtn">Tạo mã</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createBtn = document.getElementById('createVoucherBtn');
            if (!createBtn) return; // tránh lỗi nếu chưa render modal

            createBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const formData = {
                    code: document.getElementById('code').value.trim() || 'SALE' + Math.floor(Math
                        .random() * 10000),
                    type: document.getElementById('discount_type').value,
                    discount_value: document.getElementById('discount_value').value,
                    min_order_amount: document.getElementById('min_order_amount').value || 0,
                    end_date: document.getElementById('expiry_date').value,
                };

                fetch('/api/vouchers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const voucher = data.voucher;
                            alert('✅ Mã "' + voucher.code + '" đã được tạo và áp dụng ngay.');

                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'createVoucherModal'));
                            modal.hide();

                            document.getElementById('voucher_code').value = voucher.code;

                            const subtotal = cart.reduce((sum, item) => sum + item.price * item
                                .quantity, 0);
                            let discountValue = voucher.type === 'percentage' ?
                                Math.floor(subtotal * (voucher.discount_value / 100)) :
                                Number(voucher.discount_value);


                            appliedVoucher = voucher;
                            discountAmount = discountValue;

                            let discountText = voucher.type === 'percentage' ?
                                `Giảm ${voucher.discount_value}%` :
                                `Giảm ${Number(voucher.discount_value).toLocaleString()} đ`;

                            document.getElementById('voucher-info').textContent =
                                `Áp dụng mã "${voucher.code}" - ${discountText}`;


                            renderCart();
                        } else {
                            alert('⚠️ ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Lỗi khi tạo mã.');
                    });
            });
        });
    </script>
@endsection

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hóa đơn #{{ $order->id }}</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 13px; margin: 20px; color: #000; }
    .header { display: flex; justify-content: space-between; }
    .shop-info { max-width: 60%; }
    .shop-info h2 { margin: 0; font-size: 16px; }
    .barcode { text-align: right; }
    .barcode img { height: 60px; }
    .order-id { font-weight: bold; margin-top: 5px; }
    h3 { margin: 10px 0 5px; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { padding: 5px; border-bottom: 1px solid #ddd; font-size: 13px; }
    th { text-align: left; background: #f9f9f9; }
    .section { display: flex; gap: 15px; margin-top: 10px; }
    .section > div { flex: 1; border: 1px solid #ddd; padding: 8px; border-radius: 3px; }
    .total { font-weight: bold; font-size: 14px; }
    .footer { font-size: 12px; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px; }
  </style>
</head>
<body style="max-width: 800px; margin: auto; margin-top: 3rem; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5;">
  <style>
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 2rem;
    }
    .shop-info h2 {
      margin: 0 0 8px;
      font-size: 18px;
    }
    .shop-info p {
      margin: 4px 0;
      line-height: 1.4;
    }
    .barcode {
      text-align: right;
    }
    .barcode img {
      height: 60px;
      margin-bottom: 5px;
    }
    .order-id {
      font-weight: bold;
      font-size: 16px;
    }
    .section {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      gap: 20px;
      margin-bottom: 2rem;
    }
    h3 {
      margin: 10px 0;
      border-bottom: 1px solid #ddd;
      padding-bottom: 5px;
      font-size: 15px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 13px;
    }
    th {
      background: #f9f9f9;
      text-align: left;
    }
    .total {
      font-weight: bold;
      font-size: 15px;
      color: #000;
      display: inline-block;
      margin-top: 5px;
    }
    .footer {
      border-top: 1px solid #ddd;
      padding-top: 10px;
      margin-top: 2rem;
      font-size: 12px;
      text-align: center;
    }
  </style>

  <div class="header">
    <div class="shop-info">
      <h2>Nguyên liệu đóng gói 228</h2>
      <p>
        Địa chỉ: Đường khu đô thị Đồng Tàu, Thịnh Liệt, Hoàng Mai, Hà Nội<br>
        Điện thoại: 0975257980<br>
        Website: https://tongkhoht228.com<br>
        Email: CSKH@tongkhoht228.com
      </p>
    </div>
    <div class="barcode">
      <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $order->id }}&code=Code128&dpi=96" alt="barcode">
      <div class="order-id">#{{ $order->id }}</div>
    </div>
  </div>

  <div class="section">
    <div>
      <h3>Chi tiết đơn hàng</h3>
      <table>
        <tr>
          <th>Mã sản phẩm</th>
          <th>Sản phẩm</th>
          <th>Số lượng</th>
          <th>Giá</th>
        </tr>
        @foreach($order->items as $item)
        <tr>
          <td>#SP0000{{ $item->product->id }}</td>
          <td>{{ $item->product_name }}</td>
          <td>{{ $item->quantity }}</td>
          <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
        </tr>
        @endforeach
      </table>

      <h3>Thông tin thanh toán</h3>
      <p>
        Tổng giá sản phẩm: {{ number_format($order->subtotal, 0, ',', '.') }}đ<br>
        Khuyến mãi: {{ number_format($order->discount ?? 0, 0, ',', '.') }}đ<br>
        Phí vận chuyển: {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}đ<br>
        <span class="total">Tổng tiền: {{ number_format($order->total, 0, ',', '.') }}đ</span>
      </p>
    </div>

    <div>
      <h3>Thông tin đơn hàng</h3>
      <p>
        Mã đơn: #{{ $order->id }}<br>
        Ngày đặt: {{ $order->created_at->format('d/m/Y') }}<br>
        Phương thức thanh toán: {{ $order->payment_method ?? 'COD' }}<br>
        Phương thức vận chuyển: {{ $order->shipping_method ?? 'Giao hàng tận nơi' }}
      </p>

      <h3>Thông tin mua hàng</h3>
      <p>
        {{ $order->name }}<br>
        {{ $order->address }}<br>
        Điện thoại: {{ $order->phone }}
      </p>
    </div>
  </div>

  <div class="footer">
    Đăng ký tiếp thị liên kết chia sẻ hoa hồng cùng tổng kho HT228 ngay, hoa hồng lên tới 10%
  </div>
</body>

</html>

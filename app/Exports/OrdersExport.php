<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Order::with(['items', 'user'])
            ->orderBy('created_at', 'desc');

        if ($this->request->filled('search')) {
            $keyword = $this->request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        if ($this->request->filled('status') && $this->request->status !== 'all') {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('from_date') || $this->request->filled('to_date')) {
            $from = $this->request->from_date ?? '2000-01-01';
            $to = $this->request->to_date ?? now()->toDateString();
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->get();
    }


    public function map($order): array
    {
        $paymentStatusMap = [
            'pending' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền',
        ];

        $orderStatusMap = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
        ];

        $items = $order->items->map(function ($item) {
            return "{$item->product_name} x{$item->quantity}";
        })->implode("\n");

        return [
            $order->id,
            $order->name,
            $order->phone,
            $order->address,
            $order->payment_method,
            number_format($order->total, 0, ',', '.') . ' đ',
            $orderStatusMap[$order->status] ?? $order->status,
            $paymentStatusMap[$order->status_payment] ?? $order->status_payment,
            $items,
            optional($order->user)->name ?? 'Không rõ',
            $order->created_at->format('d/m/Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên khách hàng',
            'Số điện thoại',
            'Địa chỉ',
            'Phương thức thanh toán',
            'Tổng tiền',
            'Trạng thái đơn hàng',
            'Trạng thái thanh toán',
            'Sản phẩm',
            'Người dùng',
            'Ngày tạo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bôi đậm dòng đầu tiên (headings)
        return [
            1 => ['font' => ['bold' => true]],
            'I2:I1000' => [
                'alignment' => [
                    'wrapText' => true,
                ],
            ],
        ];
    }
}

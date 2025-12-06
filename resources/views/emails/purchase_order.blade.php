<!DOCTYPE html>
<html>
<head>
    <title>Request for Quotation - {{ $order->po_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <h2>Permintaan Penawaran (RFQ)</h2>
    <p>Kepada Yth. <strong>{{ $order->supplier->name }}</strong>,</p>

    <p>Kami dari <strong>Perusahaan Anda</strong> bermaksud meminta penawaran untuk barang-barang berikut:</p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%; margin-top: 20px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga Satuan (IDR)</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }} ({{ $item->product->code }})</td>
                <td style="text-align: center;">{{ $item->qty }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">Total</td>
                <td style="text-align: right;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 20px;">
        Mohon konfirmasi ketersediaan dan harga barang tersebut.
    </p>

    <p>Terima kasih,<br>Tim Purchasing</p>

</body>
</html>

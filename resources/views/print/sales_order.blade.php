<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - {{ $order->so_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: right; padding: 10px; background: #eee;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">Cetak PDF</button>
    </div>

    <div class="container">
        <div class="header">
            <h1>Sales Order</h1>
            <p>PT. ENTERPRISE RESOURCE PLANNING</p>
            <p>Jl. Teknologi No. 1, Jakarta Selatan</p>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Nomor SO</td>
                <td>: {{ $order->so_number }}</td>
                <td class="info-label">Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Customer</td>
                <td>: {{ $order->customer->name ?? '-' }}</td>
                <td class="info-label">Status</td>
                <td>: {{ ucfirst($order->status) }}</td>
            </tr>
            <tr>
                <td class="info-label">Alamat</td>
                <td colspan="3">: {{ $order->customer->address ?? '-' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Produk</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Harga Satuan</th>
                    <th style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="signature">
                <p>Dibuat Oleh,</p>
                <div class="signature-line">Admin Sales</div>
            </div>
            <div class="signature">
                <p>Disetujui Oleh,</p>
                <div class="signature-line">Manager / Customer</div>
            </div>
        </div>
    </div>

</body>
</html>

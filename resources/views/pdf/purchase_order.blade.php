<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak PO - {{ $order->po_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background-color: #f4f4f4; /* Background abu-abu di layar */
            margin: 0;
            padding: 20px;
        }
        .page-container {
            background-color: #fff;
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }
        .no-print {
            text-align: right;
            margin-bottom: 20px;
        }
        .btn-print {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            color: #444;
        }
        .company-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            width: 48%;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 100px;
            display: inline-block;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            .page-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Cetak PDF</button>
    </div>

    <div class="page-container">
        
        <div class="header">
            <h1>{{ $order->status === 'RFQ' || $order->status === 'Sent' ? 'REQUEST FOR QUOTATION' : 'PURCHASE ORDER' }}</h1>
            <div class="company-info">
                PT. ENTERPRISE RESOURCE PLANNING<br>
                Jl. Teknologi No. 1, Jakarta Selatan
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <table class="info-table">
                    <tr>
                        <td class="label">Nomor PO</td>
                        <td>: {{ $order->po_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Supplier</td>
                        <td>: <strong>{{ $order->supplier->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td>: {{ $order->supplier->address ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="info-box">
                <table class="info-table">
                    <tr>
                        <td class="label">Tanggal</td>
                        <td>: {{ date('d/m/Y', strtotime($order->order_date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td>: {{ $order->status }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Produk</th>
                    <th width="10%">Qty</th>
                    <th width="20%">Harga Satuan</th>
                    <th width="20%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product->name }}<br>
                        <small style="color: #777;">{{ $item->product->code }}</small>
                    </td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-section">
            <div class="signature-box">
                <p>Dibuat Oleh,</p>
                <div class="signature-line">Admin Purchasing</div>
            </div>
            <div class="signature-box">
                <p>Disetujui Oleh,</p>
                <div class="signature-line">Manager / Supplier</div>
            </div>
        </div>

    </div>

    <script>
        // Otomatis print saat halaman dimuat
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>

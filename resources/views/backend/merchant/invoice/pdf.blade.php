<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->invoice_id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', Arial, sans-serif;
            margin: 0;
            padding: 15px;
            background: white;
            direction: rtl;
            font-size: 12px;
            line-height: 1.3;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #7e0095;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #7e0095;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #7e0095;
            margin-bottom: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 8px;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background: #f8d7da;
            color: #721c24;
        }
        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
        }
        .merchant-info {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .merchant-info h3 {
            margin: 0 0 8px 0;
            color: #7e0095;
            font-size: 14px;
        }
        .merchant-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .detail-group h4 {
            margin: 0 0 3px 0;
            color: #333;
            font-size: 11px;
        }
        .detail-group p {
            margin: 0 0 8px 0;
            color: #666;
            font-size: 11px;
        }
        .invoice-summary {
            background: #7e0095;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item h4 {
            margin: 0 0 3px 0;
            font-size: 11px;
            opacity: 0.9;
        }
        .summary-item p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .parcels-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            font-size: 10px;
        }
        .parcels-table th,
        .parcels-table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .parcels-table th:nth-child(1),
        .parcels-table td:nth-child(1) { width: 5%; }
        .parcels-table th:nth-child(2),
        .parcels-table td:nth-child(2) { width: 25%; }
        .parcels-table th:nth-child(3),
        .parcels-table td:nth-child(3) { width: 15%; }
        .parcels-table th:nth-child(4),
        .parcels-table td:nth-child(4) { width: 12%; }
        .parcels-table th:nth-child(5),
        .parcels-table td:nth-child(5) { width: 12%; }
        .parcels-table th:nth-child(6),
        .parcels-table td:nth-child(6) { width: 12%; }
        .parcels-table th:nth-child(7),
        .parcels-table td:nth-child(7) { width: 12%; }
        .parcels-table th:nth-child(8),
        .parcels-table td:nth-child(8) { width: 12%; }
        .parcels-table th {
            background: #7e0095;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        .parcels-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        .parcels-table tr:nth-child(even) {
            background: #f8f9ff;
        }
        .total-section {
            text-align: right;
            margin-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
            font-size: 11px;
        }
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #7e0095;
            padding-top: 10px;
            margin-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background: #f8d7da;
            color: #721c24;
        }
        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @media print {
            body { margin: 0; }
            .invoice-container { border: none; }
            .print-button { display: none !important; }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #7e0095;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(126, 0, 149, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .print-button:hover {
            background: #9c27b0;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(126, 0, 149, 0.4);
        }
        .print-button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button class="print-button" onclick="printInvoice()">
        <i class="fa fa-print"></i> طباعة الفاتورة
    </button>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">{{ settings()->site_name ?? 'نظام البريد' }}</div>
            <div class="invoice-info">
                <div class="invoice-title">فاتورة</div>
                <p><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_id }}</p>
                <p><strong>التاريخ:</strong> {{ $invoice->invoice_date }}</p>
                <p><strong>تم الإنشاء:</strong> {{ $invoice->created_at->format('Y-m-d H:i:s') }}</p>
                @php
                    $statusClass = '';
                    $statusText = '';
                    switch($invoice->status) {
                        case 1: // PAID
                            $statusClass = 'status-paid';
                            $statusText = 'مدفوعة';
                            break;
                        case 2: // UNPAID
                            $statusClass = 'status-unpaid';
                            $statusText = 'غير مدفوعة';
                            break;
                        case 3: // PROCESSING
                            $statusClass = 'status-processing';
                            $statusText = 'قيد المعالجة';
                            break;
                        default:
                            $statusClass = 'status-unpaid';
                            $statusText = 'غير معروف';
                    }
                @endphp
                <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>
            </div>
        </div>

        <!-- Merchant Information -->
        <div class="merchant-info">
            <h3>الفاتورة موجهة إلى:</h3>
            <div class="merchant-details">
                <div class="detail-group">
                    <h4>اسم الشركة:</h4>
                    <p>{{ $invoice->merchant->business_name }}</p>
                    <h4>الشخص المسؤول:</h4>
                    <p>{{ $invoice->merchant->user->name }}</p>
                </div>
                <div class="detail-group">
                    <h4>البريد الإلكتروني:</h4>
                    <p>{{ $invoice->merchant->user->email }}</p>
                    <h4>رقم الهاتف:</h4>
                    <p>{{ $invoice->merchant->user->mobile }}</p>
                    <h4>العنوان:</h4>
                    <p>{{ $invoice->merchant->user->address }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Summary -->
        <div class="invoice-summary">
            <h3 style="margin: 0 0 15px 0; text-align: center; font-size: 14px;">ملخص الفاتورة</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <h4>المبلغ المحصل</h4>
                    <p>{{ settings()->currency }}{{ number_format($invoice->cash_collection, 2) }}</p>
                </div>
                <div class="summary-item">
                    <h4>إجمالي الرسوم</h4>
                    <p>{{ settings()->currency }}{{ number_format($invoice->total_charge, 2) }}</p>
                </div>
                <div class="summary-item">
                    <h4>المبلغ المستحق</h4>
                    <p>{{ settings()->currency }}{{ number_format($invoice->current_payable, 2) }}</p>
                </div>
            </div>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.3);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>حالة الدفع:</strong>
                        <span class="status-badge {{ $statusClass }}" style="margin-right: 10px;">{{ $statusText }}</span>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-size: 14px; opacity: 0.9;">إجمالي الطرود: {{ $invoiceParcels->count() }}</div>
                        <div style="font-size: 14px; opacity: 0.9;">تاريخ الإنشاء: {{ $invoice->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parcels Table -->
        <h3 style="margin-bottom: 12px; color: #7e0095; font-size: 14px;">تفاصيل الطرود</h3>
        <table class="parcels-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم العميل</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>المبلغ المحصل</th>
                    <th>رسوم التوصيل</th>
                    <th>إجمالي الرسوم</th>
                    <th>المستحق</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoiceParcels as $index => $parcel)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $parcel->parcel->customer_name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $parcelStatusClass = '';
                            $parcelStatusText = '';
                            switch($parcel->parcel_status) {
                                case 'DELIVERED':
                                    $parcelStatusClass = 'status-paid';
                                    $parcelStatusText = 'تم التسليم';
                                    break;
                                case 'RETURN_TO_COURIER':
                                    $parcelStatusClass = 'status-unpaid';
                                    $parcelStatusText = 'إرجاع للمندوب';
                                    break;
                                case 'RETURN_RECEIVED_BY_MERCHANT':
                                    $parcelStatusClass = 'status-unpaid';
                                    $parcelStatusText = 'إرجاع للتاجر';
                                    break;
                                default:
                                    $parcelStatusClass = 'status-processing';
                                    $parcelStatusText = 'قيد المعالجة';
                            }
                        @endphp
                        <span class="status-badge {{ $parcelStatusClass }}" style="font-size: 10px; padding: 4px 8px;">
                            {{ $parcelStatusText }}
                        </span>
                    </td>
                    <td>{{ $parcel->parcel->created_at ? $parcel->parcel->created_at->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ settings()->currency }}{{ number_format($parcel->collected_amount, 2) }}</td>
                    <td>{{ settings()->currency }}{{ number_format($parcel->total_delivery_amount, 2) }}</td>
                    <td>{{ settings()->currency }}{{ number_format($parcel->total_charge_amount, 2) }}</td>
                    <td>{{ settings()->currency }}{{ number_format($parcel->current_payable, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="total-section">
            <h3 style="margin-bottom: 12px; color: #7e0095; font-size: 14px;">الملخص المالي</h3>
            <div class="total-row">
                <span>إجمالي المبلغ المحصل:</span>
                <span>{{ settings()->currency }}{{ number_format($invoice->cash_collection, 2) }}</span>
            </div>
            <div class="total-row">
                <span>إجمالي الرسوم:</span>
                <span>{{ settings()->currency }}{{ number_format($invoice->total_charge, 2) }}</span>
            </div>
            <div class="total-row final">
                <span><strong>المبلغ المستحق:</strong></span>
                <span><strong>{{ settings()->currency }}{{ number_format($invoice->current_payable, 2) }}</strong></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>شكراً لتعاملك معنا!</p>
            <p>تم الإنشاء في {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <!-- JavaScript for Print Functionality -->
    <script>
        function printInvoice() {
            // Hide the print button before printing
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.style.display = 'none';
            }
            
            // Print the page
            window.print();
            
            // Show the print button again after printing
            setTimeout(() => {
                if (printButton) {
                    printButton.style.display = 'block';
                }
            }, 1000);
        }

        // Auto-print when page loads (optional)
        // Uncomment the line below if you want the print dialog to open automatically
        // window.onload = function() { setTimeout(printInvoice, 500); };

        // Keyboard shortcut for printing (Ctrl+P or Cmd+P)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printInvoice();
            }
        });
    </script>
</body>
</html>

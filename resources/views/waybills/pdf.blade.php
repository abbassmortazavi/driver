<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: "Vazir";
            src: url("{{ storage_path('fonts/Vazir.ttf') }}") format("truetype");
        }
        body {
            font-family: "Vazir", sans-serif;
            direction: rtl;
            text-align: right;
            line-height: 1.6;
        }
        * {
            letter-spacing: 0 !important;
            word-spacing: 0 !important;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .info-table th, .info-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }
        .info-table th {
            background-color: #f5f5f5;
        }
        .waybill-table {
            width: 100%;
            border-collapse: collapse;
        }
        .waybill-table th, .waybill-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .waybill-table th {
            background-color: #f5f5f5;
        }
        .total {
            margin-top: 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 14px;
            border-top: 1px solid #333;
            padding-top: 15px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>بارنامه حمل و نقل</h1>
    <p>شماره سند: {{ $transport->id }}</p>
</div>

<table class="info-table">
    <tr>
        <th>اطلاعات حمل و نقل</th>
        <th>مشخصات</th>
    </tr>
    <tr>
        <td>شناسه حمل و نقل</td>
        <td>{{ $transport->ulid }}</td>
    </tr>
    <tr>
        <td>تاریخ ایجاد</td>
        <td>{{ $transport->created_at->format('Y/m/d') }}</td>
    </tr>
</table>

<h2>لیست بارنامه‌ها</h2>
<table class="waybill-table">
    <thead>
    <tr>
        <th>شماره بارنامه</th>
        <th>راننده</th>
        <th>شناسه حمل</th>
        <th>نام محموله</th>
        <th>مشتری</th>
        <th>مبلغ</th>
        <th>واحد پول</th>
    </tr>
    </thead>
    <tbody>
    @foreach($waybills as $waybill)
        <tr>
            <td>{{ $waybill->waybill_number }}</td>
            <td>{{ $waybill?->driver->full_name }}</td>
            <td>{{ $waybill->transport_id }}</td>
            <td>{{ $waybill?->shipment->name }}</td>
            <td>{{ $waybill?->customer->full_name }}</td>
            <td>{{ number_format($waybill->price_value) }}</td>
            <td>{{ $waybill->price_currency }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="total">
    مجموع بارنامه‌ها: {{ $waybills->count() }} عدد
</div>

<div class="footer">
    <p>تاریخ چاپ: {{ now()->format('Y/m/d H:i') }}</p>
    <p>سیستم مدیریت حمل و نقل</p>
</div>
</body>
</html>

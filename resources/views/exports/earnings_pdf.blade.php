<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* DejaVu Sans is built-in to DomPDF and supports the Peso symbol */
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: right; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-left { text-align: left; }
        .peso { font-family: 'DejaVu Sans'; } /* Force symbol support */
    </style>
</head>
<body>
    <div class="header">
        <h2>Earnings Report ({{ ucfirst($filter) }})</h2>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left">Period</th>
                <th>Total Collected</th>
                @foreach($types as $type)
                    <th>{{ strtoupper($type->name) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td class="text-left">{{ $row['date'] }}</td>
                <td>&#8369;{{ number_format($row['total_amount'], 2) }}</td>
                @foreach($types as $type)
                    <td>&#8369;{{ number_format($row[$type->name] ?? 0, 2) }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

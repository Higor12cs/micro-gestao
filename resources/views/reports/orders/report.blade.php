<!DOCTYPE html>
<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: "JetBrains Mono", monospace;
            font-size: 12px;
        }

        .header {
            /* text-align: center; */
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Relatório de Pedidos</h1>
        <p>Data Emissão: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Período: {{ $startDate }} | {{ $endDate }}</p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ str_pad($order->sequential, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $order->date->format('d/m/Y') }}</td>
                    <td>{{ $order->customer->first_name }}</td>
                    <td>{{ number_format($order->total_price, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum registro encontrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>

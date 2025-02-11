@extends('adminlte::page')

@section('title', 'Dashboard')

@section('plugins.Chartjs', true)

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>Dashboard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <span class="text-muted">Últimos 30 Dias</span>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($total_price, 2, ',', '.') }}</h3>
                    <p>Faturamento</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">Mais Informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($contributionMarginPercentage, 0, ',', '.') }}<sup style="font-size: 20px">%</sup>
                    </h3>
                    <p>Margem</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">Mais Informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $orderCount }}</h3>
                    <p>Quant. Pedidos</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">Mais Informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($averageTicket, 2, ',', '.') }}</h3>
                    <p>Ticket Médio</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">Mais Informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Faturamento</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <canvas id="monthlyChart" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="dailyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var monthlyChart = document.getElementById('monthlyChart').getContext('2d');
        var dailyChart = document.getElementById('dailyChart').getContext('2d');

        var salesThisYearPerMonth = @json($salesThisYearPerMonth);
        var salesLast30DaysPerDay = @json($salesLast30DaysPerDay);

        var months = salesThisYearPerMonth.map(function(item) {
            return item.month;
        });
        var salesThisYearData = salesThisYearPerMonth.map(function(item) {
            return item.total_price;
        });

        var days = salesLast30DaysPerDay.map(function(item) {
            return item.day;
        });
        var salesLast30DaysData = salesLast30DaysPerDay.map(function(item) {
            return item.total_price;
        });

        new Chart(monthlyChart, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Faturamento Mensal (Ano Atual)',
                    data: salesThisYearData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(dailyChart, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Faturamento Diário (Últimos 30 Dias)',
                    data: salesLast30DaysData,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection

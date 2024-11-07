@extends('adminlte::page')

@section('title', 'Home')

@php
    $agora = date('H'); //
    $saudacao = '';
    if ($agora >= 5 && $agora < 12) {
        $saudacao = 'Bom dia';
    } elseif ($agora >= 12 && $agora < 18) {
        $saudacao = 'Boa tarde';
    } else {
        $saudacao = 'Boa noite';
    }

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
    $dataPorExtenso = strftime('%A, %d de %B de %Y');
@endphp

@section('content_header')
    <div class="d-flex justify-content-between">
        <div>
            <h4>{{ $saudacao }}, {{ auth()->user()->name }}!</h4>
            <span class="text-muted">{{ $dataPorExtenso }}</span>
        </div>
        <div class="ml-auto text-right">
            <h2 id="current-time"></h2>
        </div>
    </div>
@stop

@section('content')
    <!-- Section Shortcuts -->
    <div class="row">
        @foreach (config('adminlte.menu') as $item)
            @if (isset($item['header']))
                <div class="col-12">
                    <h4 class="mt-4">{{ $item['header'] }}</h4>
                </div>
            @endif

            @if (isset($item['submenu']))
                @foreach ($item['submenu'] as $submenu)
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="{{ $submenu['icon'] }} fa-2x mr-3"></i>
                                    <h5 class="card-title mb-0">{{ $submenu['text'] }}</h5>
                                </div>
                                <a href="{{ $submenu['route'] ?? $submenu['url'] }}"
                                    class="btn btn-primary mt-auto">Acessar</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif(isset($item['text']) && (isset($item['route']) || isset($item['url'])))
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <i class="{{ $item['icon'] }} fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">{{ $item['text'] }}</h5>
                            </div>
                            <a href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}"
                                class="btn btn-primary mt-auto">Acessar</a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@stop

@section('js')
    <script>
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
@stop

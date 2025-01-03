@php
    use OndrejVrto\LineChart\LineChart;
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $languages = App\Models\Language::get();
        $langDetails = $languages->where('code', config('app.locale'))->first();
    @endphp
    <section class="trade-section">
        <div class="container-fluid container-fluid--custom">
            <div class="trade-section__inner">
                <div class="trade-section__left">
                    <div class="trade-section__block one">
                        <span>@lang('Profit')</span>
                        <span>
                            <span class="profit-value">0.00</span>
                            <span class="profit-percentage">%</span>
                        </span>
                    </div>
                    <div class="trade-section__block two">
                        <span>@lang('Value')</span>
                        <span>@lang('Amount')</span>
                        <div class="time-selection">
                            <div id="time-period" class="time-period-options">
                                <div class="time-period-option" data-value="1m">@lang('1m')</div>
                                <div class="time-period-option" data-value="1d">@lang('24h')</div>
                                <div class="time-period-option" data-value="1w">@lang('1w')</div>
                                <div class="time-period-option" data-value="1M">@lang('1M')</div>
                                <div class="time-period-option" data-value="1Y">@lang('1Y')</div>
                                <div class="time-period-option" data-value="all">@lang('ALL')</div>
                            </div>
                        </div>
                    </div>
                    <div id="chart-container">
                        <div id="countdown"></div>
                        <div id="direction-indicator"></div>
                    </div>
                </div>
                <div class="trade-section__right">
                    <h6 class="title-trending">@lang('List Trending') </h6>
                    <nav class="nav-horizontal">
                        {{-- <button class="nav-horizontal__btn prev"><i class="las la-angle-left"></i></button>
                        <button class="nav-horizontal__btn next"><i class="las la-angle-right"></i></button> --}}
                        <ul class="nav-horizontal-menu" id="show-currency-list">
                        
                            @foreach ($currencies as $currency)
                                <li class="nav-horizontal-menu__item">
                                    <div class="asset-compact-card coinBtn " data-id="{{ $currency->symbol }}"
                                        data-type="{{ $currency->type }}" data-rate="{{ json_encode($currency->rate) }}">
                                        <div class="asset-compact-card__content">
                                            <h6 class="asset-compact-card__title">{{ $currency->symbol }}</h6>
                                            <h6 class="asset-compact-card__title">
                                                @if (@$langDetails->code == 'en')
                                                    USD
                                                @else
                                                    VND
                                                @endif
                                            </h6>
                                        </div>

                                        <div class="asset-compact-card__content">

                                            @if ($currency->type == Status::TRENDINGTYPE_CRYPTO && $currency->symbol != 'USDT')
                                                @php
                                                    $lastRate = null;
                                                    $dates = [];
                                                    $points = [];
                                                    for ($i = 0; $i < count($currency->rate); $i++) {
                                                        $dates[] = $currency->rate[$i][0];
                                                        $points[] = $currency->rate[$i][4];
                                                        $lastRate = $currency->rate[$i][4];
                                                    }

                                                    $svg = LineChart::new($points)
                                                        ->withColorGradient(
                                                            'rgb(48, 231, 237)',
                                                            'rgb(0, 166, 215)',
                                                            'rgb(0, 88, 179)',
                                                            'rgb(0, 27, 135)',
                                                        )
                                                        ->withDimensions(150, 50)
                                                        ->make();
                                                @endphp

                                                {!! $svg !!}
                                            @endif
                                            @if ($currency->type == Status::TRENDINGTYPE_FINANCE)
                                                @php
                                                    $lastRate = null;
                                                    $dates = [];
                                                    $points = [];
                                                    foreach ($currency->rate as $entry) {
                                                        $dates[] = $entry['Date'];
                                                        $points[] = $entry['Price per Ounce'];
                                                        $lastRate = $entry['Price per Ounce'];
                                                    }

                                                    $metal = LineChart::new($points)
                                                        ->withColorGradient(
                                                            'rgb(48, 231, 237)',
                                                            'rgb(0, 166, 215)',
                                                            'rgb(0, 88, 179)',
                                                            'rgb(0, 27, 135)',
                                                        )
                                                        ->withDimensions(150, 50)
                                                        ->make();
                                                @endphp

                                                {!! $metal !!}
                                            @endif
                                            @if ($currency->type == Status::TRENDINGTYPE_COW)
                                                @php
                                                    $lastRate = null;
                                                    $dates = [];
                                                    $points = [];

                                                    foreach ($currency->rate as $entry) {
                                                        $dates[] = $entry['timestamp'];
                                                        $points[] = $entry['rate'];
                                                        $lastRate = $entry['rate'];
                                                    }

                                                    $cow = LineChart::new($points)
                                                        ->withColorGradient(
                                                            'rgb(48, 231, 237)',
                                                            'rgb(0, 166, 215)',
                                                            'rgb(0, 88, 179)',
                                                            'rgb(0, 27, 135)',
                                                        )
                                                        ->withDimensions(150, 50)
                                                        ->make();

                                                @endphp

                                                {!! $cow !!}
                                            @endif

                                        </div>
                                        <div class="asset-compact-card__content">
                                            @php
                                                $lastRate = round($lastRate, 4);
                                                $valueChange = round(
                                                    ((end($points) - $points[count($points) - 2]) /
                                                        $points[count($points) - 2]) *
                                                        100,
                                                    2,
                                                );
                                            @endphp
                                            <h6 class="asset-compact-card__title lastRate">
                                                {{ $lastRate }}</h6>
                                            <h6 class="asset-compact-card__title"
                                                style="color: {{ $valueChange < 0 ? 'red' : 'green' }};">
                                                {{ $valueChange }} %
                                            </h6>

                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                </div>

            </div>
        </div>
    </section>
@endsection


@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/lightweight-chart.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/binary-trade.css') }}">
@endpush

@push('style')
    <style>
        .title-trending {
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            justify-content: center;
        }

        #chart-container {
            width: 100%;
            background-color: transparent;
            position: relative;
            overflow: hidden;
            border-radius: 8px
        }

        #trading-controls {
            background-color: #ec7c1242;
            border-radius: 10px;
            padding: 20px;
            height: 100%;
        }

        .btn-custom {
            background-color: #4caf50;
            color: white;
            width: 100%;
        }

        .form-control,
        .form-select {
            background-color: #1e222d46;
            border: 1px solid #363c4e1f;
            color: #d1d4dc;
            margin-bottom: 15px;
        }

        #countdown {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            z-index: 1000;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        #direction-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 1000;
            font-size: 14px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            pointer-events: none;
            display: none;
        }

        #current-price-dot {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #35c75a;
            pointer-events: none;
            animation: pulse 0.1s infinite;
        }

        #binaryTable tbody,
        tr {
            cursor: pointer;
        }

        .empty-thumb {
            padding: 0 !important;
            min-height: 145px !important;
        }

        .table tbody tr td:nth-last-child(3) {
            color: hsl(var(--white) / 0.7);
        }


        .time-selection {
            display: flex;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .time-selection label {
            font-weight: bold;
            margin-right: 10px;
            flex: 1 1 100%;
        }

        .time-period-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .time-period-option {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            flex: 1 1 auto;
            text-align: center;
        }

        .time-period-option:hover {
            background-color: #f0f0f0;
        }

        .time-period-option.active {
            background-color: #007bff;
            color: white;
        }

       @media (max-width: 1440px){
        .asset-compact-card__content svg{
            width: 50%;
        }
       }

        @media (max-width: 768px) {
            .time-selection {
                flex-direction: column;
                align-items: flex-start;
            }

            .time-selection label {
                margin-bottom: 10px;
            }

            .time-period-options {
                width: 100%;
            }

            .time-period-option {
                flex: 1 1 45%;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .time-period-option {
                flex: 1 1 100%;
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let countdownTimer = $('.timer-value').eq(0);
            $(document).on('click', '.trade-duration-presets__item', function(e) {
                let durationText = $(this).text();
                countdownTimer.text(durationText);
            });
            let trendingActivate = "{{ $defaultActive->symbol }}";
            let trendingType = "{{ $defaultActive->type }}";
            let trendingRates = @json($defaultActive->rate);
            let selectedTimePeriod = '1w';

            let BINANCE_API_URL;
            let BINANCE_WEBSOCKET_URL;
            let chart = null;
            let lineSeries = null;
            let areaSeries = null;
            let lastPrice = 0;
            let investmentPriceLine = null;
            let webSocket = null;
            let chartProperties = null;
            let direction;
            let dataIds = [];
            let isTradeRunning = false;



            function showLoading() {
                $('body').append(`<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div style="color: white; margin-top: 1rem; font-size: 1.1rem;">Loading...</div>
                    </div>
                </div>`);
            }

            function hideLoading() {
                $('#loading-overlay').remove();
            }

            $(document).on('click', '.nav-horizontal-menu__item .coinBtn', function(e) {
                e.stopPropagation();
                let clickedCoin = $(this);
                trendingActivate = clickedCoin.data('id');
                trendingType = clickedCoin.data('type');
                trendingRates = clickedCoin.data('rate');
                showLoading();
                setTimeout(() => {
                    hideLoading();
                }, 700);
                cleanupChart();
                initalizeApi(`${trendingActivate}_usdt`, selectedTimePeriod);
                initializeChart();
            });
            // chartPropertiesFunc(chartWidth,chartHeight)
            $(document).on('click', '.time-period-option', function(e) {
                e.stopPropagation();
                let clickTimePeriod = $(this);
                selectedTimePeriod = clickTimePeriod.data('value');
                showLoading();
                setTimeout(() => {
                    hideLoading();
                }, 700);
                cleanupChart();
                initalizeApi(`${trendingActivate}_usdt`, selectedTimePeriod);
                initializeChart();
            });

            function updateChartDimensions() {
                let chartWidth = Math.ceil($(".trade-section__left").outerWidth());
                let chartHeight = Math.ceil($(".trade-section__left").outerHeight());
                if (chart) {
                    chart.applyOptions({
                        width: chartWidth,
                        height: chartHeight
                    });
                }
                cleanupChart();
                initializeChart();
            }

            $(window).on('resize', function() {
                updateChartDimensions();
            });

            function chartPropertiesFunc(width, height) {
                chartProperties = {
                    width: width,
                    height: height,
                    timeScale: {
                        timeVisible: true,
                        secondsVisible: false,
                        rightOffset: 7,
                        barSpacing: 5,
                        borderColor: '#363C4E',
                        tickMarkFormatter: (time) => {
                            const date = new Date(time * 1000);
                            return date.toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                            });
                        },
                    },
                    leftPriceScale: {
                        borderColor: '#363C4E',
                    },
                    layout: {
                        background: {
                            type: 'solid',
                            color: 'transparent',
                        },
                        textColor: '#D1D4DC',
                    },
                    grid: {
                        vertLines: {
                            color: 'transparent',
                        },
                        horzLines: {
                            color: 'transparent',
                        },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                        vertLine: {
                            width: 1,
                            color: `#{{ gs('base_color') }}`,
                            style: LightweightCharts.LineStyle.Dashed,
                            labelBackgroundColor: `#{{ gs('base_color') }}`,
                        },
                        horzLine: {
                            width: 1,
                            color: `#{{ gs('base_color') }}`,
                            style: LightweightCharts.LineStyle.Dashed,
                            labelBackgroundColor: `#{{ gs('base_color') }}`,
                        },
                    },
                    handleScale: {
                        mouseWheel: true,
                        pinch: true,
                        axisPressedMouseMove: true,
                    },
                    handleScroll: {
                        mouseWheel: true,
                        pressedMouseMove: true,
                        horzTouchDrag: true,
                        vertTouchDrag: true,
                    }
                };
            }

            initalizeApi(`${trendingActivate}_usdt`, selectedTimePeriod);

            function initalizeApi(activeCoin, timePeriod) {

                if (parseInt(trendingType, 5) === 2) {
                    let symbol = activeCoin.replace('_', '');
                    BINANCE_API_URL =
                        `https://api.binance.com/api/v3/klines?symbol=${symbol.toUpperCase()}&interval=${timePeriod}&limit=2000`;
                    BINANCE_WEBSOCKET_URL = `wss://stream.binance.com:9443/ws/${symbol.toLowerCase()}@kline_1s`;
                }

            }

            function cleanupChart() {
                if (webSocket) {
                    webSocket.close();
                    webSocket = null;
                }
                if (chart) {
                    if (lineSeries) {
                        chart.removeSeries(lineSeries);
                        lineSeries = null;
                    }
                    if (areaSeries) {
                        chart.removeSeries(areaSeries);
                        areaSeries = null;
                    }

                    chart.remove();
                    chart = null;

                    const container = document.getElementById('chart-container');
                    container.innerHTML = `
                                            <div id="countdown"></div>
                                            <div id="direction-indicator"></div>
                                        `;
                }
            }

            function initializeChart() {
                let chartWidth = Math.ceil($(".trade-section__left").outerWidth());
                let chartHeight = Math.ceil($(".trade-section__left").outerHeight());

                chartPropertiesFunc(chartWidth, chartHeight);

                chart = LightweightCharts.createChart(
                    document.getElementById('chart-container'),
                    chartProperties
                );

                lineSeries = chart.addLineSeries({
                    color: '#02d428',
                    lineWidth: 2,
                    lastPriceAnimation: LightweightCharts.LastPriceAnimationMode.OnDataUpdate,
                    lastValueVisible: false,
                    priceLineVisible: false,
                });

                areaSeries = chart.addAreaSeries({
                    topColor: 'rgba(33, 150, 243, 0.56)',
                    bottomColor: 'rgba(33, 150, 243, 0.04)',
                    lineColor: `#{{ gs('base_color') }}`,
                    lineWidth: 2,
                });

                const tvLogo = document.querySelector('#tv-attr-logo');
                if (tvLogo) {
                    tvLogo.style.display = 'none';
                }
                loadHistoricalData();
                if (parseInt(trendingType, 5) === 2) {
                    initializeWebSocket();
                }
                // setupDirectionIndicators();
            }

            async function loadHistoricalData() {
                try {
                    let chartData = [];
                    if (parseInt(trendingType, 5) === 2) {
                        const response = await fetch(BINANCE_API_URL);
                        const data = await response.json();
                        chartData = data.map(d => ({
                            time: d[0] / 1000,
                            value: parseFloat(d[4]),
                        }));
                    } else {

                        const dates = [];
                        const points = [];
                        let filteredDates;
                        let filteredPoints;
                        if (parseInt(trendingType, 5) === 3) {
                            for (let i = 0; i < trendingRates.length; i++) {
                                dates.push(new Date(trendingRates[i].Date).getTime() / 1000);
                                points.push(trendingRates[i]['Price per Ounce']);
                            }
                        } else {
                            for (let i = 0; i < trendingRates.length; i++) {
                                dates.push(new Date(trendingRates[i].timestamp).getTime() / 1000);
                                points.push(trendingRates[i].rate);
                            }
                        }
                        switch (selectedTimePeriod) {
                            case '24h':
                                // Filter for the last 24 hours
                                filteredDates = null;
                                filteredPoints = null;
                                break;
                            case '1w':
                                // Filter for the last 7 days
                                filteredDates = dates.slice(-7);
                                filteredPoints = points.slice(-7);
                                break;
                            case '1M':
                                // Filter for the last 1 month (assuming 30 days)
                                filteredDates = dates.slice(-30);
                                filteredPoints = points.slice(-30);
                                break;
                            case '1Y':
                                // Filter for the last 1 year (assuming 365 days)
                                filteredDates = dates.slice(-365);
                                filteredPoints = points.slice(-365);
                                break;
                            case 'all':
                                // Show all data
                                filteredDates = dates;
                                filteredPoints = points;
                                break;
                            default:
                                // Handle default case
                                break;
                        }

                        if (filteredDates && filteredPoints) {
                            chartData = filteredDates.map((t, index) => ({
                                time: t,
                                value: filteredPoints[index]
                            }));
                        }

                    }

                    if (chartData.length != 0) {
                        const uniqueChartData = chartData.filter((v, i, a) => a.findIndex(t => (t.time === v
                                .time)) ===
                            i);

                        lineSeries.setData(uniqueChartData);
                        areaSeries.setData(uniqueChartData);
                        lastPrice = uniqueChartData[uniqueChartData.length - 1].value;
                        chart.timeScale().fitContent();
                    }


                } catch (error) {
                    console.error('Error loading historical data:', error);
                }
            }

            function initializeWebSocket() {
                webSocket = new WebSocket(BINANCE_WEBSOCKET_URL);
                webSocket.onmessage = handleWebSocketMessage;
            }

            function handleWebSocketMessage(event) {
                const message = JSON.parse(event.data);
                const candlestick = message.k;

                lastPrice = parseFloat(candlestick.c);

                const newData = {
                    time: candlestick.t / 1000,
                    value: lastPrice,
                };
                updateChartData(newData);

            }

            function updateChartData(newData) {
                lineSeries.update(newData);
                areaSeries.update(newData);
                if (investmentPriceLine) {
                    updateInvestmentLine();
                }

            }

            function updateInvestmentLine() {

                var color = direction == 'higher' ? 'green' : 'red';
                lineSeries.removePriceLine(investmentPriceLine);
                investmentPriceLine = lineSeries.createPriceLine({
                    price: investmentPriceLine.options().price,
                    color: color,
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    axisLabelVisible: true,
                    title: investmentPriceLine.options().title,
                });

            }


            function convertToSeconds(time) {
                let parts = time.split(":");
                let minutes = parseInt(parts[0], 10);
                let seconds = parseInt(parts[1], 10);
                return (minutes * 60) + seconds;
            }

            $(document).ready(function() {
                initializeChart();
            });


        })(jQuery)
    </script>
@endpush

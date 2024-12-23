@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="trade-section">
        <div class="container-fluid container-fluid--custom">
            <div class="trade-section__inner">
                <div class="trade-section__left">
                    <div class="trade-section__block one">
                        <span >@lang('Profit')</span>
                        <span>
                            <span class="profit-value">0.00</span>
                            <span class="profit-percentage">%</span>
                        </span>
                    </div>
                    <div class="trade-section__block two">
                        <span >@lang('Value')</span>
                        <span >@lang('Amount')</span>
                    </div>
                    <div id="chart-container">
                        <div id="countdown"></div>
                        <div id="direction-indicator"></div>
                    </div>
                </div>
                <div class="trade-section__right">
                    <h2>@lang('Trending')</h2>
                    <nav class="nav-horizontal">
                        <button class="nav-horizontal__btn prev"><i class="las la-angle-left"></i></button>
                        <button class="nav-horizontal__btn next"><i class="las la-angle-right"></i></button>
                        <ul class="nav-horizontal-menu" id="show-currency-list">
                            @foreach ($currencies as $currency)
                                <li class="nav-horizontal-menu__item">
                                    <div class="asset-compact-card coinBtn "
                                        data-id="{{ $currency->id }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        title="% profit">
                                        <div class="avatar">
                                            <img class="avatar-img"
                                                src=""
                                                alt="">
                                            {{-- <img class="avatar-img"
                                                src="{{ getImage(getFilePath('currency') . '/' . @$coinPair->market->currency->image, getFileSize('currency')) }}"
                                                alt=""> --}}
                                        </div>
                                        <div class="asset-compact-card__content">
                                            <h6 class="asset-compact-card__title">{{ $currency->symbol }}
                                            </h6>
                                            <span
                                                class="asset-compact-card__percentage">%</span>
                                        </div>
                                        @if (!$loop->first)
                                            <button class="asset-compact-card__close" type="button"><i
                                                    class="fas fa-times"></i></button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
        
                </div>
                {{-- <div class="trade-section__right">
                    <button type="button" class="btn--close">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="trade-section__block one">
                        <div class="trade-amount">
                            <div class="trade-amount__input-group">
                                <span class="trade-amount__label">@lang('Amount') (<span class="coin-symbol"></span>)</span>
                                <div class="trade-amount__wrapper">
                                    <input class="trade-amount__input" name="amount" value="=" type="text">
                                </div>
                            </div>
                            <div class="trade-amount__btns">
                                <button class="trade-amount__increment" type="button"><i class="fas fa-plus"></i></button>
                                <button class="trade-amount__decrement" type="button"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="trade-duration">
                            <div class="trade-duration__toggle" data-bs-toggle="dropdown">
                                <span class="label">@lang('Duration')</span>
                                <div class="wrapper">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_19_8761)">
                                            <path
                                                  d="M9.99984 18.3333C14.6022 18.3333 18.3332 14.6024 18.3332 9.99999C18.3332 5.39762 14.6022 1.66666 9.99984 1.66666C5.39746 1.66666 1.6665 5.39762 1.6665 9.99999C1.6665 14.6024 5.39746 18.3333 9.99984 18.3333Z"
                                                  stroke="#CBD5E1" stroke-width="2" stroke-linecap="round"
                                                  stroke-linejoin="round" />
                                            <path d="M10 5V10H13.75" stroke="#CBD5E1" stroke-width="2"
                                                  stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_19_8761">
                                                <rect width="20" height="20" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <span class="value timer-value">dsjkhcgjkd</span>
                                </div>
                            </div>
                            <div class="dropdown-menu">
                                <ul class="trade-duration-presets">
                                    @foreach ($durations ?? [] as $duration)
                                        <li class="trade-duration-presets__item"> jhdfc</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="trade-section__block two">
                        <div class="percentage-box">
                            <span class="percentage-box__title">@lang('Profit')</span>
                            <div class="percentage-box__wrapper">
                                <h1 class="percentage-box__total">+%</h1>
                                <h6 class="percentage-box__amount"></h6>
                            </div>
                        </div>
                    </div>
                    <div class="trade-section__block three">
                        <button type="button" class="btn-new btn-new--success w-100" id="higherBtn" data-direction="higher">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.3335 9.33333H22.6668V22.6667" stroke="white" stroke-width="3"
                                      stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9.3335 22.6667L22.6668 9.33333" stroke="white" stroke-width="3"
                                      stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>@lang('Higher')</span>
                        </button>
                        <button type="button" class="btn-new btn-new--danger w-100" id="lowerBtn" data-direction="lower">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.3335 22.6667H22.6668V9.33334" stroke="white" stroke-width="3"
                                      stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9.3335 9.33334L22.6668 22.6667" stroke="white" stroke-width="3"
                                      stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>@lang('Lower')</span>
                        </button>

                    </div> 
                </div> --}}
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

            // chartPropertiesFunc(chartWidth,chartHeight)

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
                        rightOffset: 100,
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

            initalizeApi('btc_usdt');

            function initalizeApi(activeCoin) {
                let symbol = activeCoin.replace('_', '');
                BINANCE_API_URL =
                    `https://api.binance.com/api/v3/klines?symbol=${symbol.toUpperCase()}&interval=1s&limit=2000`;
                BINANCE_WEBSOCKET_URL = `wss://stream.binance.com:9443/ws/${symbol.toLowerCase()}@kline_1s`;
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
                initializeWebSocket();
                setupDirectionIndicators()

            }

            async function loadHistoricalData() {
                try {
                    const response = await fetch(BINANCE_API_URL);
                    const data = await response.json();

                    const chartData = data.map(d => ({
                        time: d[0] / 1000,
                        value: parseFloat(d[4]),
                    }));

                    const uniqueChartData = chartData.filter((v, i, a) => a.findIndex(t => (t.time === v.time)) ===
                        i);

                    lineSeries.setData(uniqueChartData);
                    areaSeries.setData(uniqueChartData);
                    lastPrice = uniqueChartData[uniqueChartData.length - 1].value;
                    chart.timeScale().fitContent();

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

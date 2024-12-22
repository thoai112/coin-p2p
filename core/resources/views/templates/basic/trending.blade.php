@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="trade-section">
        <div class="container-fluid container-fluid--custom">
            <div class="trade-section__inner">
                <div class="trade-section__left">
                    <h1 class="trade-amount">@lang('Trending Chart')</h1>
                    <div id="chart-container">
                        <div id="countdown"></div>
                        <div id="direction-indicator"></div>
                        <div id="current-price-dot"></div>
                    </div>
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

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/lightweight-chart.js') }}"></script>
@endpush

@push('style')
    <style>
        #chart-container {
            width: 100%;
            background-color: hsl(var(--footer-bg));
            position: relative;
            overflow: hidden;
            border-radius: 8px
        }

        #trading-controls {
            background-color: #2a2e39;
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
            background-color: #1e222d;
            border: 1px solid #363c4e;
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
            let chartWidth = Math.ceil($(".trade-section__left").width());
            let chartHeight = Math.ceil($(".trade-section__left").height());
            let chartProperties = null;
            let direction;
            let dataIds = [];
            let isTradeRunning = false;
            
           


            chartPropertiesFunc(chartHeight)

            function chartPropertiesFunc(height) {
                chartProperties = {
                    width: chartWidth,
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
                    rightPriceScale: {
                        borderColor: '#363C4E',
                    },
                    layout: {
                        background: {
                            type: 'solid',
                            color: '#131722'
                        },
                        textColor: '#D1D4DC',
                    },
                    grid: {
                        vertLines: {
                            color: '#1E222D'
                        },
                        horzLines: {
                            color: '#1E222D'
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

            initalizeApi('usdt_btc');

            function initalizeApi(activeCoin) {
                let symbol = activeCoin.replace('_', '');
                BINANCE_API_URL = `https://api.binance.com/api/v3/klines?symbol=${symbol.toUpperCase()}&interval=1m&limit=4000`;
                BINANCE_WEBSOCKET_URL = `wss://stream.binance.com:9443/ws/${symbol.toLowerCase()}@kline_1m`;
            }

            // function cleanupChart() {
            //     if (webSocket) {
            //         webSocket.close();
            //         webSocket = null;
            //     }
            //     if (chart) {
            //         if (lineSeries) {
            //             chart.removeSeries(lineSeries);
            //             lineSeries = null;
            //         }
            //         if (areaSeries) {
            //             chart.removeSeries(areaSeries);
            //             areaSeries = null;
            //         }

            //         chart.remove();
            //         chart = null;

            //         const container = document.getElementById('chart-container');
            //         container.innerHTML = `
            //                                 <div id="countdown"></div>
            //                                 <div id="direction-indicator"></div>
            //                                 <div id="current-price-dot"></div>
            //                             `;
            //     }
            // }

            
            function initializeChart() {

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


            loadHistoricalData();
            
            async function loadHistoricalData() {
                try {
                    const response = await fetch(BINANCE_API_URL);
                    const data = await response.json();
                    const chartData = data.map(d => ({
                        time: d[0] / 1000,
                        value: parseFloat(d[4])
                    }));

                    const uniqueChartData = chartData.filter((v, i, a) => a.findIndex(t => (t.time === v.time)) === i);

                    lineSeries.setData(uniqueChartData);
                    areaSeries.setData(uniqueChartData);
                    lastPrice = uniqueChartData[uniqueChartData.length - 1].value;
                    chart.timeScale().fitContent();

                } catch (error) {
                    console.error('Error loading historical data:', error);
                }
            }

        })(jQuery)
    </script>
@endpush


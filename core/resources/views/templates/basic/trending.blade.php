{{-- @extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <h1 class="text-center">@lang('Trending')</h1>
        </div>
    </div>
</section>
@endsection --}}


@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="trade-section">
        <div class="container-fluid container-fluid--custom">
            <div class="trade-section__inner">
                <div class="trade-section__left">
                    <div id="chart-container">
                        <div id="countdown"></div>
                        <div id="direction-indicator"></div>
                        <div id="current-price-dot"></div>
                    </div>
                </div>
                <div class="trade-section__right">
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
                </div>
            </div>
        </div>
    </section>

    
@endsection


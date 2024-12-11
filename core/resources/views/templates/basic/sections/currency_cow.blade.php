@php
    $meta = (object) $meta;
    $content = getContent('currency_cow.content', true);
    $elements = getContent('currency_cow.element', orderById: true);
@endphp
<div class="col-lg-7 table-wrapper">
    {{-- <div class="table-wrapper"> --}}
        <div class=" table-wrapper__item">
            <div class="table-header-menu">
                <button type="button" class="table-header-menu__link market-type active" data-type="fiat">
                    <i class="las la-border-all"></i> @lang('All')
                </button>
            </div>
            <div class="market-list__left">
                {{-- @if (@$meta->from_section)
                    <a href="{{ route('market') }}" class="btn btn--sm btn--base outline">
                        <i class="las la-coins"></i> @lang('All Pair')
                    </a>
                @else
                    <form class="market-list-search">
                        <input type="search" name="market_list_serach" class="market-list-search-field form--control"
                            placeholder="@lang('Search here ')...">
                        <i class="las la-search"></i>
                    </form>
                @endif --}}
                <form class="market-list-search">
                    <input type="search" name="market_list_serach" class="market-list-search-field form--control"
                        placeholder="@lang('Search here ')...">
                    <i class="las la-search"></i>
                </form>
            </div>
        </div>
        <table class="table coin-pair-list-table coin-pair-list">
            <thead>
                <tr>
                    <th>@lang('CODE')</th>
                    <th>@lang('BASIC UNIT')</th>
                    <th>@lang('VND')</th>
                    {{-- <th>@lang('24h Change')</th>
                <th class="text-start">@lang('Marketcap')</th> --}}
                </tr>
            </thead>
            <tbody id="market-list-body"></tbody>
        </table>
        @if (!@$meta->from_section)
            <div class="text-center mt-5">
                <button type="button" class="btn btn--base outline btn--sm load-more-market-list d-none">
                    <i class="fa fa-spinner"></i> @lang('Load More')
                </button>
            </div>
        @endif
    {{-- </div> --}}
</div>

<div class="col-lg-5 currency-item-cow">
   
        <div class="section-heading">
            <h4 class="section-heading__title"> {{ __(@$content->data_values->cow_heading) }} </h4>
            <p class="coincheck-item__desc"> {{ __(@$content->data_values->cow_subheading) }}</p>

            {{-- @foreach ($elements as $element)
                <p class="coincheck-item__desc"> {{ __(@$element->data_values->subheading) }}</p>
            @endforeach --}}


    </div>
</div>


@push('script')
    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                } else {
                    entry.target.classList.remove('visible');
                }
            });
        }, {
            threshold: 1.0,
        });

        observer.observe(document.querySelector('.currency-item-cow'));
    </script>


    <script>
        "use strict";
        (function($) {

            @if (!app()->offsetExists('lisiten_market_data_event'))
                pusherConnection('market-data', marketChangeHtml);
                @php app()->offsetSet('lisiten_market_data_event',true) @endphp
            @endif

            let type = "all";
            let loadMore = false;
            let skip = 0;
            let limit = "{{ $meta->limit ?? 15 }}";
            let search = "";

            $('.market-type').on('click', function(e) {
                $('.market-type').removeClass('active');
                $(this).addClass('active');
                type = $(this).data('type');
                resetVariable()
                getPairList();
            });

            $('.load-more-market-list').on('click', function(e) {
                loadMore = true;
                getPairList();
            });

            $('.market-list-search').on('submit', function(e) {
                e.preventDefault();
                search = $(this).find('.market-list-search-field').val()
                resetVariable();
                getPairList();
            });

            function getPairList() {
                let action = "{{ route('cow.list') }}";
                $.ajax({
                    url: action,
                    type: "GET",
                    dataType: 'json',
                    cache: false,
                    data: {
                        type,
                        skip,
                        limit,
                        search
                    },
                    beforeSend:function(){
                        if(loadMore){
                            $('.load-more-market-list').html(`<i class="fa fa-spinner fa-spin"></i>`)
                        }
                    },
                    complete:function(){
                        if(loadMore){
                            $('.load-more-market-list').html(`<i class="fa fa-spinner"></i> @lang('Load More')`)
                        }else{
                            removeSkeleton();
                        }
                    },
                    success: function(resp) {

                        if (!resp.success) {
                            notify('error', resp.message);
                            return false;
                        }
                        let html = '';
                        if (resp.currencies.length <= 0) {
                            html += `<tr class="text-center">
                                <td colspan="100%">
                                    <div class="empty-thumb">
                                        <img src="{{ asset('assets/images/extra_images/empty.png') }}"/>
                                        <p class="empty-sell">${loadMore ? 'No more value found' : 'No value found'}</p>
                                    </div>
                                </td>
                            </tr>`;
                            $('.load-more-market-list').addClass('d-none');
                            loadMore ? $('#market-list-body').append(html) : $('#market-list-body').html(html);
                            return;
                        }
                        // let tradeUlr = "{{ route('trade', ':symbol') }}";
                        $.each(resp.currencies || [], function(i, currency) {
                            html += `
                            <tr class="${!loadMore ? 'skeleton' : ''}">
                                <td>
                                    <div class="customer d-flex align-items-center">
                                        <div class="customer__content">
                                            <h6 class="customer__name">${currency.symbol}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                     <div class="customer d-flex align-items-center">
                                        <div class="customer__content">
                                            <h6 class="customer__name">${currency.basicunit}  ${currency.minorSingle}</h6>
                                        </div>
                                    </div>
                                   
                                </td>
                                <td>
                                    <div class="customer d-flex align-items-center">
                                        <div class="customer__content">
                                            <h6 class="customer__name">${currency.rate}</h6>
                                        </div>
                                    </div>
                                   
                                </td>
                                
                            </tr>
                            `
                        });



                        $('.load-more-market-list').removeClass('d-none');
                        loadMore ? $('#market-list-body').append(html) : $('#market-list-body').html(html);
                        if (skip == 0) {
                            tableDataLabel();
                        }
                        skip += parseInt(limit);
                        if (parseInt(skip) >= parseInt(resp.total)) {
                            $('.load-more-market-list').addClass('d-none')
                        } else {
                            $('.load-more-market-list').removeClass('d-none')
                        }
                    }
                });
            }
            getPairList();

            function resetVariable() {
                loadMore = false;
                skip = 0;
                limit = "{{ $meta->limit ?? 20 }}";
            }

            function removeSkeleton() {
                setTimeout(() => {
                    $('.coin-pair-list tr').removeClass('skeleton');
                }, 1000);
            }
            removeSkeleton();

        })(jQuery);
    </script>
@endpush


@if (!app()->offsetExists('pusher_script'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>
        <script src="{{ asset('assets/global/js/broadcasting.js') }}"></script>
    @endpush
    @php app()->offsetSet('pusher_script',true) @endphp
@endif

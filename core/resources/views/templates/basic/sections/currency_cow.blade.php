@php
    $meta = (object) $meta;
    $content = getContent('currency_cow.content', true);
    $elements = getContent('currency_cow.element', orderById: true);
@endphp
<div class="col-lg-6 table-wrapper">
    {{-- <div class="table-wrapper"> --}}
    <div class=" table-wrapper__item">
        <div class="table-header-menu">
            <button type="datetime-local" class="table-header-menu__link market-type active date-range" data-type="fiat"
                id="showDateRangePicker">
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
    <div class="table-container">
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
        </table>
        <div class="table-body-container">
            <table class="table coin-pair-list-table coin-pair-list">
                <tbody id="market-list-body"></tbody>
            </table>
        </div>
    </div>
    @if (!@$meta->from_section)
        <div class="text-center mt-5">
            <button type="button" class="btn btn--base outline btn--sm load-more-market-list d-none">
                <i class="fa fa-spinner"></i> @lang('Load More')
            </button>
        </div>
    @endif
    {{-- </div> --}}
</div>

<div class="col-lg-6 currency-item-cow">

    <div class="section-heading">
        <h4 class="section-heading__title"> {{ __(@$content->data_values->cow_heading) }} </h4>
        <p class="coincheck-item__desc"> {{ __(@$content->data_values->cow_subheading) }}</p>

        {{-- @foreach ($elements as $element)
                <p class="coincheck-item__desc"> {{ __(@$element->data_values->subheading) }}</p>
            @endforeach --}}


    </div>
</div>


@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush
@push('script')
    <script>
        (function($) {
            "use strict"

            // const datePicker = $('.date-range').daterangepicker({
            //     autoUpdateInput: false,
            //     locale: {
            //         cancelLabel: 'Clear'
            //     },
            //     showDropdowns: true,
            //     ranges: {
            //         'Today': [moment(), moment()],
            //         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            //         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            //         'Last 15 Days': [moment().subtract(14, 'days'), moment()],
            //         'Last 30 Days': [moment().subtract(30, 'days'), moment()],
            //         'This Month': [moment().startOf('month'), moment().endOf('month')],
            //         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
            //             .endOf('month')
            //         ],
            //         'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
            //         'This Year': [moment().startOf('year'), moment().endOf('year')],
            //     },
            //     maxDate: moment()
            // });


            // const changeDatePickerText = (event, startDate) => {
            //     $(event.target).val(startDate.format('MMMM DD, YYYY'));
            // }

            // $('.date-range').on('apply.datepicker', (event, picker) => changeDatePickerText(event, picker
            //     .startDate));

            // if ($('.date-range').val()) {
            //     let dateRange = $('.date-range').val().split(' - ');
            //     $('.date-range').data('datepicker').setStartDate(new Date(dateRange[0]));
            //     // $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            // }

            const datePicker = $('.date-range').daterangepicker({
                singleDatePicker: true, // Enable single date selection
                autoUpdateInput: false, // Prevent automatic value update
                showDropdowns: true, // Allow year/month dropdowns
                locale: {
                    format: 'MMMM DD, YYYY', // Date format
                    cancelLabel: 'Clear' // Label for the clear button
                },
                maxDate: moment() // Set the maximum date to today
            });

            // Update input field with the selected date
            $('.date-range').on('apply.daterangepicker', function(event, picker) {
                // $(this).val(picker.startDate.format('MMMM DD, YYYY')); // Format date and update input
                const selectedDate = picker.startDate.format('MMMM DD, YYYY');
                $(this).val(selectedDate); // Format date and update input
                $('#showDateRangePicker').val(selectedDate); // Set button text to default label
                console.log("Selected Date:", selectedDate); // Log selected date to console
            });

            // Clear input field on cancel
            $('.date-range').on('cancel.daterangepicker', function() {
                $(this).val(''); // Clear the input field
            });

        })(jQuery);
    </script>
    <style>
        .datepicker {
            z-index: 9999 !important;
        }

        .date-range {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
@endpush



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
            let date = '2025-01-01';

            $('.market-type').on('click', function(e) {
                $('.market-type').removeClass('active');
                $(this).addClass('active');
                $('.date-range').click();
                // type = $(this).data('type');
                // resetVariable()
                // getPairList();
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
                        date,
                        type,
                        skip,
                        limit,
                        search
                    },
                    beforeSend: function() {
                        if (loadMore) {
                            $('.load-more-market-list').html(`<i class="fa fa-spinner fa-spin"></i>`)
                        }
                    },
                    complete: function() {
                        if (loadMore) {
                            $('.load-more-market-list').html(
                                `<i class="fa fa-spinner"></i> @lang('Load More')`)
                        } else {
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
                            loadMore ? $('#market-list-body').append(html) : $('#market-list-body').html(
                                html);
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
                                            <h6 class="customer__name">${showAmount(currency.rate,2)}</h6>
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

@php
    $meta = (object) $meta;
    $content = getContent('currency_cow.content', true);
    $elements = getContent('currency_cow.element', orderById: true);
@endphp
<div class="col-lg-6 table-wrapper">
    {{-- <div class="table-wrapper"> --}}
    <div class="table-wrapper__item">
        <div class="table-header-menu">
            <button type="datetime-local" class="table-header-menu__link market-type active date-range" data-type="fiat"
                id="showDateRangePicker">
                <i class="las la-border-all"></i> @lang('${now()}')
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
    <div class="cow-value">
        <span id ="cow-value"></span>
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
        <p>
            (
            <span>
                V<sub>1</sub>
            </span>
            +
            <span>
                V<sub>2</sub>
            </span>
            + ... +
            <span>
                V<sub>158</sub>
            </span>
            ) / 158
        </p>
        <span className="cow-row">
            <span id="average-price"></span>
            <span className="cow"> = 1 Cow</span>
            <i class="las la-retweet"></i>
            <span id="selectedCurrency">None</span>
            <select id="currency" name="currency">
                <option value="">Select</option>
            </select>
        </span>

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

            // Get current date in YY-MM-DD format
            const nowDate = moment().format('YYYY-MM-DD'); // Format the date using Moment.js

            let type = "all";
            let loadMore = false;
            let skip = 0;
            let limit = "{{ $meta->limit ?? 15 }}";
            let search = "";
            let date = nowDate;

            // Initialize the single date picker
            const datePicker = $('#showDateRangePicker').daterangepicker({
                singleDatePicker: true, // Enable single date selection
                autoUpdateInput: false, // Prevent automatic value update
                showDropdowns: true, // Allow year/month dropdowns
                locale: {
                    format: 'YYYY-MM-DD', // Date format for selection
                    cancelLabel: 'Clear', // Label for the clear button
                },
                maxDate: moment() // Set the maximum date to today
            });

            // Set default label to the button with the current date
            $('#showDateRangePicker').html(`<i class="las la-border-all"></i> ${nowDate}`);

            // Update button content on date selection
            $('#showDateRangePicker').on('apply.daterangepicker', function(event, picker) {
                const selectedDate = picker.startDate.format('YYYY-MM-DD');
                date = selectedDate;
                $(this).html(`<i class="las la-border-all"></i> ${selectedDate}`); // Update button content
                getPairList();
            });

            // Reset button content to default with current date on cancel
            $('#showDateRangePicker').on('cancel.daterangepicker', function() {
                $(this).html(
                    `<i class="las la-border-all"></i> ${nowDate}`);
                date = nowDate;
                getPairList();
            });

            //get pairlist
            @if (!app()->offsetExists('lisiten_market_data_event'))
                pusherConnection('market-data', marketChangeHtml);
                @php app()->offsetSet('lisiten_market_data_event',true) @endphp
            @endif



            $('.market-type').on('click', function(e) {
                $('.market-type').removeClass('active');
                $(this).addClass('active');
                $('.date-range').click();
            });

            $('.load-more-market-list').on('click', function(e) {
                loadMore = true;
                getPairList();
            });

            // $('.market-list-search').on('submit', function(e) {
            //     e.preventDefault();
            //     search = $(this).find('.market-list-search-field').val()
            //     resetVariable();
            //     getPairList();
            // });

            $('.market-list-search').on('submit', function(e) {
                e.preventDefault();

                // Get the search value
                let search = $(this).find('.market-list-search-field').val().trim();

                if (search === '') {
                    getPairList();
                }
                // Reset variables and table before performing the search
                resetVariable();
                // getPairList();

                // Highlight rows in the table
                highlightTableRows(search);
            });

            // function highlightTableRows(search) {
            //     if (!search) return; // Exit if the search value is empty

            //     // Iterate over table rows and add highlighting
            //     $('#market-list-body tr').each(function() {
            //         let rowText = $(this).text().toLowerCase();
            //         if (rowText.includes(search.toLowerCase())) {
            //             $(this).addClass('highlight'); // Add a highlight class
            //         } else {
            //             $(this).removeClass('highlight'); // Remove the highlight class if not matching
            //         }
            //     });
            // }

            function highlightTableRows(search) {
                if (!search) return; // Exit if the search value is empty

                // Clear any existing highlights first
                $('#market-list-body tr').removeClass('highlight'); // Target the correct rows

                let foundMatch = false; // Flag to track if any match is found

                // Iterate over table rows and add highlighting
                $('#market-list-body tr').each(function() {
                    let rowText = $(this).text().toLowerCase();
                    if (rowText.includes(search.toLowerCase())) {
                        $(this).addClass('highlight'); // Add a highlight class
                        if (!foundMatch) {
                            // Scroll the table container to the first matching row
                            $('.table-body-container').animate({
                                scrollTop: $(this).offset().top - $('.table-body-container').offset()
                                    .top +
                                    $('.table-body-container').scrollTop() - 50
                            }, 300); // Smooth scroll to the row (adjust the -50 as needed)
                            foundMatch = true; // Ensure we only scroll to the first match
                        }
                    }
                });

                if (!foundMatch) {
                    // If no match is found, you can show a message or handle it as needed
                    console.log("No matching rows found");
                }
            }


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

                        $('#cow-value').html(
                            `<span id="cow-value"> 1 COW = ${resp.cow.toFixed(5)} {{ session('lang', 'en') === 'vn' ? 'VND' : 'USD' }}</span>`);

                        $('#average-price').html(
                            `<span className="average-price">${resp.cow.toFixed(3)} {{ session('lang', 'en') === 'vn' ? 'VND' : 'USD' }}</span>`);

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

                        $.each(resp.currencies || [], function(i, currency) {
                            $('#currency').append(
                                $('<option>', {
                                    value: showAmount(currency.rate,2),
                                    text: `${currency.symbol}`
                                })
                            );
                        });
                        $('#currency').on('change', function () {
                            const cow = $(this).val();
                            $('#selectedCurrency').text(cow || 'None');
                        });
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
    <style>
        .cow-value {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .datepicker {
            z-index: 9999 !important;
        }

        .date-range {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .highlight {
            background-color: #71e2f16e;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: rgba(13, 41, 73, 0.418); 
            border: 1px solid #65e8ff;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            outline: none;
            width: 30%;
            max-width: 100px;
            cursor: pointer;
            transition: all 0.3s ease;
            scrollbar-width: none;
        }
        select::-webkit-scrollbar {
            display: none; 
        }

        select:hover {
            background-color: rgba(13, 41, 73, 0.418);
            border-color: #999;
        }

        select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(78, 149, 224, 0.5);
        }

        select option {
            background-color: #102239f5;
            max-width: 50px; 
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        #selectedCurrency {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
@endpush


@if (!app()->offsetExists('pusher_script'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>
        <script src="{{ asset('assets/global/js/broadcasting.js') }}"></script>
    @endpush
    @php app()->offsetSet('pusher_script',true) @endphp
@endif

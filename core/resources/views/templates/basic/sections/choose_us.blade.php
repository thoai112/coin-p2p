@php
    $content = getContent('choose_us.content', true);
    $elements = getContent('choose_us.element', orderById: true);
@endphp
<div class="coincheck-section py-120">
    <div class="coincheck-section__shape">
        <img src="{{ getImage('assets/images/frontend/choose_us/' . @$content->data_values->shape_image, '920x480') }}">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="section-heading">
                    <h2 class="section-heading__title">{{ __(@$content->data_values->heading) }} </h2>

                    @foreach ($elements as $element)
                        <p class="coincheck-item__desc"> {{ __(@$element->data_values->subheading) }}</p>
                    @endforeach

                </div>
            </div>

            <div class="col-lg-5">

                <div class="banner-right__rotate">
                    <img src="{{ getImage('assets/images/frontend/choose_us/' . @$content->data_values->rotate_image, '400x400') }}">
                </div>
                
            </div>
        </div>
        {{-- <div class="row justify-content-center gy-4">
            @foreach ($elements as $element)
            <div class="col-lg-3 col-md-6 col-sm-6 col-xsm-6">
                <div class="coincheck-item">
                    <div class="coincheck-item__icon">
                       @php  echo @$element->data_values->icon; @endphp
                    </div>
                    <h4 class="coincheck-item__title"> {{ __(@$element->data_values->heading) }}</h4>
                    <p class="coincheck-item__desc"> {{ __(@$element->data_values->subheading) }}</p>
                </div>
            </div>
            @endforeach
        </div> --}}
    </div>
</div>

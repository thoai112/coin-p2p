@php
    $content = getContent('currency_cow.content', true);
@endphp
<div class = "py-120 table-section ">
    <div class="table-section__shape light-mood">
        <img src="{{ asset($activeTemplateTrue.'images/shapes/table-1.png') }}">
    </div>
    <div class="table-section__shape dark-mood style">
        <img src="{{ asset($activeTemplateTrue.'images/shapes/table-12.png') }}">
    </div>
    <div class="container">
        <h2>{{ __(@$content->data_values->table_heading) }}</h2>
        <div class="row">
            <x-flexible-view :view="$activeTemplate.'sections.currency_cow'" :meta="['from_section' => true ]" />
        </div>
    </div>
</div>

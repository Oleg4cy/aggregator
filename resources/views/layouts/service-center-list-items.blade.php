@foreach ($serviceCenters as $serviceCenter)
    <li>
        @include('layouts.service-center-card', ['serviceCenter' => $serviceCenter])
    </li>
@endforeach

{{-- <button class="btn service-center-list__more" data-state="close">
    <span>Показать еще</span>
</button> --}}

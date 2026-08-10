<table class="hours">
    <thead>
        <tr>
            @foreach($workingHours as $day)
                @if ($day->day_of_week > 5)
                <th class="hours__weekend">
                    {{ \App\Services\DayService::getDayByNum($day->day_of_week) }}
                </th>
                @else
                <th>
                    {{ \App\Services\DayService::getDayByNum($day->day_of_week) }}
                </th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            @foreach($workingHours as $day)
            <td>
                {{-- <div class="hours__dot hours__dot--active">&nbsp;</div> --}}
                @if($day->is_open)
                <div class="hours__dot">&nbsp;</div>
                @endif
            </td>
            @endforeach
        </tr>
        <tr>
            @foreach($workingHours as $day)
            <td>{{ $day->is_open ? ($day->open_time ? \Carbon\Carbon::parse($day->open_time)->format('H:i') : '00:00'): 'Выходной' }}</td>
            @endforeach
        </tr>

        <tr>
            @foreach($workingHours as $day)
            <td>
                @if($day->is_open)
                <div class="hours__dot">&nbsp;</div>
                @endif
            </td>
            @endforeach
        </tr>

        <tr>
            @foreach($workingHours as $day)
            <td>{{ $day->is_open ? ($day->close_time ? \Carbon\Carbon::parse($day->close_time)->format('H:i') : '23:59') : '' }}</td>
            @endforeach
        </tr>

        <tr>
            @foreach($workingHours as $day)
            <td>
                @if($day->is_open)
                <div class="hours__dot">&nbsp;</div>
                @endif
            </td>
            @endforeach
        </tr>
    </tbody>
</table>

<table class="hours hours--mobile">
    <tbody>
        @foreach($workingHours as $day)
            @if ($day->day_of_week > 5)
            <tr class="hours__weekend">
                <th>
                    {{ \App\Services\DayService::getDayByNum($day->day_of_week) }}
                </th>
            @else
            <tr>
                <th>
                    {{ \App\Services\DayService::getDayByNum($day->day_of_week) }}
                </th>
            @endif
                <td>
                    {{ $day->is_open ? ($day->open_time ? \Carbon\Carbon::parse($day->open_time)->format('H:i') : '00:00'): 'Выходной' }}

                    {{ $day->is_open ? ($day->close_time ? ' - ' . \Carbon\Carbon::parse($day->close_time)->format('H:i') : ' - 23:59') : '' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

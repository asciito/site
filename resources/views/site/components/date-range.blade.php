@props([
    'from',
    'to' => null,
    'relative' => false,
    'emptyStateTo' => null,
])

@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\HtmlString;
    use Illuminate\Support\Pluralizer;
    use Illuminate\View\ViewException;

    /**
     * Both dates will reset it's time to 00:00:00.000000
     *
     * @var Carbon $from The starting of the date range
     * @var ?Carbon $to The end of the date range if available
     */
    $from = $from->setTime(0, 0);
    $to = $to?->setTime(0, 0);

    throw_if($to?->lessThan($from), ViewException::class, 'The `to` date must be greater than or equal to `from` date');

    if (filled($to) && $relative) {
        $yearsFloat = $from->diffInYears($to); // float
        $years = (int) $yearsFloat;
        $labelYears = $yearsFloat > $years ? $years + 1 : $years;

        $relativeDate = ($yearsFloat > $years ? "Less than " : "") . "$labelYears " . Pluralizer::plural('year', $labelYears);
    }
@endphp

@if ($to && $relative)
    <time datetime="{{$from->format('Y-m-d')}}/{{ $to->format('Y-m-d') }}">{{ $relativeDate }}</time>
@else
    <time datetime="{{ $from->format('Y-m-d') }}">{{ $from->format('M d, Y') }}</time>
    -
    @if ($to)
        <time datetime="{{ $to->format('Y-m-d') }}">{{ $to->format('M d, Y') }}</time>
    @else
        @if ($emptyStateTo instanceof HtmlString)
            {{ $emptyStateTo }}
        @elseif(filled($emptyStateTo) && is_string($emptyStateTo))
            <span>{{ $emptyStateTo }}</span>
        @else
            <span>Not available</span>
        @endif
    @endif
@endif

@props([
    'from',
    'to' => null,
    'relative' => false
])

@php
    use Illuminate\Support\Carbon;use Illuminate\Support\Pluralizer;

    /**
     * @var Carbon $from The starting of the date range
     * @var ?Carbon $to The end of the date range if available
     */

    if ($to && $relative) {
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
        <time datetime="{{ $to->format('Y-m-d') }}">{{ $from->format('M d, Y') }}</time>
    @else
        <span>Not available</span>
    @endif
@endif

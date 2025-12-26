@props([
    'from',
    'to' => null,
    'relative' => false,
    'emptyStateTo' => null,
])

@php
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ViewException;use function App\Helpers\relativeRangeDate;

    throw_if($to?->lessThan($from), ViewException::class, 'The `to` date must be greater than or equal to `from` date');
@endphp

@if ($to && $relative)
    <time datetime="{{$from->format('Y-m-d')}}/{{ $to->format('Y-m-d') }}">{{ relativeRangeDate($from, $to) }}</time>
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

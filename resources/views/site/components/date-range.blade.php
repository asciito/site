@props([
    'from',
    'to' => null,
    'relative' => false,
    'showDates' => true,
    'emptyStateTo' => null,
])

@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ViewException;
    use function App\Helpers\relativeRangeDate;

    /**
    * @var Carbon $from
    * @var Carbon|null $to
    * @var bool $relative
    * @var bool $showDates
    * @var string|HtmlString|null $emptyStateTo
    */

    throw_if($to?->lessThan($from), ViewException::class, 'The `to` date must be greater than or equal to `from` date');
@endphp

@if ($to && $relative)
    <time datetime="{{$from->format('Y-m-d')}}/{{ $to->format('Y-m-d') }}">
        @if ($showDates) {{ $from->format('M Y') }} - {{ $to->format('M Y') }} <span>&#8231;<span> @endif  {{ relativeRangeDate($from, $to) }}
    </time>
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

@php
    /** @var \App\Models\Post $record */
    $record = $this->getRecord();
@endphp

<div>
    <div class="fi-prose">
        <h1>{{ $record->title }}</h1>

        <hr>

        @if ($toc = $record->getTableOfContent(withLinks: false))
            <h2>Table of Content</h2>

            {!! $toc !!}
        @endif

        <hr>

        {{ $record->getContent(withTorchlight: false) }}
    </div>
</div>

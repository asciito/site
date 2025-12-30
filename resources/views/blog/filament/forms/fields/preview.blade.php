@php
    /** @var \App\Models\Post $record */
    $record = $this->getRecord();
@endphp

<div>
    <div class="fi-prose">
        <h1>{{ $record->title }}</h1>

        <hr>

        {{ $record->getTableOfContent(withLinks: false) }}

        <hr>

        {{ $record->getContent(withTorchlight: false) }}
    </div>
</div>

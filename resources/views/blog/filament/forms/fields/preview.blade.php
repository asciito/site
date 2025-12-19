@php
    $record = $this->getRecord();
@endphp

<div>
    <div class="fi-prose">
        <h1>{{ $record->title }}</h1>

        {{ $record->getContent(withTorchlight: true) }}
    </div>
</div>

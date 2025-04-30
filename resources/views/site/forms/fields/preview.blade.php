@php
    $record = $this->getRecord();
@endphp

<div>
    <div class="prose">
        <h1>{{ $record->title }}</h1>

        {{ $record->getContent(withTorchlight: false) }}
    </div>
</div>

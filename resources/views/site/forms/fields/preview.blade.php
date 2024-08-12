@php
    $record = $this->getRecord();
@endphp

<div class="prose">
    {{ $record->getContent(withTorchlight: false) }}
</div>

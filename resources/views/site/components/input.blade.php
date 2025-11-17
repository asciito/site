<div @class(['grid grid-cols-[auto_1fr] has-focus:ring-2 has-focus:ring-slate-600 items-center bg-harlequin selection:bg-white p-1'])>
    <div class="col-span-1 text-2xl p-0 px-2">
        >
    </div>

    <div class="col-span-1">
        <input
            id="{{ $id ?? $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder ?? \Illuminate\Support\Str::of($name)->snake(' ')->title() }}"
            {{ $attributes->class([
                'w-full text-2xl',
                'bg-transparent focus:outline-none',
                'placeholder:text-slate-600 uppercase focus:placeholder:text-slate-600/50',
                'read-only:opacity-50',
            ]) }}
        >
    </div>
</div>

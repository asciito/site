<div @class(['grid grid-cols-[auto_1fr] has-[:focus]:ring-2 has-[:focus]:ring-slate-600 items-center bg-harlequin p-1'])>
    <div class="col-span-1 text-2xl p-0 px-2">
        >
    </div>

    <div class="col-span-1">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            {{ $attributes->class([
                'w-full text-2xl',
                'bg-transparent focus:outline-none',
                'placeholder:text-slate-600 uppercase'
            ]) }}
        >
    </div>
</div>

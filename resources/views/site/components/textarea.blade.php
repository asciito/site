<div @class(['grid grid-cols-[auto_1fr] has-[:focus]:ring-2 has-[:focus]:ring-slate-600 items-start bg-harlequin p-1'])>
    <div class="col-span-1 text-2xl p-0 px-2">
        >
    </div>

    <div class="col-span-1">
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes->class([
                'w-full bg-transparent p-0 text-2xl',
                'focus:outline-none',
                'placeholder:text-slate-600 uppercase focus:placeholder:text-slate-600/50'
            ]) }}
        ></textarea>
    </div>
</div>

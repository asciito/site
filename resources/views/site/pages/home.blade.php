<x-site::layout>
    <div class="w-full h-32 grayscale mb-5">
        <div class="w-full h-full overflow-hidden bg-cover bg-center"
             style="background-image: url('{{ asset('img/pixel-windows-xp.jpg') }}')">
        </div>
    </div>

    <h2 class="text-4xl mb-8">Recent Posts</h2>

    <livewire:posts />
</x-site::layout>

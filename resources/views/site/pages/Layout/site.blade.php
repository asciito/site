<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#33ff33">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {!! seo($page ?? null) !!}

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class(['relative pt-16' => \Illuminate\Support\Facades\Auth::user()])>
    @auth
        <livewire:adminbar />
    @endauth

    <x-site::navigation/>

    <main class="relative border-b-2 border-slate-400">
        <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto my-10 p-4">
            <div class="max-w-3xl mx-auto">
                {{ $slot }}
            </div>
        </div>
    </main>

    <footer>
        <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto flex flex-col justify-between md:flex-row items-center px-4 py-8">
            <ul class="flex justify-center space-x-4">
                <li class="w-6">
                    <a href="https://github.com/{{ $settings->github_handler }}" target="_blank">
                        <x-site::icons.github class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>

                <li class="w-6">
                    <a href="https://x.com/{{ $settings->twitter_handler }}" target="_blank">
                        <x-site::icons.x-twitter class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>

                <li class="w-6">
                    <a href="https://www.instagram.com/{{ $settings->instagram_handler }}" target="_blank">
                        <x-site::icons.instagram class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>

                <li class="w-6">
                    <a href="https://www.linkedin.com/in/{{ $settings->linkedin_handler }}" target="_blank">
                        <x-site::icons.linkedin class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>
            </ul>

            <div class="mt-4 md:mt-0">
                <livewire:download-resume/>
            </div>
        </div>
    </footer>

    <div
        x-data="{
            visible: false,
            handleScroll() {
                const scrollTop = document.body.scrollTop || document.documentElement.scrollTop;

                if (scrollTop >= 64) {
                    this.visible = true;
                } else {
                    this.visible = false;
                }
            }
        }"
        x-show="visible"
        x-transition
        @scroll.window.throttle.25ms="handleScroll"
        class="fixed bottom-5 right-5 h-10 w-10 drop-shadow">
            <x-site::button
                @click="window.scroll({top: 0, behavior: 'smooth'})"
                class="relative rounded-full h-10 w-10 !p-0">
                <x-icon name="heroicon-s-arrow-up" class="w-5 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"/>
            </x-site::button>
    </div>

    @livewireScripts
</body>
</html>

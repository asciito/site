@php
    use function Coyotito\LaravelSettings\Helpers\settings;
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#33ff33">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {!! seo($page ?? null) !!}

    @vite('resources/css/app.css')
    @filamentStyles
</head>
<body>
    <div
        x-bind:class="{ 'sticky top-0 z-100 bg-white/50 backdrop-blur-md animate-fade': isSticky }"
        x-data="{
            isSticky: false,
            getScrollPosition: () => document.body.scrollTop || document.documentElement.scrollTop,
            handleScroll: function () {
                const currentPosition = this.getScrollPosition();
                const navigationBarHeight = $el.offsetHeight;

                if (currentPosition >= navigationBarHeight + 100) {
                    this.isSticky = true;
                } else {
                    if (currentPosition <= 10) {
                        this.isSticky = false;
                    }
                }
            }
        }"
        x-init="handleScroll();"
        @scroll.window.throttle.50ms="handleScroll()"
    >
        @auth
            <livewire:adminbar />
        @endauth

        @if ($shouldShowNavigation)
            <x-site::navigation />
        @endif
    </div>

    <main @class(["relative", "border-b-2 border-slate-400" => $showFooter])>
        <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto my-10 p-4">
            <div class="max-w-3xl mx-auto">
                {{ $slot }}
            </div>
        </div>
    </main>

    @if($showFooter)
        <footer>
            <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto flex flex-col justify-between md:flex-row items-center px-4 py-8 gap-4">
                <ul class="flex justify-center space-x-4">
                    <li class="w-6">
                        <a href="https://github.com/{{ settings('github_handler') }}" target="_blank">
                            <span class="sr-only">GitHub</span>
                            <x-site::icons.github class="fill-slate-800 hover:fill-slate-600"/>
                        </a>
                    </li>

                    <li class="w-6">
                        <a href="https://x.com/{{ settings('twitter_handler') }}" target="_blank">
                            <span class="sr-only">X (formerly known as Twitter)</span>
                            <x-site::icons.x-twitter class="fill-slate-800 hover:fill-slate-600"/>
                        </a>
                    </li>

                    <li class="w-6">
                        <a href="https://www.instagram.com/{{ settings('instagram_handler') }}" target="_blank">
                            <span class="sr-only">Instagram</span>
                            <x-site::icons.instagram class="fill-slate-800 hover:fill-slate-600"/>
                        </a>
                    </li>

                    <li class="w-6">
                        <a href="https://www.linkedin.com/in/{{ settings('linkedin_handler') }}" target="_blank">
                            <span class="sr-only">Linked In</span>
                            <x-site::icons.linkedin class="fill-slate-800 hover:fill-slate-600"/>
                        </a>
                    </li>
                </ul>

                <ol class="flex gap-4 text-sm">
                    <li><a href="{{ route('terms') }}" class="font-semibold underline underline-offset-2 hover:text-dark-blue-200 visited:text-dark-blue">Terms of Use</a></li>
                    <li><a href="{{ route('privacy') }}" class="font-semibold underline underline-offset-2 hover:text-dark-blue-200 visited:text-dark-blue">Privacy Policy</a></li>
                </ol>

                <div>
                    <livewire:download-resume/>
                </div>
            </div>
        </footer>
    @endif

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
                class="relative rounded-full h-10 w-10 p-0!">
                <x-icon name="heroicon-s-arrow-up" class="w-5 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"/>
            </x-site::button>
    </div>

    @vite('resources/js/app.js')
    @filamentScripts
</body>
</html>

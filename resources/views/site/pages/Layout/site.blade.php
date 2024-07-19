<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#33ff33">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('app.name') . \Illuminate\Support\Str::title($titlePage ? " | $titlePage" : '') }}</title>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-site::navigation/>

    <main class="border-b-2 border-slate-400">
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
                    <a href="https://github.com/asciito/">
                        <x-site::icons.github class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>

                <li class="w-6">
                    <a href="https://x.com/asciito/">
                        <x-site::icons.x-twitter class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>

                <li class="w-6">
                    <a href="https://www.instagram.com/asciito/">
                        <x-site::icons.instagram class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>

                <li class="w-6">
                    <a href="https://www.linkedin.com/in/asciito/">
                        <x-site::icons.linkedin class="fill-slate-800 hover:fill-slate-600"/>
                    </a>
                </li>
            </ul>

            <div class="mt-4 md:mt-0">
                <livewire:download-resume/>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>

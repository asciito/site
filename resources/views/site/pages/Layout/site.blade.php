<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#33ff33">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('app.name') . \Illuminate\Support\Str::title($titlePage ? " | $titlePage" : '') }}</title>

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

            <form method="POST">
                @csrf

                <x-site::button class="px-6 py-2 bg-[rgba(51,255,51,1.0)] cursor-pointer">
                    <!-- TODO: Check a way to make an effect like the following link
                    https://www.google.com/url?sa=i&url=https%3A%2F%2Fslidesgo.com%2Ftheme%2F1980s-computer-screen-style-minitheme&psig=AOvVaw1vDDs-C0-3lBRoXsD4-zc_&ust=1721231325936000&source=images&cd=vfe&opi=89978449&ved=0CBQQjRxqFwoTCJjyusD0q4cDFQAAAAAdAAAAABAE
                    <span class="text-white [text-shadow:_1px_1px_1px_rgba(100,116,139)]">DOWNLOAD CV</span>
                    -->
                    DOWNLOAD CV
                </x-site::button>
            </form>
        </div>
    </footer>
</body>
</html>

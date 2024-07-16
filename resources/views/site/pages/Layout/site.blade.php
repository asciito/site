<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Home</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-site::navigation/>

    <main class="border-b-2 border-slate-400">
        <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto my-10 p-4">
            {{ $slot }}
        </div>
    </main>

    <footer></footer>
</body>
</html>

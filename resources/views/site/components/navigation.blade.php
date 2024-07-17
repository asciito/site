<header class="border-b-2 border-slate-400">
    <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto">
        <div class="grid grid-cols-[15rem_1fr_15rem] items-center">
            <div id="brand"  class="p-4">
                <a href="/">
                    <h1 class="uppercase text-4xl">
                        <span>ASCII</span><span class="text-[rgba(51,255,51,1.0)]">.</span><span class="text-slate-500/60">TO</span>
                    </h1>
                </a>
            </div>

            <nav id="menu" class="p-4">
                <ol class="flex flex-wrap justify-center space-x-4">
                    <li>
                        <a href="{{ route('home') }}" class="relative group p-1">
                            <span class="relative z-10">HOME</span>
                            <span @class([
                                "absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2",
                                "w-full h-full",
                                "scale-0 group-hover:scale-110 bg-transparent group-hover:bg-[rgba(51,255,51,1.0)]" => $notCurrent = ! \Illuminate\Support\Facades\Route::is('home'),
                                "bg-[rgba(51,255,51,1.0)]" => ! $notCurrent,
                                "transition duration-100",
                            ])></span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('contact') }}" class="relative group p-1">
                            <span class="relative z-10">CONTACT</span>
                            <span @class([
                                "absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2",
                                "w-full h-full",
                                "scale-0 group-hover:scale-110 bg-transparent group-hover:bg-[rgba(51,255,51,1.0)]" => $notCurrent = ! \Illuminate\Support\Facades\Route::is('contact'),
                                "bg-[rgba(51,255,51,1.0)]" => ! $notCurrent,
                                "transition duration-100",
                            ])></span>
                        </a>
                    </li>
                </ol>
            </nav>

            <div id="cv" class="p-4 w-full">
                <form method="POST" class="flex justify-end w-full">
                    @csrf

                    <x-site::button>
                        <!-- TODO: Check a way to make an effect like the following link
                        https://www.google.com/url?sa=i&url=https%3A%2F%2Fslidesgo.com%2Ftheme%2F1980s-computer-screen-style-minitheme&psig=AOvVaw1vDDs-C0-3lBRoXsD4-zc_&ust=1721231325936000&source=images&cd=vfe&opi=89978449&ved=0CBQQjRxqFwoTCJjyusD0q4cDFQAAAAAdAAAAABAE
                        <span class="text-white [text-shadow:_1px_1px_1px_rgba(100,116,139)]">DOWNLOAD CV</span>
                        -->
                        <span class="text-slate-800">DOWNLOAD CV</span>
                    </x-site::button>
                </form>
            </div>
        </div>
    </div>
</header>

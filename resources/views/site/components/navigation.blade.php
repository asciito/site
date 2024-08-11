<header class="border-b-2 border-slate-400">
    <div class="w-full md:max-w-5xl lg:max-w-7xl mx-auto">
        <div class="relative grid grid-cols-[minmax(5rem,15rem)_1fr_minmax(5rem,15rem)] items-center">
            <div id="brand" class="p-4">
                <a href="/">
                    <h1 class="uppercase text-3xl md:text-4xl">
                        <span>ASCII</span><span class="text-[rgba(51,255,51,1.0)]">.</span><span class="text-slate-500/80">TO</span>
                    </h1>
                </a>
            </div>

            <div class="md:grid md:grid-cols-subgrid col-span-2 flex justify-end items-center p-4" x-data="{
                toggle: () => {
                    $refs.nav.classList.toggle('hidden');
                }
            }">
                <div class="md:hidden space-y-1 cursor-pointer select-none py-1 pl-1" x-on:click="toggle()">
                    <span class="block w-10 h-1 bg-dark-blue-600"></span>
                    <span class="block w-10 h-1 bg-dark-blue-600"></span>
                    <span class="block w-10 h-1 bg-dark-blue-600"></span>
                </div>

                <div x-ref="nav" class="md:grid md:grid-cols-subgrid md:col-span-2 items-center hidden absolute z-[100] md:static top-full left-0 bg-slate-50 p-4 md:p-0 md:bg-transparent w-full md:justify-between">
                    <nav id="menu" class="p-4 md:block">
                        <ol class="flex flex-col md:flex-row md:flex-wrap justify-center items-center md:items-center space-y-4 md:space-y-0 md:space-x-4 text-xl">
                            <li>
                                <a href="{{ route('home') }}" class="relative group p-1">
                                    <span class="relative z-[100]">HOME</span>
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
                                    <span class="relative z-[100]">CONTACT</span>
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

                    <div class="flex justify-center md:justify-end">
                        <livewire:download-resume/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

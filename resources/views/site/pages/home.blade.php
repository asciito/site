<x-site::layout>
    <div class="max-w-3xl mx-auto">
        <div class="w-full h-32 grayscale mb-5">
            <div class="w-full h-full overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('img/pixel-windows-xp.jpg') }}')">
            </div>
        </div>

        <h2 class="text-4xl mb-8">Recent Posts</h2>

        <section>
            <form method="POST" class="flex space-x-2 mb-5">
                <div class="w-full">
                    <input id="search" type="search" name="search" placeholder="Search by name" class="px-2 py-1 w-full text-lg p-0 border-none bg-[rgba(51,255,51,1.0)] placeholder:text-slate-800 outline-none focus:ring focus:ring-slate-500">
                </div>

                <button class="text-lg px-4 py-1 bg-[rgba(51,255,51,1.0)] cursor-pointer">SEARCH</button>
            </form>

            <div class="flex flex-wrap space-y-5">
                <article class="space-y-2">
                    <header>
                        <h3 class="text-2xl">Lorem ipsum dolor it</h3>

                        <p class="text-sm text-slate-500">Posted: <time datetime="2024-07-16">2 minutes ago</time></p>
                    </header>

                    <div class="space-y-2">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. A
                            adipisci, ipsum labore laudantium neque nobis rem repudiandae
                            sunt tenetur voluptate!
                        </p>

                        <button class="inline-block px-3 py-1 bg-[rgba(51,255,51,1.0)] text-sm cursor-pointer">
                            VISIT
                        </button>
                    </div>
                </article>

                <article>
                    <header>
                        <h3 class="text-2xl">Lorem ipsum dolor it</h3>

                        <p class="text-sm text-slate-500">Posted: <time datetime="2024-07-15">1 day ago</time></p>
                    </header>

                    <div class="space-y-2">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. A
                            adipisci, ipsum labore laudantium neque nobis rem repudiandae
                            sunt tenetur voluptate!
                        </p>

                        <button class="inline-block px-3 py-1 bg-[rgba(51,255,51,1.0)] text-sm cursor-pointer">
                            VISIT
                        </button>
                    </div>
                </article>

                <article>
                    <header>
                        <h3 class="text-2xl">Lorem ipsum dolor it</h3>

                        <p class="text-sm text-slate-500">Posted: <time datetime="2024-07-09">1 week ago</time></p>
                    </header>

                    <div class="space-y-2">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. A
                            adipisci, ipsum labore laudantium neque nobis rem repudiandae
                            sunt tenetur voluptate!
                        </p>

                        <button class="inline-block px-3 py-1 bg-[rgba(51,255,51,1.0)] text-sm cursor-pointer">
                            VISIT
                        </button>
                    </div>
                </article>
            </div>

            <div class="flex justify-center">
                <button class="mt-10 px-3 py-1 bg-[rgba(51,255,51,1.0)] text-xl cursor-pointer">
                    LOAD MORE...
                </button>
            </div>
        </section>
    </div>
</x-site::layout>

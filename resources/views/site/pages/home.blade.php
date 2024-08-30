@php
use Illuminate\Support\Facades\Storage;

$settings = app(\App\Site\SiteSettings::class);

$seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
    title: $settings->site_name,
    description: $settings->site_description,
    image: $settings->site_image ? Storage::disk('public')->url($settings->site_image) : null,
);
@endphp

<x-site::layout :page="$seoData">
    <header class="block text-center space-y-4 mb-8">
        <h1 class="text-2xl md:text-4xl lg:text-5xl !leading-snug"><span>Hi, I'm Ayax Córdova</span> <span class="block">(A.K.A <strong>@asciito</strong>)<span></h1>
        <p>As a Software Engineer, I share insights on PHP (<strong>Laravel</strong>), JavaScript, Python, and web development. Explore tutorials, coding tips, and programming guides to boost your skills. Feel free to reach out for tech advice!</p>
    </header>

    <div class="w-full h-32 grayscale mb-5">
        <div class="w-full h-full overflow-hidden bg-cover bg-center"
             style="background-image: url('{{ asset('img/pixel-windows-xp.jpg') }}')">
        </div>
    </div>

    <h2 class="text-4xl mb-8">Recent Posts</h2>

    <livewire:posts />

    <section id="about-me" role="region" aria-labelby="region-heading" class="mt-18 sm:mt-24">
        <h3 id="region-heading" class="text-3xl mb-8 text-center">Ayax Córdova</h3>

       <div class="prose prose-pre:ring-1 prose-pre:ring-black/5 prose-pre:shadow max-w-none mt-8">
            <div>
                <div class="w-full flex justify-center sm:w-auto sm:inline sm:float-end">
                    <img src="{{ asset('img/profile_image.jpg') }}" width="400" height="400" class="m-0 md:ml-2 rounded-full w-full max-w-56 sm:max-w-40 shadow" alt="Profile Image of Ayax Córdova">
                </div>

                <p>Hi! I'm Ayax Córdova. I'm from Mexico, a beautiful place known for its amazing food, like tacos (I absolutely love tacos, especially real tacos from <strong>Mi México lindo</strong>). I am a <strong>Software Engineer with over 6 years of experience</strong>. I graduated in <time datetime="2018-12">December 2018</time> as a <strong>Technical Artist</strong>, a unique combination of Software Engineering and Graphic Design.</p>
            </div>

            <div>
                <p>Currently, I am dedicated to <strong>building my own small business</strong> called <a rel="nofollow noopener" target="_blank" href="https://coyotito.com.mx">COYOTITO</a>, focusing on creating SaaS products to address the needs of individuals and small businesses here in Mexico.</p>

                <p>Here are my main goals:</p>

                <ol>
                    <li>Continue learning and improving my skills in PHP, Python, and JavaScript.</li>
                    <li>Support the Open Source projects that I use.</li>
                    <li>Develop accessible software for people with disabilities.</li>
                    <li>Hire individuals from minority groups and teach them how to code.</li>
                </ol>

                <p>These are my primary goals, but another important goal is to <strong>have more free time to spend with my family</strong>. My vision is to move away from the current system (capitalism) and help build a better system where everyone can benefit.</p>
            </div>
        </div>

    </section>
</x-site::layout>

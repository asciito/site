@php
    use App\Models\User;
    use RalphJSmit\Laravel\SEO\Support\SEOData;
    use function Coyotito\LaravelSettings\Helpers\settings;

    $user = User::first();

    $seoData = new SEOData(
        title: 'My work experience | '.(settings('name') ?? config('app.name')),
        description: 'Explore my professional experience and skills. Download my résumé and contact me to discuss open roles, projects, or collaborations'
    );
@endphp

<x-site::layout :page="$seoData">
    <div class="text-center mb-10">
        <h1 class="text-4xl lg:text-5xl leading-snug! mb-8 print:hidden">Yes, you found me!</h1>

        <p class="print:hidden">
            Are you HR, or just someone curious about my professional journey?
            Take a look around, download my résumé, and <a href="{{ route('contact') }}" class="text-dark-blue-100 hover:text-dark-blue-400"><strong>Contact Me</strong></a>
            — I’d be happy to chat to collaborate and discuss your next project 😉
        </p>

        <div class="not-print:hidden">
            <p class="text-xl">
                <span class="block">
                    Hi, I'm <strong class="uppercase text-[1.2em]">{{ $user?->name ?? 'John Doe' }}</strong>, and you're
                    viewing the <i>print</i> version of my <strong>résumé</strong>.
                </span>

                <span class="block">
                    Feel free to <strong>download</strong> it and reach out if you'd like to discuss potential
                    opportunities or collaborations!
                </span>
            </p>

            <br>

            <p>
                Please contact me at <a href="mailto:ayax.cordova@aydev.mx"><strong>ayax.cordova@aydev.mx</strong></a>,
                visit my site <a href="{{ route('home') }}"><strong>{{ settings('name') }}</strong></a> or find my social media links
                at the bottom of the page.
            </p>
        </div>
    </div>

    <hr class="my-12 print:hidden">

    <div>
        <livewire:experience/>
    </div>
</x-site::layout>

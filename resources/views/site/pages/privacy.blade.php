@php
    use function Coyotito\LaravelSettings\Helpers\settings;

    $url = route('home');
    $site_url_without_http = preg_replace("/https?:\/\/(www\.)?/i", "", $url);

    $seo = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        title: 'Privacy Policy'.' | '.(settings('name') ?? config('app.name')),
        description: "Read asciito's Privacy Policy to learn how we protect your personal information. Understand our data collection, usage, and sharing practices. Your privacy and security are our top priorities at asciito.",
    );
@endphp

<x-site::layout :page="$seo">
    <h1 class="text-4xl lg:text-5xl leading-snug! text-center mb-8">Privacy Policy</h1>

    <div class="content">
        <p>Welcome to asciito's Privacy Policy page. This document outlines how we collect, use, and protect your personal information when you visit our website. Our Privacy Policy provides detailed information about the types of data we gather, the purposes for which we use it, and the measures we take to ensure its security. We are committed to maintaining your trust and safeguarding your privacy. For any questions or concerns, please contact us at <a href="mailto:ayax.cordova@aydev.mx">ayax.cordova@aydev.mx</a>.</p>

        <h2>Information We Collect</h2>
        <p>We collect information you provide directly to us, such as when you contact us through our contact form</p>

        <h2>How We Use Your Information</h2>
        <p>We use your information to provide, maintain, and improve our services, and to communicate with you.</p>

        <h2>Sharing of Information</h2>
        <p>We do not share your personal information with third parties except to comply with the law, develop our products, or protect our rights.</p>

        <h2>Your Rights</h2>
        <p>You have the right to access, correct, or delete your personal data. You may also object to the processing of your data under certain circumstances.</p>

        <h2>Changes to This Policy</h2>
        <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>
    </div>

</x-site::layout>

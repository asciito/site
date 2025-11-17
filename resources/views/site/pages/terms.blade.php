@php
    $url = route('home');
    $site_url_without_http = preg_replace("/https?:\/\/(www\.)?/i", "", $url);

    $settings = \App\Helpers\app_settings();

    $seo = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        title: 'Terms of Use'.' | '.($settings->name ?? config('app.name')),
        description: "Read the Terms of Use for Asciito's blog to understand the rules and regulations governing the use of our website. Learn about our policies on intellectual property, acceptable use, and more. Stay informed and safe while navigating $site_url_without_http.",
    );
@endphp

<x-site::layout :page="$seo">
    <h1 class="text-4xl lg:text-5xl leading-snug! text-center mb-8">Terms of Use</h1>

    <div class="content">
        <p>Welcome to asciito's Terms of Use page. Here, you'll find detailed information about the rules and regulations that govern your use of our website. Our Terms of Use cover important aspects such as intellectual property rights, acceptable use of our site, and our policies regarding public code and proprietary content. Please read these terms carefully to ensure a safe and enjoyable experience while using Asciito. For any questions or clarifications, feel free to contact us at <a href="mailto:ayax.cordova@aydev.mx">ayax.cordova@aydev.mx</a>.</p>

        <p>By accessing this website, we assume you accept these terms of use in full. Do not continue to use <a href="{{ $url }}">{{ $site_url_without_http }}</a> if you do not accept all of the terms and conditions stated on this page.</p>

        <h2>Public Code</h2>
        <p>The code used to run this blog is open source and publicly available. You are free to view, modify, and use the code under the terms of the applicable open source license. Please refer to the repository for specific licensing information.</p>

        <h2>Intellectual Property Rights</h2>
        <p>Unless otherwise stated, asciito and/or its licensors own the intellectual property rights published in the posts on this website and materials used on <a href="{{ $url }}">{{ $site_url_without_http }}</a>. All intellectual property rights are reserved.</p>

        <h2>License to Use Content</h2>
        <p>You may view, download (for caching purposes only), and print pages from the website for your own personal use, subject to the restrictions set out below and elsewhere in these terms of use.</p>
        <p>You must not:</p>
        <ul>
            <li>Republish material from this website (including republication on another website);</li>
            <li>Sell, rent, or sub-license material from the website;</li>
            <li>Show any material from the website in public;</li>
            <li>Reproduce, duplicate, copy, or otherwise exploit material on this website for a commercial purpose;</li>
            <li>Edit or otherwise modify any material on the website;</li>
            <li>Redistribute material from this website, except for content specifically and expressly made available for redistribution.</li>
        </ul>

        <h2>Acceptable Use</h2>
        <p>You must not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of <a href="{{ $url }}">{{ $site_url_without_http }}</a> or in any way which is unlawful, illegal, fraudulent, or harmful, or in connection with any unlawful, illegal, fraudulent, or harmful purpose or activity.</p>
        <p>You must not use this website to copy, store, host, transmit, send, use, publish, or distribute any material which consists of (or is linked to) any spyware, computer virus, Trojan horse, worm, keystroke logger, rootkit, or other malicious computer software.</p>
        <p>You must not conduct any systematic or automated data collection activities (including without limitation scraping, data mining, data extraction, and data harvesting) on or in relation to this website without asciito's express written consent.</p>

        <h2>Changes to These Terms</h2>
        <p>We may update these terms from time to time, and the changes will be posted on this page. By continuing to use the website after such changes, you agree to the new terms of use.</p>

        <h2>Contact Information</h2>
        <p>If you have any questions about these terms, please contact us at <a href="mailto:ayax.cordova@aydev.mx">ayax.cordova@aydev.mx</a>.</p>
    </div>
</x-site::layout>

<?php

use function Pest\Laravel\artisan;
use function Pest\Laravel\get;

beforeEach(function () {
    \Spatie\TestTime\TestTime::freeze('Y-m-d\TH:i:sP', '2024-01-01T00:00:00+00:00');

    Pest\Laravel\actingAs(\App\Models\User::factory()->create());
});

function xml_to_array(string $xml): array
{
    $xmlObject = simplexml_load_string($xml);

    return json_decode(
        json_encode($xmlObject),
        true,
    );
}

it('generate sitemap', function () {
    $storage = Storage::fake('public');
    $site_url = config('app.url');

    \Illuminate\Support\Facades\File::partialMock()
        ->shouldReceive('lastModified')
        ->andReturn(
            now()->getTimestamp()
        );

    artisan('generate:sitemap')
        ->expectsOutputToContain('Generating sitemap')
        ->assertSuccessful();

    expect($storage->path('sitemap.xml'))
        ->toBeFile()
        ->and(xml_to_array($storage->get('sitemap.xml')))
            ->toMatchArray(xml_to_array(<<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
                <url>
                    <loc>{$site_url}</loc>
                    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
                </url>
                <url>
                    <loc>{$site_url}/contact</loc>
                    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
                </url>
                <url>
                    <loc>{$site_url}/terms-of-use</loc>
                    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
                </url>
                <url>
                    <loc>{$site_url}/privacy-policy</loc>
                    <lastmod>2024-01-01T00:00:00+00:00</lastmod>
                </url>
            </urlset>
            XML));
});

it('generate site map with posts', function () {
    $storage = Storage::fake('public');
    $site_url = config('app.url');
    $titles = ['fake-title-one', 'fake-title-two'];

    foreach ($titles as $title) {
        \App\Models\Post::factory()->published()->create([
            'slug' => $title,
        ]);
    }

    artisan('generate:sitemap')
        ->expectsOutputToContain('Generating sitemap')
        ->assertSuccessful();

    expect($storage->path('sitemap.xml'))
        ->toBeFile()
        ->and(xml_to_array($storage->get('sitemap.xml')))
        ->toMatchArraySubset(xml_to_array(<<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <url>
                <loc>{$site_url}/fake-title-one</loc>
                <lastmod>2024-01-01T00:00:00+00:00</lastmod>
            </url>
            <url>
                <loc>{$site_url}/fake-title-two</loc>
                <lastmod>2024-01-01T00:00:00+00:00</lastmod>
            </url>
        </urlset>
        XML));
});


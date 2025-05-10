<?php

use function Pest\Laravel\get;

beforeEach(function () {
    \Spatie\TestTime\TestTime::freeze('Y-m-d', '2024-01-01');

    Pest\Laravel\actingAs(\App\Models\User::factory()->create());
});

function visit(\App\Blog\Models\Post $post): \Pest\Expectation
{
    $response = get(route('post', $post))->assertOk();

    expect()
        ->extend('toSeeTimeTag', function (\Illuminate\Support\Carbon|string $date) use ($response) {
            $formatted_date = is_string($date) ? $date : $date->format('Y-m-d');

            $response->assertSee("<time datetime=\"$formatted_date\">", false);

            return $this;
        });

    expect()->extend('toSeeText', function (string $text) use ($response) {
        $response->assertSeeText($text);

        return $this;
    });

    return expect($post);
}

it('`Created Today`', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    visit($post)
        ->isPublished()->toBeFalse()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Created Today');

    \Spatie\TestTime\TestTime::addMinute();

    visit(
        tap($post)->touch()
    )->not->toSeeText('Created Today');
});

it('`Created on`', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    \Spatie\TestTime\TestTime::addDay();

    visit($post)
        ->isPublished()
        ->toBeFalse()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Created on January 01, 2024');

    visit(
        tap($post)->publish()
    )->not->toSeeText('Created on January 01, 2024');
});

it('`Updated Today`', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    \Spatie\TestTime\TestTime::addDay();

    $post->touch();

    visit($post)
        ->isPublished()
        ->toBeFalse()
        ->toSeeTimeTag('2024-01-02')
        ->toSeeText('Updated Today');

    visit(
        tap($post)->publish()
    )->not->toSeeText('Updated Today');
});

it('`Updated on`', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    \Spatie\TestTime\TestTime::addDay();

    $post->touch();

    \Spatie\TestTime\TestTime::addDay();

    visit($post)
        ->isPublished()
        ->toBeFalse()
        ->toSeeTimeTag('2024-01-02')
        ->toSeeText('Updated on January 02, 2024');

    tap($post, fn ($post) => $post->delete())->restore();

    visit(
        $post
    )->not->toSeeText('Updated on January 02, 2024');
});

it('`Published Today`', function () {
    $post = \App\Blog\Models\Post::factory()->published()->create();

    visit($post)
        ->isPublished()
        ->toBeTrue()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Published Today');

    \Spatie\TestTime\TestTime::addDay();

    visit(
        $post
    )->not->toSeeText('Published Today');
});

it('`Published on`', function () {
    $post = \App\Blog\Models\Post::factory()->published()->create();

    \Spatie\TestTime\TestTime::addDay();

    visit($post)
        ->isPublished()
        ->toBeTrue()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Published on January 01, 2024');

    tap($post, fn ($post) => $post->archive())->restore();

    visit(
        $post
    )->not->toSeeText('Published on January 01, 2024');
});

it('Published but updated', function () {
    $post = \App\Blog\Models\Post::factory()->published()->create();

    \Spatie\TestTime\TestTime::addDay();

    $post->touch();

    \Spatie\TestTime\TestTime::addDay();

    visit($post)
        ->isPublished()
        ->toBeTrue()
        ->toSeeTimeTag('2024-01-02')
        ->toSeeText('Updated on January 02, 2024');

    \Spatie\TestTime\TestTime::addDay();

    visit(
        tap($post)->touch()
    )->not->toSeeText('Updated on January 02, 2024');
});

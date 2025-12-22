<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;
use Pest\Expectation;
use Spatie\TestTime\TestTime;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    TestTime::freeze('Y-m-d', '2024-01-01');

    actingAs(User::factory()->create());
});

function toVisit(Post $post): Expectation
{
    $response = get(route('post', $post))->assertOk();

    expect()
        ->extend('toSeeTimeTag', function (Carbon|string $date) use ($response) {
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
    $post = Post::factory()->create();

    toVisit($post)
        ->isPublished()->toBeFalse()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Created Today');

    TestTime::addMinute();

    toVisit(
        tap($post)->touch()
    )->not->toSeeText('Created Today');
});

it('`Created on`', function () {
    $post = Post::factory()->create();

    TestTime::addDay();

    toVisit($post)
        ->isPublished()
        ->toBeFalse()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Created on January 01, 2024');

    toVisit(
        tap($post)->publish()
    )->not->toSeeText('Created on January 01, 2024');
});

it('`Updated Today`', function () {
    $post = Post::factory()->create();

    TestTime::addDay();

    $post->touch();

    toVisit($post)
        ->isPublished()
        ->toBeFalse()
        ->toSeeTimeTag('2024-01-02')
        ->toSeeText('Updated Today');

    toVisit(
        tap($post)->publish()
    )->not->toSeeText('Updated Today');
});

it('`Updated on`', function () {
    $post = Post::factory()->create();

    TestTime::addDay();

    $post->touch();

    TestTime::addDay();

    toVisit($post)
        ->isPublished()
        ->toBeFalse()
        ->toSeeTimeTag('2024-01-02')
        ->toSeeText('Updated on January 02, 2024');

    tap($post, fn ($post) => $post->delete())->restore();

    toVisit(
        $post
    )->not->toSeeText('Updated on January 02, 2024');
});

it('`Published Today`', function () {
    $post = Post::factory()->published()->create();

    toVisit($post)
        ->isPublished()
        ->toBeTrue()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Published Today');

    TestTime::addDay();

    toVisit(
        $post
    )->not->toSeeText('Published Today');
});

it('`Published on`', function () {
    $post = Post::factory()->published()->create();

    TestTime::addDay();

    toVisit($post)
        ->isPublished()
        ->toBeTrue()
        ->toSeeTimeTag('2024-01-01')
        ->toSeeText('Published on January 01, 2024');

    tap($post, fn ($post) => $post->archive())->restore();

    toVisit(
        $post
    )->not->toSeeText('Published on January 01, 2024');
});

it('Published but updated', function () {
    $post = Post::factory()->published()->create();

    TestTime::addDay();

    $post->touch();

    TestTime::addDay();

    toVisit($post)
        ->isPublished()
        ->toBeTrue()
        ->toSeeTimeTag('2024-01-02')
        ->toSeeText('Updated on January 02, 2024');

    TestTime::addDay();

    toVisit(
        tap($post)->touch()
    )->not->toSeeText('Updated on January 02, 2024');
});

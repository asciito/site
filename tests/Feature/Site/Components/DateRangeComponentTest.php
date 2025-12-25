<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\View\ViewException;

dataset('dates', [
    ['2018-01-01', '2018-03-01'],
    ['2019-01-01', '2019-01-10'],
    ['2020-01-01', '2020-02-20'],
    ['2021-02-01', '2021-11-01'],
    ['2022-02-01', '2022-05-01'],
    ['2023-02-01', '2023-11-11'],
    ['2024-02-01', '2024-07-09'],
    ['2025-02-01', '2025-10-02'],
]);

it('can render range', function (string $from, string $to) {
    $from = Carbon::createFromFormat('Y-m-d', $from);
    $to = Carbon::createFromFormat('Y-m-d', $to);

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $to])
        ->assertSeeText($from->format('M d, Y'))
        ->assertSeeText($to->format('M d, Y'));
})->with('dates');

it('can render range without to date', function (string $from, string $to) {
    $from = Carbon::createFromFormat('Y-m-d', $from);
    $to = Carbon::createFromFormat('Y-m-d', $to);

    $this
        ->view('site::components.date-range', ['from' => $from])
        ->assertSeeText($from->format('M d, Y'))
        ->assertDontSeeText($to->format('M d, Y'))
        ->assertSeeText('Not available');
})->with('dates');

it('fail if `to` date is less than `from` date', function (string $from) {
    $from = Carbon::createFromFormat('Y-m-d', $from)->setTime(0, 0);
    $lessThan = $from->clone()->subSecond();

    expect(fn () => $this->view('site::components.date-range', ['from' => $from, 'to' => $lessThan]))
        ->toThrow(ViewException::class, 'The `to` date must be greater than or equal to `from` date')
        ->and(fn () => $this->view('site::components.date-range', ['from' => $from, 'to' => $from->clone()->subDay()]))
        ->toThrow(ViewException::class, 'The `to` date must be greater than or equal to `from` date');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $equalDate = $from->clone()->addSecond()])
        ->assertSeeText($from->format('M d, Y'))
        ->assertDontSeeText('Not available')
        ->assertSeeText($equalDate->format('M d, Y'));
})->with('dates');

it('fail if missing `from` date', function () {
    $this->view('site::components.date-range');
})->throws(ViewException::class, 'Undefined variable $from');

it('render provided `empty` state', function (string $from) {
    $from = Carbon::createFromFormat('Y-m-d', $from);

    $this
        ->view('site::components.date-range', ['from' => $from, 'emptyStateTo' => 'Nothing to see here'])
        ->assertSeeText($from->format('M d, Y'))
        ->assertDontSeeText('Not available')
        ->assertSeeText('Nothing to see here');

    $this
        ->view('site::components.date-range', ['from' => $from, 'emptyStateTo' => new HtmlString('<p>Nothing to see here</p>')])
        ->assertSeeText($from->format('M d, Y'))
        ->assertDontSeeText('Not available')
        ->assertSee('<p>Nothing to see here</p>', false);
})->with('dates');

it('fail to render `empty` state when both dates are given', function (string $from, string $to) {
    $from = Carbon::createFromFormat('Y-m-d', $from);
    $to = Carbon::createFromFormat('Y-m-d', $to);

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $to, 'emptyStateTo' => 'Nothing to see here'])
        ->assertDontSeeText('Nothing to see here')
        ->assertSeeText($from->format('M d, Y'))
        ->assertSeeText($to->format('M d, Y'));
})->with('dates');

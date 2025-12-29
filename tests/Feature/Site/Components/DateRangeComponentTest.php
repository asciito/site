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

it('render relative date', function (string $from) {
    $from = Carbon::createFromFormat('Y-m-d', $from);
    $to = $from->clone();

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $to, 'relative' => true])
        ->assertSeeText($from->format('M Y').' - '.$to->format('M Y'))
        ->assertSeeText('Less than 3 months');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $cloned = $to->clone()->addMonths(3)->subDay(), 'relative' => true])
        ->assertSeeText($from->format('M Y').' - '.$cloned->format('M Y'))
        ->assertSeeText('Less than 3 months');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $cloned = $to->clone()->addMonths(3)->addDay(), 'relative' => true])
        ->assertSeeText($from->format('M Y').' - '.$cloned->format('M Y'))
        ->assertSeeText('Less than 6 months');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $cloned = $to->clone()->addMonths(6)->subDay(), 'relative' => true])
        ->assertSeeText($from->format('M Y').' - '.$cloned->format('M Y'))
        ->assertSeeText('Less than 6 months');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $cloned = $to->clone()->addMonths(6)->addDay(), 'relative' => true])
        ->assertSeeText($from->format('M Y').' - '.$cloned->format('M Y'))
        ->assertSeeText('Less than 1 year');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $cloned = $to->clone()->addMonths(9), 'relative' => true])
        ->assertSeeText($from->format('M Y').' - '.$cloned->format('M Y'))
        ->assertSeeText('Less than 1 year');

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $to->clone()->addYear(), 'relative' => true])
        ->assertSeeText('1 year');
})->with('dates');

it('will not render relative date if `to` date is not given', function (string $from) {
    $from = Carbon::createFromFormat('Y-m-d', $from);

    $this
        ->view('site::components.date-range', ['from' => $from])
        ->assertSeeText($from->format('M d, Y'))
        ->assertSeeText('Not available');
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

it('will not render `empty` state when both dates are given', function (string $from, string $to) {
    $from = Carbon::createFromFormat('Y-m-d', $from);
    $to = Carbon::createFromFormat('Y-m-d', $to);

    $this
        ->view('site::components.date-range', ['from' => $from, 'to' => $to, 'emptyStateTo' => 'Nothing to see here'])
        ->assertDontSeeText('Nothing to see here')
        ->assertSeeText($from->format('M d, Y'))
        ->assertSeeText($to->format('M d, Y'));
})->with('dates');

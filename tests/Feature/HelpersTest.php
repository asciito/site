<?php

use App\Helpers\InvalidRangeException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Pluralizer;

use function App\Helpers\relativeRangeDate;

dataset(
    'years',
    fn () => array_map(
        fn (int $year): Carbon => Carbon::createFromDate($year, 1, 1)->setTime(0, 0),
        range(2018, 2025)
    )
);

it('get relative date range', function (Carbon $from) {
    $to = $from->clone()->addYear();
    $years = $from->diff($to)->years;
    $yearsPluralLabel = Pluralizer::plural('year', $years);

    expect(relativeRangeDate($from, $to))
        ->toBe("$years $yearsPluralLabel");
})->with('years');

it('get relative date range less than year(s)', function (Carbon $from) {
    $to = $from->clone()->addYear();
    $years = $from->diff($to)->years;
    $yearsPluralLabel = Pluralizer::plural('year', $years);

    expect(relativeRangeDate($from, $to->subDay()))
        ->toBe("Less than $years $yearsPluralLabel");
})->with('years');

it('get relative date range less than year with months', function (Carbon $from) {
    $to = $from->clone()->addYear();
    $years = $from->diff($to)->years;

    $yearsPluralLabel = Pluralizer::plural('year', $years);

    expect(relativeRangeDate($from, $to->clone()->addDay()))
        ->toBe("Less than $years $yearsPluralLabel and 3 months")
        ->and(relativeRangeDate($from, $to->clone()->addMonths(3)->addDay()))
        ->toBe("Less than $years $yearsPluralLabel and 6 months");
})->with('years');

it('fail when `end date` is less than `start date`', function (Carbon $from) {
    $to = $from->clone();

    expect(fn () => relativeRangeDate($from, $to->subDay()))
        ->toThrow(InvalidRangeException::class, 'The end date must be greater than the start date');
})->with('years');

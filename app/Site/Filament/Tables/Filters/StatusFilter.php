<?php

namespace App\Site\Filament\Tables\Filters;

use Filament\Tables\Filters\TernaryFilter;

class StatusFilter extends TernaryFilter
{
    public static function getDefaultName(): ?string
    {
        return 'status';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Status');

        $this->trueLabel('Published');
        $this->falseLabel('Draft');
        $this->placeholder('All');

        $this->queries(
            true: fn ($query) => $query->onlyPublished(),
            false: fn ($query) => $query->onlyDrafts(),
            blank: fn ($query) => $query->withDrafts(),
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

class SEO extends \RalphJSmit\Laravel\SEO\Models\SEO
{
    #[Override]
    public function model(): MorphTo
    {
        /** @phpstan-ignore-next-line */
        return parent::model()->withDrafts();
    }
}

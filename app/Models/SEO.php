<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class SEO extends \RalphJSmit\Laravel\SEO\Models\SEO
{
    public function model(): MorphTo
    {
        return parent::model()->withDrafts();
    }
}

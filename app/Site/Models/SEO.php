<?php

namespace App\Site\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class SEO extends \RalphJSmit\Laravel\SEO\Models\SEO
{
    public function model(): MorphTo
    {
        return parent::model()->withDrafts();
    }
}

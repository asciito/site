<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Override;

abstract class TestCase extends BaseTestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}

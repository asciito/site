<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Thrown when the `end date` is less than the `start date`
 */
class InvalidRangeException extends Exception
{
    public function __construct()
    {
        parent::__construct('The end date must be greater than the start date');
    }
}

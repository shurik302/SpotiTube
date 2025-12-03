<?php
declare(strict_types=1);

namespace App\Domain\Exceptions;

use RuntimeException;

/**
 * Base exception for domain layer violations.
 */
class DomainException extends RuntimeException
{
}

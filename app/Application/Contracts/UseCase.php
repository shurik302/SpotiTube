<?php
declare(strict_types=1);

namespace App\Application\Contracts;

/**
 * @template TRequest
 * @template TResult
 */
interface UseCase
{
    /**
     * @param TRequest $request
     * @return TResult
     */
    public function execute(mixed $request = null): mixed;
}

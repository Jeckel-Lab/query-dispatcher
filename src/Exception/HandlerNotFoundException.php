<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\Exception;

use JeckelLab\Contract\Core\QueryDispatcher\Exception\QueryDispatcherException;
use RuntimeException;

/**
 * Class HandlerNotFoundException
 * @package JeckelLab\QueryDispatcher\Exception
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
class HandlerNotFoundException extends RuntimeException implements QueryDispatcherException
{
    public function __construct(string $handlerName, string $queryName, \Throwable $exception)
    {
        parent::__construct(
            message: sprintf(
                'No query handler instance for %s found in container for %s',
                $handlerName,
                $queryName
            ),
            previous: $exception
        );
    }
}

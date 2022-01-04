<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 04/01/2022
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\Exception;

use JeckelLab\Contract\Core\QueryDispatcher\Exception\QueryDispatcherException;
use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use RuntimeException;

/**
 * Class HandlerForQueryAlreadyDefinedException
 * @package JeckelLab\QueryDispatcher\Exception
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
class HandlerForQueryAlreadyDefinedException extends RuntimeException implements QueryDispatcherException
{
    /**
     * @param class-string<Query> $queryName
     * @param class-string<QueryHandler>|QueryHandler $definedHandler
     */
    public function __construct(string $queryName, string|QueryHandler $definedHandler)
    {
        parent::__construct(
            sprintf(
                'Another handler is already defined for query %s (Defined handler: %s)',
                $queryName,
                is_object($definedHandler) ? get_class($definedHandler) : $definedHandler
            )
        );
    }
}

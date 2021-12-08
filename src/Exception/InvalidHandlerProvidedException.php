<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\Exception;

use JeckelLab\Contract\Core\Exception\RuntimeException;
use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\Exception\QueryBusException;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;

/**
 * Class InvalidHandlerProvidedException
 * @package JeckelLab\QueryDispatcher\Exception
 * @psalm-immutable
 */
class InvalidHandlerProvidedException extends RuntimeException implements QueryBusException
{
    /**
     * @param mixed $handlerClassName
     * @psalm-suppress ImpureFunctionCall
     */
    public function __construct(mixed $handlerClassName)
    {
        $message = match (true) {
            is_object($handlerClassName) => sprintf(
                'Invalid query handler provided, %s needs to implements %s interface',
                get_class($handlerClassName),
                QueryHandler::class
            ),
            is_string($handlerClassName) && class_exists($handlerClassName) => sprintf(
                'Invalid query handler provided, %s needs to implements %s interface',
                $handlerClassName,
                QueryHandler::class
            ),
            default => sprintf(
                'Invalid query handler provided, handler needs to be an implementation of %s interface',
                QueryHandler::class
            )
        };

        parent::__construct($message);
    }
}

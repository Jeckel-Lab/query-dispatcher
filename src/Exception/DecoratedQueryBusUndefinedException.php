<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\Exception;

use JeckelLab\Contract\Core\QueryDispatcher\Exception\QueryDispatcherException;
use LogicException;

/**
 * Class DecoratedQueryBusUndefinedException
 * @package JeckelLab\QueryDispatcher\Exception
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
class DecoratedQueryBusUndefinedException extends LogicException implements QueryDispatcherException
{
    public function __construct()
    {
        parent::__construct("You should call 'decorate()' method before dispatching queries on the decorator");
    }
}

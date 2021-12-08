<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\Exception;

use JeckelLab\Contract\Core\Exception\LogicException;
use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\Exception\QueryBusException;

/**
 * Class DecoratedQueryBusUndefinedException
 * @package JeckelLab\QueryDispatcher\Exception
 * @psalm-immutable
 */
class DecoratedQueryBusUndefinedException extends LogicException implements QueryBusException
{
    public function __construct()
    {
        parent::__construct("You should call 'decorate()' method before dispatching queries on the decorator");
    }
}

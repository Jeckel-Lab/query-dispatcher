<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

namespace JeckelLab\QueryDispatcher\QueryBus\Decorator;

use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\QueryBus;

/**
 * Interface QueryBusDecorator
 * @package JeckelLab\QueryDispatcher\QueryBus\Decorator
 */
interface QueryBusDecorator extends QueryBus
{
    /**
     * @param QueryBus $queryBus
     * @return QueryBusDecorator
     */
    public function decorate(QueryBus $queryBus): QueryBusDecorator;
}

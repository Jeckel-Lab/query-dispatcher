<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\QueryBus;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\QueryBus;
use JeckelLab\Contract\Core\QueryDispatcher\ViewModel\ViewModel;
use JeckelLab\QueryDispatcher\Resolver\QueryHandlerResolverInterface;

/**
 * Class QueryDispatcher
 * @package JeckelLab\QueryDispatcher\QueryBus
 */
class QueryDispatcher implements QueryBus
{
    /**
     * @param QueryHandlerResolverInterface $queryHandlerResolver
     */
    public function __construct(private QueryHandlerResolverInterface $queryHandlerResolver)
    {
    }

    /**
     * @param Query $query
     * @return ViewModel
     */
    public function dispatch(Query $query): ViewModel
    {
        return $this->queryHandlerResolver->resolve($query)($query);
    }
}

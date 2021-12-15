<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

namespace JeckelLab\QueryDispatcher\Resolver;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;

/**
 * Interface QueryHandlerResolverInterface
 * @package JeckelLab\QueryDispatcher\Resolver
 */
interface QueryHandlerResolverInterface
{
    /**
     * @param Query $query
     * @return QueryHandler
     */
    public function resolve(Query $query): QueryHandler;
}

<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\QueryBus\Decorator;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\QueryBus;
use JeckelLab\Contract\Core\QueryDispatcher\ViewModel\ViewModel;
use JeckelLab\QueryDispatcher\Exception\DecoratedQueryBusUndefinedException;

abstract class AbstractQueryBusDecorator implements QueryBusDecorator
{
    private ?QueryBus $queryBus = null;

    /**
     * @param QueryBus $queryBus
     * @return QueryBusDecorator
     */
    public function decorate(QueryBus $queryBus): QueryBusDecorator
    {
        $this->queryBus = $queryBus;
        return $this;
    }

    /**
     * @param Query $query
     * @return ViewModel
     */
    public function dispatch(Query $query): ViewModel
    {
        $query = $this->preDispatch($query);
        if (null === $this->queryBus) {
            throw new DecoratedQueryBusUndefinedException();
        }
        return $this->postDispatch(
            $query,
            $this->queryBus->dispatch($query)
        );
    }

    /**
     * @param Query $query
     * @return Query
     * @infection-ignore-all
     */
    protected function preDispatch(Query $query): Query
    {
        return $query;
    }

    /**
     * @param Query     $query
     * @param ViewModel $viewModel
     * @return ViewModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @infection-ignore-all
     */
    protected function postDispatch(Query $query, ViewModel $viewModel): ViewModel
    {
        return $viewModel;
    }
}

<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\QueryBus\Decorator;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\ViewModel\ViewModel;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerDecorator
 * @package JeckelLab\QueryDispatcher\QueryBus\Decorator
 */
class LoggerDecorator extends AbstractQueryBusDecorator
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @param Query $query
     * @return Query
     */
    protected function preDispatch(Query $query): Query
    {
        $this->logger->debug('Start dispatch query: ' . get_class($query));
        return $query;
    }

    /**
     * @param Query     $query
     * @param ViewModel $viewModel
     * @return ViewModel
     */
    protected function postDispatch(Query $query, ViewModel $viewModel): ViewModel
    {
        $this->logger->debug(sprintf('Dispatch query %s completed: %s', get_class($query), get_class($viewModel)));
        return $viewModel;
    }
}

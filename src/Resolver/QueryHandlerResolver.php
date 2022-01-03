<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher\Resolver;

use JeckelLab\Contract\Core\QueryDispatcher\Exception\NoHandlerDefinedForQueryException;
use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use JeckelLab\QueryDispatcher\Exception\HandlerNotFoundException;
use JeckelLab\QueryDispatcher\Exception\InvalidHandlerProvidedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Class QueryHandlerResolver
 * @package JeckelLab\QueryDispatcher\Resolver
 */
class QueryHandlerResolver implements QueryHandlerResolverInterface
{
    /**
     * @var array<class-string<Query>, QueryHandler>
     */
    private array $handlerInstances = [];

    /**
     * @param array<class-string<Query>, class-string<QueryHandler>|QueryHandler> $handlers
     * @param ContainerInterface $container
     */
    public function __construct(
        private array $handlers,
        private ContainerInterface $container
    ) {
    }

    /**
     * @param Query $query
     * @return QueryHandler
     */
    public function resolve(Query $query): QueryHandler
    {
        $queryClass = get_class($query);
        if (! isset($this->handlerInstances[$queryClass])) {
            $this->handlerInstances[$queryClass] = $this->findHandlerInstance($query);
        }
        return $this->handlerInstances[$queryClass];
    }

    /**
     * @param Query $query
     * @return QueryHandler
     */
    private function findHandlerInstance(Query $query): QueryHandler
    {
        $handler = $this->findConfiguredHandler($query);

        if ($handler instanceof QueryHandler) {
            return $handler;
        }

        try {
            $instance = $this->container->get($handler);
        } catch (ContainerExceptionInterface $e) {
            throw new HandlerNotFoundException($handler, get_class($query), $e);
        }

        if (! $instance instanceof QueryHandler) {
            throw new InvalidHandlerProvidedException($instance);
        }

        return $instance;
    }

    /**
     * @param Query $query
     * @return QueryHandler|class-string<QueryHandler>
     */
    private function findConfiguredHandler(Query $query): QueryHandler|string
    {
        // Find direct command handler
        if (isset($this->handlers[get_class($query)])) {
            return $this->handlers[get_class($query)];
        }

        // Find a command handler for a parent class or interface
        foreach ($this->handlers as $queryName => $handler) {
            /** @infection-ignore-all */
            if ($query instanceof $queryName || in_array($queryName, class_implements($query) ?: [], true)) {
                return $handler;
            }
        }

        throw new NoHandlerDefinedForQueryException($query);
    }
}

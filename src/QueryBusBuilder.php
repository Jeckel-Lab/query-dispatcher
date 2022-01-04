<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

declare(strict_types=1);

namespace JeckelLab\QueryDispatcher;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\QueryBus;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use JeckelLab\QueryDispatcher\Exception\HandlerForQueryAlreadyDefinedException;
use JeckelLab\QueryDispatcher\QueryBus\Decorator\QueryBusDecorator;
use JeckelLab\QueryDispatcher\QueryBus\QueryDispatcher;
use JeckelLab\QueryDispatcher\Resolver\QueryHandlerResolver;
use Psr\Container\ContainerInterface;

class QueryBusBuilder
{
    /** @var array<class-string<Query>, QueryHandler|class-string<QueryHandler>> */
    private array $handlers = [];

    /** @var list<QueryBusDecorator|class-string<QueryBusDecorator>> */
    private array $decorators = [];

    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @param class-string<QueryHandler>|QueryHandler ...$handlers
     * @return $this
     */
    public function addQueryHandler(string|QueryHandler ...$handlers): self
    {
        foreach ($handlers as $handler) {
            foreach ($handler::getHandledQueries() as $queryName) {
                if (isset($this->handlers[$queryName]) && $this->handlers[$queryName] !== $handler) {
                    /** @psalm-suppress MixedArgumentTypeCoercion */
                    throw new HandlerForQueryAlreadyDefinedException($queryName, $this->handlers[$queryName]);
                }
                $this->handlers[$queryName] = $handler;
            }
        }
        return $this;
    }

    /**
     * @param class-string<QueryBusDecorator>|QueryBusDecorator ...$decorators
     * @return $this
     */
    public function addDecorator(string|QueryBusDecorator ...$decorators): self
    {
        foreach ($decorators as $decorator) {
            $this->decorators[] = $decorator;
        }
        return $this;
    }

    public function build(): QueryBus
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $resolver = new QueryHandlerResolver($this->handlers, $this->container);
        /** @var QueryBus $queryBus */
        $queryBus = new QueryDispatcher($resolver);

        foreach ($this->decorators as $decorator) {
            /** @var QueryBusDecorator $instance */
            $instance = is_string($decorator) ? $this->container->get($decorator) : $decorator;

            /** @var QueryBus $queryBus */
            $queryBus = $instance->decorate($queryBus);
        }

        return $queryBus;
    }
}

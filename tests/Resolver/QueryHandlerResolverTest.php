<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

namespace Tests\JeckelLab\QueryDispatcher\Resolver;

use JeckelLab\Contract\Core\QueryDispatcher\Exception\NoHandlerDefinedForQueryException;
use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use JeckelLab\QueryDispatcher\Exception\HandlerNotFoundException;
use JeckelLab\QueryDispatcher\Exception\InvalidHandlerProvidedException;
use JeckelLab\QueryDispatcher\Resolver\QueryHandlerResolver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class QueryHandlerResolverTest extends TestCase
{
    /**
     * Test resolve without defined handlers
     */
    public function testResolveWithNoHandlers(): void
    {
        $this->expectException(NoHandlerDefinedForQueryException::class);
        $container = $this->createMock(ContainerInterface::class);
        $resolver = new QueryHandlerResolver([], $container);
        $resolver->resolve($this->createMock(Query::class));
    }

    /**
     * Test resolve without container
     */
    public function testResolveWithoutContainer(): void
    {
        $query = $this->createMock(Query::class);
        $handler = $this->createMock(QueryHandler::class);
        $container = $this->createMock(ContainerInterface::class);

        $resolver = new QueryHandlerResolver([get_class($query) => $handler], $container);
        $this->assertSame($handler, $resolver->resolve($query));
    }

    public function testResolveWithContainer(): void
    {
        $handlerName = 'query.handler';

        $query = $this->createMock(Query::class);
        $handler = $this->createMock(QueryHandler::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with($handlerName)
            ->willReturn($handler);

        $resolver = new QueryHandlerResolver([get_class($query) => $handlerName], $container);
        $this->assertSame($handler, $resolver->resolve($query));
    }

    public function testResolveWithContainerButServiceIsNotAQueryHandler(): void
    {
        $this->expectException(InvalidHandlerProvidedException::class);
        $this->expectExceptionMessage(
            'Invalid query handler provided, stdClass needs to implements ' . QueryHandler::class . ' interface'
        );

        $handlerName = 'query.handler';

        $query = $this->createMock(Query::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with($handlerName)
            ->willReturn(new \stdClass());

        $resolver = new QueryHandlerResolver([get_class($query) => $handlerName], $container);
        $resolver->resolve($query);
    }

    public function testResolveWithContainerAndOverriddenQuery(): void
    {
        $handlerName = 'query.handler';

        $command = new class implements Query {
        };
        $handler = $this->createMock(QueryHandler::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with($handlerName)
            ->willReturn($handler);

        $resolver = new QueryHandlerResolver([Query::class => $handlerName], $container);
        $this->assertSame($handler, $resolver->resolve($command));
    }

    public function testResolveWithContainerThrowsException(): void
    {
        $query = $this->createMock(Query::class);
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage(
            'No query handler instance for query.handler found in container for ' . get_class($query)
        );

        $handlerName = 'query.handler';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with($handlerName)
            ->willThrowException(new class extends \RuntimeException implements ContainerExceptionInterface {
            });

        $resolver = new QueryHandlerResolver([get_class($query) => $handlerName], $container);
        $resolver->resolve($query);
    }
}

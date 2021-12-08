<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

namespace Tests\JeckelLab\QueryDispatcher\QueryBus\Decorator;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\QueryBus;
use JeckelLab\Contract\Core\QueryDispatcher\ViewModel\ViewModel;
use JeckelLab\QueryDispatcher\Exception\DecoratedQueryBusUndefinedException;
use JeckelLab\QueryDispatcher\QueryBus\Decorator\LoggerDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerDecoratorTest extends TestCase
{
    public function testDispatchWithoutDefinedDecoratedQueryBusShouldFail(): void
    {
        $this->expectException(DecoratedQueryBusUndefinedException::class);
        $this->expectExceptionMessage(
            "You should call 'decorate()' method before dispatching queries on the decorator"
        );

        $query = $this->createMock(Query::class);
        $queryBusDecorated = new LoggerDecorator($this->createMock(LoggerInterface::class));

        $queryBusDecorated->dispatch($query);
    }

    public function testDecorateWithLogger(): void
    {
        $query = $this->createMock(Query::class);
        $viewModel = $this->createMock(ViewModel::class);

        $queryBus = $this->createMock(QueryBus::class);
        $queryBus->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($viewModel);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->withConsecutive(
                ['Start dispatch query: ' . get_class($query)],
                [ sprintf('Dispatch query %s completed: %s', get_class($query), get_class($viewModel))]
            );

        $queryBusDecorated = new LoggerDecorator($logger);

        $queryBusDecorated->decorate($queryBus);

        $this->assertSame($viewModel, $queryBusDecorated->dispatch($query));
    }
}

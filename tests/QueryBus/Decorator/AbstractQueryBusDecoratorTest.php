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
use PHPUnit\Framework\TestCase;
use Tests\JeckelLab\QueryDispatcher\Fixture\FixtureQueryBusDecorator;

class AbstractQueryBusDecoratorTest extends TestCase
{
    public function testDispatchWithoutDefinedDecoratedQueryBusShouldFail(): void
    {
        $this->expectException(DecoratedQueryBusUndefinedException::class);
        $this->expectExceptionMessage(
            "You should call 'decorate()' method before dispatching queries on the decorator"
        );

        $query = $this->createMock(Query::class);

        $queryBusDecorated = new FixtureQueryBusDecorator();
        $queryBusDecorated->dispatch($query);
    }

    public function testDispatchWithEmptyDecorator(): void
    {
        $query = $this->createMock(Query::class);
        $viewModel = $this->createMock(ViewModel::class);
        $queryBus = $this->createMock(QueryBus::class);
        $queryBus->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($viewModel);

        $queryBusDecorated = new FixtureQueryBusDecorator();
        $queryBusDecorated->decorate($queryBus);

        $this->assertSame($viewModel, $queryBusDecorated->dispatch($query));
    }
}

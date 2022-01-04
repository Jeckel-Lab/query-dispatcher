<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

namespace Tests\JeckelLab\QueryDispatcher;

use JeckelLab\Contract\Core\QueryDispatcher\QueryBus\QueryBus;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use JeckelLab\QueryDispatcher\Exception\HandlerForQueryAlreadyDefinedException;
use JeckelLab\QueryDispatcher\QueryBus\QueryDispatcher;
use JeckelLab\QueryDispatcher\QueryBusBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\JeckelLab\QueryDispatcher\Fixture\FixtureDuplicateQueryHandler;
use Tests\JeckelLab\QueryDispatcher\Fixture\FixtureQuery;
use Tests\JeckelLab\QueryDispatcher\Fixture\FixtureQueryBusDecorator;
use Tests\JeckelLab\QueryDispatcher\Fixture\FixtureQueryHandler;
use Tests\JeckelLab\QueryDispatcher\Fixture\FixtureViewModel;

class QueryBusBuilderTest extends TestCase
{
    public function testBuildEmptyQueryBus(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $commandBus = (new QueryBusBuilder($container))
            ->build();

        $this->assertInstanceOf(QueryBus::class, $commandBus);
        $this->assertInstanceOf(QueryDispatcher::class, $commandBus);
    }

    public function testBuildWithValidCommandHandler(): void
    {
        $query = new FixtureQuery();
        $viewModel = new FixtureViewModel();

        $handler = $this->createMock(QueryHandler::class);
        $handler->expects($this->once())
            ->method('__invoke')
            ->with($query)
            ->willReturn($viewModel);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(FixtureQueryHandler::class)
            ->willReturn($handler);

        $queryBus = (new QueryBusBuilder($container))
            ->addQueryHandler(FixtureQueryHandler::class)
            ->build();

        $this->assertInstanceOf(QueryBus::class, $queryBus);
        $this->assertInstanceOf(QueryDispatcher::class, $queryBus);
        $this->assertSame($viewModel, $queryBus->dispatch($query));
    }

    public function testBuildWithTwoDifferentHandlerForSameCommandShouldFail(): void
    {
        $this->expectException(HandlerForQueryAlreadyDefinedException::class);
        $this->expectExceptionMessage(
            'Another handler is already defined for query Tests\JeckelLab\QueryDispatcher\Fixture\FixtureQuery ' .
            '(Defined handler: Tests\JeckelLab\QueryDispatcher\Fixture\FixtureQueryHandler)'
        );

        $container = $this->createMock(ContainerInterface::class);
        $builder = new QueryBusBuilder($container);

        $builder->addQueryHandler(
            FixtureQueryHandler::class,
            FixtureDuplicateQueryHandler::class
        );
    }

    public function testBuildWithTwiceSameHandlerForSameCommand(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $builder = new QueryBusBuilder($container);

        $builder->addQueryHandler(
            FixtureQueryHandler::class,
            FixtureQueryHandler::class
        );

        $commandBus = $builder->build();

        $this->assertInstanceOf(QueryBus::class, $commandBus);
        $this->assertInstanceOf(QueryDispatcher::class, $commandBus);
    }

    public function testBuildWithSingleDecorator(): void
    {
        $decorator = $this->createStub(FixtureQueryBusDecorator::class);
        $decorator->expects($this->once())
            ->method('decorate')
            ->with($this->isInstanceOf(QueryDispatcher::class))
            ->will($this->returnSelf());

        $container = $this->createMock(ContainerInterface::class);
        $builder = new QueryBusBuilder($container);
        $builder->addDecorator($decorator);

        $commandBus = $builder->build();
        $this->assertSame($decorator, $commandBus);
    }

    public function testBuildWithChainedDecorators(): void
    {
        $decoratorOne = $this->createStub(FixtureQueryBusDecorator::class);
        $decoratorOne->expects($this->once())
            ->method('decorate')
            ->with($this->isInstanceOf(QueryDispatcher::class))
            ->will($this->returnSelf());

        $decoratorTwo =  $this->createStub(FixtureQueryBusDecorator::class);
        $decoratorTwo->expects($this->once())
            ->method('decorate')
            ->with($decoratorOne)
            ->will($this->returnSelf());

        $container = $this->createMock(ContainerInterface::class);
        $builder = new QueryBusBuilder($container);
        $builder->addDecorator($decoratorOne, $decoratorTwo);

        $commandBus = $builder->build();
        $this->assertSame($decoratorTwo, $commandBus);
    }
}

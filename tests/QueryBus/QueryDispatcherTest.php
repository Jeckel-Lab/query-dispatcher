<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/12/2021
 */

namespace Tests\JeckelLab\QueryDispatcher\QueryBus;

use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use JeckelLab\Contract\Core\QueryDispatcher\ViewModel\ViewModel;
use JeckelLab\QueryDispatcher\Exception\HandlerNotFoundException;
use JeckelLab\QueryDispatcher\QueryBus\QueryDispatcher;
use JeckelLab\QueryDispatcher\Resolver\QueryHandlerResolverInterface;
use PHPUnit\Framework\TestCase;

class QueryDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $viewModel = $this->createMock(ViewModel::class);
        $query = $this->createMock(Query::class);
        $handler = $this->createMock(QueryHandler::class);
        $handler->expects($this->once())
            ->method('__invoke')
            ->with($query)
            ->willReturn($viewModel);

        $resolver = $this->createMock(QueryHandlerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn($handler);

        $dispatcher = new QueryDispatcher($resolver);

        $this->assertSame($viewModel, $dispatcher->dispatch($query));
    }

    /**
     * Test dispatch when resolver throw an Exception (no handler founds)
     */
    public function testDispatchWithErrorResolver(): void
    {
        $exception = new HandlerNotFoundException('foo bar', 'query', new \Exception);

        $query = $this->createMock(Query::class);
        $resolver = $this->createMock(QueryHandlerResolverInterface::class);
        $resolver->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willThrowException($exception);

        $this->expectException(HandlerNotFoundException::class);

        (new QueryDispatcher($resolver))->dispatch($query);
    }
}

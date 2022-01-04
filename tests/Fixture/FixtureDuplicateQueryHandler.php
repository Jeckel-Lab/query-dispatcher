<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 04/01/2022
 */

declare(strict_types=1);

namespace Tests\JeckelLab\QueryDispatcher\Fixture;

use JeckelLab\Contract\Core\QueryDispatcher\Exception\InvalidQueryTypeException;
use JeckelLab\Contract\Core\QueryDispatcher\Query\Query;
use JeckelLab\Contract\Core\QueryDispatcher\QueryHandler\QueryHandler;
use JeckelLab\Contract\Core\QueryDispatcher\ViewModel\ViewModel;

class FixtureDuplicateQueryHandler implements QueryHandler
{
    public static function getHandledQueries(): array
    {
        return [ FixtureQuery::class ];
    }

    public function __invoke(Query $query): ViewModel
    {
        return new FixtureViewModel();
    }
}

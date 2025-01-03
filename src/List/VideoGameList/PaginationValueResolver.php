<?php

declare(strict_types=1);

namespace App\List\VideoGameList;

use App\Model\ValueObject\Direction;
use App\Model\ValueObject\Sorting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTargetedValueResolver('pagination')]
final readonly class PaginationValueResolver implements ValueResolverInterface
{
    /**
     * resolve
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable <int, \App\List\VideoGameList\Pagination>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if ($argumentType !== Pagination::class) {
            return [];
        }

        if (!is_string($request->query->get('sorting','')) || !is_string($request->query->get('direction', ''))) {
            throw new \Exception('Erreur de pagination');
        }
        return [new Pagination(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10),
            Sorting::tryFromName($request->query->get('sorting', '')) ?? Sorting::ReleaseDate,
            Direction::tryFromName($request->query->get('direction', '')) ?? Direction::Descending,
        )];
    }
}

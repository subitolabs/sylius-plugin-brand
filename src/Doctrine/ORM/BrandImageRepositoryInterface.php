<?php

declare(strict_types=1);

namespace Loevgaard\SyliusBrandPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Loevgaard\SyliusBrandPlugin\Entity\BrandInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface BrandImageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $brandSlug
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder(string $brandSlug): QueryBuilder;

    /**
     * @param BrandInterface $brand
     * @param string $type
     *
     * @return iterable
     */
    public function createPaginatorForBrandAndType(BrandInterface $brand, string $type): iterable;
}

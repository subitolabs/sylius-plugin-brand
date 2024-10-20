<?php

declare(strict_types=1);

namespace Tests\Loevgaard\SyliusBrandPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Loevgaard\SyliusBrandPlugin\Model\BrandInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class BrandContext implements Context
{
    public function __construct(private readonly RepositoryInterface $brandRepository, private readonly FactoryInterface $brandFactory)
    {
    }

    /**
     * @Given The store has a brand :brandName
     */
    public function storeHasABrand($brandName): void
    {
        $brand = $this->createBrand($brandName);

        $this->saveBrand($brand);
    }

    private function createBrand(string $name): BrandInterface
    {
        /** @var BrandInterface $brand */
        $brand = $this->brandFactory->createNew();

        $brand->setName($name);
        $brand->setCode(strtolower($name));

        return $brand;
    }

    private function saveBrand(BrandInterface $brand): void
    {
        $this->brandRepository->add($brand);
    }
}

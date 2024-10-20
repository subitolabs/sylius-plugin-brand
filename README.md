# Sylius Brand Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

<a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="100"></a>

If you want to add a brand to your products this is the plugin to use. Use cases:
- Add brand logo to your product pages
- Filter by brand in the frontend or backend, i.e. product lists

## Screenshots

<details><summary>CLICK TO SEE</summary>

Menu:

![Screenshot showing admin menu](docs/images/admin-menu-with-brand.png)

Brand admin pages:

![Screenshot showing brand admin index page](docs/images/admin-brand-index.png)

![Screenshot showing brand admin update page](docs/images/admin-brand-update.png)

![Screenshot showing brand admin media tab at update page](docs/images/admin-brand-update-tab-media.png)

Products admin pages:

![Screenshot showing product admin index page with brand filter](docs/images/admin-product-index-filter-with-brand.png)

![Screenshot showing product admin index page with brand column](docs/images/admin-product-index-brand-column.png)

![Screenshot showing brand tab at product admin update page](docs/images/admin-product-update-tab-brand.png)

</details>

## Installation

### Step 1: Download the plugin

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
$ composer require loevgaard/sylius-brand-plugin
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.


### Step 2: Enable the plugin

Then, enable the plugin by adding it to the list of registered plugins/bundles
in `config/bundles.php` file of your project before (!) `SyliusGridBundle`:

```php
<?php

# config/bundles.php

return [
    // ...
    Loevgaard\SyliusBrandPlugin\LoevgaardSyliusBrandPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    // ...
];
```

### Step 3: Configure the plugin

```yaml
# config/packages/loevgaard_sylius_brand.yaml

imports:
    # ...
    - { resource: "@LoevgaardSyliusBrandPlugin/Resources/config/app/config.yaml" }

    # If you want to see Brand column at admin products list - uncomment next line
    # - { resource: "@LoevgaardSyliusBrandPlugin/Resources/config/grids/sylius_admin_product.yaml" }
```

```yaml
# config/routes/loevgaard_sylius_brand.yaml

loevgaard_sylius_brand:
    resource: "@LoevgaardSyliusBrandPlugin/Resources/config/routing.yaml"
```

### Step 4: Extend services and entities

#### Extend `Product`

- If you use `annotations` mapping:

    ```php
    <?php
    
    declare(strict_types=1);
    
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Loevgaard\SyliusBrandPlugin\Model\ProductInterface as LoevgaardSyliusBrandPluginProductInterface;
    use Loevgaard\SyliusBrandPlugin\Model\ProductTrait as LoevgaardSyliusBrandPluginProductTrait;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_product")
     */
    class Product extends BaseProduct implements LoevgaardSyliusBrandPluginProductInterface
    {
        use LoevgaardSyliusBrandPluginProductTrait;
    }
    ```
    
- If you use `xml` mapping:

    ```php
    <?php
    // src/Model/Product.php
    
    declare(strict_types=1);
    
    namespace App\Model;
    
    use Loevgaard\SyliusBrandPlugin\Model\ProductInterface as LoevgaardSyliusBrandPluginProductInterface;
    use Loevgaard\SyliusBrandPlugin\Model\ProductTrait as LoevgaardSyliusBrandPluginProductTrait;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    
    class Product extends BaseProduct implements LoevgaardSyliusBrandPluginProductInterface
    {
        use LoevgaardSyliusBrandPluginProductTrait;
        
        // ...
    }
    ```
    
    ```xml
    <?xml version="1.0" encoding="UTF-8"?>
    
    <!-- config/doctrine/model/Product.orm.xml -->
    
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
    
        <mapped-superclass name="App\Model\Product" table="sylius_product">
            <many-to-one field="brand" target-entity="Loevgaard\SyliusBrandPlugin\Model\BrandInterface" inversed-by="products">
                <join-column name="brand_id" on-delete="SET NULL" />
            </many-to-one>
        </mapped-superclass>
    
    </doctrine-mapping>
    ```

#### Extend `ProductRepository`

```php
<?php
# src/Doctrine/ORM/ProductRepository.php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Loevgaard\SyliusBrandPlugin\Doctrine\ORM\ProductRepositoryInterface as LoevgaardSyliusBrandPluginProductRepositoryInterface;
use Loevgaard\SyliusBrandPlugin\Doctrine\ORM\ProductRepositoryTrait as LoevgaardSyliusBrandPluginProductRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository implements LoevgaardSyliusBrandPluginProductRepositoryInterface
{
    use LoevgaardSyliusBrandPluginProductRepositoryTrait;

    // ...
}
```

#### Configure

```yaml
config/services.yaml

sylius_product:
    resources:
        product:
            classes:
                model: App\Model\Product # Or App\Entity\Product
                repository: App\Doctrine\ORM\ProductRepository

```  

### Step 5: Update your database schema

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

## Fixtures

- Include prefedined brand fixtures to play with on your dev environment:

    ```yaml
    # config/packages/loevgaard_sylius_brand.yaml
    
    imports:
        - { resource: "@LoevgaardSyliusBrandPlugin/Resources/config/app/fixtures.yaml" }
    ```

- Or write your own:

  - Add a new yaml file to the folder `config/packages` and name it as you wish, e.g. `my_own_fixtures.yaml`.

  - Fill this yaml with your own brand fixtures and don't forget to declare the definition of
    your product(s) before this brand definition or use existing product(s) code.
    
    ```
    # config/packages/my_own_fixtures.yaml
    
    sylius_fixtures:
       suites:
           my_own_brand_fixtures:
                fixtures:
                    loevgaard_sylius_brand_plugin_brand:
                        options:
                            custom:
                                my_brand:
                                    name: 'My brand'
                                    code: 'my-brand'
                                    images:
                                        local_image:
                                            type: logo
                                            path: images/my-brand/logo.jpg
                                        3rd_party_plugin_image:
                                            type: black-and-white
                                            path: '@SomePlugin/Resources/images/my-brand/black-and-white.jpg'
                                    products:
                                      - product_code_1
                                      - product_code_2
                                      - product_code_3
    ```

    See example at `src/Resources/config/app/fixtures.yaml`.

 3. Load your fixtures

    ```bash
    php bin/console sylius:fixture:load my_own_brand_fixtures
    ```

## Installation and usage for plugin development

To run test application to play with just type `composer try`.

### Sonata blocks available

* `loevgaard_sylius_brand.admin.brand.create.tab_details`
* `loevgaard_sylius_brand.admin.brand.update.tab_details`
* `loevgaard_sylius_brand.admin.brand.create.tab_media`
* `loevgaard_sylius_brand.admin.brand.update.tab_media`

### Events available

* `loevgaard_sylius_brand.menu.admin.brand.form` to customize `Brand` 
  admin form like you usually do with `Product` form via
  `sylius.menu.admin.product.form` event.

## Contribute

Please, run `composer all` to run all checks and tests before committing.

### Contribute by translating

We use the same service as Sylius for translating, namely [Crowdin](https://crowdin.com/project/sylius-brand-plugin). You can help out by translating this project into your mother tongue ;)

Thanks!

[ico-version]: https://poser.pugx.org/loevgaard/sylius-brand-plugin/v/stable
[ico-license]: https://poser.pugx.org/loevgaard/sylius-brand-plugin/license
[ico-github-actions]: https://github.com/loevgaard/SyliusBrandPlugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/loevgaard/SyliusBrandPlugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2FSyliusPluginSkeleton%2F3.x

[link-packagist]: https://packagist.org/packages/loevgaard/sylius-brand-plugin
[link-github-actions]: https://github.com/loevgaard/SyliusBrandPlugin/actions
[link-code-coverage]: https://codecov.io/gh/loevgaard/SyliusBrandPlugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/loevgaard/SyliusBrandPlugin/3.x

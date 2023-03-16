# Laravel HasMany Sync

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fliva/laravel-hasmany-sync?style=flat-square)](https://packagist.org/packages/fliva/laravel-hasmany-sync)
[![License](https://img.shields.io/packagist/l/f-liva/laravel-hasmany-sync?style=flat-square)](license.md)
[![Codecov](https://img.shields.io/codecov/c/github/f-liva/laravel-hasmany-sync?style=flat-square)](https://codecov.io/gh/f-liva/laravel-hasmany-sync)
[![TravisCI](https://img.shields.io/travis/f-liva/laravel-hasmany-sync?style=flat-square)](https://travis-ci.org/f-liva/laravel-hasmany-sync)
[![StyleCI](https://styleci.io/repos/202400425/shield)](https://styleci.io/repos/202400425)

With this package you will be able to synchronize your **HasMany** relationships just as you normally would for **BelongsToMany** relationships. The usage is the same, follow the official Laravel documentation.

Thanks to [korridor/laravel-has-many-sync](https://github.com/korridor/laravel-has-many-sync) for the original idea. This package reconstructs the mechanism using the original Laravel approach and operation.

## Installation

You can install the package via composer with following command:

```bash
composer require f-liva/laravel-hasmany-sync
```
## Usage

Configure your **HasMany** relationship as you normally would

```php
class Customer extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }
}
```

Synchronize the relationship as you normally would in a **BelongsToMany** relationship

```php
$customer->contacts()->sync([1, 2, 3]);

// Or with attributes...

$customer->contacts()->sync([1 => ['name' => 'Foo'], 2 => ['name' => 'Bar'], 3]);
```

The sync method accepts the same parameters described in [Eloquent Relationships - Syncing Relations](https://laravel.com/docs/9.x/eloquent-relationships#syncing-associations)

You can also synchronize the relationship without knowing the identifiers of the related records. In this case, specify in the call the `syncRelatedKey: false` parameter.

```php
$customer->contacts()->sync([1, 2, 3], syncRelatedKey: false);

// Or with attributes...

$customer->contacts()->sync([['name' => 'Foo'], ['name' => 'Bar'], ...]);
```

## Contributing

I am open for suggestions and contributions. Just create an issue or a pull request.

### Testing

The `composer test` command runs all tests with [phpunit](https://phpunit.de/).
The `composer test-coverage` command runs all tests with phpunit and creates a coverage report into the `coverage` folder.

### Codeformatting/Linting

The `composer fix` command formats the code with [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
The `composer lint` command checks the code with [phpcs](https://github.com/squizlabs/PHP_CodeSniffer).

## License

This package is licensed under the MIT License (MIT). Please see [license file](license.md) for more information.


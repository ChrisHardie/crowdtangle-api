
# A minimal PHP implementation of the CrowdTangle API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrishardie/crowdtangle-api.svg?style=flat-square)](https://packagist.org/packages/chrishardie/crowdtangle-api)
[![Total Downloads](https://img.shields.io/packagist/dt/chrishardie/crowdtangle-api.svg?style=flat-square)](https://packagist.org/packages/chrishardie/crowdtangle-api)

This is a minimal PHP implementation of the [CrowdTangle API](https://help.crowdtangle.com/en/articles/1189612-crowdtangle-api). It contains a subset of the methods available. I am open to PRs that add extra methods to the client.

Here are a few examples on how you can use the package:

```php
$client = new ChrisHardie\CrowdtangleApi\Client($accessToken);

// get lists
$client->getLists();

// get accounts in a list
$client->getAccountsForList($listId);

// get posts
$client->getPosts([
    'accounts' => '12345678',
    'startDate' => '2022-03-01',
]);

// get a single post
$client->getPost($postId);
```

View the [full CrowdTangle API Documentation](https://github.com/CrowdTangle/API/wiki) for details on available parameters and syntax.

## Installation

You can install the package via composer:

```bash
composer require chrishardie/crowdtangle-api
```

## Usage

Here are the API methods currently supported:

* `getLists()` - Retrieve the lists, saved searches and saved post lists for an account
* `getAccountsForList($listId, $parameters, $maxRecords)` - Retrieve the accounts for a given list
* `getPosts($parameters, $maxRecords)` - Retrieve a set of posts for the given parameters
* `getPost($postId)` - Retrieves a specific post

In most cases the library is simply passing the required arguments to the CrowdTangle API.

In the case of methods that support pagination (currently, `getAccountsForList()` and  `getPosts()`), by default this library will attempt to retrieve all records on all pages, 100 at a time up to 1000 maximum. You can change this by passing a lower value for `$maxRecords`. Note that CrowdTangle API throttling limits may apply.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Chris Hardie](https://github.com/ChrisHardie)
- [All Contributors](../../contributors)

Inspired and structured after Spatie's [Dropbox API](https://github.com/spatie/dropbox-api).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

# Magento 2 Sage200 API

[![Github license](https://img.shields.io/github/license/SelectCo/magento2-module-sage200-api "Github license")](https://github.com/SelectCo/magento2-module-sage200-api/blob/main/LICENSE)
[![Open issues](https://img.shields.io/github/issues/SelectCo/magento2-module-sage200-api "Open issues")](https://github.com/SelectCo/magento2-module-sage200-api/issues)
[![Open Pull Requests](https://img.shields.io/github/issues-pr/SelectCo/magento2-module-sage200-api "Open Pull Requests")](https://github.com/SelectCo/magento2-module-sage200-api/pulls)
[![Last commit](https://img.shields.io/github/last-commit/SelectCo/magento2-module-sage200-api "Last commit")](https://github.com/SelectCo/magento2-module-sage200-api/commits/main)

An unofficial API client to interact with the Sage200 API.


## Requirements

- Sage200
    - Sage200 API setup and configured.
    - Developer subscription key
    - Client ID & Client Secret
- Magento 2
- PHP ^7.3


## Sage 200 API Credentials Request Form
Admin configuration for Refresh Token Expiry Days must match the value on Sage 200 API Credentials Request Form.

The callback URL entered on the request form must be ```https://{SUBDOMAIN}.{DOMAIN}/{ADMIN_ROUTE}/s200/oauth/callback```

e.g. ```https://shop.example.com/admin_5fj7t/s200/oauth/callback```


## Authentication

This client does not provide any authenticating methods.
The connector is expecting a bearer token which is used to authenticate requests.
The bearer token along with the subscription key must be passed to the connector on initialization.

For more information, please refer to the official [Sage200 API Documentation](https://developer.sage.com/200/api/).


## Installation

Add GitHub repo to your composer.json repositories.
```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/SelectCo/magento2-module-sage200-api.git"
        }
    ]
```
Require package with composer.
```shell
composer require select-co\module-sage200-api
```


## Configuration
Admin configurations must be set before use.

- API OAuth Details
  - Client Id
  - Client Secret
  - Developer Subscription Key
  - X-Site Id
  - X-Company Id

Default values used for:-
- Sage200 Base Url
- Authorise URL
- Access Token URL
- Resource Owner URL
- Scope Access


## Token Management
Token management is done through Admin => System => Other Settings => Sage200 Token Management where you can refresh, or get a new token.

Details of a valid token are displayed here with available companies.


## Usage
```php
$results = json_decode(
    $this->connector->send(
        'sop_orders',
        'GET',
        $query
    ), true
);
```
```php
$results = json_decode(
    $this->connector->send(
      'sop_orders/' . $id,
      'GET',
      $query
    ), true
);
```


## Changelog

Please see [CHANGELOG](CHANGELOG) for more information on what has changed recently.


## Resources

[Sage 200 API Documentation](https://developer.sage.com/200/reference/)
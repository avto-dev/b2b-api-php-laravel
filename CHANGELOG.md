# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog] and this project adheres to [Semantic Versioning].

## [v2.4.0] - 2018-11-13

### Fixed

- Return type of `webhook` option has type object [#6]

## [v2.3.0] - 2018-08-15

### Added

- Config settings for manage web-hooks

## [v2.2.0] - 2018-06-28

### Added

- `is_force` flag to `makeReport` method [#2]

### Changed

- Issues & PR templates
- Update requirement avto-dev/b2b-api-php to version `2.3.0`

## [v2.1.0] - 2018-06-10

### Changed

- CI config updated
- Required minimal PHPUnit version now `5.7.10`
- Required minimal Laravel version now `5.4.3`
- Disabled HTML coverage report (CI errors)
- Unimportant PHPDoc blocks removed

[v2.4.0]: https://github.com/avto-dev/b2b-api-php-laravel/compare/v2.3.0...eldario:some-fix
[v2.3.0]: https://github.com/avto-dev/b2b-api-php-laravel/compare/v2.2.0...v2.3.0
[v2.2.0]: https://github.com/avto-dev/b2b-api-php-laravel/compare/v2.1.0...v2.2.0
[v2.1.0]: https://github.com/avto-dev/b2b-api-php-laravel/compare/v2.0.12...v2.1.0

[#2]: https://github.com/avto-dev/b2b-api-php-laravel/issues/2
[#6]: https://github.com/avto-dev/b2b-api-php-laravel/issues/6

[Keep a Changelog]: http://keepachangelog.com/en/1.0.0/
[Semantic Versioning]: http://semver.org/spec/v2.0.0.html

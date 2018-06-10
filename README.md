<p align="center">
  <img src="https://laravel.com/assets/img/components/logo-laravel.svg" alt="Laravel" width="240" />
</p>

# Расширенный клиент для работы с B2B API

[![Version][badge_version]][link_packagist]
[![Build Status][badge_build_status]][link_build_status]
[![StyleCI][badge_styleci]][link_styleci]
[![Coverage][badge_coverage]][link_coverage]
[![Code Quality][badge_quality]][link_coverage]
[![Issues][badge_issues]][link_issues]
[![License][badge_license]][link_license]
[![Downloads count][badge_downloads_count]][link_packagist]

При помощи данного пакета вы сможете интегрировать сервис по работе с B2B API в ваше Laravel приложение с помощью нескольких простых шагов.

> Более подробно о работе самого клиента по работе с B2B API смотрите в [его репозитории][b2b_api_client].

## Установка

Для установки данного пакета выполните в терминале следующую команду:

```shell
$ composer require avto-dev/b2b-api-php-laravel "^2.0.11"
```

> Для этого необходим установленный `composer`. Для его установки перейдите по [данной ссылке][getcomposer].

> Обратите внимание на то, что необходимо фиксировать мажорную версию устанавливаемого пакета.

> Данный пакет **не использует** автоматическую регистрацию своего сервис-провайдера *(данная возможность появилась в Laravel v5.5.x)*. Так же рекомендуется создавать **свой** класс сервис-провайдера, наследовать его от поставляемого с данным пакетом, и уже его регистрировать. Причина данной рекомендации крайне проста - таким образом вы получаете более тонкие возможности переопределения логики инициализации контейнеров и перекрытия поставляемых методы - своими.

После чего создайте в директории `./app/Providers` файл `B2BApiServiceProvider.php` со следующим содержимым:

```php
<?php

namespace App\Providers;

use AvtoDev\B2BApiLaravel\B2BApiServiceProvider as VendorB2BApiServiceProvider;

/**
 * Class B2BApiServiceProvider.
 */
class B2BApiServiceProvider extends VendorB2BApiServiceProvider
{
    //
}
```

Затем зарегистрируйте этот сервис-провайдер в секции `providers` файла `./config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\B2BApiServiceProvider::class,
]
```

И "опубликуйте" необходимые для пакета ресурсы с помощью команды:

```shell
$ ./artisan vendor:publish --provider="App\Providers\B2BApiServiceProvider"
```

> Данная команда создаст файл `./config/b2b-api-client.php` с настройками "по умолчанию", которые вам следует переопределить на свои.

После чего откройте файл `./config/b2b-api-client.php` и укажите в нем ваши реквизиты для подключения к сервису B2B API.

> С новыми версиями пакета могут добавляться новые опции в конфигурационном файле. Пожалуйста, не забывайте время от времени проверять этот момент.

## Использование

Данный пакет регистрирует 2 IoC контейнера:

 * Репозиторий типов отчетов: `AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository`
 * Сервис по работе с B2B API: `AvtoDev\B2BApiLaravel\B2BApiService`

**Репозиторий типов отчетов** отвечает за первоначальную загрузку данных о типах отчетов из конфигурационного файла, и предоставляет удобный доступ у ним *(методы проверки их наличия, извлечения, и так далее)*.

Доступ к нему осуществляется как с помощью непосредственного извлечения по имени класса или алиасу, так и с помощью фасада `ReportTypesRepositoryFacade`.

**Сервис по работе с B2B API** предназначен для как для реализации удобного доступа к инстансу самого клиента с помощью метода `->client()`, так и реализует удобные методы по базовым операциям с отчетами *(такими как создание, получение контента и обновление данных в отчете, без необходимости ручной генерации токена авторизации)*. Так же он содержит и другие методы, о чем смотрите исходный код сервиса.

> Более подробно о том, как работать с клиентом смотрите в [данном репозитории][b2b_api_client].

Доступ к нему так же осуществляется как с помощью непосредственного извлечения по имени класса или алиасу, так и с помощью фасада `B2BApiServiceFacade`.

### События

Вы можете установить свои слушатели на следующие события:

 * `AvtoDev\B2BApiLaravel\Events\BeforeRequestSending` - происходит **перед тем**, как осуществляется запрос к сервису B2B API;
 * `AvtoDev\B2BApiLaravel\Events\AfterRequestSending` - происходит **после того**, как был осуществлен запрос к сервису B2B API.
 
Более подробную информацию о том, как можно использовать слушателей событий вы можете найти по [этой ссылке][laravel_events].

## Тестирование

Для тестирования данного пакета используется фреймворк `phpunit`. Для запуска тестов выполните в терминале:

```shell
$ git clone git@github.com:avto-dev/b2b-api-php-laravel.git ./b2b-api-php-laravel && cd $_
$ composer update --dev
$ composer test
```

## Поддержка и развитие

Если у вас возникли какие-либо проблемы по работе с данным пакетом, пожалуйста, создайте соответствующий `issue` в данном репозитории.

> Имейте в виду, что если проблемы связаны с работой B2B API клиента (а не сервиса по работе с ним) - необходимо создать `issue` в его репозитории по [данной ссылке][b2b_api_client].

Если вы способны самостоятельно реализовать тот функционал, что вам необходим - создайте PR с соответствующими изменениями. Крайне желательно сопровождать PR соответствующими тестами, фиксирующими работу ваших изменений. После проверки и принятия изменений будет опубликована новая минорная версия.

## Лицензирование

Код данного пакета распространяется под лицензией [MIT][link_license].

[badge_version]:https://img.shields.io/packagist/v/avto-dev/b2b-api-php-laravel.svg?style=flat&maxAge=30
[badge_downloads_count]:https://img.shields.io/packagist/dt/avto-dev/b2b-api-php-laravel.svg?style=flat&maxAge=30
[badge_license]:https://img.shields.io/packagist/l/avto-dev/b2b-api-php-laravel.svg?style=flat&maxAge=30
[badge_build_status]:https://scrutinizer-ci.com/g/avto-dev/b2b-api-php-laravel/badges/build.png?b=master
[badge_styleci]:https://styleci.io/repos/106786234/shield
[badge_coverage]:https://scrutinizer-ci.com/g/avto-dev/b2b-api-php-laravel/badges/coverage.png?b=master
[badge_quality]:https://scrutinizer-ci.com/g/avto-dev/b2b-api-php-laravel/badges/quality-score.png?b=master
[badge_issues]:https://img.shields.io/github/issues/avto-dev/b2b-api-php-laravel.svg?style=flat&maxAge=30
[link_packagist]:https://packagist.org/packages/avto-dev/b2b-api-php-laravel
[link_styleci]:https://styleci.io/repos/106786234/
[link_license]:https://github.com/avto-dev/b2b-api-php-laravel/blob/master/LICENSE
[link_build_status]:https://scrutinizer-ci.com/g/avto-dev/b2b-api-php-laravel/build-status/master
[link_coverage]:https://scrutinizer-ci.com/g/avto-dev/b2b-api-php-laravel/?branch=master
[link_issues]:https://github.com/avto-dev/b2b-api-php-laravel/issues
[getcomposer]:https://getcomposer.org/download/
[b2b_api_client]:https://github.com/avto-dev/b2b-api-php
[laravel_events]:https://laravel.com/docs/5.5/events

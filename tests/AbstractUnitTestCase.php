<?php

namespace AvtoDev\B2BApiLaravel\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use AvtoDev\B2BApiLaravel\B2BApiServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

abstract class AbstractUnitTestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Register our service-provider manually
        $app->register(B2BApiServiceProvider::class);

        return $app;
    }

    /**
     * @param Application $app
     *
     * @return ConfigRepository
     */
    protected function getConfigRepository(Application $app)
    {
        return $app->make('config');
    }

    /**
     * Assert value is float.
     *
     * @param $value
     */
    protected function assertIsFloat($value)
    {
        $this->assertTrue(is_float($value), 'Value has not float type');
    }

    /**
     * Calls a instance method (public/private/protected) by its name.
     *
     * @param object $object
     * @param string $method_name
     * @param array  $args
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public function callMethod($object, $method_name, array $args = [])
    {
        $class  = new \ReflectionClass($object);

        $method = $class->getMethod($method_name);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}

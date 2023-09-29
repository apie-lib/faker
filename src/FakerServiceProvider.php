<?php
namespace Apie\Faker;

use Apie\ServiceProviderGenerator\UseGeneratedMethods;
use Illuminate\Support\ServiceProvider;

/**
 * This file is generated with apie/service-provider-generator from file: faker.yaml
 * @codecoverageIgnore
 */
class FakerServiceProvider extends ServiceProvider
{
    use UseGeneratedMethods;

    public function register()
    {
        $this->app->singleton(
            \Faker\Generator::class,
            function ($app) {
                return call_user_func(
                    'Apie\\Common\\Wrappers\\GeneralServiceFactory::createFaker',
                    $this->getTaggedServicesIterator('apie.faker')
                );
                
            }
        );
        $this->app->singleton(
            \Apie\Faker\Command\ApieSeedCommand::class,
            function ($app) {
                return new \Apie\Faker\Command\ApieSeedCommand(
                    $app->make(\Apie\Core\BoundedContext\BoundedContextHashmap::class),
                    $app->make('apie')
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Faker\Command\ApieSeedCommand::class,
            array(
              0 => 'console.command',
            )
        );
        $this->app->tag([\Apie\Faker\Command\ApieSeedCommand::class], 'console.command');
        $this->app->singleton(
            \Apie\Faker\Datalayers\FakerDatalayer::class,
            function ($app) {
                return new \Apie\Faker\Datalayers\FakerDatalayer(
                    $app->make(\Faker\Generator::class),
                    $app->make(\Apie\Core\Datalayers\Search\LazyLoadedListFilterer::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Faker\Datalayers\FakerDatalayer::class,
            array(
              0 => 'apie.datalayer',
            )
        );
        $this->app->tag([\Apie\Faker\Datalayers\FakerDatalayer::class], 'apie.datalayer');
        $this->app->bind('apie.faker', \Faker\Generator::class);
        
        
    }
}

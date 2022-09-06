<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Infra\ClassMapInflector;
use Workshop\Domains\Wallet\Infra\RandomNumberDecorator;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;

class WalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(WalletMessageRepository::class, function (Application $application){
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(
                    classNameInflector: new ExplicitlyMappedClassNameInflector(config('wallet.class-map')),
                    payloadSerializer: new ObjectMapperPayloadSerializer()
                ),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(),
                new MessageDecoratorChain(
                    new DefaultHeadersDecorator(
                        inflector: $inflector = new ExplicitlyMappedClassNameInflector(config('wallet.class-map'))
                    ),
                    new RandomNumberDecorator(),
                ),
                $inflector,
            );
        });
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(base_path('domains/Wallet/infra/config/wallet.php'), 'wallet');
    }
}

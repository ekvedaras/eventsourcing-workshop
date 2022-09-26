<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\ObjectMapperPayloadSerializer;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Decorators\EventIDDecorator;
use Workshop\Domains\Wallet\Infra\ClassMapInflector;
use Workshop\Domains\Wallet\Infra\EloquentTransactionsReadModelRepository;
use Workshop\Domains\Wallet\Infra\EloquentWalletBalanceRepository;
use Workshop\Domains\Wallet\Infra\NotificationService;
use Workshop\Domains\Wallet\Infra\RandomNumberDecorator;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use Workshop\Domains\Wallet\Projectors\WalletBalanceProjector;
use Workshop\Domains\Wallet\Reactors\ReportHighBalanceReactor;
use Workshop\Domains\Wallet\Tests\InMemoryNotificationService;

class WalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NotificationService::class, InMemoryNotificationService::class);
        $this->app->bind(TransactionsReadModelRepository::class, EloquentTransactionsReadModelRepository::class);
        $this->app->bind(WalletBalanceRepository::class, EloquentWalletBalanceRepository::class);

        $this->app->bind(WalletMessageRepository::class, function (Application $application){
            return new WalletMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_messages',
                serializer: new ConstructingMessageSerializer(classNameInflector: new ExplicitlyMappedClassNameInflector(config('wallet.class-map'))),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(
                    new SynchronousMessageDispatcher(
                        $this->app->make(TransactionsProjector::class),
                        $this->app->make(WalletBalanceProjector::class),
                        $this->app->make(ReportHighBalanceReactor::class),
                    )
                ),
                new MessageDecoratorChain(
                    new EventIDDecorator(),
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

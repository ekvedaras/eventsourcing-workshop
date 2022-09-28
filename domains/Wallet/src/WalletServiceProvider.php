<?php

namespace Workshop\Domains\Wallet;

use EventSauce\Clock\Clock;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\ExplicitlyMappedClassNameInflector;
use EventSauce\EventSourcing\MessageDecoratorChain;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use EventSauce\EventSourcing\Upcasting\UpcasterChain;
use EventSauce\EventSourcing\Upcasting\UpcastingMessageSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Robertbaelde\PersistingMessageBus\DefaultMessageDecorator;
use Robertbaelde\PersistingMessageBus\Laravel\IlluminateMessageRepository;
use Robertbaelde\PersistingMessageBus\MessageBus;
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
use Workshop\Domains\Wallet\PublicEvents\Balance\Balance;
use Workshop\Domains\Wallet\Reactors\PublishBalanceEventsReactor;
use Workshop\Domains\Wallet\Reactors\ReportHighBalanceReactor;
use Workshop\Domains\Wallet\Tests\InMemoryNotificationService;
use Workshop\Domains\Wallet\Upcasters\TokenAmountCorrectionsUpcaster;
use Workshop\Domains\Wallet\Upcasters\TransactedAtUpcaster;

class WalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NotificationService::class, InMemoryNotificationService::class);
        $this->app->bind(TransactionsReadModelRepository::class, EloquentTransactionsReadModelRepository::class);
        $this->app->bind(WalletBalanceRepository::class, EloquentWalletBalanceRepository::class);

        $this->app->bind(WalletMessageRepository::class, function (Application $application) {
            return new WalletMessageRepository(
                connection:  $application->make(DatabaseManager::class)->connection(),
                tableName:   'wallet_messages',
                serializer:  new UpcastingMessageSerializer(
                                 eventSerializer: new ConstructingMessageSerializer(
                                                      classNameInflector: $this->getClassNameInflector()
                                                  ),
                                 upcaster:        new UpcasterChain(
                                                      new TransactedAtUpcaster(),
                                                      new TokenAmountCorrectionsUpcaster(app(Clock::class)),
                                                  )
                             ),
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
                        $this->app->make(PublishBalanceEventsReactor::class),
                        $this->app->make(ReportHighBalanceReactor::class),
                    )
                ),
                new MessageDecoratorChain(
                    new EventIDDecorator(),
                    new DefaultHeadersDecorator(
                        inflector: $this->getClassNameInflector()
                    ),
                    new RandomNumberDecorator(),
                ),
                $this->getClassNameInflector(),
            );
        });

        $this->app->bind('WalletPublicEvents', function (Application $application) {
            return new IlluminateMessageRepository(
                connection: $application->make(DatabaseManager::class)->connection(),
                tableName: 'wallet_public_events',
                tableSchema: new \Robertbaelde\PersistingMessageBus\DefaultTableSchema(),
            );
        });

        $this->app->bind(MessageDispatcher::class, function (Application $application) {
            return new \Robertbaelde\PersistingMessageBus\MessageDispatcher(
                messageBus: new MessageBus(
                    topic: new Balance(),
                    messageRepository: $application->make('WalletPublicEvents'),
                ),
                messageDecorator: new DefaultMessageDecorator($application->make(Clock::class)),
            );
        });
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(base_path('domains/Wallet/infra/config/wallet.php'), 'wallet');
    }

    /**
     * @return ExplicitlyMappedClassNameInflector
     */
    private function getClassNameInflector(): ExplicitlyMappedClassNameInflector
    {
        return new ExplicitlyMappedClassNameInflector(config('wallet.class-map'));
    }
}

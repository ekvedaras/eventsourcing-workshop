<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use EventSauce\UuidEncoding\StringUuidEncoder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
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
                serializer: new ConstructingMessageSerializer(),
                tableSchema: new DefaultTableSchema(),
                uuidEncoder: new StringUuidEncoder(),
            );
        });

        $this->app->bind(WalletRepository::class, function () {
            return new WalletRepository(
                $this->app->make(WalletMessageRepository::class),
                new MessageDispatcherChain(),
                new RandomNumberDecorator(new DefaultHeadersDecorator()),
                new DotSeparatedSnakeCaseInflector(),
            );
        });
    }
}

<?php

namespace Workshop\Domains\Wallet\Reactors;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\PublicEvents\Balance\BalanceUpdated;

class PublishBalanceEventsReactor extends EventConsumer
{
    public function __construct(
        private readonly WalletBalanceRepository $walletBalance,
        private readonly MessageDispatcher $messageDispatcher,
    )
    {
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $message = new BalanceUpdated(
            tokens: $this->walletBalance->getBalance($message->aggregateRootId()) + $event->tokens,
        );

        $this->messageDispatcher->dispatch($message);
    }

    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $message = new BalanceUpdated(
            tokens: $this->walletBalance->getBalance($message->aggregateRootId()) - $event->tokens,
        );

        $this->messageDispatcher->dispatch($message);
    }
}
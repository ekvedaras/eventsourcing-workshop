<?php

namespace Workshop\Domains\Wallet\Projectors;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;

class WalletBalanceProjector extends EventConsumer
{
    public function __construct(private readonly WalletBalanceRepository $walletBalance)
    {
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $this->walletBalance->incrementBalance($message->aggregateRootId(), $event->tokens);
    }

    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $this->walletBalance->decrementBalance($message->aggregateRootId(), $event->tokens);
    }
}
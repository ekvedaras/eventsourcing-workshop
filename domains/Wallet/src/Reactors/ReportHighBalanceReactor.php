<?php

namespace Workshop\Domains\Wallet\Reactors;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Infra\NotificationService;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;

class ReportHighBalanceReactor extends EventConsumer
{
    public const threshold = 100;

    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly WalletBalanceRepository $walletBalance,
    )
    {
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $newBalance = $this->walletBalance->getBalance($message->aggregateRootId());

        if ($newBalance >= self::threshold && $newBalance - $event->tokens < self::threshold) {
            $this->notificationService->sendWalletHighBalanceNotification($message->aggregateRootId());
        }
    }
}
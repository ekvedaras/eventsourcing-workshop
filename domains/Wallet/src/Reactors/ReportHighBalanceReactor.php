<?php

namespace Workshop\Domains\Wallet\Reactors;

use Carbon\Carbon;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\NotificationService;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;

class ReportHighBalanceReactor extends EventConsumer
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        
    }

    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {

    }
}
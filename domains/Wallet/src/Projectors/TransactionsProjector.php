<?php

namespace Workshop\Domains\Wallet\Projectors;

use Carbon\Carbon;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\ReplayingMessages\TriggerBeforeReplay;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;

final class TransactionsProjector extends EventConsumer implements TriggerBeforeReplay
{
    public function __construct(private readonly TransactionsReadModelRepository $transactions)
    {
    }

    public function beforeReplay(): void
    {
        $this->transactions->flush();
    }

    public function handleTokensDeposited(TokensDeposited $event, Message $message): void
    {
        $this->transactions->addTransaction(
            eventId:      $message->header(Header::EVENT_ID),
            walletId:     $message->aggregateRootId()->toString(),
            amount:       $event->tokens,
            transactedAt: Carbon::createFromImmutable($message->timeOfRecording()),
            description: $event->description,
        );
    }

    public function handleTokensWithdrawn(TokensWithdrawn $event, Message $message): void
    {
        $this->transactions->addTransaction(
            eventId:      $message->header(Header::EVENT_ID),
            walletId:     $message->aggregateRootId()->toString(),
            amount:       -$event->tokens,
            transactedAt: Carbon::createFromImmutable($message->timeOfRecording()),
            description: $event->description,
        );
    }
}
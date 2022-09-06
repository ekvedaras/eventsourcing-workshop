<?php

namespace Workshop\Domains\Wallet\Infra;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class RandomNumberDecorator implements MessageDecorator
{
    public function __construct(private readonly MessageDecorator|null $next)
    {
    }

    public function decorate(Message $message): Message
    {
        $message = $message->withHeader('random-number', mt_rand());

        return $this->next?->decorate($message) ?? $message;
    }
}
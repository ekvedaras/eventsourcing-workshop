<?php

namespace Workshop\Domains\Wallet\Infra;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class RandomNumberDecorator implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        return $message->withHeader('random-number', mt_rand());
    }
}
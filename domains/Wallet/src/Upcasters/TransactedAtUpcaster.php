<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;
use Illuminate\Support\Arr;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;

class TransactedAtUpcaster implements Upcaster
{
    private const eventsToUpcast = ['tokens-withdrawn', 'tokens-deposited'];

    public function upcast(array $message): array
    {
        if (!in_array($message['headers'][Header::EVENT_TYPE], self::eventsToUpcast)) {
            return $message;
        }

        $message['payload']['transacted_at'] ??= $message['headers'][Header::TIME_OF_RECORDING] ?? null;

        return $message;
    }
}
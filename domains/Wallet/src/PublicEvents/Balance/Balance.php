<?php

namespace Workshop\Domains\Wallet\PublicEvents\Balance;

use Robertbaelde\PersistingMessageBus\BaseTopic;

class Balance extends BaseTopic
{
    public const BalanceUpdated = BalanceUpdated::class;

    public function getMessages(): array
    {
        return [
            'BalanceUpdated' => self::BalanceUpdated,
        ];
    }

    public function getName(): string
    {
        return 'Balance';
    }
}
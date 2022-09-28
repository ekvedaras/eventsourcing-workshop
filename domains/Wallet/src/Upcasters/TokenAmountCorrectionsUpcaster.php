<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;
use Illuminate\Contracts\Config\Repository as Config;

class TokenAmountCorrectionsUpcaster implements Upcaster
{
    private const eventsToUpcast = ['tokens-withdrawn', 'tokens-deposited'];

    public function __construct(private readonly Config $config)
    {
    }

    public function upcast(array $message): array
    {
        if (!in_array($message['headers'][Header::EVENT_TYPE], self::eventsToUpcast)) {
            return $message;
        }

        $message['payload']['tokens'] = $this->config->get('wallet.corrections.' . $message['headers'][Header::AGGREGATE_ROOT_ID])
                                        ?? $message['payload']['tokens'];

        return $message;
    }
}
<?php

use Workshop\Domains\Wallet\Events\FailedToWithdrawDueToInsufficientTokens;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

return [
    'class-map' => [
        Wallet::class => 'wallet',
        WalletId::class => 'wallet-id',

        TokensDeposited::class => 'tokens-deposited',
        TokensWithdrawn::class => 'tokens-withdrawn',
        FailedToWithdrawDueToInsufficientTokens::class => 'failed-to-withdraw-due-to-insufficient-tokens',
    ],
];
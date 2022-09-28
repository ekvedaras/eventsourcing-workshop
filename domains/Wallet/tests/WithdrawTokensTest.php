<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Events\FailedToWithdrawDueToInsufficientTokens;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Exceptions\BalanceException;
use Workshop\Domains\Wallet\Wallet;

class WithdrawTokensTest extends WalletTestCase
{
    public function test_it_can_withdraw_tokens(): void
    {
        $this->given(new TokensDeposited(100, 'demo', $this->currentTime()))
            ->when(fn(Wallet $wallet) => $wallet->withdraw(100, 'demo', $this->clock()))
            ->then(new TokensWithdrawn(100, 'demo', $this->currentTime()));
    }

    public function test_it_cannot_overdraw_tokens(): void
    {
        $this->given(new TokensDeposited(100, 'demo', $this->currentTime()))
            ->when(function (Wallet $wallet) {
                try {
                    $wallet->withdraw(101, 'demo', $this->clock());
                    $this->fail('Balance exception was expected');
                } catch (BalanceException $exception) {
                    $this->assertEquals(BalanceException::insufficientTokens(
                        attemptedToWithdraw: 101,
                        balance: 100,
                    ), $exception);
                }
            })
            ->then(new FailedToWithdrawDueToInsufficientTokens(attempted: 101, balance: 100));
    }
}
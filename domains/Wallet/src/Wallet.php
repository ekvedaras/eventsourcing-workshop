<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Events\FailedToWithdrawDueToInsufficientTokens;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Exceptions\BalanceException;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    private int $balance = 0;

    public function deposit(int $tokens, string $description): void
    {
        $this->recordThat(new TokensDeposited($tokens, $description));
    }

    public function withdraw(int $tokens, string $description): void
    {
        if ($tokens > $this->balance) {
            $this->recordThat(new FailedToWithdrawDueToInsufficientTokens(
                attempted: $tokens,
                balance: $this->balance,
            ));

            throw BalanceException::insufficientTokens(attemptedToWithdraw: $tokens, balance: $this->balance);
        }

        $this->recordThat(new TokensWithdrawn($tokens, $description));
    }

    private function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->balance += $event->tokens;
    }

    private function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->balance -= $event->tokens;
    }

    private function applyFailedToWithdrawDueToInsufficientTokens(FailedToWithdrawDueToInsufficientTokens $event): void
    {
    }
}

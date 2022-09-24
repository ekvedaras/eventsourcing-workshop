<?php

namespace Workshop\Domains\Wallet\Exceptions;

use DomainException;

final class BalanceException extends DomainException
{
    public static function insufficientTokens(int $attemptedToWithdraw, int $balance): self
    {
        return new self("Insufficient tokens. Attempted to withdraw $attemptedToWithdraw, but only have $balance");
    }
}
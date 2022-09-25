<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Infra\NotificationService;
use Workshop\Domains\Wallet\WalletId;

class InMemoryNotificationService implements NotificationService
{

    private array $notifications = [];

    public function sendWalletHighBalanceNotification(WalletId $walletId): void
    {
        $this->notifications[] = $walletId->toString();
    }

    public function notificationSentExactlyOnceForWallet(WalletId $walletId): bool
    {
        return count(array_keys($this->notifications, $walletId->toString())) === 1;
    }
}

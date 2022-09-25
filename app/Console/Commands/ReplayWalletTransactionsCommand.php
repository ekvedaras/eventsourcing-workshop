<?php

namespace App\Console\Commands;

use EventSauce\EventSourcing\OffsetCursor;
use EventSauce\EventSourcing\ReplayingMessages\ReplayMessages;
use Illuminate\Console\Command;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;

class ReplayWalletTransactionsCommand extends Command
{
    protected $signature = 'wallet:replay-transactions';

    protected $description = 'Replay wallet projectors';

    public function handle(WalletMessageRepository $walletMessageRepository, TransactionsProjector $transactions)
    {
        $replayMessages = new ReplayMessages(
            $walletMessageRepository,
            $transactions,
        );

        $cursor = OffsetCursor::fromStart(limit: 100);

        process_batch:
        $result = $replayMessages->replayBatch($cursor);
        $cursor = $result->cursor();

        if ($result->messagesHandled() > 0) {
            goto process_batch;
        }
    }
}

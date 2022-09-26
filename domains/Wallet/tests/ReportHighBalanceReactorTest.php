<?php

namespace Workshop\Domains\Wallet\Tests;

use Carbon\CarbonImmutable;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use Workshop\Domains\Wallet\Projectors\WalletBalanceProjector;
use Workshop\Domains\Wallet\Reactors\ReportHighBalanceReactor;
use Workshop\Domains\Wallet\WalletId;

class ReportHighBalanceReactorTest extends MessageConsumerTestCase
{
    private readonly WalletId $walletId;
    private readonly InMemoryNotificationService $notificationService;
    private readonly InMemoryWalletBalanceRepository $balanceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletId = WalletId::generate();
    }

    /** @test */
    public function testItReportsIfBalanceWentOverThreshold(): void
    {
        $this->balanceRepository->incrementBalance($this->walletId, ReportHighBalanceReactor::threshold - 1);
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensDeposited(1, 'test', CarbonImmutable::parse('2022-09-08 13:16:35.790434+0000'))
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                                ])
            )
            ->then(function () {
                $this->notificationService->notificationSentExactlyOnceForWallet($this->walletId);
            })
            ->when(
                (new Message(
                    new TokensDeposited(10, 'test')
                ))->withHeaders([
                                    Header::EVENT_ID => 'event-id-2',
                                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                ])
            )->then(function () {
                $this->notificationService->notificationSentExactlyOnceForWallet($this->walletId);
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->notificationService = new InMemoryNotificationService();
        $this->balanceRepository = new InMemoryWalletBalanceRepository();

        return new ReportHighBalanceReactor($this->notificationService, $this->balanceRepository);
    }
}
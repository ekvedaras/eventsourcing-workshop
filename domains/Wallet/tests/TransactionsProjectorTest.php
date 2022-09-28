<?php

namespace Workshop\Domains\Wallet\Tests;

use Carbon\CarbonImmutable;
use EventSauce\Clock\Clock;
use EventSauce\Clock\TestClock;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\TestUtilities\MessageConsumerTestCase;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use Workshop\Domains\Wallet\WalletId;

class TransactionsProjectorTest extends MessageConsumerTestCase
{
    private readonly WalletId $walletId;
    private readonly InMemoryTransactionsRepository $transactionsRepository;
    private readonly Clock $clock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletId = WalletId::generate();
        $this->clock = new TestClock();
    }

    /** @test */
    public function testItAddsTransactionToTheTransactionsOnDeposit(): void
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensDeposited(10, 'demo', $this->clock->now())
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                                ])
            )
            ->then(function () {
                $transactions = $this->transactionsRepository->getTransactions();
                $this->assertCount(1, $transactions);

                $transaction = $transactions[0];
                $this->assertEquals(10, $transaction['amount']);
                $this->assertEquals($this->walletId->toString(), $transaction['walletId']);
            });
    }

    /** @test */
    public function testItAddsTransactionToTheTransactionsOnWithdrawal(): void
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->given(
                (new Message(
                    new TokensDeposited(10, 'demo', $this->clock->now())
                ))->withHeaders([
                                    Header::EVENT_ID => 'event-id-1',
                                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                ])
            )
            ->when(
                (new Message(
                    new TokensWithdrawn(7, 'demo', $this->clock->now())
                ))->withHeaders([
                                    Header::EVENT_ID => 'event-id-2',
                                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                ])
            )
            ->then(function () {
                $transactions = $this->transactionsRepository->getTransactions();
                $this->assertCount(2, $transactions);

                $transaction = $transactions[1];
                $this->assertEquals(-7, $transaction['amount']);
                $this->assertEquals($this->walletId->toString(), $transaction['walletId']);
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->transactionsRepository = new InMemoryTransactionsRepository();

        return new TransactionsProjector($this->transactionsRepository);
    }
}
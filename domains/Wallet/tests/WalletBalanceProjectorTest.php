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
use Workshop\Domains\Wallet\WalletId;

class WalletBalanceProjectorTest extends MessageConsumerTestCase
{
    private readonly WalletId $walletId;
    private readonly InMemoryWalletBalanceRepository $balanceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletId = WalletId::generate();
    }

    /** @test */
    public function testItIncrementsBalanceOnDeposit(): void
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->when(
                (new Message(
                    new TokensDeposited(10, 'demo', CarbonImmutable::parse('2022-09-08 13:16:35.790434+0000'))
                ))->withHeaders([
                    Header::EVENT_ID => 'event-id',
                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                                ])
            )
            ->then(function () {
                $this->assertEquals(10, $this->balanceRepository->getBalance($this->walletId));
            });
    }

    /** @test */
    public function testItDecrementsBalanceOnWithdrawal(): void
    {
        $this
            ->givenNextMessagesHaveAggregateRootIdOf($this->walletId)
            ->given(
                (new Message(
                    new TokensDeposited(10, 'demo', CarbonImmutable::parse('2022-09-08 13:16:35.790434+0000'))
                ))->withHeaders([
                                    Header::EVENT_ID => 'event-id-1',
                                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                ])
            )
            ->when(
                (new Message(
                    new TokensWithdrawn(7, 'demo')
                ))->withHeaders([
                                    Header::EVENT_ID => 'event-id-2',
                                    Header::TIME_OF_RECORDING => '2022-09-08 13:16:35.790434+0000',
                                    Header::TIME_OF_RECORDING_FORMAT => 'Y-m-d H:i:s.uO',
                                ])
            )
            ->then(function () {
                $this->assertEquals(3, $this->balanceRepository->getBalance($this->walletId));
            });
    }

    public function messageConsumer(): MessageConsumer
    {
        $this->balanceRepository = new InMemoryWalletBalanceRepository();

        return new WalletBalanceProjector($this->balanceRepository);
    }
}
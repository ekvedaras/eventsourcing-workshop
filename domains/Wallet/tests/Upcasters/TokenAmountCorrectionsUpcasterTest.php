<?php

namespace Workshop\Domains\Wallet\Tests\Upcasters;

use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;
use Workshop\Domains\Wallet\Upcasters\TokenAmountCorrectionsUpcaster;

class TokenAmountCorrectionsUpcasterTest extends TestCase
{
    public function setUp(): void
    {
        $this->upcaster = new TokenAmountCorrectionsUpcaster(
            (new Repository([
                                'wallet' => [
                                    'corrections' => [
                                        'b8d0b0e0-5c1a-4b1e-8c7c-1c6b1b1b1b1b' => 10,
                                    ],
                                ],
                            ]))
        );
        parent::setUp();
    }

    /** @test */
    public function it_skips_messages_that_are_not_tokens_deposited_or_tokens_withdrawn()
    {
        $input = [
            'headers' => [
                '__event_type' => 'random',
            ],
            'payload' => [],
        ];
        $output = $this->upcast($input);

        $this->assertEquals($output, $input);
    }

    /** @test */
    public function events_without_corrections_defined_will_not_be_changed()
    {
        $input = [
            'headers' => [
                '__event_type' => 'tokens-withdrawn',
            ],
            'payload' => [
                'tokens' => 100,
            ],
        ];
        $output = $this->upcast($input);

        $this->assertEquals($output, $input);
    }

    /** @test */
    public function events_with_corrections_defined_will_be_changed()
    {
        $input = [
            'headers' => [
                '__event_type'        => 'tokens-deposited',
            ],
            'payload' => [
                'tokens' => 100,
            ],
        ];
        $output = $this->upcast($input);

        $input['payload']['tokens'] = 10;
        $this->assertEquals($output, $input);
    }

    private function upcast(array $input): array
    {
        return $this->upcaster->upcast($input);
    }
}

<?php

namespace Workshop\Domains\Wallet\Tests\Upcasters;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Workshop\Domains\Wallet\Upcasters\TransactionCorrectionUpcaster;

class TransactionCorrectionUpcasterTest extends TestCase
{
    /** @test */
    public function it_skips_messages_that_are_not_tokens_deposited_or_tokens_withdrawn()
    {
        $input = [
            'headers' => [
                '__event_type' => 'random'
            ],
            'payload' => []
        ];

        $this->upcaster = new TransactionCorrectionUpcaster([]);

        $output = $this->upcast($input);

        $this->assertEquals($output, $input);
    }

    /** @test */
    public function it_corrects_the_amount_for_transactions_in_the_config()
    {
        $corrections = [
            'b8d0b0e0-5c1a-4b1e-8c7c-1c6b1b1b1b1b' => $newAmount = 10,
        ];

        $upcaster = new TransactionCorrectionUpcaster($corrections);

        $input = [
            'headers' => [
                '__event_id' => 'b8d0b0e0-5c1a-4b1e-8c7c-1c6b1b1b1b1b',
                '__event_type' => 'tokens_deposited',
            ],
            'payload' => [
                'tokens' => 100,
            ],
        ];

        $output = $upcaster->upcast($input);

        $this->assertEquals($newAmount, $output['payload']['tokens']);
    }

    /** @test */
    public function it_skips_transactions_not_in_the_corrections_map()
    {
        $corrections = [
            'b8d0b0e0-5c1a-4b1e-8c7c-1c6b1b1b1b1b' => 10,
        ];

        $upcaster = new TransactionCorrectionUpcaster($corrections);

        $input = [
            'headers' => [
                '__event_id' => '2c70c5c3-5bff-4935-b1c0-e21e7d3e7abc',
                '__event_type' => 'tokens_deposited',
            ],
            'payload' => [
                'tokens' => 100,
            ],
        ];

        $output = $upcaster->upcast($input);

        $this->assertEquals(100, $output['payload']['tokens']);
    }

    private function upcast(array $input): array
    {
        return $this->upcaster->upcast($input);
    }
}

<?php

namespace Workshop\Domains\Wallet\Tests\Upcasters;

use Eris\Generators;
use Eris\TestTrait;
use PHPUnit\Framework\TestCase;
use Workshop\Domains\Wallet\Upcasters\TransactedAtUpcaster;

class TransactedAtUpcasterTest extends TestCase
{
    use TestTrait;

    public function setUp(): void
    {
        $this->upcaster = new TransactedAtUpcaster();
        parent::setUp();
    }

    /** @test */
    public function it_skips_messages_that_are_not_tokens_deposited_or_tokens_withdrawn()
    {
        $input = [
            'headers' => [
                '__event_type' => 'random'
            ],
            'payload' => []
        ];
        $output = $this->upcast($input);

        $this->assertEquals($output, $input);
    }

    /** @test */
    public function events_with_a_transacted_at_value_in_the_payload_will_not_be_changed()
    {
        $this->forAll(
            Generators::date(),
        )->then(function (\DateTime $date) {
            $input = [
                'headers' => [
                    '__event_type' => 'tokens_deposited',
                ],
                'payload' => [
                    'transacted_at' => $date->format(DATE_ATOM),
                ]
            ];
            $output = $this->upcast($input);

            $this->assertEquals($output, $input);
        });
    }

    /** @test */
    public function events_without_transacted_at_will_get_recorded_at_as_transacted_at()
    {
        $this->forAll(
            Generators::date(),
        )->then(function (\DateTime $date) {
            $input = [
                'headers' => [
                    '__event_type' => 'tokens_deposited',
                    '__time_of_recording' => $date->format(DATE_ATOM),
                ],
                'payload' => [
                ]
            ];
            $output = $this->upcast($input);

            $input['payload']['transacted_at'] = $input['headers']['__time_of_recording'];
            $this->assertEquals($output, $input);
        });
    }

    private function upcast(array $input): array
    {
        return $this->upcaster->upcast($input);
    }
}

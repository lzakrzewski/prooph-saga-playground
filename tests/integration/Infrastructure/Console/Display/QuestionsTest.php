<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Console\Display;

use Infrastructure\Console\Display\Questions;
use Infrastructure\Listener\CollectsMessages;
use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\CreateOrder;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use tests\UsesContainerTestCase;

class QuestionsTest extends UsesContainerTestCase
{
    /** @var Questions */
    private $questions;

    /** @test @dataProvider inputs */
    public function it_asks_question_and_then_dispatches_right_command(array $inputs, string $expectedCommand): void
    {
        $this->display($inputs);

        $this->assertThatCommandWasDispatched($expectedCommand);
    }

    /** @test */
    public function it_fails_when_input_is_invalid(): void
    {
        $this->expectException(\Exception::class);

        $this->display(['10', '']);
    }

    public function inputs()
    {
        return [
            [
                ['1', '5'],
                CreateOrder::class,
            ],
            [
                ['2', '5'],
                MakeReservation::class,
            ],
            [
                ['3', '500'],
                MakePayment::class,
            ],
            [
                ['4', '5'],
                AddSeatsToWaitList::class,
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->questions = $this->container()->get(Questions::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->questions = null;
    }

    private function display(array $inputs): void
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, implode(PHP_EOL, $inputs));
        rewind($stream);
        $input = new ArrayInput([]);
        $input->setStream($stream);

        $this->questions->display($input, new BufferedOutput());
    }

    private function assertThatCommandWasDispatched(string $expectedCommand): void
    {
        $messages = $this
            ->container()
            ->get(CollectsMessages::class)
            ->all();

        $this->assertNotEmpty($messages);
        $this->assertInstanceOf($expectedCommand, $messages[0]);
    }
}

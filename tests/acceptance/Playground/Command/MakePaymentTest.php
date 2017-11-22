<?php

declare(strict_types=1);

namespace tests\acceptance\Playground\Command;

use Playground\Command\MakePayment;
use Playground\Event\PaymentAccepted;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class MakePaymentTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_notifies_that_payment_has_been_accepted()
    {
        $this
            ->scenario
            ->when(new MakePayment($paymentId = Uuid::uuid4(), 5 * 100))
            ->then(new PaymentAccepted($paymentId, 500));
    }
}

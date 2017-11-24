<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\Command;

use Messaging\Command\MakePayment;
use Messaging\Event\PaymentAccepted;
use Ramsey\Uuid\Uuid;
use tests\UsesScenarioTestCase;

class MakePaymentTest extends UsesScenarioTestCase
{
    /** @test */
    public function it_notifies_that_payment_has_been_accepted()
    {
        $this
            ->scenario()
            ->when(new MakePayment($paymentId = Uuid::uuid4(), 500))
            ->then(new PaymentAccepted($paymentId, 500));
    }
}

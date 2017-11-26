<?php

declare(strict_types=1);

namespace Infrastructure\Console\Output;

use Infrastructure\Listener\CollectsMessages;
use Messaging\Command;
use Messaging\DomainEvent;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TableWithMessages
{
    /** @var CollectsMessages */
    private $collectedMessages;

    public function __construct(CollectsMessages $collectedMessages)
    {
        $this->collectedMessages = $collectedMessages;
    }

    public function display(OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Name of message', 'type', 'payload']);

        foreach ($this->collectedMessages->all() as $message) {
            if (false === $message instanceof DomainEvent && false === $message instanceof Command) {
                continue;
            }

            if ($message instanceof DomainEvent) {
                $table->addRow(
                    [
                        $this->comment((new \ReflectionClass($message))->getShortName()),
                        $this->comment('DomainEvent'),
                        $this->comment(json_encode($message->payload())),
                    ]
                );
                continue;
            }

            $table->addRow(
                [
                    $this->info((new \ReflectionClass($message))->getShortName()),
                    $this->info('Command'),
                    $this->info(json_encode($message->payload())),
                ]
            );
        }

        $table->render();
    }

    private function comment(string $value): string
    {
        return sprintf('<comment>%s</comment>', $value);
    }

    private function info(string $value): string
    {
        return sprintf('<info>%s</info>', $value);
    }
}

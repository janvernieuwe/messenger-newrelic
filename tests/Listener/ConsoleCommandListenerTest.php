<?php

namespace Arxus\NewrelicMessengerBundle\Tests\Listener;

use Arxus\NewrelicMessengerBundle\Listener\ConsoleCommandListener;
use Arxus\NewrelicMessengerBundle\Newrelic\NewrelicManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleCommandListenerTest extends TestCase
{
    /**
     * @var MockObject|Command
     */
    private $commandMock;

    /**
     * @var MockObject|ConsoleCommandEvent
     */
    private $eventMock;

    /**
     * @var NewrelicManager|MockObject
     */
    private $newrelicManagerMock;

    public function setUp(): void
    {
        // Create mocks
        $this->commandMock = $this->createMock(Command::class);
        $this->eventMock = new ConsoleCommandEvent(
            $this->command = new Command('messenger:consume'),
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
        $this->newrelicManagerMock = $this->createMock(NewrelicManager::class);
    }

    public function testInvokeEnabled(): void
    {
//        $this->commandMock
//            ->expects($this->once())
//            ->method('getName')
//            ->willReturn('messenger:consume');
        $this->newrelicManagerMock
            ->expects($this->once())
            ->method('endTransaction');
        $this->newrelicManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
//        $this->eventMock
//            ->expects($this->once())
//            ->method('getCommand')
//            ->willReturn($this->commandMock);
        $commandListener = new ConsoleCommandListener($this->newrelicManagerMock);
        $commandListener($this->eventMock);
    }

    public function testInvokeDisabled(): void
    {
        $this->commandMock
            ->expects($this->never())
            ->method('getName')
            ->willReturn('messenger:consume');
        $this->newrelicManagerMock
            ->expects($this->never())
            ->method('endTransaction');
        $this->newrelicManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);
//        $this->eventMock
//            ->expects($this->once())
//            ->method('getCommand')
//            ->willReturn($this->commandMock);
        $commandListener = new ConsoleCommandListener($this->newrelicManagerMock);
        $commandListener($this->eventMock);
    }

    public function testInvokeOtherCommand(): void
    {
        $this->eventMock = new ConsoleCommandEvent(
            $this->command = new Command('other:command'),
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
        $this->newrelicManagerMock
            ->expects($this->never())
            ->method('endTransaction');
        $this->newrelicManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
//        $this->eventMock
//            ->expects($this->once())
//            ->method('getCommand')
//            ->willReturn($this->commandMock);
        $commandListener = new ConsoleCommandListener($this->newrelicManagerMock);
        $commandListener($this->eventMock);
    }

    public function testInvokeNoCommand(): void
    {
        $this->eventMock = new ConsoleCommandEvent(
            $this->command = new Command('other:command'),
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
        $this->newrelicManagerMock
            ->expects($this->never())
            ->method('endTransaction');
        $this->newrelicManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
//        $this->eventMock
//            ->expects($this->once())
//            ->method('getCommand')
//            ->willReturn('');
        $commandListener = new ConsoleCommandListener($this->newrelicManagerMock);
        $commandListener($this->eventMock);
    }
}

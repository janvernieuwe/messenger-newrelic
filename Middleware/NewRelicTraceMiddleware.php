<?php


namespace Arxus\NewrelicMessengerBundle\Middleware;

use Arxus\NewrelicMessengerBundle\Newrelic\NewrelicManager;
use Arxus\NewrelicMessengerBundle\Stamp\TraceStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class NewRelicTraceMiddleware implements MiddlewareInterface
{
    /**
     * @var NewrelicManager
     */
    private $newrelicManager;

    public function __construct(NewrelicManager $newrelicManager)
    {
        $this->newrelicManager = $newrelicManager;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (!$this->newrelicManager->isEnabled()) {
            return $stack->next()->handle($envelope, $stack);
        }

        // Add the stamp
        $envelope = $envelope->with(new TraceStamp($this->newrelicManager->insertTrace()));

        return $stack->next()->handle($envelope, $stack);
    }
}

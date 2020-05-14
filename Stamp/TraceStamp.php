<?php

namespace Arxus\NewrelicMessengerBundle\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class TraceStamp implements StampInterface
{
    private $traceData;

    public function __construct(array $traceData)
    {
        $this->traceData = $traceData;
    }

    public function getTraceData(): array
    {
        return $this->traceData;
    }
}

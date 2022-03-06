<?php

namespace AndrewNicols\MoodleRay;

use AndrewNicols\MoodleRay\Loggers\QueryLogger;
use Spatie\Ray\Payloads\Payload;
use Spatie\Ray\Ray as BaseRay;

class Ray extends BaseRay
{
    /** @var \AndrewNicols\MoodleRay\Loggers\QueryLogger */
    protected static $queryLogger;

    public static function bootForMoodle()
    {
        static::$queryLogger = new QueryLogger();

        Payload::$originFactoryClass = OriginFactory::class;
    }

    public function isEnabled(): bool
    {
        return static::$enabled;
    }

    public function showQueries(): self
    {
        static::$queryLogger->showQueries();

        return $this;
    }

    public function queries(): self
    {
        return $this->showQueries();
    }

    public function stopShowingQueries(): self
    {
        static::$queryLogger->stopShowingQueries();

        return $this;
    }

    public function sendQueryToRay(...$args): self
    {
        static::$queryLogger->sendQueryToRay(...$args);

        return $this;
    }

    public function sendRequest($payloads, array $meta = []): BaseRay
    {
        if (! $this->isEnabled()) {
            return $this;
        }

        return BaseRay::sendRequest($payloads, $meta);
    }
}

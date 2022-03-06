<?php

namespace AndrewNicols\MoodleRay;

use AndrewNicols\MoodleRay\Loggers\QueryLogger;
use Spatie\Ray\Payloads\Payload;
use Spatie\Ray\Ray as BaseRay;

class Ray extends BaseRay
{
    /** @var bool Whether DB queries are logged */
    protected static $showingQueries = false;

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
        static::$showingQueries = true;

        return $this;
    }

    public function showingQueries(): bool
    {
        static::$showingQueries === true;
    }

    public function stopShowingQueries(): self
    {
        static::$showingQueries = false;

        return $this;
    }

    public function sendQueryToRay($sql, $params, $timeInSeconds): self
    {
        if (!$this->showingQueries()) {
            return $this;
        }

        $timeInMilliSeconds = $timeInSeconds * 1000;

        $payload = new ExecutedQueryPayload($sql, $params, $timeInMilliSeconds);

        return ray()->sendRequest($payload);
    }

    public function sendRequest($payloads, array $meta = []): BaseRay
    {
        if (! $this->isEnabled()) {
            return $this;
        }

        return BaseRay::sendRequest($payloads, $meta);
    }
}

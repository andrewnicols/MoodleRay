<?php

namespace AndrewNicols\MoodleRay;

use AndrewNicols\MoodleRay\Payloads\ExecutedQueryPayload;
use Spatie\Ray\Payloads\Payload;
use Spatie\Ray\Ray as BaseRay;

class Ray extends BaseRay
{
    /** @var bool Whether DB queries are logged */
    protected static $showingQueries = false;

    /** @var bool Whether Moodle Events are logged */
    protected static $showingEvents = false;

    public static function bootForMoodle()
    {
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
        return static::$showingQueries === true;
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

    public function logEvents(): self
    {
        static::$showingEvents = true;

        return $this;
    }

    public function loggingEvents(): bool
    {
        return static::$showingEvents === true;
    }

    public function stopLoggingEvents(): self
    {
        static::$showingEvents = false;

        return $this;
    }

    public function logEvent(...$args): self
    {
        if (!$this->loggingEvents()) {
            return $this;
        }

        return $this->send(...$args)->label('Moodle Event');
    }
}

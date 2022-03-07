<?php

namespace AndrewNicols\MoodleRay;

use AndrewNicols\MoodleRay\Payloads\ExecutedQueryPayload;
use Spatie\Ray\Payloads\Payload;
use Spatie\Ray\Ray as BaseRay;

class Ray extends BaseRay
{
    /** @var bool Whether DB queries are showged */
    protected static $showingQueries = false;

    /** @var bool Whether Moodle Events are showged */
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

        return mray()->sendRequest($payload);
    }

    public function sendRequest($payloads, array $meta = []): BaseRay
    {
        if (! $this->isEnabled()) {
            return $this;
        }

        return BaseRay::sendRequest($payloads, $meta);
    }

    public function showEvents(): self
    {
        static::$showingEvents = true;

        return $this;
    }

    public function showingEvents(): bool
    {
        return static::$showingEvents === true;
    }

    public function stopShowingEvents(): self
    {
        static::$showingEvents = false;

        return $this;
    }

    public function showEvent(...$args): self
    {
        if (!$this->showingEvents()) {
            return $this;
        }

        return $this->send(...$args)->label('Moodle Event');
    }
}

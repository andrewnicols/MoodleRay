<?php

namespace AndrewNicols\MoodleRay\Loggers;

use AndrewNicols\MoodleRay\Payloads\ExecutedQueryPayload;

class QueryLogger
{
    /** @var bool */
    protected $active = false;

    public function showQueries(): self
    {
        if ($this->active) {
            return $this;
        }

        $this->active = true;

        return $this;
    }

    public function stopShowingQueries(): self
    {
        if (!$this->active) {
            return $this;
        }

        $this->active = false;

        return $this;
    }

    public function sendQueryToRay($sql, $params, $timeInSeconds): void
    {
        if (!$this->active) {
            return;
        }

        $timeInMilliSeconds = $timeInSeconds * 1000;

        $payload = new ExecutedQueryPayload($sql, $params, $timeInMilliSeconds);

        ray()->sendRequest($payload);
    }
}

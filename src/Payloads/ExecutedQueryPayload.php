<?php

namespace AndrewNicols\MoodleRay\Payloads;

use Spatie\Ray\Payloads\Payload;

class ExecutedQueryPayload extends Payload
{
    /** @var string */
    protected $sql;

    /** @var array  */
    protected $params;

    /** @var float */
    protected $time;

    public function __construct(string $sql, array $params, float $time)
    {
        $this->sql = $sql;
        $this->params = $params;
        $this->time = $time;
    }

    public function getType(): string
    {
        return 'executed_query';
    }

    public function getContent(): array
    {
        global $DB;

        return [
            'sql' => $this->sql,
            'bindings' => $this->params,
            'time' => $this->time,
            'connection_name' => $DB->get_name(),
        ];
    }
}

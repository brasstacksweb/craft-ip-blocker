<?php

namespace brasstacksweb\craftipblocker\models;

use craft\base\Model;

class AttemptStats extends Model
{
    public string $pattern;
    public string $ip;
    public int $count;
    public \DateTime $firstAttempt;
    public \DateTime $lastAttempt;

    public function getAttributes($names = null, $except = [], $onlyChanged = false): array
    {
        return array_merge(parent::getAttributes($names, $except, $onlyChanged), [
            'firstAttempt' => $this->firstAttempt->format('Y-m-d H:i:s'),
            'lastAttempt' => $this->lastAttempt->format('Y-m-d H:i:s'),
        ]);
    }

    public function attributeLabels(): array
    {
        return [
            'pattern' => 'Pattern',
            'ip' => 'IP Address',
            'count' => 'Number of Attempts',
            'firstAttempt' => 'First Attempt',
            'lastAttempt' => 'Last Attempt',
        ];
    }
}

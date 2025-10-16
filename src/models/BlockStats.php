<?php

namespace brasstacksweb\craftipblocker\models;

use craft\base\Model;

class BlockStats extends Model
{
    public string $ip;
    public string $reason;
    public int $count;
    public \DateTime $firstBlocked;
    public \DateTime $expires;
    public bool $isActive;

    public function getAttributes($names = null, $except = [], $onlyChanged = false): array
    {
        return array_merge(parent::getAttributes($names, $except, $onlyChanged), [
            'firstBlocked' => $this->firstBlocked->format('Y-m-d H:i:s'),
            'expires' => $this->expires->format('Y-m-d H:i:s'),
            'isActive' => $this->isActive ? 'Yes' : 'No',
        ]);
    }

    public function attributeLabels(): array
    {
        return [
            'ip' => 'IP Address',
            'reason' => 'Reason',
            'count' => 'Number of Attempts',
            'firstBlocked' => 'First Blocked',
            'expires' => 'Expires',
            'isActive' => 'Is Active',
        ];
    }
}

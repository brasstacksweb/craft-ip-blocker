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
}

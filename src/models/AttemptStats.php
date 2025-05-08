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
}

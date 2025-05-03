<?php

namespace brasstacksweb\craftipblocker\models;

use craft\base\Model;

class Condition extends Model
{
    // TODO: Add exception type?
    public ?string $pattern = null;
    public int $maxAttempts = 5;
    public int $blockTime = 3600; // 1 hour
    public int $detectionWindow = 300; // 5 minutes
}

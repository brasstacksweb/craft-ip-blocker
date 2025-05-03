<?php

namespace brasstacksweb\craftipblocker\models;

use craft\base\Model;

class Settings extends Model
{
    public array $conditions = [];

    public function setAttributes($values, $safeOnly = true): void
    {
        $this->conditions = array_map(fn ($c) => new Condition($c), $values['conditions'] ?? []);
    }
}

<?php

namespace brasstacksweb\craftipblocker\models;

use craft\base\Model;

class Condition extends Model
{
    // TODO: Add exception type?
    public ?string $pattern = null;
    public int $maxAttempts = 5;
    public int $detectionWindow = 300; // 5 minutes
    public int $blockTime = 3600; // 1 hour

    public function attributeHints(): array
    {
        return [
            'pattern' => 'String or regex pattern (excluding /) to match against exception message',
            'maxAttempts' => 'The maximum number of allowed attempts matching the pattern before the I.P. address is blocked',
            'detectionWindow' => 'The window of time (in seconds) that failed requests are counted toward the maximum',
            'blockTime' => 'The duration of time (in seconds) that an I.P. address is blocked after matching the pattern and meeting the maximum attempts',
        ];
    }

    public function getAttributeType(string $name): string
    {
        return [
            'pattern' => 'singleline',
            'maxAttempts' => 'number',
            'blockTime' => 'number',
            'detectionWindow' => 'number',
        ][$name] ?? 'singleline';
    }

    public function getAttributePlaceholder(string $name): string
    {
        return [
            'pattern' => 'Invalid asset handle',
            'maxAttempts' => '5',
            'blockTime' => '360',
            'detectionWindow' => '300',
        ][$name] ?? '';
    }

    public function getAttributeWidth(string $name): string
    {
        return [
            'pattern' => '40%',
            'maxAttempts' => '20%',
            'blockTime' => '20%',
            'detectionWindow' => '20%',
        ][$name] ?? '100%';
    }
}

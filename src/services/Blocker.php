<?php

namespace brasstacksweb\craftipblocker\services;

use brasstacksweb\craftipblocker\models\Condition;
use brasstacksweb\craftipblocker\records\Attempt;
use brasstacksweb\craftipblocker\records\Block;
use craft\helpers\DateTimeHelper;
use yii\base\Component;
use yii\web\ForbiddenHttpException;

class Blocker extends Component
{
    public function checkIp(string $ip, array $conditions)
    {
        $now = time();

        if ($this->isBlocked($ip, $now)) {
            throw new ForbiddenHttpException('Your IP address is temporarily blocked.');
        }

        $this->cleanup($ip, $now, $conditions);
    }

    public function recordFailedAttempt(string $ip, Condition $condition): bool
    {
        $now = time();
        $attempt = Attempt::findOne([
            'pattern' => $condition->pattern,
            'ip' => $ip,
        ]) ?? new Attempt([
            'pattern' => $condition->pattern,
            'ip' => $ip,
            'firstAttempt' => DateTimeHelper::toIso8601($now),
            'count' => 0,
        ]);
        $attempt->count++;
        $attempt->lastAttempt = DateTimeHelper::toIso8601($now);

        // Check if attempts occurred within detection window
        if ($attempt->firstAttempt >= ($now - $condition->detectionWindow)) {
            // Check if max attempts reached
            if ($attempt->count >= $condition->maxAttempts) {
                $this->blockIP($ip, $now, $condition);
            }
        } else {
            // Reset counter if outside detection window
            $attempt->count = 1;
            $attempt->firstAttempt = DateTimeHelper::toIso8601($now);
        }

        return $attempt->save();
    }

    public function getAttempts(): array
    {
        return Attempt::find()->all();
    }

    public function getActiveBlocks(int $now): array
    {
        return Block::find()
            ->andWhere(['>', 'expires', DateTimeHelper::toIso8601($now)])
            ->all();
    }

    public function getExpiredBlocks(int $now): array
    {
        return Block::find()
            ->andWhere(['<=', 'expires', DateTimeHelper::toIso8601($now)])
            ->all();
    }

    private function isBlocked(string $ip, int $now): bool
    {
        return Block::find()
            ->where(['ip' => $ip])
            ->andWhere(['>', 'expires', DateTimeHelper::toIso8601($now)])
            ->exists();
    }

    private function cleanup(string $ip, int $now, array $conditions): void
    {
        foreach ($conditions as $c) {
            $buffer = $c->detectionWindow * 1.2;
            $cutoff = DateTimeHelper::toIso8601($now - $buffer);

            Attempt::deleteAll([
                'and',
                ['ip' => $ip],
                ['pattern' => $c->pattern],
                ['<=', 'lastAttempt', $cutoff],
            ]);
        }
    }

    private function blockIP(string $ip, int $now, Condition $condition): bool
    {
        $block = new Block([
            'ip' => $ip,
            'expires' => DateTimeHelper::toIso8601($now + $condition->blockTime),
            'reason' => $condition->pattern,
        ]);

        return $block->save();
    }
}

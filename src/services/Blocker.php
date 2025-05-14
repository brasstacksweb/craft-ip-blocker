<?php

namespace brasstacksweb\craftipblocker\services;

use brasstacksweb\craftipblocker\models\AttemptStats;
use brasstacksweb\craftipblocker\models\BlockStats;
use brasstacksweb\craftipblocker\models\Condition;
use brasstacksweb\craftipblocker\records\Attempt;
use brasstacksweb\craftipblocker\records\Block;
use craft\helpers\ConfigHelper;
use craft\helpers\DateTimeHelper;
use yii\base\Component;
use yii\db\Expression;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class Blocker extends Component
{
    public function checkIp(string $ip, array $conditions)
    {
        if ($this->isBlocked($ip)) {
            throw new ForbiddenHttpException('Your IP address is temporarily blocked.');
        }

        $this->cleanup($ip, $conditions);
    }

    public function matchException(Condition $condition, HttpException $exception): bool
    {
        // TODO: Add exeption type check from condition in addition to pattern
        $pattern = $condition->pattern;

        // Trim slashes
        $pattern = trim($pattern, '/');

        // Escape delimiters, removing already escaped delimiters first
        $pattern = str_replace(['\/', '/'], ['/', '\/'], $pattern);

        $message = $exception->getMessage();
        $previous = $exception->getPrevious()?->getMessage() ?? '';

        return preg_match('/'.$pattern.'/', $message) || preg_match('/'.$pattern.'/', $previous);
    }

    public function recordFailedAttempt(string $ip, Condition $condition): bool
    {
        $attempt = new Attempt([
            'pattern' => $condition->pattern,
            'ip' => $ip,
        ]);

        if (!$attempt->save()) {
            return false;
        }

        $window = ConfigHelper::durationInSeconds($condition->detectionWindow);
        $cutoff = DateTimeHelper::toIso8601(time() - $window);
        $failedAttempts = Attempt::find()
            ->where([
                'pattern' => $condition->pattern,
                'ip' => $ip,
            ])
            ->andWhere(['>=', 'dateCreated', $cutoff])
            ->count();

        if ($failedAttempts >= $condition->maxAttempts) {
            return $this->blockIP($ip, $condition);
        }

        return true;
    }

    public function getAttemptStats(): array
    {
        $attempts = Attempt::find()
            ->select([
                'pattern',
                'ip',
                'COUNT(*) as count',
                'MIN(dateCreated) as firstAttempt',
                'MAX(dateCreated) as lastAttempt',
            ])
            ->groupBy(['pattern', 'ip'])
            ->asArray()
            ->all();

        return array_map(fn ($a) => new AttemptStats($a), $attempts);
    }

    public function getBlockStats(): array
    {
        $blocks = Block::find()
            ->select([
                'ip',
                'reason',
                'COUNT(*) as count',
                'MIN(dateCreated) as firstBlocked',
                'MAX(expires) as expires',
                'MAX(expires) > NOW() as isActive',
            ])
            ->groupBy(['ip', 'reason'])
            ->asArray()
            ->all();

        return array_map(fn ($b) => new BlockStats($b), $blocks);
    }

    private function isBlocked(string $ip): bool
    {
        return Block::find()
            ->where(['ip' => $ip])
            ->andWhere('expires > NOW()')
            ->exists();
    }

    private function cleanup(string $ip, array $conditions): void
    {
        foreach ($conditions as $c) {
            $buffer = $c->detectionWindow * 2;
            $cutoff = DateTimeHelper::toIso8601(time() - $buffer);

            Attempt::updateAll(['dateDeleted' => new Expression('NOW()')], [
                'and',
                ['ip' => $ip],
                ['pattern' => $c->pattern],
                ['<=', 'dateCreated', $cutoff],
            ]);
        }
    }

    private function blockIP(string $ip, Condition $condition): bool
    {
        $blockTime = ConfigHelper::durationInSeconds($condition->blockTime);
        $block = new Block([
            'ip' => $ip,
            'expires' => DateTimeHelper::toIso8601(time() + $blockTime),
            'reason' => $condition->pattern,
        ]);

        return $block->save();
    }
}

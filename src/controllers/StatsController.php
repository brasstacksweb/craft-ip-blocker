<?php

namespace brasstacksweb\craftipblocker\controllers;

use brasstacksweb\craftipblocker\IPBlocker;
use craft\web\Controller;
use yii\web\Response;

/**
 * Stats controller.
 */
class StatsController extends Controller
{
    public $defaultAction = 'index';
    protected array|bool|int $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    public function actionAttempts(): Response
    {
        return $this->renderTemplate('craft-ip-blocker/_attempts', [
            'attempts' => IPBlocker::getInstance()->blocker->getAttempts(),
        ]);
    }

    public function actionBlocks(): Response
    {
        $now = time();

        return $this->renderTemplate('craft-ip-blocker/_blocks', [
            'activeBlocks' => IPBlocker::getInstance()->blocker->getActiveBlocks($now),
            'expiredBlocks' => IPBlocker::getInstance()->blocker->getExpiredBlocks($now),
        ]);
    }
}

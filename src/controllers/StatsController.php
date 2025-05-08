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
            'attempts' => IPBlocker::getInstance()->blocker->getAttemptStats(),
        ]);
    }

    public function actionBlocks(): Response
    {
        return $this->renderTemplate('craft-ip-blocker/_blocks', [
            'blocks' => IPBlocker::getInstance()->blocker->getBlockStats(),
        ]);
    }
}

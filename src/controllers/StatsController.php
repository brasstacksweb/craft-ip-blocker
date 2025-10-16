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
        $page = \Craft::$app->getRequest()->getQueryParam('page', 1);
        $limit = \Craft::$app->getRequest()->getQueryParam('limit', 20);
        $sort = \Craft::$app->getRequest()->getQueryParam('sort', 'lastAttempt');
        $direction = \Craft::$app->getRequest()->getQueryParam('direction', 'desc');

        $stats = IPBlocker::getInstance()->blocker->getAttemptStats($page, $limit, $sort, $direction);

        return $this->renderTemplate('craft-ip-blocker/_attempts', [
            'attempts' => $stats['attempts'],
            'paginator' => $stats['paginator'],
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function actionBlocks(): Response
    {
        $page = \Craft::$app->getRequest()->getQueryParam('page', 1);
        $limit = \Craft::$app->getRequest()->getQueryParam('limit', 20);
        $sort = \Craft::$app->getRequest()->getQueryParam('sort', 'expires');
        $direction = \Craft::$app->getRequest()->getQueryParam('direction', 'desc');

        $stats = IPBlocker::getInstance()->blocker->getBlockStats($page, $limit, $sort, $direction);

        return $this->renderTemplate('craft-ip-blocker/_blocks', [
            'blocks' => $stats['blocks'],
            'paginator' => $stats['paginator'],
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}

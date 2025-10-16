<?php

namespace brasstacksweb\craftipblocker;

use brasstacksweb\craftipblocker\models\Settings;
use brasstacksweb\craftipblocker\records\Attempt;
use brasstacksweb\craftipblocker\services\Blocker;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\ExceptionEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Gc;
use craft\web\ErrorHandler;
use craft\web\UrlManager;
use yii\base\Event;

/**
 * IP Blocker plugin.
 *
 * @method static IPBlocker getInstance()
 * @method        Settings  getSettings()
 *
 * @author Brass Tacks Web <help@brasstacksweb.com>
 * @copyright Brass Tacks Web
 * @license https://craftcms.github.io/license/ Craft License
 */
class IPBlocker extends Plugin
{
    public string $schemaVersion = '1.2.0';
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'blocker' => Blocker::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $request = \Craft::$app->getRequest();

        if ($request->getIsConsoleRequest()) {
            return;
        }

        if ($request->getIsCpRequest()) {
            $this->controllerNamespace = 'brasstacksweb\craftipblocker\controllers';

            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_CP_URL_RULES,
                function (RegisterUrlRulesEvent $event) {
                    $event->rules['craft-ip-blocker/blocks'] = 'craft-ip-blocker/stats/blocks';
                    $event->rules['craft-ip-blocker/attempts'] = 'craft-ip-blocker/stats/attempts';
                }
            );
        }

        $ip = $request->getUserIp();
        $conditions = $this->getSettings()->conditions;

        if (count($conditions) === 0) {
            return;
        }

        $this->blocker->checkIp($ip, $conditions);

        Event::on(
            ErrorHandler::class,
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) use ($ip, $conditions) {
                foreach ($conditions as $c) {
                    if ($this->blocker->matchException($c, $event->exception)) {
                        $this->blocker->recordFailedAttempt($ip, $c);
                    }
                }
            }
        );

        Event::on(
            Gc::class,
            Gc::EVENT_RUN,
            function () {
                \Craft::$app->gc->hardDelete(Attempt::tableName());
            }
        );

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        // \Craft::$app->onInit(function () {
        // });
    }

    public function getCpNavItem(): ?array
    {
        return array_merge(parent::getCpNavItem(), [
            'url' => 'craft-ip-blocker/blocks',
        ]);
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return \Craft::$app->view->renderTemplate('craft-ip-blocker/_settings.twig', [
            'settings' => $this->getSettings(),
        ]);
    }
}

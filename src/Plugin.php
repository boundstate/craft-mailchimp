<?php
namespace boundstate\mailchimp;

use Craft;

class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    public function init()
    {
        parent::init();

        $this->setComponents([
            'mailchimp' => \boundstate\mailchimp\services\Mailchimp::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new \boundstate\mailchimp\models\Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml()
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        return Craft::$app->getView()->renderTemplate('mailchimp/settings', [
            'settings' => $settings
        ]);
    }
}

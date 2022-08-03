<?php
namespace boundstate\mailchimp;

use boundstate\mailchimp\models\Settings;
use boundstate\mailchimp\services\Mailchimp;

use Craft;
use craft\base\Model;

class Plugin extends \craft\base\Plugin
{
    public bool $hasCpSettings = true;

    public function init()
    {
        parent::init();

        $this->setComponents([
            'mailchimp' => Mailchimp::class,
        ]);
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        return Craft::$app->getView()->renderTemplate('mailchimp/settings', [
            'settings' => $settings
        ]);
    }
}

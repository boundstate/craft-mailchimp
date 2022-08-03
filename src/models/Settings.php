<?php
namespace boundstate\mailchimp\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    public string $apiKey = '';
    public string $audienceId = '';
    public ?string $successFlashMessage = null;

    public function init(): void
    {
        parent::init();

        if ($this->successFlashMessage === null) {
            $this->successFlashMessage = Craft::t('mailchimp', 'Thanks for subscribing.');
        }
    }

    public function rules(): array
    {
        return [
            [['apiKey', 'successFlashMessage'], 'required'],
            [['apiKey', 'audienceId', 'successFlashMessage'], 'string'],
        ];
    }
}

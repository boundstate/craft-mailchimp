<?php
namespace boundstate\mailchimp\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    /**
     * @var string Mailchimp API key
     */
    public $apiKey;

    /**
     * @var string Audience ID
     */
    public $audienceId;

    /**
     * @var string|null
     */
    public $successFlashMessage;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->successFlashMessage === null) {
            $this->successFlashMessage = Craft::t('mailchimp', 'Thanks for subscribing.');
        }
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['apiKey', 'successFlashMessage'], 'required'],
            [['apiKey', 'audienceId', 'successFlashMessage'], 'string'],
        ];
    }
}

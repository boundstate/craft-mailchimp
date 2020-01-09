<?php
namespace boundstate\mailchimp\models;

use Craft;
use craft\base\Model;

class Subscription extends Model
{
    /**
     * @var string|null
     */
    public $listId;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var array|null
     */
    public $mergeFields;

    /**
     * @var string[]|null
     */
    public $tags;

    /**
     * @var ApiError|null
     */
    public $apiError;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Craft::t('mailchimp', 'Your Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['listId', 'email'], 'required'],
            [['email'], 'required'],
        ];
    }
}

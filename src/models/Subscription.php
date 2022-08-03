<?php
namespace boundstate\mailchimp\models;

use Craft;
use craft\base\Model;

class Subscription extends Model
{
    public ?string $listId = null;
    public ?string $email = null;
    public ?array $mergeFields = null;
    public ?array $tags = null;
    public ?ApiError $apiError = null;

    public function attributeLabels(): array
    {
        return [
            'email' => Craft::t('mailchimp', 'Your Email'),
        ];
    }

    public function rules(): array
    {
        return [
            [['listId', 'email'], 'required'],
            [['email'], 'required'],
        ];
    }
}

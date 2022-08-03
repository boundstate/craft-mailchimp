<?php
namespace boundstate\mailchimp\services;

use boundstate\mailchimp\models\ApiError;
use boundstate\mailchimp\models\Subscription;
use boundstate\mailchimp\Plugin;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use DrewM\MailChimp\MailChimp as MailchimpClient;
use yii\base\InvalidConfigException;

class Mailchimp extends Component
{
    private ?MailchimpClient $client = null;

    /**
     * Subscribes a member to a list.
     * Adds any tags to the member if they already exist.
     * @throws InvalidConfigException if the plugin settings don't validate
     */
    public function subscribe(Subscription $subscription): bool
    {
        // Get the plugin settings and make sure they validate before doing anything
        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            throw new InvalidConfigException('The Mailchimp settings don’t validate.');
        }

        if (!$subscription->listId) {
            $subscription->listId = App::parseEnv($settings->audienceId);
        }

        if (!$subscription->validate()) {
            Craft::info('Mailchimp subscription not saved due to validation error.', __METHOD__);
            return false;
        }

        $result = $this->addListMember($subscription->listId, $subscription->email, [
            'email_address' => $subscription->email,
            'status' => 'subscribed',
            'merge_fields' => $subscription->mergeFields ?? new \stdClass(),
            'tags' => $subscription->tags ?? [],
        ]);

        if ($result && $subscription->tags) {
            // in case list member already exists, add tags with additional API
            $this->addListMemberTags($subscription->listId, $subscription->email, $subscription->tags);
        }

        if (!$this->getClient()->success()) {
            Craft::error('Mailchimp error: ' . $this->getClient()->getLastError(), __METHOD__);

            if ($result) {
                $subscription->apiError = ApiError::fromApiResult($result);
            }

            return false;
        }

        return true;
    }

    /**
     * Adds a member to a list.
     */
    private function addListMember(string $listId, string $email, ?array $options): array
    {
        $subscriberHash = MailchimpClient::subscriberHash($email);
        return $this->getClient()->put("lists/$listId/members/$email", $options);
    }

    /**
     * Adds tags to a list member.
     *
     * @param string $listId
     * @param string $email
     * @param string[] $tags
     * @return void
     */
    private function addListMemberTags(string $listId, string $email, array $tags): void
    {
        $subscriberHash = MailchimpClient::subscriberHash($email);
        $this->getClient()->post("lists/$listId/members/$email/tags", [
            'tags' => $this->encodeTagsToAdd($tags),
        ]);
    }

    private function getClient(): MailchimpClient {
        $settings = Plugin::getInstance()->getSettings();

        if (!$this->client) {
            $apiKey = App::parseEnv($settings->apiKey);
            $this->client = new MailchimpClient($apiKey);
        }

        return $this->client;
    }

    private function encodeTagsToAdd(array $tags): array
    {
        $result = [];

        foreach ($tags as $tag) {
            $result[] = ['name' => $tag, 'status' => 'active'];
        }

        return $result;
    }
}

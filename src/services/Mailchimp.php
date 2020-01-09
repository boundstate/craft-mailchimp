<?php
namespace boundstate\mailchimp\services;

use boundstate\mailchimp\models\ApiError;
use boundstate\mailchimp\models\Subscription;
use boundstate\mailchimp\Plugin;

use Craft;
use craft\base\Component;

use DrewM\MailChimp\MailChimp as MailchimpClient;

class Mailchimp extends Component
{
    /**
     * @var MailchimpClient
     */
    private $client;

    /**
     * Subscribes a member to a list.
     * Adds any tags to the member if they already exist.
     * 
     * @param Subscription $subscription
     * @throws InvalidConfigException if the plugin settings don't validate
     * @return bool
     */
    public function subscribe(Subscription $subscription): bool
    {
        // Get the plugin settings and make sure they validate before doing anything
        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            throw new InvalidConfigException('The Mailchimp settings don’t validate.');
        }

        if (!$subscription->listId) {
            $subscription->listId = Craft::parseEnv($settings->audienceId);
        }

        if (!$subscription->validate()) {
            Craft::info('Mailchimp subscription not saved due to validation error.', __METHOD__);
            return false;
        }

        $result = $this->addListMember($subscription->listId, $subscription->email, [
            'email_address' => $subscription->email,
            'status' => 'subscribed',
            'merge_fields' => $subscription->mergeFields ?? new \stdClass(),
            'tags' => $subscription->tags,
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
     * 
     * @param string $listId
     * @param string $email
     * @param array $options
     * @return array
     */
    private function addListMember($listId, $email, $options)
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
     * @return array
     */
    private function addListMemberTags($listId, $email, $tags)
    {
        $subscriberHash = MailchimpClient::subscriberHash($email);
        return $this->getClient()->post("lists/$listId/members/$email/tags", [
            'tags' => $this->encodeTagsToAdd($tags),
        ]);
    }

    /**
     * @return MailchimpClient
     */
    private function getClient(): MailchimpClient {
        $settings = Plugin::getInstance()->getSettings();

        if (!$this->client) {
            $apiKey = Craft::parseEnv($settings->apiKey);
            $this->client = new MailchimpClient($apiKey);
        }

        return $this->client;
    }

    /**
     * @param string[] $tags
     * @return array
     */
    private function encodeTagsToAdd($tags): array
    {
        $result = [];
        
        foreach ($tags as $tag) {
            $result[] = ['name' => $tag, 'status' => 'active'];
        }
    
        return $result;
    }
}

<?php
namespace boundstate\mailchimp\controllers;

use boundstate\mailchimp\models\Subscription;
use boundstate\mailchimp\Plugin;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class SubscribeController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = true;

    /**
     * Subscribes a member to a MailChimp list.
     * 
     * @return Response|null
     */
    public function actionIndex()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $urlManager = Craft::$app->getUrlManager();
        $plugin = Plugin::getInstance();
        $settings = $plugin->getSettings();

        $subscription = new Subscription();
        $subscription->listId = $request->getBodyParam('listId');
        $subscription->email = $request->getBodyParam('email');
        $subscription->mergeFields = $request->getBodyParam('mergeFields');
        $subscription->tags = $request->getBodyParam('tags');

        if (!$plugin->mailchimp->subscribe($subscription)) {
            if ($request->getAcceptsJson()) {
                return $this->asJson(['errors' => $subscription->getErrors()]);
            }

            Craft::$app->getSession()->setError(Craft::t('mailchimp', 'There was a problem with your submission, please check the form and try again!'));
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['subscription' => $subscription]
            ]);
            return null;
        }

        if ($request->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        Craft::$app->getSession()->setNotice($settings->successFlashMessage);
        return $this->redirectToPostedUrl($subscription);
    }
}

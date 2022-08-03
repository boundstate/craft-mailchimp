<?php
namespace boundstate\mailchimp\controllers;

use boundstate\mailchimp\models\Subscription;
use boundstate\mailchimp\Plugin;

use Craft;
use craft\errors\MissingComponentException;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class SubscribeController extends Controller
{
    protected int|bool|array $allowAnonymous = true;

    /**
     * Subscribes a member to a MailChimp list.
     * @throws MissingComponentException
     * @throws BadRequestHttpException
     */
    public function actionIndex(): ?Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $plugin = Plugin::getInstance();
        $settings = $plugin->getSettings();

        $subscription = new Subscription();
        $subscription->listId = $request->getBodyParam('listId');
        $subscription->email = $request->getBodyParam('email');
        $subscription->mergeFields = $request->getBodyParam('mergeFields');
        $subscription->tags = $request->getBodyParam('tags');

        if (!$plugin->mailchimp->subscribe($subscription)) {
            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'apiError' => $subscription->apiError,
                    'errors' => $subscription->getErrors(),
                ]);
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

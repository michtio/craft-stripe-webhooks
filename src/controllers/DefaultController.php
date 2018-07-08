<?php
/**
 * Stripe Webhooks plugin for Craft CMS 3.x.
 *
 * Handle Stripe webhooks in a CraftCMS application
 *
 * @link      https://rias.be
 *
 * @copyright Copyright (c) 2018 Rias
 */

namespace rias\stripewebhooks\controllers;

use Craft;
use craft\web\Controller;
use rias\stripewebhooks\exceptions\WebhookFailed;
use rias\stripewebhooks\records\StripeWebhookCall;
use rias\stripewebhooks\StripeWebhooks;
use Stripe\Webhook;

/**
 * @author    Rias
 *
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    // Protected Properties
    // =========================================================================

    /**
     * @var bool|array Allows anonymous access to this controller's actions.
     *                 The actions must be in 'kebab-case'
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config = []);
        $this->enableCsrfValidation = false;
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $this->requirePostRequest();
        $this->verifySignature();

        $eventPayload = json_decode(Craft::$app->getRequest()->getRawBody());
        $modelClass = StripeWebhooks::$plugin->settings->model;

        $stripeWebhookCall = new StripeWebhookCall([
            'siteId'  => Craft::$app->getSites()->getCurrentSite()->id,
            'type'    => $eventPayload->type ?? '',
            'payload' => json_encode($eventPayload),
        ]);
        $stripeWebhookCall->save(false);

        try {
            $stripeWebhookCall->process();
        } catch (Exception $exception) {
            $stripeWebhookCall->saveException($exception);

            throw $exception;
        }
    }

    protected function verifySignature()
    {
        $signature = Craft::$app->getRequest()->getHeaders()->get('Stripe-Signature');
        $secret = StripeWebhooks::$plugin->getSettings()->signingSecret;
        $payload = Craft::$app->getRequest()->getRawBody();

        if (!$signature) {
            throw WebhookFailed::missingSignature();
        }

        try {
            Webhook::constructEvent($payload, $signature, $secret);
        } catch (Exception $exception) {
            throw WebhookFailed::invalidSignature($signature);
        }

        if (empty($secret)) {
            throw WebhookFailed::signingSecretNotSet();
        }

        return true;
    }
}

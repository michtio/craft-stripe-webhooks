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

/**
 * Stripe Webhooks config.php.
 *
 * This file exists only as a template for the Stripe Webhooks settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'stripe-webhooks.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [

    /*
     * Stripe will sign each webhook using a secret. You can find the used secret at the
     * webhook configuration settings: https://dashboard.stripe.com/account/webhooks.
     */
    'signingSecret' => '',

    /*
     * You can define the job that should be run when a certain webhook hits your application
     * here. The key is the name of the Stripe event type with the `.` replaced by a `_`.
     *
     * You can find a list of Stripe webhook types here:
     * https://stripe.com/docs/api#event_types.
     */
    'jobs' => [
        // 'source_chargeable' => \modules\sitemodule\jobs\StripeWebhooks\HandleChargeableSource::class,
        // 'charge_failed' => \modules\sitemodule\jobs\StripeWebhooks\HandleFailedCharge::class,
    ],

    /*
     * The classname of the model to be used. The class should equal or extend
     * rias\stripewebhooks\records\StripeWebhookCall.
     */
    'model' => \rias\stripewebhooks\records\StripeWebhookCall::class,

    /*
     * The url of the Stripe endpoint you want to use in your application
     */
    'endpoint' => 'stripe-webhooks',

];

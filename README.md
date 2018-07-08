![icon](./src/icon.svg)

[![Latest Version](https://img.shields.io/github/release/rias500/craft-stripe-webhooks.svg?style=flat-square)](https://github.com/rias500/craft-stripe-webhooks/releases)
[![Quality Score](https://img.shields.io/scrutinizer/g/rias500/craft-stripe-webhooks.svg?style=flat-square)](https://scrutinizer-ci.com/g/rias500/craft-stripe-webhooks)
[![StyleCI](https://styleci.io/repos/140159579/shield)](https://styleci.io/repos/140159579)
[![Total Downloads](https://img.shields.io/packagist/dt/rias/craft-stripe-webhooks.svg?style=flat-square)](https://packagist.org/packages/rias/craft-stripe-webhooks)

# Handle Stripe webhooks in a CraftCMS application

[Stripe](https://stripe.com) can notify your application of events using webhooks. This plugin can help you handle those webhooks. Out of the box it will verify the Stripe signature of all incoming requests. All valid calls will be logged to the database. You can easily define jobs or events that should be dispatched when specific events hit your app.

This plugin will not handle what should be done after the webhook request has been validated and the right job or event is called. You should still code up any work (eg. regarding payments) yourself.

Before using this plugin we highly recommend reading [the entire documentation on webhooks over at Stripe](https://stripe.com/docs/webhooks).

## Support Open Source. Buy beer.

This plugin is licensed under a MIT license, which means that it's completely free open source software, and you can use it for whatever and however you wish. If you're using it and want to support the development, buy me a beer over at Beerpay!

[![Beerpay](https://beerpay.io/Rias500/craft-stripe-webhooks/badge.svg?style=beer-square)](https://beerpay.io/Rias500/craft-stripe-webhooks)

## Requirements

This plugin requires Craft CMS 3.0.0.

## Configuration

Create a `stripe-webhooks.php` config file with the following contents, or copy the one from the root of this plugin.


```php
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
```

In the `signingSecret` key of the config file you should add a valid webhook secret. You can find the secret used at [the webhook configuration settings on the Stripe dashboard](https://dashboard.stripe.com/account/webhooks).

## Usage

Stripe will send out webhooks for several event types. You can find the [full list of events types](https://stripe.com/docs/api#event_types) in the Stripe documentation.

Stripe will sign all requests hitting the webhook url of your app. This package will automatically verify if the signature is valid. If it is not, the request was probably not sent by Stripe.
 
Unless something goes terribly wrong, this plugin will always respond with a `200` to webhook requests. Sending a `200` will prevent Stripe from resending the same event over and over again. All webhook requests with a valid signature will be logged in the `stripewebhooks_stripewebhookcall` table. The table has a `payload` column where the entire payload of the incoming webhook is saved.

If the signature is not valid, the request will not be logged in the `stripewebhooks_stripewebhookcall` table but a `rias\stripewebhooks\exceptions\WebhookFailed` exception will be thrown.
If something goes wrong during the webhook request the thrown exception will be saved in the `exception` column. In that case the controller will send a `500` instead of `200`. 
 
There are two ways this plugin enables you to handle webhook requests: you can opt to queue a job or listen to the events the package will fire.

### Handling webhook requests using jobs 
If you want to do something when a specific event type comes in you can define a job that does the work. Here's an example of such a job:

```php
  <?php
  
  namespace modules\sitemodule\jobs\StripeWebhooks;
  
  use Craft;
  use craft\queue\BaseJob;
  
  class HandleChargeableSource extends BaseJob
  {
      /** @var \rias\stripewebhooks\records\StripeWebhookCall */
      public $model;
  
      public function execute($queue)
      {
          // do your work here
          
          // you can access the payload of the webhook call with `$this->model->payload`
      }
  }
```

After having created your job you must register it at the `jobs` array in the `stripe-webhooks.php` config file. The key should be the name of [the stripe event type](https://stripe.com/docs/api#event_types) where but with the `.` replaced by `_`. The value should be the fully qualified classname.

```php
// config/stripe-webhooks.php

'jobs' => [
    'source_chargeable' => \modules\sitemodule\jobs\StripeWebhooks\HandleChargeableSource::class,
],
```


### Handling webhook requests using events

Instead of queueing jobs to perform some work when a webhook request comes in, you can opt to listen to the events this package will fire. Whenever a valid request hits your app, the package will fire a `stripe-webhooks::<name-of-the-event>` event.

The payload of the events will be the instance of `StripeWebhookCall` that was created for the incoming request. 

You can add a listener in your plugin or module's init function
```php
public function init()
{
    Event::on(
        \rias\stripewebhooks\records\StripeWebhookCall::class,
        'stripe-webhooks::source.chargeable',
        function (\rias\stripewebhooks\events\WebhookEvent $event) {
            $webhookCall = $event->model;
        }
    );
}
```

## Credits
- [Spatie's Laravel Stripe Webhooks Package](https://github.com/spatie/laravel-stripe-webhooks)
- [All Contributors](../../contributors)
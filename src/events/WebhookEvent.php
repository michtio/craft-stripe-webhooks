<?php

namespace rias\stripewebhooks\events;

use rias\stripewebhooks\records\StripeWebhookCall;
use yii\base\Event;

class WebhookEvent extends Event
{
    /** @var StripeWebhookCall */
    public $model;
}

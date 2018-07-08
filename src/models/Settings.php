<?php
/**
 * Stripe Webhooks plugin for Craft CMS 3.x
 *
 * Handle Stripe webhooks in a CraftCMS application
 *
 * @link      https://rias.be
 * @copyright Copyright (c) 2018 Rias
 */

namespace rias\stripewebhooks\models;

use rias\stripewebhooks\records\StripeWebhookCall;
use rias\stripewebhooks\StripeWebhooks;

use Craft;
use craft\base\Model;

/**
 * @author    Rias
 * @package   StripeWebhooks
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /** @var string */
    public $signingSecret = '';

    /** @var array */
    public $jobs = [];

    /** @var string */
    public $model = StripeWebhookCall::class;

    /** @var string */
    public $endpoint = 'stripe-webhooks';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['signingSecret', 'string'],
            ['model', 'string'],
            ['endpoint', 'string'],
        ];
    }
}

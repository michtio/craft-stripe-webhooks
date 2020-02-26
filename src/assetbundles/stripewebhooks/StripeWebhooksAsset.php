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

namespace rias\stripewebhooks\assetbundles\StripeWebhooks;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Rias
 *
 * @since     1.0.0
 */
class StripeWebhooksAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->sourcePath = '@rias/stripewebhooks/assetbundles/stripewebhooks/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/StripeWebhooks.js',
        ];

        $this->css = [
            'css/StripeWebhooks.css',
        ];

        parent::init();
    }
}

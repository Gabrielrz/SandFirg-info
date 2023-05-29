<?php

namespace miAppPaypal;
require __DIR__ . '/../../vendor/autoload.php';
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use Dotenv\Dotenv;
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

class PayPalClient
{
    /**
     * Returns PayPal HTTP client instance with environment which has access
     * credentials context. This can be used invoke PayPal API's provided the
     * credentials have the access to do so.
     */
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }

    /**
     * Setting up and Returns PayPal SDK environment with PayPal Access credentials.
     * For demo purpose, we are using SandboxEnvironment. In production this will be
     * ProductionEnvironment.
     */
    public static function environment()
    {
        $dotenv = Dotenv::createMutable(__DIR__.'/../../');
        $dotenv->safeLoad();

        $clientId = $_ENV["CLIENT_ID"] ?: "";
        $clientSecret = $_ENV["CLIENT_SECRET"] ?: "";
        if($_ENV["PAYPAL_STATUS"]=='production'){
          return new ProductionEnvironment($clientId, $clientSecret);
        }else if($_ENV['PAYPAL_STATUS']=='sandbox'){
          return new SandboxEnvironment($clientId, $clientSecret);

        }

    }
}

<?php


/**
 * AFCF7 Settings
 *
 * Authorize Library function.
 *
 * @package WordPress
 * @subpackage Accept Authorize.NET Payments Using Contact Form 7
 * @since 1.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require AFCF7_PLUGIN_PATH . 'lib/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
 * Function: validate_merchant
 *
 * - Used to validate the Merchant information to show the card form.
 *
 * @param  string  $login_id        Login ID
 * @param  string  $transaction_key Transaction Key
 * @param  bool    $env             Live/Debug mode.
 *
 * @return mixed
 */
function validate_merchant($login_id, $transaction_key, $env = null)
{
    /* Create a merchantAuthenticationType object with authentication details
			retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName($login_id);
    $merchantAuthentication->setTransactionKey($transaction_key);

    // Set the transaction's refId
    $refId = 'ref' . time();

    // Get all existing customer profile ID's
    $request = new AnetAPI\GetCustomerProfileIdsRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $controller = new AnetController\GetCustomerProfileIdsController($request);

    $response = (!empty($env)
        ? $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX)
        : $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION)
    );

    if (
        ($response != null)
        && ($response->getMessages()->getResultCode() == 'Ok')
    ) {
        return true;
    } else {
        $errorMessages = $response->getMessages()->getMessage();

        $messages = array();

        if (!empty($errorMessages)) {
            foreach ($errorMessages as $k => $v) {
                $messages[] = $v->getText();
            }
        }

        return implode('\n', $messages);
    }
    return false;
}

/**
 * Function: _validate_fields
 *
 * @method _validate_fields
 *
 * @param int $form_id
 *
 * @return string
 */
function _validate_fields($form_id)
{
    $mode_sandbox            = get_post_meta($form_id, CF7ADN_META_PREFIX . 'mode_sandbox', true);
    $sandbox_login_id        = get_post_meta($form_id, CF7ADN_META_PREFIX . 'sandbox_login_id', true);
    $sandbox_transaction_key = get_post_meta($form_id, CF7ADN_META_PREFIX . 'sandbox_transaction_key', true);
    $live_login_id           = get_post_meta($form_id, CF7ADN_META_PREFIX . 'live_login_id', true);
    $live_transaction_key    = get_post_meta($form_id, CF7ADN_META_PREFIX . 'live_transaction_key', true);

    if (!empty($mode_sandbox)) {

        if (empty($sandbox_login_id))
            return __('Please enter Sandbox Login ID.', CF7ADN_PREFIX);

        if (empty($sandbox_transaction_key))
            return __('Please enter Sandbox Transaction Key.', CF7ADN_PREFIX);
    }

    if (empty($mode_sandbox)) {

        if (empty($live_login_id))
            return __('Please enter Merchant Login ID.', CF7ADN_PREFIX);

        if (empty($live_transaction_key))
            return __('Please enter Merchant Transaction Key.', CF7ADN_PREFIX);
    }

    return false;
}

/**
 * Function: getUserIpAddr
 *
 * @method getUserIpAddr
 *
 * @return string
 */
function getUserIpAddr()
{
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

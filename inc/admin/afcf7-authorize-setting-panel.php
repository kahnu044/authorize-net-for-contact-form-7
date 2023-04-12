<?php

/**
 * Adds custom panels to the Contact Form 7 form editor.
 *
 * @param array $panels An array of existing panels in the form editor.
 * @return array An array of modified panels that includes the custom panels.
 */

add_filter('wpcf7_editor_panels', 'afcf7_register_settings_panel');

function afcf7_register_settings_panel($panels)
{

    $panels['afcf7-settings-panel'] = array(
        'title'    => __('Authorize.Net Settings', 'contact-form-7-authorize-net-addon'),
        'callback' => 'afcf7_additional_settings',
    );

    return $panels;
}


/**
 * Adding Authorize.Net fields in Authorize.Net Settings tab
 *
 * @param $cf7
 */

function afcf7_additional_settings($cf7)
{
    // Template file
    require_once(AFCF7_TEMPLATE_PATH . 'afcf7-panel-settings.php');
}



/**
 * Action: wpcf7_save_contact_form
 *
 * - Save save_authorize_settings fields data.
 *
 * @param object $WPCF7_form
 */

function afcf7_save_authorize_settings($WPCF7_form)
{

    $wpcf7 = WPCF7_ContactForm::get_current();

    if (!empty($wpcf7)) {
        $post_id = $wpcf7->id;
    }

    $form_fields = array(
        AFCF7_META_PREFIX . 'use_authorize',
        AFCF7_META_PREFIX . 'mode_live',
        AFCF7_META_PREFIX . 'debug',
        AFCF7_META_PREFIX . 'sandbox_login_id',
        AFCF7_META_PREFIX . 'sandbox_transaction_key',
        AFCF7_META_PREFIX . 'live_login_id',
        AFCF7_META_PREFIX . 'live_transaction_key',

        AFCF7_META_PREFIX . 'currency',
        AFCF7_META_PREFIX . 'amount',
        AFCF7_META_PREFIX . 'email',
        AFCF7_META_PREFIX . 'success_returnurl',
        AFCF7_META_PREFIX . 'cancel_returnurl',

        // Customer Details fields
        AFCF7_META_PREFIX . 'customer_details',
        AFCF7_META_PREFIX . 'first_name',
        AFCF7_META_PREFIX . 'last_name',
        AFCF7_META_PREFIX . 'company_name',
        AFCF7_META_PREFIX . 'address',
        AFCF7_META_PREFIX . 'city',
        AFCF7_META_PREFIX . 'state',
        AFCF7_META_PREFIX . 'zip_code',
        AFCF7_META_PREFIX . 'country',
    );

    /**
     * Filter custom form setting fields
     *
     * @var array $form_fields
     */

    $form_fields = apply_filters('afcf7_modify_authorize_settings_field_value', $form_fields);

    if (!empty($form_fields)) {
        foreach ($form_fields as $key) {
            $keyval = sanitize_text_field($_REQUEST[$key]);
            update_post_meta($post_id, $key, $keyval);
        }
    }
}

add_action('wpcf7_save_contact_form', 'afcf7_save_authorize_settings', 20, 2);
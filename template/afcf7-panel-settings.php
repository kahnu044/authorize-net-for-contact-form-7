<?php

$post_id = (isset($_REQUEST['post']) ? sanitize_text_field($_REQUEST['post']) : '');

if (empty($post_id)) {
    $wpcf7 = WPCF7_ContactForm::get_current();
    $post_id = $wpcf7->id();
}

/* Scan All Form Tags */
if ($post_id != "") {
    $afcf7_form = WPCF7_ContactForm::get_instance($post_id);
    $afcf7_all_tags = $afcf7_form->collect_mail_tags();
}


$use_authorize           = get_post_meta( $post_id, AFCF7_META_PREFIX . 'use_authorize', true);
$mode_live               = get_post_meta( $post_id, AFCF7_META_PREFIX . 'mode_live', true );
$debug_authorize         = get_post_meta( $post_id, AFCF7_META_PREFIX . 'debug', true );
$sandbox_login_id        = get_post_meta( $post_id, AFCF7_META_PREFIX . 'sandbox_login_id', true );
$sandbox_transaction_key = get_post_meta( $post_id, AFCF7_META_PREFIX . 'sandbox_transaction_key', true );
$live_login_id           = get_post_meta( $post_id, AFCF7_META_PREFIX . 'live_login_id', true );
$live_transaction_key    = get_post_meta( $post_id, AFCF7_META_PREFIX . 'live_transaction_key', true );
$amount                  = get_post_meta( $post_id, AFCF7_META_PREFIX . 'amount', true );
$quantity                = get_post_meta( $post_id, AFCF7_META_PREFIX . 'quantity', true );
$email                   = get_post_meta( $post_id, AFCF7_META_PREFIX . 'email', true );
$description             = get_post_meta( $post_id, AFCF7_META_PREFIX . 'description', true );

$success_returnURL       = get_post_meta( $post_id, AFCF7_META_PREFIX . 'success_returnurl', true );
$cancel_returnURL        = get_post_meta( $post_id, AFCF7_META_PREFIX . 'cancel_returnurl', true );
$message                 = get_post_meta( $post_id, AFCF7_META_PREFIX . 'message', true );

$currency                = get_post_meta( $post_id, AFCF7_META_PREFIX . 'currency', true );

$customer_details        = get_post_meta( $post_id, AFCF7_META_PREFIX . 'customer_details', true );

$first_name              = get_post_meta( $post_id, AFCF7_META_PREFIX . 'first_name', true );
$last_name               = get_post_meta( $post_id, AFCF7_META_PREFIX . 'last_name', true );
$company_name            = get_post_meta( $post_id, AFCF7_META_PREFIX . 'company_name', true );
$address                 = get_post_meta( $post_id, AFCF7_META_PREFIX . 'address', true );
$city                    = get_post_meta( $post_id, AFCF7_META_PREFIX . 'city', true );
$state                   = get_post_meta( $post_id, AFCF7_META_PREFIX . 'state', true );
$zip_code                = get_post_meta( $post_id, AFCF7_META_PREFIX . 'zip_code', true );
$country                 = get_post_meta( $post_id, AFCF7_META_PREFIX . 'country', true );


$use_authorize  = true;
$mode_live = true;
echo var_dump($mode_live);
$currency_code = array(
    'AUD' => 'Australian Dollar',
    'BRL' => 'Brazilian Real',
    'CAD' => 'Canadian Dollar',
    'CZK' => 'Czech Koruna',
    'DKK' => 'Danish Krone',
    'EUR' => 'Euro',
    'HKD' => 'Hong Kong Dollar',
    'HUF' => 'Hungarian Forint',
    'ILS' => 'Israeli New Sheqel',
    'JPY' => 'Japanese Yen',
    'MYR' => 'Malaysian Ringgit',
    'MXN' => 'Mexican Peso',
    'NOK' => 'Norwegian Krone',
    'NZD' => 'New Zealand Dollar',
    'PHP' => 'Philippine Peso',
    'PLN' => 'Polish Zloty',
    'GBP' => 'Pound Sterling',
    'RUB' => 'Russian Ruble',
    'SGD' => 'Singapore Dollar',
    'SEK' => 'Swedish Krona',
    'CHF' => 'Swiss Franc',
    'TWD' => 'Taiwan New Dollar',
    'THB' => 'Thai Baht',
    'TRY' => 'Turkish Lira',
    'USD' => 'U.S. Dollar'
);

$selected = '';


$args = array(
    'post_type'      => array('page'),
    'orderby'        => 'title',
    'posts_per_page' => -1
);
$pages = get_posts($args);
$all_pages = array();
if (!empty($pages)) {
    foreach ($pages as $page) {
        $all_pages[$page->ID] = $page->post_title;
    }
}

wp_enqueue_style('wp-pointer');
wp_enqueue_script('wp-pointer');
?>


<style>
    #contact-form-editor .form-table th {
        width: auto !important;
    }

    .afcf7-settings .afcf7-left-box {
        padding: 8px 12px;
        margin-bottom: 20px;
    }

    #contact-form-editor .form-table td select {
        width: 100% !important;
    }

    .afcf7-tooltip {
        display: inline-block;
        width: 18px;
        height: 18px;
        vertical-align: middle;
        border-radius: 100%;
        margin-left: 10px;
        background-image: url(data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhcyIgZGF0YS1pY29uPSJpbmZvLWNpcmNsZSIgY2xhc3M9InN2Zy1pbmxpbmUtLWZhIGZhLWluZm8tY2lyY2xlIGZhLXctMTYiIHJvbGU9ImltZyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiI+PHBhdGggZmlsbD0iY3VycmVudENvbG9yIiBkPSJNMjU2IDhDMTE5LjA0MyA4IDggMTE5LjA4MyA4IDI1NmMwIDEzNi45OTcgMTExLjA0MyAyNDggMjQ4IDI0OHMyNDgtMTExLjAwMyAyNDgtMjQ4QzUwNCAxMTkuMDgzIDM5Mi45NTcgOCAyNTYgOHptMCAxMTBjMjMuMTk2IDAgNDIgMTguODA0IDQyIDQycy0xOC44MDQgNDItNDIgNDItNDItMTguODA0LTQyLTQyIDE4LjgwNC00MiA0Mi00MnptNTYgMjU0YzAgNi42MjctNS4zNzMgMTItMTIgMTJoLTg4Yy02LjYyNyAwLTEyLTUuMzczLTEyLTEydi0yNGMwLTYuNjI3IDUuMzczLTEyIDEyLTEyaDEydi02NGgtMTJjLTYuNjI3IDAtMTItNS4zNzMtMTItMTJ2LTI0YzAtNi42MjcgNS4zNzMtMTIgMTItMTJoNjRjNi42MjcgMCAxMiA1LjM3MyAxMiAxMnYxMDBoMTJjNi42MjcgMCAxMiA1LjM3MyAxMiAxMnYyNHoiPjwvcGF0aD48L3N2Zz4=);
        background-repeat: no-repeat;
        background-size: cover;
    }
</style>

<?php


echo '<div class="afcf7-settings">' .
	'<div class="afcf7-left-box postbox">' .
		'<input style="display: none;" id="' . AFCF7_META_PREFIX . 'customer_details" name="' . AFCF7_META_PREFIX . 'customer_details" type="checkbox" value="1" ' . checked( $customer_details, 1, false ) . ' />' .
		'<table class="form-table">' .
			'<tbody>' .
				'<tr class="form-field">' .
					'<th scope="row">' .
						'<label for="' . AFCF7_META_PREFIX . 'use_authorize">' .
							__( 'Enable Authorize.Net Payment Form', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7enable-authorizenet-payment-form"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'use_authorize" name="' . AFCF7_META_PREFIX . 'use_authorize" type="checkbox" class="enable_required" value="1" ' . checked( $use_authorize, 1, false ) . '/>' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'mode_live">' .
							__( 'Enable Live API Mode', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7enable-live-api-mode"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'mode_live" name="' . AFCF7_META_PREFIX . 'mode_live" type="checkbox" value="1" ' . checked( $mode_live, 1, false ) . ' />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field">' .
					'<th scope="row">' .
						'<label for="' . AFCF7_META_PREFIX . 'debug">' .
							__( 'Enable Debug Mode', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7enable-debug-mode"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'debug" name="' . AFCF7_META_PREFIX . 'debug" type="checkbox" value="1" ' . checked( $debug_authorize, 1, false ) . '/>' .
					'</td>' .
				'</tr>';

				/**
				 * - Add new field at the start.
				 *
				 * @var int $post_id
				 */
                do_action(  'afcf7_add_new_field_start', $post_id );

				echo '<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'sandbox_login_id">' .
							__( 'Sandbox Login ID (required)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7sandbox-login-id"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'sandbox_login_id" name="' . AFCF7_META_PREFIX . 'sandbox_login_id" type="text" class="large-text form-required-fields" value="' . esc_attr( $sandbox_login_id ) . '" ' . ( empty( $mode_live ) && !empty( $use_authorize ) ? 'required' : '' ) . '  />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'sandbox_transaction_key">' .
							__( 'Sandbox Transaction Key (required)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7sandbox-transaction-key"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'sandbox_transaction_key" name="' . AFCF7_META_PREFIX . 'sandbox_transaction_key" type="text" class="large-text form-required-fields" value="' . esc_attr( $sandbox_transaction_key ) . '"  ' . ( empty( $mode_live ) && !empty( $use_authorize ) ? 'required' : '' ) . '  />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'live_login_id">' .
							__( 'Live Login ID (required)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7live-login-id"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'live_login_id" name="' . AFCF7_META_PREFIX . 'live_login_id" type="text" class="large-text form-required-fields" value="' . esc_attr( $live_login_id ) . '" ' . ( !empty( $mode_live ) && !empty( $use_authorize ) ? 'required' : '' ) . '/>' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'live_transaction_key">' .
							__( 'Live Transaction Key (required)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7live-transaction-key"></span>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'live_transaction_key" name="' . AFCF7_META_PREFIX . 'live_transaction_key" type="text" class="large-text form-required-fields" value="' . esc_attr( $live_transaction_key ) . '" ' . ( !empty( $mode_live ) && !empty( $use_authorize ) ? 'required' : '' ) . '/>' .
					'</td>' .
				'</tr>' .
                '<tr class="form-field">' .
                    '<th>' .
                        '<label for="' . AFCF7_META_PREFIX . 'currency">' .
                            __( 'Select Currency (required)', 'contact-form-7-authorize-net-addon' ) .
                        '</label>' .
                        '<span class="afcf7-tooltip" id="afcf7select-currency"></span>' .
                    '</th>' .
                    '<td>' .
                        '<select id="' . AFCF7_META_PREFIX . 'currency" name="' . AFCF7_META_PREFIX . 'currency" ' . ( !empty( $use_authorize ) ? 'required' : '' ) . '>';
                        echo '<option value="">Select Currency</option>';
                            if ( !empty( $currency_code ) ) {
                                foreach ( $currency_code as $key => $value ) {
                                    echo '<option value="' . esc_attr( $key ) . '" ' . selected( $currency, $key, false ) . '>' . esc_attr( $value ) . '</option>';
                                }
                            }

                        echo '</select>' .
                    '</td>' .
                '</tr/>' .
				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'amount">' .
							__( 'Amount Field Name (required)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7amount-field"></span>' .
					'</th>' .
					'<td>' .
						'<select id="' . AFCF7_META_PREFIX . 'amount" class="form-required-fields" name="' . AFCF7_META_PREFIX . 'amount" ' . ( !empty( $use_authorize ) ? 'required' : '' ) . '>';
							echo '<option value="">Select Field Name</option>';
									if ( !empty( $afcf7_all_tags ) ) {
										foreach ( $afcf7_all_tags as $key => $value ) {
											echo '<option value="' . esc_attr( $value ) . '" ' . selected( $amount, $value, false ) . '>' . esc_attr( $value ) . '</option>';
										}
								}
						echo '</select>' .
					'</td>' .
				'</tr>' .

				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'email">' .
							__( 'Customer Email Field Name (Optional)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7customer-email-field-name"></span>' .
					'</th>' .
					'<td>' .
						'<select id="' . AFCF7_META_PREFIX . 'email" name="' . AFCF7_META_PREFIX . 'email">';
						echo '<option value="">Select Field Name</option>';
								if ( !empty( $afcf7_all_tags ) ) {
									foreach ( $afcf7_all_tags as $key => $value ) {
										echo '<option value="' . esc_attr( $value ) . '" ' . selected( $email, $value, false ) . '>' . esc_attr( $value ) . '</option>';
									}
							}
						echo '</select>' .
					'</td>' .
				'</tr>' .


				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'success_returnurl">' .
							__( 'Success Return URL (Optional)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7success-return-url"></span>' .
					'</th>' .
					'<td>' .
						'<select id="' . AFCF7_META_PREFIX . 'success_returnurl" name="' . AFCF7_META_PREFIX . 'success_returnurl">' .
							'<option>' . __( 'Select page', 'contact-form-7-authorize-net-addon' ) . '</option>';

							if( !empty( $all_pages ) ) {
								foreach ( $all_pages as $post_id => $title ) {
									echo '<option value="' . esc_attr( $post_id ) . '" ' . selected( $success_returnURL, $post_id, false )  . '>' . $title . '</option>';
								}
							}

						echo '</select>' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'cancel_returnurl">' .
							__( 'Cancel Return URL (Optional)', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
						'<span class="afcf7-tooltip" id="afcf7cancel-return-url"></span>' .
					'</th>' .
					'<td>' .
						'<select id="' . AFCF7_META_PREFIX . 'cancel_returnurl" name="' . AFCF7_META_PREFIX . 'cancel_returnurl">' .
							'<option>' . __( 'Select page', 'contact-form-7-authorize-net-addon' ) . '</option>';

							if( !empty( $all_pages ) ) {
								foreach ( $all_pages as $post_id => $title ) {
									echo '<option value="' . esc_attr( $post_id ) . '" ' . selected( $cancel_returnURL, $post_id, false )  . '>' . $title . '</option>';
								}
							}

						echo '</select>' .
					'</td>' .
				'</tr>'.

				'<tr class="form-field">' .
					'<th colspan="2">' .
						'<label for="' . AFCF7_META_PREFIX . 'customer_details">' .
							'<h3 style="margin: 0;">' .
								__( 'Customer Details', 'contact-form-7-authorize-net-addon' ) .
								'<span class="arrow-switch"></span>' .
							'</h3>' .
						'</label>' .
					'</th>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'first_name">' .
							__( 'First Name', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'first_name" name="' . AFCF7_META_PREFIX . 'first_name" type="text" class="regular-text" value="' . esc_attr( $first_name ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'last_name">' .
							__( 'Last Name', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'last_name" name="' . AFCF7_META_PREFIX . 'last_name" type="text" class="regular-text" value="' . esc_attr( $last_name ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'company_name">' .
							__( 'Company Name', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'company_name" name="' . AFCF7_META_PREFIX . 'company_name" type="text" class="regular-text" value="' . esc_attr( $company_name ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'address">' .
							__( 'Address', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'address" name="' . AFCF7_META_PREFIX . 'address" type="text" class="regular-text" value="' . esc_attr( $address ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'city">' .
							__( 'City', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'city" name="' . AFCF7_META_PREFIX . 'city" type="text" class="regular-text" value="' . esc_attr( $city ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'state">' .
							__( 'State', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'state" name="' . AFCF7_META_PREFIX . 'state" type="text" class="regular-text" value="' . esc_attr( $state ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'zip_code">' .
							__( 'Zip Code', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'zip_code" name="' . AFCF7_META_PREFIX . 'zip_code" type="text" class="regular-text" value="' . esc_attr( $zip_code ) . '" />' .
					'</td>' .
				'</tr>' .
				'<tr class="form-field hide-show">' .
					'<th>' .
						'<label for="' . AFCF7_META_PREFIX . 'country">' .
							__( 'Country', 'contact-form-7-authorize-net-addon' ) .
						'</label>' .
					'</th>' .
					'<td>' .
						'<input id="' . AFCF7_META_PREFIX . 'country" name="' . AFCF7_META_PREFIX . 'country" type="text" class="regular-text" value="' . esc_attr( $country ) . '" />' .
					'</td>' .
				'</tr>';

				/**
				 * - Add new field at the end.
				 *
				 * @var int $post_id
				 */
				do_action(  'afcf7_add_new_field_end', $post_id );

				echo '<input type="hidden" name="post" value="' . esc_attr( $post_id ) . '">' .
			'</tbody>' .
		'</table>' .
	'</div>' .

'</div>';



add_action('admin_print_footer_scripts', function () {
    ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery('#afcf7enable-authorizenet-payment-form').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7enable-authorizenet-payment-form').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Enable Authorize.Net Payment</h3>' .
                                        '<p>To make enable Authorize.Net Payment with this Form.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });
            //jQuery selector to point to
            jQuery('#afcf7enable-live-api-mode').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7enable-live-api-mode').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Sandbox mode</h3>' .
                                        '<p>Check the Authorize.Net testing guide <a href="https://developer.authorize.net/hello_world/testing_guide/" target="_blank">here</a>.This will display "sandbox mode" warning on checkout.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7enable-debug-mode').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7enable-debug-mode').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Debug mode</h3>' .
                                        '<p>From this we can get the whole response of Payment Gateway and display in each Entry detail Page.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });


            jQuery('#afcf7sandbox-login-id').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7sandbox-login-id').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Get Your Sandbox Login ID</h3>' .
                                        '<p>Get it from <a href="https://sandbox.authorize.net" target="_blank"> Sandbox Authorize.net</a> then <strong> Account > Security Settings > API  Credentials & Keys </strong> page  in your Authorize.Net account.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7sandbox-transaction-key').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7sandbox-transaction-key').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Get Your Sandbox Transaction Key</h3>' .
                                        '<p>Get it from <a href="https://sandbox.authorize.net" target="_blank"> Sandbox Authorize.net</a> then <strong>Account > Security Settings > API Credentials & Keys </strong> page in your Authorize.Net account. For security reasons, you cannot view your Transaction Key, but you will be able to generate a new one. </p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7live-login-id').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7live-login-id').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Get Your Live Login ID</h3>' .
                                        '<p>Get it from <a href="https://account.authorize.net" target="_blank">Authorize.net</a> then <strong>Account > Security Settings > API Credentials & Keys </strong> page  in your Authorize.Net account.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7live-transaction-key').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7live-transaction-key').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Get Your Live Transaction Key</h3>' .
                                        '<p>Get it from <a href="https://account.authorize.net" target="_blank">Authorize.net</a> then <strong> Account > Security Settings > API Credentials & Keys </strong> page in your Authorize.Net account. For security reasons, you cannot view your Transaction Key, but you will be able to generate a new one. </p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7amount-field').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7amount-field').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Add Amount Name</h3>' .
                                        '<p>Add here the Name of amount field created in Form. Its required because payment will capture payble amount from this field.</p>' .
                                        '<p><strong><span style="color:red">Note:</span> Save the FORM details to view the list of fields.</strong></p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7quantity-field-name').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7quantity-field-name').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Add Quantity Field Name</h3>' .
                                        '<p>Add here the Name of quantity field created in Form.</p>' .
                                        '<p><strong><span style="color:red">Note:</span> Save the FORM details to view the list of fields.</strong></p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7customer-email-field-name').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7customer-email-field-name').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Add Customer Email Field Name</h3>' .
                                        '<p>Add here the Name of customer email field created in Form.</p>' .
                                        '<p><strong><span style="color:red">Note:</span> Save the FORM details to view the list of fields.</strong></p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7description-field-name').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7description-field-name').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Add Description Field Name</h3>' .
                                        '<p>Add here the Name of description field created in Form.</p>' .
                                        '<p><strong><span style="color:red">Note:</span> Save the FORM details to view the list of fields.</strong></p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7select-currency').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7select-currency').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Select Currency</h3>' .
                                        '<p>Select the currency which is selected from your authorize.net merchant account.<br/><strong>Note:</strong>Authorize.net dont provide multiple currencies for single account</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7cancel-return-url').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7cancel-return-url').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Select Cancel Page URL</h3>' .
                                        '<p>Here is the list of all your WP pages. You need to create your Cancel Page and select that page from this dropdown. So, when any payment will canceled then on return it will redirect on this Cancel Page.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

            jQuery('#afcf7success-return-url').on('mouseenter click', function() {
                jQuery('body .wp-pointer-buttons .close').trigger('click');
                jQuery('#afcf7success-return-url').pointer({
                    pointerClass: 'wp-pointer afcf7pointer',
                    content: '<?php
                                _e(
                                    '<h3>Select Success Page URL</h3>' .
                                        '<p>Here is the list of all your WP pages. You need to create your Success Page and select that page from this dropdown. <br/>So, when any payment will successfully done then on return it will redirect on this Success Page.<br/> On success Page you can use our shortcode <b>[authorize-details]</b> to show transaction detail.</p>',
                                    'contact-form-7-authorize-net-addon'
                                ); ?>',
                    position: 'left center',
                }).pointer('open');
            });

        });
    </script>
<?php
    echo ob_get_clean();
});
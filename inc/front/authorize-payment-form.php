<?php

add_action('wpcf7_admin_init', 'afcf7_tag_generator', 55, 0);

function afcf7_tag_generator()
{
    $tag_generator = WPCF7_TagGenerator::get_instance();
    $tag_generator->add(
        'authorize_payment',
        __('Authorize.Net', 'contact-form-7'),
        'afcf7_tag_generator_block',
    );
}

/**
 * -Render CF7 Shortcode settings into backend.
 *
 * @method wpcf7_tag_generator_authorize_net
 *
 * @param  object $contact_form
 * @param  array  $args
 */

function afcf7_tag_generator_block($contact_form, $args = '')
{
    $args = wp_parse_args($args, array());
    $type = $args['id'];

    $description = __("Generate a form-tag for to display Authorize.Net payment form", 'contact-form-7-authorize-net-addon');
?>
    <div class="control-box">
        <fieldset>
            <legend><?php echo esc_html($description); ?></legend>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'contact-form-7-authorize-net-addon')); ?></label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr($args['content'] . '-id'); ?>"><?php echo esc_html(__('Id attribute', 'contact-form-7')); ?></label>
                        </th>
                        <td>
                            <input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr($args['content'] . '-id'); ?>" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr($args['content'] . '-class'); ?>"><?php echo esc_html(__('Class attribute', 'contact-form-7')); ?></label>
                        </th>
                        <td>
                            <input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr($args['content'] . '-class'); ?>" />
                        </td>
                    </tr>

                </tbody>
            </table>
        </fieldset>
    </div>
<?php
}



add_action('wpcf7_init', 'wpcf7_add_form_tag_kailashblock', 10, 0);

function wpcf7_add_form_tag_kailashblock()
{
    wpcf7_add_form_tag(
        array('authorize_payment', 'authorize_payment*'),
        'wpcf7_add_form_tag_authorize_net',
        array('name-attr' => true)
    );
}


/**
 * - Render CF7 Shortcode on front end.
 *
 * @method wpcf7_add_form_tag_authorize_net
 *
 * @param $tag
 *
 * @return html
 */
function wpcf7_add_form_tag_authorize_net($tag)
{

    if (empty($tag->name)) {
        return '';
    }

    $validation_error = wpcf7_get_validation_error($tag->name);

    $class = wpcf7_form_controls_class($tag->type, 'wpcf7-text');

    if (in_array($tag->basetype, array('email', 'url', 'tel'))) {
        $class .= ' wpcf7-validates-as-' . $tag->basetype;
    }

    if ($validation_error) {
        $class .= ' wpcf7-not-valid';
    }

    $atts = array();

    if ($tag->is_required()) {
        $atts['aria-required'] = 'true';
    }

    $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

    $atts['value'] = 1;

    $atts['type'] = 'hidden';
    $atts['name'] = $tag->name;
    $atts = wpcf7_format_atts($atts);

    $form_instance = WPCF7_ContactForm::get_current();
    $form_id = $form_instance->id();

    $use_authorize           = get_post_meta($form_id, AFCF7_META_PREFIX . 'use_authorize', true);
    $mode_live            = get_post_meta($form_id, AFCF7_META_PREFIX . 'mode_live', true);
    $sandbox_login_id        = get_post_meta($form_id, AFCF7_META_PREFIX . 'sandbox_login_id', true);
    $sandbox_transaction_key = get_post_meta($form_id, AFCF7_META_PREFIX . 'sandbox_transaction_key', true);
    $live_login_id           = get_post_meta($form_id, AFCF7_META_PREFIX . 'live_login_id', true);
    $live_transaction_key    = get_post_meta($form_id, AFCF7_META_PREFIX . 'live_transaction_key', true);


    if (empty($use_authorize)) {
        return;
    }

    // if (!empty($this->_validate_fields($form_id)))
    //     return $this->_validate_fields($form_id);

    $login_id        = (!empty($mode_live) ? $sandbox_login_id : $live_login_id);
    $transaction_key = (!empty($mode_live) ? $sandbox_transaction_key : $live_transaction_key);

    // $merchant_validate = $this->validate_merchant($login_id, $transaction_key, $mode_live);

    $value = (string) reset($tag->values);

    $found = 0;
    $html = '';

    // if (!empty($merchant_validate) && $merchant_validate != 1) {
    //     return '<div class="cf7adn-error">' . wp_kses_post($merchant_validate) . '</div>';
    // }

    ob_start();


    if ($contact_form = wpcf7_get_current_contact_form()) {
        $form_tags = $contact_form->scan_form_tags();

        foreach ($form_tags as $k => $v) {

            if ($v['type'] == $tag->type) {
                $found++;
            }

            if ($v['name'] == $tag->name) {
                if ($found <= 1) {

                    echo    '<div class="cf7adn-form-code">
									<div id="authorize-payment">

											<label for="authorize_card_holder_name">Name on Card <span class="required">*</span></label>
											<input type="text" id="authorize_card_holder_name" " name="' . $tag->basetype . '[card_holder_name]" size="20" class="' . $class . '" required placeholder="John Doe" onkeydown="return /[a-zA-Z ]/i.test(event.key)">

											<label for="authorize_card_number">Credit card number <span class="required">*</span></label>
											<input type="text" autocomplete="off" id="authorize_card_number" name="' . $tag->basetype . '[card_number]" data-authorize="number" class="credit-card ' . $class . '" maxlength="19" required placeholder="1111-2222-3333-4444" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength)">

											<div class="authorize-row">
												<div class="col-50">
													<label for="authorize_expire">Exp Year <span class="required">*</span></label>
													<input type="text" id="authorize_expire" name="' . $tag->basetype . '[expire]"  maxlength="5" placeholder="12/26" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength)">
												</div>
												<div class="col-50">
													<label for="authorize_cvv">CVV <span class="required">*</span></label>
													<input type="number" id="authorize_cvv" name="' . $tag->basetype . '[cvv_number]" data-authorize="cvc" class="' . $class . '" maxlength="4" placeholder="123" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength)">
												</div>
											</div>
									</div>
								</div>';
                }

                break;
            }
        }
    }

    return ob_get_clean();
}

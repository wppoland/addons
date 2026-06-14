<?php
/**
 * "Add-Ons" product-data panel in the WooCommerce product editor.
 *
 * @var array<int, array<string, mixed>> $add_ons Stored add-on definitions.
 *
 * @package Addons/Templates
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template-scope variables supplied by ProductData.

defined('ABSPATH') || exit;

$addons_rows = isset($add_ons) && is_array($add_ons) ? $add_ons : array();
?>
<div id="addons_product_data" class="panel woocommerce_options_panel">
    <div class="options_group">
        <p class="form-field">
            <label><?php esc_html_e('Product add-ons', 'addons'); ?></label>
            <span class="description">
                <?php esc_html_e('Define options customers can choose before adding this product to the cart. A positive price is added to the line total.', 'addons'); ?>
            </span>
        </p>

        <div class="addons-admin-rows" data-addons-rows>
            <?php foreach ($addons_rows as $addons_i => $addons_row) : ?>
                <?php
                $addons_label    = (string) ($addons_row['label'] ?? '');
                $addons_type     = (string) ($addons_row['type'] ?? 'text');
                $addons_required = ! empty($addons_row['required']);
                $addons_price    = (string) ($addons_row['price'] ?? '');
                $addons_options  = '';

                if (isset($addons_row['options']) && is_array($addons_row['options'])) {
                    $addons_lines = array();
                    foreach ($addons_row['options'] as $addons_opt_label => $addons_opt_price) {
                        $addons_lines[] = $addons_opt_label . ' | ' . $addons_opt_price;
                    }
                    $addons_options = implode("\n", $addons_lines);
                }
                ?>
                <div class="addons-admin-row" data-addons-row>
                    <p class="form-field">
                        <input type="text" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][label]" placeholder="<?php esc_attr_e('Label', 'addons'); ?>" value="<?php echo esc_attr($addons_label); ?>" />
                        <select name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][type]">
                            <option value="text" <?php selected($addons_type, 'text'); ?>><?php esc_html_e('Text', 'addons'); ?></option>
                            <option value="checkbox" <?php selected($addons_type, 'checkbox'); ?>><?php esc_html_e('Checkbox', 'addons'); ?></option>
                            <option value="select" <?php selected($addons_type, 'select'); ?>><?php esc_html_e('Select', 'addons'); ?></option>
                        </select>
                        <input type="text" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][price]" placeholder="<?php esc_attr_e('Price', 'addons'); ?>" value="<?php echo esc_attr($addons_price); ?>" style="width:6em;" />
                        <label>
                            <input type="checkbox" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][required]" value="1" <?php checked($addons_required); ?> />
                            <?php esc_html_e('Required', 'addons'); ?>
                        </label>
                        <button type="button" class="button addons-admin-remove" data-addons-remove><?php esc_html_e('Remove', 'addons'); ?></button>
                    </p>
                    <p class="form-field">
                        <textarea name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][options]" rows="3" placeholder="<?php esc_attr_e('Select options, one per line: Label | price', 'addons'); ?>" style="width:100%;"><?php echo esc_textarea($addons_options); ?></textarea>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="form-field">
            <button type="button" class="button addons-admin-add" data-addons-add><?php esc_html_e('Add option', 'addons'); ?></button>
        </p>

        <script type="text/template" id="addons-row-template">
            <div class="addons-admin-row" data-addons-row>
                <p class="form-field">
                    <input type="text" name="addons_def[__INDEX__][label]" placeholder="<?php esc_attr_e('Label', 'addons'); ?>" value="" />
                    <select name="addons_def[__INDEX__][type]">
                        <option value="text"><?php esc_html_e('Text', 'addons'); ?></option>
                        <option value="checkbox"><?php esc_html_e('Checkbox', 'addons'); ?></option>
                        <option value="select"><?php esc_html_e('Select', 'addons'); ?></option>
                    </select>
                    <input type="text" name="addons_def[__INDEX__][price]" placeholder="<?php esc_attr_e('Price', 'addons'); ?>" value="" style="width:6em;" />
                    <label>
                        <input type="checkbox" name="addons_def[__INDEX__][required]" value="1" />
                        <?php esc_html_e('Required', 'addons'); ?>
                    </label>
                    <button type="button" class="button addons-admin-remove" data-addons-remove><?php esc_html_e('Remove', 'addons'); ?></button>
                </p>
                <p class="form-field">
                    <textarea name="addons_def[__INDEX__][options]" rows="3" placeholder="<?php esc_attr_e('Select options, one per line: Label | price', 'addons'); ?>" style="width:100%;"></textarea>
                </p>
            </div>
        </script>
    </div>
</div>
<?php
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

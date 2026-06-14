<?php
/**
 * Default settings, merged under the option key `addons_settings`.
 *
 * @package Addons
 *
 * @return array<string, mixed>
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

return [
    // Master switch: render fields and apply price deltas.
    'enabled'        => true,
    // Heading shown above the add-on fields on the product page. Empty hides it.
    'group_title'    => 'Product options',
    // Show the formatted price next to paid options.
    'show_prices'    => true,
    // Show a red asterisk next to required field labels.
    'show_required'  => true,
    // Wrap the group in a styled card on the product page.
    'card_style'     => true,
];

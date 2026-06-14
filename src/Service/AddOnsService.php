<?php

declare(strict_types=1);

namespace Addons\Service;

use Addons\Contract\HasHooks;
use WPPoland\StorefrontKit\AddOns\ProductAddOnsEngine;

defined('ABSPATH') || exit;

/**
 * Thin adapter over the storefront-kit {@see ProductAddOnsEngine}.
 *
 * Supplies this plugin's text-domain ('addons'), option storage, the per-product
 * meta read closure and the front-end fields template. All add-on orchestration
 * (render under the form, validation, cart capture, price adjustment, cart/order
 * display) lives in the namespace-neutral engine; this class only wires
 * localisation, settings, the product-meta reader and the template path.
 */
final class AddOnsService implements HasHooks
{
    public const OPTION   = 'addons_settings';
    public const META_KEY = '_addons_definitions';

    private ?ProductAddOnsEngine $engine = null;

    public function __construct()
    {
        // The engine ships with storefront-kit >= 1.5.0. When present, wire it
        // with this plugin's text-domain / option / meta. Otherwise leave the
        // service inert (see registerHooks()).
        if (! class_exists(ProductAddOnsEngine::class)) {
            return;
        }

        $this->engine = new ProductAddOnsEngine(
            cartKey: 'addons_selections',
            fieldPrefix: 'addons_field_',
            fieldsTemplate: 'add-on-fields',
            labels: [
                'group_title'    => __('Product options', 'addons'),
                'required_error' => __('Please complete the "{label}" option before adding to cart.', 'addons'),
            ],
            isEnabled: fn (): bool => $this->isEnabled(),
            settings: fn (): array => $this->settings(),
            productMeta: fn (\WC_Product $product) => $this->productMeta($product),
            renderTemplate: function (string $template, array $context): void {
                $this->renderTemplate($template, $context);
            },
        );
    }

    public function registerHooks(): void
    {
        if ($this->engine instanceof ProductAddOnsEngine) {
            add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
            $this->engine->registerHooks();
            return;
        }

        // storefront-kit < 1.5.0 has no ProductAddOnsEngine. Bump the
        // `wppoland/storefront-kit` constraint (composer update) to enable
        // add-ons. No hooks are registered until the engine is present.
    }

    /**
     * Enqueue the lightweight front-end stylesheet on product pages only.
     */
    public function enqueueAssets(): void
    {
        if (! function_exists('is_product') || ! is_product() || ! $this->isEnabled()) {
            return;
        }

        wp_enqueue_style(
            'addons',
            \Addons\Plugin::instance()->url('assets/css/addons.css'),
            [],
            \Addons\VERSION,
        );
    }

    private function isEnabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    /**
     * Read the stored add-on definitions for a product (host owns the meta key).
     *
     * @return array<int, array<string, mixed>>
     */
    private function productMeta(\WC_Product $product): array
    {
        $raw = $product->get_meta(self::META_KEY, true);

        return is_array($raw) ? $raw : [];
    }

    /**
     * Stored settings merged over packaged defaults.
     *
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        $stored = get_option(self::OPTION, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        /** @var array<string, mixed> $defaults */
        $defaults = require ADDONS_DIR . 'config/defaults.php';

        return array_merge($defaults, $stored);
    }

    /**
     * Render a packaged template, exposing the context as local variables.
     *
     * @param array<string, mixed> $context
     */
    private function renderTemplate(string $template, array $context): void
    {
        $file = ADDONS_DIR . 'templates/' . $template . '.php';

        if (! is_readable($file)) {
            return;
        }

        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Controlled, internal template context only.
        extract($context, EXTR_SKIP);
        require $file;
    }
}

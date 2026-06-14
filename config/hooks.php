<?php
/**
 * Boot order: services listed here are resolved from the container and have
 * their registerHooks() called during Plugin::boot(). Each must implement
 * Addons\Contract\HasHooks.
 *
 * @package Addons
 *
 * @return array<class-string>
 */

declare(strict_types=1);

use Addons\Admin\ProductData;
use Addons\Admin\Settings;
use Addons\Service\AddOnsService;

defined('ABSPATH') || exit;

return [
    AddOnsService::class,
    ...(is_admin() ? [ProductData::class, Settings::class] : []),
];

<?php

/**
 * Plugin Name: VpinUnf Core
 * Plugin URI:  https://github.com/nftsaavedra/wptheme-vice-unf
 * Description: Core functionality and domain data (Custom Post Types, Taxonomies, and REST API endpoints) for the VpinUnf theme.
 * Version:     1.1.0
 * Author:      VpinUnf
 * License:     ISC
 * Text Domain: vpinunf-core
 */

if (! defined('ABSPATH')) {
    exit;
}

define('VPINUNF_CORE_VERSION', '1.1.0');
define('VPINUNF_CORE_PATH', plugin_dir_path(__FILE__));
define('VPINUNF_CORE_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader PSR-4 simple para VpinUnf\Core
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'VpinUnf\\Core\\';
    $base_dir = VPINUNF_CORE_PATH . 'src/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

add_action('admin_init', function (): void {
    $theme = wp_get_theme();
    if ('vpinunf' !== $theme->get_stylesheet() && 'vpinunf' !== $theme->get_template()) {
        add_action('admin_notices', function (): void {
            echo '<div class="notice notice-warning is-dismissible"><p><strong>Advertencia:</strong> El plugin <em>VpinUnf Core</em> está diseñado para funcionar en conjunto con el tema <strong>VpinUnf</strong>. Ciertas funcionalidades del frontend podrían no mostrarse correctamente con el tema actual, pero tus datos están seguros.</p></div>';
        });
    }
});

/**
 * Bootstrap del Plugin (Contenedor / Registry simple)
 */
function vpinunf_core_bootstrap(): void
{
    // 1. Inicializar API Endpoints y Ajax
    $api_endpoints = new \VpinUnf\Core\Api\Endpoints();
    $api_endpoints->register_hooks();

    $ajax_endpoints = new \VpinUnf\Core\Api\Ajax();
    $ajax_endpoints->register_hooks();

    // 2. Registrar Custom Post Types inyectando las dependencias (DI)
    $cpts = [
        new \VpinUnf\Core\PostType\Slider(),
        new \VpinUnf\Core\PostType\Evento(),
        new \VpinUnf\Core\PostType\Socio(),
        new \VpinUnf\Core\PostType\Reglamento(),
        new \VpinUnf\Core\PostType\Autoridad(),
        new \VpinUnf\Core\PostType\Dependencia()
    ];
    $registrar = new \VpinUnf\Core\PostType\Registrar($cpts);
    add_action('init', [$registrar, 'register_all'], 0);

    // 3. Registrar Hooks de Servicios
    $slider_service = new \VpinUnf\Core\Service\SliderService();
    $slider_service->register_hooks();

    $eventos_service = new \VpinUnf\Core\Service\EventosService();
    $eventos_service->register_hooks();

    $socios_service = new \VpinUnf\Core\Service\SocioService();
    $socios_service->register_hooks();

    $post_service = new \VpinUnf\Core\Service\PostService();
    $post_service->register_hooks();

    // 4. Registrar MetaBoxes de CPTs
    $autoridad_metabox = new \VpinUnf\Core\MetaBox\AutoridadMetaBox();
    $autoridad_metabox->register_hooks();

    $dependencia_metabox = new \VpinUnf\Core\MetaBox\DependenciaMetaBox();
    $dependencia_metabox->register_hooks();

    $evento_metabox = new \VpinUnf\Core\MetaBox\EventoMetaBox();
    $evento_metabox->register_hooks();

    $slider_metabox = new \VpinUnf\Core\MetaBox\SliderMetaBox();
    $slider_metabox->register_hooks();

    $reglamento_metabox = new \VpinUnf\Core\MetaBox\ReglamentoMetaBox();
    $reglamento_metabox->register_hooks();

    $socio_metabox = new \VpinUnf\Core\MetaBox\SocioMetaBox();
    $socio_metabox->register_hooks();
}

vpinunf_core_bootstrap();

<?php

/**
 * Plugin Name: ViceUnf Core
 * Plugin URI:  https://github.com/nftsaavedra/wptheme-vice-unf
 * Description: Core functionality and domain data (Custom Post Types, Taxonomies, and REST API endpoints) for the ViceUnf theme.
 * Version:     1.1.0
 * Author:      ViceUnf
 * License:     ISC
 * Text Domain: viceunf-core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('VICEUNF_CORE_VERSION', '1.1.0');
define('VICEUNF_CORE_PATH', plugin_dir_path(__FILE__));
define('VICEUNF_CORE_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader PSR-4 simple para ViceUnf\Core
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'ViceUnf\\Core\\';
    $base_dir = VICEUNF_CORE_PATH . 'src/';
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
    if ('wptheme-vice-unf' !== $theme->get_stylesheet() && 'wptheme-vice-unf' !== $theme->get_template()) {
        add_action('admin_notices', function (): void {
            echo '<div class="notice notice-warning is-dismissible"><p><strong>Advertencia:</strong> El plugin <em>ViceUnf Core</em> está diseñado para funcionar en conjunto con el tema <strong>ViceUnf</strong>. Ciertas funcionalidades del frontend podrían no mostrarse correctamente con el tema actual, pero tus datos están seguros.</p></div>';
        });
    }
});

/**
 * Bootstrap del Plugin (Contenedor / Registry simple)
 */
function viceunf_core_bootstrap(): void
{
    // 1. Inicializar API Endpoints y Ajax
    $api_endpoints = new \ViceUnf\Core\Api\Endpoints();
    $api_endpoints->register_hooks();

    $ajax_endpoints = new \ViceUnf\Core\Api\Ajax();
    $ajax_endpoints->register_hooks();

    // 2. Registrar Custom Post Types inyectando las dependencias (DI)
    $cpts = [
        new \ViceUnf\Core\PostType\Slider(),
        new \ViceUnf\Core\PostType\Evento(),
        new \ViceUnf\Core\PostType\Socio(),
        new \ViceUnf\Core\PostType\Reglamento(),
        new \ViceUnf\Core\PostType\Autoridad(),
        new \ViceUnf\Core\PostType\Dependencia()
    ];
    $registrar = new \ViceUnf\Core\PostType\Registrar($cpts);
    add_action('init', [$registrar, 'register_all'], 0);

    // 3. Registrar Hooks de Servicios
    $slider_service = new \ViceUnf\Core\Service\SliderService();
    $slider_service->register_hooks();

    $eventos_service = new \ViceUnf\Core\Service\EventosService();
    $eventos_service->register_hooks();

    $socios_service = new \ViceUnf\Core\Service\SocioService();
    $socios_service->register_hooks();

    $post_service = new \ViceUnf\Core\Service\PostService();
    $post_service->register_hooks();

    // 4. Registrar MetaBoxes de CPTs
    $autoridad_metabox = new \ViceUnf\Core\MetaBox\AutoridadMetaBox();
    $autoridad_metabox->register_hooks();

    $dependencia_metabox = new \ViceUnf\Core\MetaBox\DependenciaMetaBox();
    $dependencia_metabox->register_hooks();

    $evento_metabox = new \ViceUnf\Core\MetaBox\EventoMetaBox();
    $evento_metabox->register_hooks();

    $slider_metabox = new \ViceUnf\Core\MetaBox\SliderMetaBox();
    $slider_metabox->register_hooks();

    $reglamento_metabox = new \ViceUnf\Core\MetaBox\ReglamentoMetaBox();
    $reglamento_metabox->register_hooks();

    $socio_metabox = new \ViceUnf\Core\MetaBox\SocioMetaBox();
    $socio_metabox->register_hooks();
}

viceunf_core_bootstrap();

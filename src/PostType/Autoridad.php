<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

/**
 * Configuración del Custom Post Type: Autoridad
 */
class Autoridad implements PostTypeInterface
{

    public function get_slug(): string
    {
        return 'autoridad';
    }

    public function get_args(): array
    {
        $labels = array(
            'name'                  => _x('Autoridades', 'Post Type General Name', 'viceunf-core'),
            'singular_name'         => _x('Autoridad', 'Post Type Singular Name', 'viceunf-core'),
            'menu_name'             => __('Autoridades', 'viceunf-core'),
            'name_admin_bar'        => __('Autoridad', 'viceunf-core'),
            'add_new_item'          => __('Añadir Nueva Autoridad', 'viceunf-core'),
            'add_new'               => __('Añadir Nueva', 'viceunf-core'),
            'new_item'              => __('Nueva Autoridad', 'viceunf-core'),
            'edit_item'             => __('Editar Autoridad', 'viceunf-core'),
            'view_item'             => __('Ver Autoridad', 'viceunf-core'),
            'all_items'             => __('Todas las Autoridades', 'viceunf-core'),
            'search_items'          => __('Buscar Autoridades', 'viceunf-core'),
            'not_found'             => __('No se encontraron autoridades.', 'viceunf-core'),
            'not_found_in_trash'    => __('No se encontraron autoridades en la papelera.', 'viceunf-core'),
            'featured_image'        => __('Fotografía de la Autoridad', 'viceunf-core'),
            'set_featured_image'    => __('Establecer Fotografía', 'viceunf-core'),
            'remove_featured_image' => __('Quitar Fotografía', 'viceunf-core'),
        );

        return array(
            'label'               => __('Autoridad', 'viceunf-core'),
            'description'         => __('Autoridades y Personal Directivo Universitario', 'viceunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'thumbnail'),
            'public'              => false, // Para no generar single.php directo nativo si se usará via bloque.
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-businessman', // Icono cambiado a Puesto/Hombre de Negocios
            'capability_type'     => 'post',
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_rest'        => true, // Crítico para permitir selección/referenciación en Editor de Bloques Gutenberg.
        );
    }
}

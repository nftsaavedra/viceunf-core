<?php

namespace VpinUnf\Core\PostType;

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
            'name'                  => _x('Autoridades', 'Post Type General Name', 'vpinunf-core'),
            'singular_name'         => _x('Autoridad', 'Post Type Singular Name', 'vpinunf-core'),
            'menu_name'             => __('Autoridades', 'vpinunf-core'),
            'name_admin_bar'        => __('Autoridad', 'vpinunf-core'),
            'add_new_item'          => __('Añadir Nueva Autoridad', 'vpinunf-core'),
            'add_new'               => __('Añadir Nueva', 'vpinunf-core'),
            'new_item'              => __('Nueva Autoridad', 'vpinunf-core'),
            'edit_item'             => __('Editar Autoridad', 'vpinunf-core'),
            'view_item'             => __('Ver Autoridad', 'vpinunf-core'),
            'all_items'             => __('Todas las Autoridades', 'vpinunf-core'),
            'search_items'          => __('Buscar Autoridades', 'vpinunf-core'),
            'not_found'             => __('No se encontraron autoridades.', 'vpinunf-core'),
            'not_found_in_trash'    => __('No se encontraron autoridades en la papelera.', 'vpinunf-core'),
            'featured_image'        => __('Fotografía de la Autoridad', 'vpinunf-core'),
            'set_featured_image'    => __('Establecer Fotografía', 'vpinunf-core'),
            'remove_featured_image' => __('Quitar Fotografía', 'vpinunf-core'),
        );

        return array(
            'label'               => __('Autoridad', 'vpinunf-core'),
            'description'         => __('Autoridades y Personal Directivo Universitario', 'vpinunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'custom-fields'),
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

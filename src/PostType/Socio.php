<?php

declare(strict_types=1);

namespace VpinUnf\Core\PostType;

/**
 * Configuración del Custom Post Type: Socio
 */
class Socio implements PostTypeInterface
{

    public function get_slug(): string
    {
        return 'socio';
    }

    public function get_args(): array
    {
        $labels = array(
            'name'                  => _x('Socios', 'Post Type General Name', 'vpinunf-core'),
            'singular_name'         => _x('Socio', 'Post Type Singular Name', 'vpinunf-core'),
            'menu_name'             => __('Socios', 'vpinunf-core'),
            'name_admin_bar'        => __('Socio', 'vpinunf-core'),
            'add_new_item'          => __('Añadir Nuevo Socio', 'vpinunf-core'),
            'add_new'               => __('Añadir Nuevo', 'vpinunf-core'),
            'new_item'              => __('Nuevo Socio', 'vpinunf-core'),
            'edit_item'             => __('Editar Socio', 'vpinunf-core'),
            'view_item'             => __('Ver Socio', 'vpinunf-core'),
            'all_items'             => __('Todos los Socios', 'vpinunf-core'),
            'search_items'          => __('Buscar Socios', 'vpinunf-core'),
            'not_found'             => __('No se encontraron socios.', 'vpinunf-core'),
            'not_found_in_trash'    => __('No se encontraron socios en la papelera.', 'vpinunf-core'),
            'featured_image'        => __('Logo del Socio', 'vpinunf-core'),
            'set_featured_image'    => __('Establecer Logo del Socio', 'vpinunf-core'),
            'remove_featured_image' => __('Quitar Logo del Socio', 'vpinunf-core'),
        );

        return array(
            'label'               => __('Socio', 'vpinunf-core'),
            'description'         => __('Logos de Socios Académicos', 'vpinunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'page-attributes', 'custom-fields'),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 7,
            'menu_icon'           => 'dashicons-businessperson',
            'capability_type'     => 'post',
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_rest'        => true,
        );
    }
}

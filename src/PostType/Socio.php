<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

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
            'name'                  => _x('Socios', 'Post Type General Name', 'viceunf-core'),
            'singular_name'         => _x('Socio', 'Post Type Singular Name', 'viceunf-core'),
            'menu_name'             => __('Socios', 'viceunf-core'),
            'name_admin_bar'        => __('Socio', 'viceunf-core'),
            'add_new_item'          => __('Añadir Nuevo Socio', 'viceunf-core'),
            'add_new'               => __('Añadir Nuevo', 'viceunf-core'),
            'new_item'              => __('Nuevo Socio', 'viceunf-core'),
            'edit_item'             => __('Editar Socio', 'viceunf-core'),
            'view_item'             => __('Ver Socio', 'viceunf-core'),
            'all_items'             => __('Todos los Socios', 'viceunf-core'),
            'search_items'          => __('Buscar Socios', 'viceunf-core'),
            'not_found'             => __('No se encontraron socios.', 'viceunf-core'),
            'not_found_in_trash'    => __('No se encontraron socios en la papelera.', 'viceunf-core'),
            'featured_image'        => __('Logo del Socio', 'viceunf-core'),
            'set_featured_image'    => __('Establecer Logo del Socio', 'viceunf-core'),
            'remove_featured_image' => __('Quitar Logo del Socio', 'viceunf-core'),
        );

        return array(
            'label'               => __('Socio', 'viceunf-core'),
            'description'         => __('Logos de Socios Académicos', 'viceunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'page-attributes'),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 7,
            'menu_icon'           => 'dashicons-businessperson',
            'capability_type'     => 'post',
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
        );
    }
}

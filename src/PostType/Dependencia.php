<?php

declare(strict_types=1);

namespace VpinUnf\Core\PostType;

/**
 * Configuración del Custom Post Type: Dependencia
 * Utilizado para conformar el organigrama (Direcciones, Unidades, Institutos)
 */
class Dependencia implements PostTypeInterface
{
    public function get_slug(): string
    {
        return 'dependencia';
    }

    public function get_args(): array
    {
        $labels = array(
            'name'                  => _x('Dependencias', 'Post Type General Name', 'vpinunf-core'),
            'singular_name'         => _x('Dependencia', 'Post Type Singular Name', 'vpinunf-core'),
            'menu_name'             => __('Dependencias', 'vpinunf-core'),
            'name_admin_bar'        => __('Dependencia', 'vpinunf-core'),
            'parent_item_colon'     => __('Dependencia Superior (Padre):', 'vpinunf-core'),
            'all_items'             => __('Todas las Dependencias', 'vpinunf-core'),
            'add_new_item'          => __('Añadir Nueva Dependencia', 'vpinunf-core'),
            'add_new'               => __('Añadir Nueva', 'vpinunf-core'),
            'new_item'              => __('Nueva Dependencia', 'vpinunf-core'),
            'edit_item'             => __('Editar Dependencia', 'vpinunf-core'),
            'update_item'           => __('Actualizar Dependencia', 'vpinunf-core'),
            'view_item'             => __('Ver Dependencia', 'vpinunf-core'),
            'search_items'          => __('Buscar Dependencia', 'vpinunf-core'),
            'not_found'             => __('No se encontraron dependencias.', 'vpinunf-core'),
            'not_found_in_trash'    => __('No se encontraron dependencias en la papelera.', 'vpinunf-core'),
        );

        return array(
            'label'               => __('Dependencia', 'vpinunf-core'),
            'description'         => __('Direcciones, Unidades y Oficinas Universitarias', 'vpinunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'page-attributes', 'custom-fields'),
            'hierarchical'        => true, // Permitir arbol (Padre - Hijo) -> Nivel 0 -> 1 -> 2
            'public'              => false, // Se consumirá vía Bloques y API REST
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-networking', // Icono de red/organigrama
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_rest'        => true, // Crítico para usar en bloques de Gutenberg
        );
    }
}

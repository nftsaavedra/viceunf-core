<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

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
            'name'                  => _x('Dependencias', 'Post Type General Name', 'viceunf-core'),
            'singular_name'         => _x('Dependencia', 'Post Type Singular Name', 'viceunf-core'),
            'menu_name'             => __('Dependencias', 'viceunf-core'),
            'name_admin_bar'        => __('Dependencia', 'viceunf-core'),
            'parent_item_colon'     => __('Dependencia Superior (Padre):', 'viceunf-core'),
            'all_items'             => __('Todas las Dependencias', 'viceunf-core'),
            'add_new_item'          => __('Añadir Nueva Dependencia', 'viceunf-core'),
            'add_new'               => __('Añadir Nueva', 'viceunf-core'),
            'new_item'              => __('Nueva Dependencia', 'viceunf-core'),
            'edit_item'             => __('Editar Dependencia', 'viceunf-core'),
            'update_item'           => __('Actualizar Dependencia', 'viceunf-core'),
            'view_item'             => __('Ver Dependencia', 'viceunf-core'),
            'search_items'          => __('Buscar Dependencia', 'viceunf-core'),
            'not_found'             => __('No se encontraron dependencias.', 'viceunf-core'),
            'not_found_in_trash'    => __('No se encontraron dependencias en la papelera.', 'viceunf-core'),
        );

        return array(
            'label'               => __('Dependencia', 'viceunf-core'),
            'description'         => __('Direcciones, Unidades y Oficinas Universitarias', 'viceunf-core'),
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

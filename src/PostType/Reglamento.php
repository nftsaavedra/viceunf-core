<?php

declare(strict_types=1);

namespace VpinUnf\Core\PostType;

/**
 * Configuración del Custom Post Type: Reglamento
 */
class Reglamento implements PostTypeInterface, HasTaxonomiesInterface
{

    public function get_slug(): string
    {
        return 'reglamento';
    }

    public function get_args(): array
    {
        $labels = array(
            'name'               => _x('Reglamentos', 'Post Type General Name', 'vpinunf-core'),
            'singular_name'      => _x('Reglamento', 'Post Type Singular Name', 'vpinunf-core'),
            'menu_name'          => __('Reglamentos', 'vpinunf-core'),
            'name_admin_bar'     => __('Reglamento', 'vpinunf-core'),
            'add_new_item'       => __('Añadir Nuevo Reglamento', 'vpinunf-core'),
            'add_new'            => __('Añadir Nuevo', 'vpinunf-core'),
            'new_item'           => __('Nuevo Reglamento', 'vpinunf-core'),
            'edit_item'          => __('Editar Reglamento', 'vpinunf-core'),
            'view_item'          => __('Ver Reglamento', 'vpinunf-core'),
            'all_items'          => __('Todos los Reglamentos', 'vpinunf-core'),
            'search_items'       => __('Buscar Reglamentos', 'vpinunf-core'),
            'not_found'          => __('No se encontraron reglamentos.', 'vpinunf-core'),
            'not_found_in_trash' => __('No se encontraron reglamentos en la papelera.', 'vpinunf-core'),
        );

        return array(
            'label'               => __('Reglamento', 'vpinunf-core'),
            'description'         => __('Documentos normativos y reglamentos', 'vpinunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'revisions'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 8,
            'menu_icon'           => 'dashicons-media-document',
            'capability_type'     => 'post',
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
        );
    }

    /**
     * Registra dependencias extra como taxonomías.
     */
    public function register_taxonomies(): void
    {
        $labels = array(
            'name'              => _x('Categorías de Reglamento', 'taxonomy general name', 'vpinunf-core'),
            'singular_name'     => _x('Categoría de Reglamento', 'taxonomy singular name', 'vpinunf-core'),
            'search_items'      => __('Buscar Categorías', 'vpinunf-core'),
            'all_items'         => __('Todas las Categorías', 'vpinunf-core'),
            'parent_item'       => __('Categoría Padre', 'vpinunf-core'),
            'parent_item_colon' => __('Categoría Padre:', 'vpinunf-core'),
            'edit_item'         => __('Editar Categoría', 'vpinunf-core'),
            'update_item'       => __('Actualizar Categoría', 'vpinunf-core'),
            'add_new_item'      => __('Añadir Nueva Categoría', 'vpinunf-core'),
            'new_item_name'     => __('Nombre de la Nueva Categoría', 'vpinunf-core'),
            'menu_name'         => __('Categorías', 'vpinunf-core'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'categoria-reglamento'),
            'show_in_rest'      => true,
        );

        register_taxonomy('categoria_reglamento', array($this->get_slug()), $args);
    }
}

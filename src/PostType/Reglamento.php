<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

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
            'name'               => _x('Reglamentos', 'Post Type General Name', 'viceunf-core'),
            'singular_name'      => _x('Reglamento', 'Post Type Singular Name', 'viceunf-core'),
            'menu_name'          => __('Reglamentos', 'viceunf-core'),
            'name_admin_bar'     => __('Reglamento', 'viceunf-core'),
            'add_new_item'       => __('Añadir Nuevo Reglamento', 'viceunf-core'),
            'add_new'            => __('Añadir Nuevo', 'viceunf-core'),
            'new_item'           => __('Nuevo Reglamento', 'viceunf-core'),
            'edit_item'          => __('Editar Reglamento', 'viceunf-core'),
            'view_item'          => __('Ver Reglamento', 'viceunf-core'),
            'all_items'          => __('Todos los Reglamentos', 'viceunf-core'),
            'search_items'       => __('Buscar Reglamentos', 'viceunf-core'),
            'not_found'          => __('No se encontraron reglamentos.', 'viceunf-core'),
            'not_found_in_trash' => __('No se encontraron reglamentos en la papelera.', 'viceunf-core'),
        );

        return array(
            'label'               => __('Reglamento', 'viceunf-core'),
            'description'         => __('Documentos normativos y reglamentos', 'viceunf-core'),
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
            'name'              => _x('Categorías de Reglamento', 'taxonomy general name', 'viceunf-core'),
            'singular_name'     => _x('Categoría de Reglamento', 'taxonomy singular name', 'viceunf-core'),
            'search_items'      => __('Buscar Categorías', 'viceunf-core'),
            'all_items'         => __('Todas las Categorías', 'viceunf-core'),
            'parent_item'       => __('Categoría Padre', 'viceunf-core'),
            'parent_item_colon' => __('Categoría Padre:', 'viceunf-core'),
            'edit_item'         => __('Editar Categoría', 'viceunf-core'),
            'update_item'       => __('Actualizar Categoría', 'viceunf-core'),
            'add_new_item'      => __('Añadir Nueva Categoría', 'viceunf-core'),
            'new_item_name'     => __('Nombre de la Nueva Categoría', 'viceunf-core'),
            'menu_name'         => __('Categorías', 'viceunf-core'),
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

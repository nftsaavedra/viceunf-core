<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

/**
 * Configuración del Custom Post Type: Slider
 */
class Slider implements PostTypeInterface
{

    public function get_slug(): string
    {
        return 'slider';
    }

    public function get_args(): array
    {
        $labels = array(
            'name'                  => _x('Sliders', 'Post Type General Name', 'viceunf-core'),
            'singular_name'         => _x('Slider', 'Post Type Singular Name', 'viceunf-core'),
            'menu_name'             => __('Sliders', 'viceunf-core'),
            'add_new_item'          => __('Añadir Nuevo Slider', 'viceunf-core'),
            'edit_item'             => __('Editar Slider', 'viceunf-core'),
            'featured_image'        => __('Imagen de Fondo', 'viceunf-core'),
            'set_featured_image'    => __('Establecer Imagen de Fondo', 'viceunf-core'),
            'remove_featured_image' => __('Quitar Imagen de Fondo', 'viceunf-core'),
            'use_featured_image'    => __('Usar como Imagen de Fondo', 'viceunf-core'),
        );

        return array(
            'label'               => __('Slider', 'viceunf-core'),
            'description'         => __('Contenido para el slider principal', 'viceunf-core'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'revisions'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-images-alt2',
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        );
    }
}

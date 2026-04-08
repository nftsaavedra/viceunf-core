<?php

namespace VpinUnf\Core\PostType;

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
            'name'                  => _x('Sliders', 'Post Type General Name', 'vpinunf-core'),
            'singular_name'         => _x('Slider', 'Post Type Singular Name', 'vpinunf-core'),
            'menu_name'             => __('Sliders', 'vpinunf-core'),
            'add_new_item'          => __('Añadir Nuevo Slider', 'vpinunf-core'),
            'edit_item'             => __('Editar Slider', 'vpinunf-core'),
            'featured_image'        => __('Imagen de Fondo', 'vpinunf-core'),
            'set_featured_image'    => __('Establecer Imagen de Fondo', 'vpinunf-core'),
            'remove_featured_image' => __('Quitar Imagen de Fondo', 'vpinunf-core'),
            'use_featured_image'    => __('Usar como Imagen de Fondo', 'vpinunf-core'),
        );

        return array(
            'label'               => __('Slider', 'vpinunf-core'),
            'description'         => __('Contenido para el slider principal', 'vpinunf-core'),
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

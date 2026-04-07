<?php

declare(strict_types=1);

namespace VpinUnf\Core\PostType;

/**
 * Configuración del Custom Post Type: Evento
 */
class Evento implements PostTypeInterface
{

    public function get_slug(): string
    {
        return 'evento';
    }

    public function get_args(): array
    {
        $labels = array(
            'name'          => _x('Eventos', 'Post Type General Name', 'vpinunf-core'),
            'singular_name' => _x('Evento', 'Post Type Singular Name', 'vpinunf-core'),
            'menu_name'     => __('Eventos', 'vpinunf-core'),
            'all_items'     => __('Todos los Eventos', 'vpinunf-core'),
            'add_new_item'  => __('Añadir Nuevo Evento', 'vpinunf-core'),
            'edit_item'     => __('Editar Evento', 'vpinunf-core'),
        );

        return array(
            'label'              => __('Evento', 'vpinunf-core'),
            'description'        => __('Contenido para la sección de eventos', 'vpinunf-core'),
            'labels'             => $labels,
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-calendar-alt',
            'publicly_queryable' => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'show_in_rest'       => true,
        );
    }
}

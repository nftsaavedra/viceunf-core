<?php

declare(strict_types=1);

namespace ViceUnf\Core\Api;

class Endpoints
{

    public function register_hooks(): void
    {
        add_action('rest_api_init', [$this, 'register_slider_fields']);
        add_action('rest_api_init', [$this, 'register_dependencia_fields']);
        add_action('rest_api_init', [$this, 'register_autoridad_fields']);
    }

    public function register_slider_fields(): void
    {
        $slider_fields = [
            '_slider_subtitle_key'        => 'string',
            '_slider_description_key'     => 'string',
            '_slider_text_alignment_key'  => 'string',
            '_slider_btn1_text_key'       => 'string',
            '_slider_btn2_text_key'       => 'string',
            '_slider_btn2_link_key'       => 'string',
            '_slider_video_link_key'      => 'string',
            '_slider_link_type_key'       => 'string',
            '_slider_link_url_key'        => 'string',
            '_slider_link_content_id_key' => 'integer',
        ];

        foreach ($slider_fields as $meta_key => $type) {
            register_post_meta('slider', $meta_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => $type,
                'auth_callback'     => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }

        // Campo "calculado" para el enlace final del botón 1 permanece como rest_field
        register_rest_field('slider', 'btn1_final_href', [
            'get_callback' => [$this, 'get_slider_btn1_final_href'],
            'schema'       => [
                'type'        => 'string',
                'format'      => 'uri',
                'description' => 'Calculated final URL for button 1.',
                'context'     => ['view', 'edit'],
            ],
        ]);

        register_rest_field('slider', 'featured_image_url', [
            'get_callback' => function (array $object): string|bool {
                if (! empty($object['featured_media'])) {
                    return get_the_post_thumbnail_url((int) $object['id'], 'full');
                }
                return false;
            },
            'schema' => [
                'type'        => ['string', 'boolean'],
                'format'      => 'uri',
                'description' => 'Featured image URL if exists, else false.',
                'context'     => ['view', 'edit'],
            ],
        ]);
    }

    public function get_slider_btn1_final_href(array $object, string $field_name, \WP_REST_Request $request): string
    {
        $link_type       = get_post_meta((int) $object['id'], '_slider_link_type_key', true);
        $link_url        = get_post_meta((int) $object['id'], '_slider_link_url_key', true);
        $link_content_id = get_post_meta((int) $object['id'], '_slider_link_content_id_key', true);

        if ($link_type === 'url' && ! empty($link_url)) {
            return esc_url((string) $link_url);
        }
        if ($link_type === 'content' && ! empty($link_content_id)) {
            return get_permalink((int) $link_content_id) ?: '';
        }
        return '';
    }

    public function register_dependencia_fields(): void
    {
        $fields = [
            '_dependencia_resolucion'              => 'string',
            '_dependencia_resolucion_source_type'  => 'string',
            '_dependencia_resolucion_file_id'      => 'integer',
            '_dependencia_resolucion_external_url' => 'string',
            '_dependencia_siglas'                  => 'string',
            '_dependencia_correo'                  => 'string',
            '_dependencia_telefono'                => 'string',
            '_dependencia_ubicacion'               => 'string',
            '_dependencia_horario'                 => 'string',
            '_dependencia_autoridad_id'            => 'integer',
        ];

        foreach ($fields as $meta_key => $type) {
            register_post_meta('dependencia', $meta_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => $type,
                'auth_callback'     => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }
    }

    public function register_autoridad_fields(): void
    {
        $fields = [
            '_autoridad_grado'      => 'string',
            '_autoridad_correo'     => 'string',
            '_autoridad_orcid'      => 'string',
            '_autoridad_cti_vitae'  => 'string',
        ];

        foreach ($fields as $meta_key => $type) {
            register_post_meta('autoridad', $meta_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => $type,
                'auth_callback'     => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }
    }
}

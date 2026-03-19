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
        $slider_string_fields = [
            '_slider_subtitle_key',
            '_slider_description_key',
            '_slider_text_alignment_key',
            '_slider_btn1_text_key',
            '_slider_btn2_text_key',
            '_slider_link_type_key',
        ];

        foreach ($slider_string_fields as $meta_key) {
            register_post_meta('slider', $meta_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => fn() => current_user_can('edit_posts'),
            ]);
        }

        // Campos de URL — sanitización especializada
        foreach (['_slider_btn2_link_key', '_slider_video_link_key', '_slider_link_url_key'] as $url_key) {
            register_post_meta('slider', $url_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'auth_callback'     => fn() => current_user_can('edit_posts'),
            ]);
        }

        // Campo entero
        register_post_meta('slider', '_slider_link_content_id_key', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'auth_callback'     => fn() => current_user_can('edit_posts'),
        ]);

        // Campo calculado — enlace final btn1
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
        // Strings genéricos
        $string_fields = [
            '_dependencia_resolucion',
            '_dependencia_resolucion_source_type',
            '_dependencia_siglas',
            '_dependencia_telefono',
            '_dependencia_ubicacion',
            '_dependencia_horario',
        ];
        foreach ($string_fields as $meta_key) {
            register_post_meta('dependencia', $meta_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => fn() => current_user_can('edit_posts'),
            ]);
        }

        // URL externa — sanitización de URI
        register_post_meta('dependencia', '_dependencia_resolucion_external_url', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback'     => fn() => current_user_can('edit_posts'),
        ]);

        // Email institucional
        register_post_meta('dependencia', '_dependencia_correo', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_email',
            'auth_callback'     => fn() => current_user_can('edit_posts'),
        ]);

        // Enteros — IDs de archivos y autoridades
        foreach (['_dependencia_resolucion_file_id', '_dependencia_autoridad_id'] as $int_key) {
            register_post_meta('dependencia', $int_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'auth_callback'     => fn() => current_user_can('edit_posts'),
            ]);
        }
    }

    public function register_autoridad_fields(): void
    {
        // Grado académico — texto plano
        register_post_meta('autoridad', '_autoridad_grado', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => fn() => current_user_can('edit_posts'),
        ]);

        // Email institucional
        register_post_meta('autoridad', '_autoridad_correo', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_email',
            'auth_callback'     => fn() => current_user_can('edit_posts'),
        ]);

        // URLs externas — ORCID y CTI Vitae
        foreach (['_autoridad_orcid', '_autoridad_cti_vitae'] as $url_key) {
            register_post_meta('autoridad', $url_key, [
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'auth_callback'     => fn() => current_user_can('edit_posts'),
            ]);
        }
    }
}

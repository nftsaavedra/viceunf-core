<?php

declare(strict_types=1);

namespace ViceUnf\Core\Api;

class Endpoints
{

    public function register_hooks(): void
    {
        add_action('rest_api_init', [$this, 'register_slider_fields']);
    }

    public function register_slider_fields(): void
    {
        $slider_fields = [
            '_slider_subtitle_key',
            '_slider_description_key',
            '_slider_text_alignment_key',
            '_slider_btn1_text_key',
            '_slider_btn2_text_key',
            '_slider_btn2_link_key',
            '_slider_video_link_key',
            '_slider_link_type_key',
            '_slider_link_url_key',
            '_slider_link_content_id_key',
        ];

        foreach ($slider_fields as $field) {
            register_rest_field('slider', $field, [
                'get_callback' => [$this, 'get_meta_value'],
            ]);
        }

        // Campo "calculado" para el enlace final del botón 1
        register_rest_field('slider', 'btn1_final_href', [
            'get_callback' => [$this, 'get_slider_btn1_final_href'],
        ]);

        // Exponemos la URL de la imagen destacada de forma explícita
        register_rest_field('slider', 'featured_image_url', [
            'get_callback' => function (array $object): string|bool {
                if (! empty($object['featured_media'])) {
                    return get_the_post_thumbnail_url((int) $object['id'], 'full');
                }
                return false;
            },
        ]);
    }

    public function get_meta_value(array $object, string $field_name, \WP_REST_Request $request): mixed
    {
        return get_post_meta((int) $object['id'], $field_name, true);
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
}

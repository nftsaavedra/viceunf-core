<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

/**
 * Service: SliderService
 * 
 * Capa de abstracción para consultas del CPT 'slider'.
 * Incluye lógica de caché estricta para optimización (Patrón Proxy/Cache Repository).
 */
class SliderService
{

    public function register_hooks(): void
    {
        add_action('save_post_slider', [$this, 'clear_sliders_cache']);
    }

    /**
     * Obtiene los sliders más recientes para la portada, usando caché de transients.
     */
    public function get_front_sliders(int $limit = 5): \WP_Query
    {
        $cache_key = 'viceunf_slider_query_cache_' . $limit;
        $slider_query = get_transient($cache_key);

        if (false === $slider_query) {
            $args = [
                'post_type'              => 'slider',
                'posts_per_page'         => max(1, $limit),
                'orderby'                => 'date',
                'order'                  => 'DESC',
                'no_found_rows'          => true,
                'update_post_meta_cache' => true,
                'update_post_term_cache' => false,
            ];
            $slider_query = new \WP_Query($args);

            // Guardar en la caché temporal por 12 horas.
            set_transient($cache_key, $slider_query, 12 * HOUR_IN_SECONDS);
        }

        return $slider_query;
    }

    /**
     * Borra la caché de los sliders. Ideal para enganchar al guardar un slider.
     */
    public function clear_sliders_cache(): void
    {
        delete_transient('viceunf_slider_query_cache_5');
    }
}

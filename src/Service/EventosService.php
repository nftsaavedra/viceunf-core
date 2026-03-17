<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

class EventosService
{

    /**
     * Invalida todas las caches relacionadas con eventos
     * Se debe llamar cuando se crea, actualiza o elimina un evento
     */
    public function clear_eventos_cache(): void
    {
        // Limpiar caches para diferentes límites comunes
        $common_limits = [2, 3, 4, 6, 8, 10];
        
        foreach ($common_limits as $limit) {
            delete_transient("viceunf_eventos_home_{$limit}");
        }
    }

    /**
     * Registra los hooks para invalidar cache automáticamente
     */
    public function register_hooks(): void
    {
        // Invalidar cache cuando se guarda, actualiza o elimina un evento
        add_action('save_post_evento', [$this, 'clear_eventos_cache']);
        add_action('wp_trash_post', [$this, 'handle_event_deletion']);
        add_action('untrash_post', [$this, 'handle_event_restoration']);
    }

    /**
     * Maneja la eliminación de eventos (incluye trash)
     */
    public function handle_event_deletion(int $post_id): void
    {
        if (get_post_type($post_id) === 'evento') {
            $this->clear_eventos_cache();
        }
    }

    /**
     * Maneja la restauración de eventos desde trash
     */
    public function handle_event_restoration(int $post_id): void
    {
        if (get_post_type($post_id) === 'evento') {
            $this->clear_eventos_cache();
        }
    }

    /**
     * Devuelve los próximos eventos.
     * Retorna un array con la data lista para la vista.
     * 
     * @return array<int, array<string, mixed>>
     */
    public function get_eventos_home(int $limit = 4): array
    {
        // Intentar obtener desde cache primero
        $cache_key = "viceunf_eventos_home_{$limit}";
        $cached = get_transient($cache_key);
        
        if (false !== $cached) {
            return $cached;
        }

        $args = [
            'post_type'              => 'evento',
            'posts_per_page'         => max(1, $limit),
            'meta_key'               => '_evento_date_key',
            'orderby'                => 'meta_value_date',
            'order'                  => 'DESC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
        ];
        $query = new \WP_Query($args);
        $eventos = [];

        if ($query->have_posts()) {
            $today_timestamp = strtotime(wp_date('Y-m-d'));

            while ($query->have_posts()) {
                $query->the_post();

                $post_id        = get_the_ID();
                $event_date_raw = get_post_meta($post_id, '_evento_date_key', true) ?: '';
                $event_start    = get_post_meta($post_id, '_evento_start_time_key', true) ?: '';
                $event_end      = get_post_meta($post_id, '_evento_end_time_key', true) ?: '';
                $event_address  = get_post_meta($post_id, '_evento_address_key', true) ?: '';

                $event_timestamp = strtotime((string) $event_date_raw) ?: 0;
                $is_past         = ($event_timestamp < $today_timestamp);

                $event_day = '';
                $event_month = '';
                $start_time_formatted = '';
                $end_time_formatted   = '';

                try {
                    if (!empty($event_date_raw)) {
                        $datetime_object     = new \DateTime((string) $event_date_raw, wp_timezone());
                        $corrected_timestamp = $datetime_object->getTimestamp();
                        $event_day           = wp_date('d', $corrected_timestamp);
                        $event_month         = wp_date('M', $corrected_timestamp);
                    }

                    if (!empty($event_start) && !empty($event_end) && !empty($event_date_raw)) {
                        $start_datetime_obj   = new \DateTime($event_date_raw . ' ' . $event_start, wp_timezone());
                        $end_datetime_obj     = new \DateTime($event_date_raw . ' ' . $event_end, wp_timezone());
                        $start_time_formatted = wp_date('g:i a', $start_datetime_obj->getTimestamp());
                        $end_time_formatted   = wp_date('g:i a', $end_datetime_obj->getTimestamp());
                    }
                } catch (\Throwable $e) {
                    // Fail gracefully on date parsing errors to avoid white screen of death
                    error_log('ViceUnf_Core EventosService Date Parse Error: ' . $e->getMessage());
                }

                $thumbnail_html = '';
                if (has_post_thumbnail($post_id)) {
                    $thumbnail_html = wp_get_attachment_image(get_post_thumbnail_id($post_id), 'large');
                }

                $eventos[] = [
                    'id'             => $post_id,
                    'title'          => get_the_title(),
                    'permalink'      => get_permalink(),
                    'excerpt'        => wp_trim_words(get_the_excerpt(), 20, '...'),
                    'thumbnail_html' => $thumbnail_html,
                    'day'            => $event_day,
                    'month'          => $event_month,
                    'is_past'        => $is_past,
                    'has_time'       => (!empty($event_start) && !empty($event_end)),
                    'start_time'     => $start_time_formatted,
                    'end_time'       => $end_time_formatted,
                    'address'        => $event_address,
                    'has_date'       => !empty($event_date_raw)
                ];
            }
            wp_reset_postdata();
        }

        // Guardar en cache por 15 minutos
        set_transient($cache_key, $eventos, 15 * MINUTE_IN_SECONDS);

        return $eventos;
    }
}

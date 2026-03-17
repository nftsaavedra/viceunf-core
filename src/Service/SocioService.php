<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

/**
 * Service: SocioService
 * 
 * Abstrae las consultas a la base de datos para el CPT 'socio' con optimizaciones de cache.
 */
class SocioService
{
    private const CACHE_KEY = 'viceunf_socios_all';
    private const CACHE_DURATION = HOUR_IN_SECONDS; // 1 hora

    /**
     * Obtiene la lista completa de socios ordenados por Menu Order con cache.
     */
    public function get_all_socios(): \WP_Query
    {
        // Intentar obtener desde cache primero
        $cached_query = get_transient(self::CACHE_KEY);
        
        if (false !== $cached_query) {
            return $cached_query;
        }

        $args = [
            'post_type'              => 'socio',
            'posts_per_page'         => -1, // Mostrar todos
            'orderby'                => 'menu_order',
            'order'                  => 'ASC',
            'no_found_rows'          => true, // Optimización: no contar total
            'update_post_meta_cache' => false, // Optimización: no actualizar meta cache
            'update_post_term_cache' => false, // Optimización: no actualizar term cache
            'fields'                 => 'ids', // Optimización: solo IDs si no necesitamos full posts
        ];

        $query = new \WP_Query($args);
        
        // Guardar en cache por 1 hora
        set_transient(self::CACHE_KEY, $query, self::CACHE_DURATION);

        return $query;
    }

    /**
     * Obtiene solo IDs de socios (más ligero para carousel)
     */
    public function get_socio_ids(): array
    {
        $cache_key = self::CACHE_KEY . '_ids';
        $cached_ids = get_transient($cache_key);
        
        if (false !== $cached_ids) {
            return $cached_ids;
        }

        $args = [
            'post_type'              => 'socio',
            'posts_per_page'         => -1,
            'orderby'                => 'menu_order',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'fields'                 => 'ids', // Solo IDs
        ];

        $query = new \WP_Query($args);
        $socio_ids = $query->posts;
        
        // Guardar en cache
        set_transient($cache_key, $socio_ids, self::CACHE_DURATION);

        return $socio_ids;
    }

    /**
     * Obtiene socios con datos específicos para carousel (optimizado)
     */
    public function get_socios_for_carousel(): array
    {
        $cache_key = self::CACHE_KEY . '_carousel';
        $cached_data = get_transient($cache_key);
        
        if (false !== $cached_data) {
            return $cached_data;
        }

        $socio_ids = $this->get_socio_ids();
        $socios_data = [];

        foreach ($socio_ids as $socio_id) {
            $socios_data[] = [
                'id' => $socio_id,
                'title' => get_the_title($socio_id),
                'url' => get_post_meta($socio_id, '_socio_url_key', true),
                'thumbnail' => get_the_post_thumbnail_url($socio_id, 'medium'),
                'permalink' => get_permalink($socio_id)
            ];
        }
        
        // Guardar en cache
        set_transient($cache_key, $socios_data, self::CACHE_DURATION);

        return $socios_data;
    }

    /**
     * Limpia el cache cuando se actualiza un socio
     */
    public function clear_socios_cache(): void
    {
        delete_transient(self::CACHE_KEY);
        delete_transient(self::CACHE_KEY . '_ids');
        delete_transient(self::CACHE_KEY . '_carousel');
    }

    /**
     * Registra hooks para invalidar cache automáticamente
     */
    public function register_hooks(): void
    {
        // Invalidar cache cuando se guarda, actualiza o elimina un socio
        add_action('save_post_socio', [$this, 'clear_socios_cache']);
        add_action('wp_trash_post', [$this, 'handle_socio_deletion']);
        add_action('untrash_post', [$this, 'handle_socio_restoration']);
        add_action('delete_post', [$this, 'handle_socio_deletion']);
    }

    /**
     * Maneja la eliminación de socios
     */
    public function handle_socio_deletion(int $post_id): void
    {
        if (get_post_type($post_id) === 'socio') {
            $this->clear_socios_cache();
        }
    }

    /**
     * Maneja la restauración de socios
     */
    public function handle_socio_restoration(int $post_id): void
    {
        if (get_post_type($post_id) === 'socio') {
            $this->clear_socios_cache();
        }
    }
}

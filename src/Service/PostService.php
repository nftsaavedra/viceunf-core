<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

/**
 * Service: PostService
 * 
 * Abstrae las consultas a la base de datos para entradas (posts).
 */
class PostService
{
    private const CACHE_KEY_PREFIX = 'viceunf_recent_posts_';
    private const CACHE_DURATION = HOUR_IN_SECONDS; // 1 hora

    public function get_recent_posts(int $number_of_posts = 5, array $categories = []): \WP_Query
    {
        // Crear una key de cache basada en los argumentos
        $cache_key = self::CACHE_KEY_PREFIX . md5($number_of_posts . implode(',', $categories));
        $cached_query = get_transient($cache_key);
        
        if (false !== $cached_query) {
            return $cached_query;
        }

        $args = [
            'post_type'              => 'post',
            'post_status'            => 'publish',
            'posts_per_page'         => $number_of_posts,
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true, // Optimización
            'update_post_meta_cache' => false, // Optimización
            'update_post_term_cache' => false, // Optimización
        ];

        if (!empty($categories)) {
            $args['category__in'] = $categories;
        }

        $query = new \WP_Query($args);
        
        // Guardar en cache por 1 hora
        set_transient($cache_key, $query, self::CACHE_DURATION);

        return $query;
    }

    public function get_related_posts(int $current_post_id, int $limit = 3): \WP_Query
    {
        $categories = get_the_category($current_post_id);
        
        if (empty($categories)) {
            return new \WP_Query();
        }

        $category_ids = wp_list_pluck($categories, 'term_id');

        $cache_key = self::CACHE_KEY_PREFIX . 'related_' . $current_post_id . '_' . md5(implode(',', $category_ids) . $limit);
        $cached_query = get_transient($cache_key);
        
        if (false !== $cached_query) {
            return $cached_query;
        }

        $args = [
            'post_type'              => 'post',
            'post_status'            => 'publish',
            'posts_per_page'         => $limit,
            'category__in'           => $category_ids,
            'post__not_in'           => [$current_post_id],
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];

        $query = new \WP_Query($args);
        
        set_transient($cache_key, $query, self::CACHE_DURATION);

        return $query;
    }

    public function clear_posts_cache(): void
    {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_viceunf\_recent\_posts\_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\_viceunf\_recent\_posts\_%'");
    }

    public function register_hooks(): void
    {
        add_action('save_post_post', [$this, 'clear_posts_cache']);
        add_action('wp_trash_post', [$this, 'handle_post_deletion']);
        add_action('untrash_post', [$this, 'handle_post_restoration']);
        add_action('delete_post', [$this, 'handle_post_deletion']);
    }

    public function handle_post_deletion(int $post_id): void
    {
        if (get_post_type($post_id) === 'post') {
            $this->clear_posts_cache();
        }
    }

    public function handle_post_restoration(int $post_id): void
    {
        if (get_post_type($post_id) === 'post') {
            $this->clear_posts_cache();
        }
    }
}

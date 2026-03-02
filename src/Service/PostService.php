<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

/**
 * Service: PostService
 * 
 * Actúa como un Repositorio/Servicio para abstraer las consultas a la base de datos
 * relacionadas con el post type estándar 'post' (Entradas/Noticias del blog).
 */
class PostService
{

    /**
     * Obtiene las entradas recientes del blog.
     *
     * @param int   $limit      Número de posts a recuperar.
     * @param array<int> $categories Array de IDs de categorías para filtrar.
     * @return \WP_Query Objeto con los resultados.
     */
    public function get_recent_posts(int $limit = 3, array $categories = []): \WP_Query
    {
        $args = [
            'post_type'              => 'post',
            'posts_per_page'         => max(1, $limit),
            'ignore_sticky_posts'    => 1,
            'post_status'            => 'publish',
            'no_found_rows'          => true, // Optimización SQL
            'update_post_term_cache' => false,
        ];

        if (! empty($categories)) {
            $args['category__in'] = $categories;
        }

        return new \WP_Query($args);
    }

    /**
     * Obtiene entradas relacionadas a un post específico basado en sus categorías.
     *
     * @param int $post_id ID del post actual.
     * @param int $limit Número máximo de entradas a retornar.
     * @return \WP_Query|false Objeto con los resultados o false si no hay categorías.
     */
    public function get_related_posts(int $post_id, int $limit = 3): \WP_Query|bool
    {
        $categories = get_the_category($post_id);
        if (empty($categories) || is_wp_error($categories)) {
            return false;
        }

        $category_ids = wp_list_pluck($categories, 'term_id');

        $args = [
            'post_type'              => 'post',
            'posts_per_page'         => max(1, $limit),
            'post_status'            => 'publish',
            'post__not_in'           => [$post_id],
            'category__in'           => $category_ids,
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'orderby'                => 'date',
            'order'                  => 'DESC',
        ];

        return new \WP_Query($args);
    }
}

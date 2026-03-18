<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

/**
 * Service: CarouselService
 * 
 * Abstrae las consultas a la base de datos para entidades dinámicas orientadas a carruseles.
 */
class CarouselService
{
    public function get_entities(string $post_type, int $limit): \WP_Query
    {
        $args = [
            'post_type'              => $post_type,
            'post_status'            => 'publish',
            'posts_per_page'         => $limit,
            'orderby'                => 'menu_order date',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];

        return new \WP_Query($args);
    }
}

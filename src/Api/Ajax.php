<?php

declare(strict_types=1);

namespace ViceUnf\Core\Api;

class Ajax
{
    public function register_hooks(): void
    {
        add_action('wp_ajax_viceunf_search_content', [$this, 'search_content_handler']);
        add_action('wp_ajax_viceunf_search_pages_only', [$this, 'search_pages_handler']);
    }

    /**
     * Búsqueda de contenido (posts + pages)
     */
    public function search_content_handler(): void
    {
        $this->ajax_search_posts(['post', 'page']);
    }

    /**
     * Búsqueda de páginas únicamente
     */
    public function search_pages_handler(): void
    {
        $this->ajax_search_posts(['page']);
    }

    /**
     * Lógica DRY reutilizada por los endpoints de búsqueda.
     *
     * @param array<string> $post_types Tipo(s) de post a buscar.
     */
    private function ajax_search_posts(array $post_types): void
    {
        check_ajax_referer('slider_metabox_nonce_action', 'nonce');

        $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        if (empty($search_term)) {
            wp_send_json_error('Término de búsqueda vacío.');
            return;
        }

        $query_args = [
            'post_type'      => $post_types,
            'posts_per_page' => 10,
            's'              => $search_term,
            'no_found_rows'  => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false
        ];

        $results_query = new \WP_Query($query_args);
        $results       = [];

        if ($results_query->have_posts()) {
            while ($results_query->have_posts()) {
                $results_query->the_post();
                $post_obj = get_post_type_object(get_post_type());
                $results[] = [
                    'id'    => get_the_ID(),
                    'title' => get_the_title(),
                    'type'  => $post_obj ? $post_obj->labels->singular_name : 'Contenido',
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json_success($results);
    }
}

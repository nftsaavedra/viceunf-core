<?php

declare(strict_types=1);

namespace ViceUnf\Core\Api;

class Ajax
{
    public function register_hooks(): void
    {
        add_action('wp_ajax_viceunf_search_content',    [$this, 'search_content_handler']);
        add_action('wp_ajax_viceunf_search_pages_only', [$this, 'search_pages_handler']);
        add_action('wp_ajax_viceunf_search_autoridades', [$this, 'search_autoridades_handler']);
        add_action('wp_ajax_viceunf_search_icons',      [$this, 'search_icons_handler']);
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
     * Búsqueda de Autoridades únicamente
     */
    public function search_autoridades_handler(): void
    {
        $this->ajax_search_posts(['autoridad']);
    }

    /**
     * Búsqueda de íconos Font Awesome desde el archivo JSON del tema.
     * Usada por el icon-picker de los bloques en el editor.
     */
    public function search_icons_handler(): void
    {
        check_ajax_referer('viceunf_ajax_nonce_action', 'nonce');

        if (! current_user_can('edit_posts')) {
            wp_send_json_error('Sin permisos.');
            return;
        }

        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        if (strlen($search) < 2) {
            wp_send_json_error('Mínimo 2 caracteres.');
            return;
        }

        // Ruta al JSON de íconos ubicado en el tema activo
        $json_path = get_stylesheet_directory() . '/assets/data/fontawesome-icons.json';
        if (! file_exists($json_path)) {
            wp_send_json_error('Archivo de íconos no encontrado.');
            return;
        }

        $raw   = file_get_contents($json_path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $icons = json_decode($raw, true);
        if (! is_array($icons)) {
            wp_send_json_error('Error al leer el archivo de íconos.');
            return;
        }

        $search_lower = strtolower($search);
        $results = array_filter($icons, fn(array $icon): bool =>
            str_contains(strtolower($icon['name'] ?? ''), $search_lower) ||
            str_contains(strtolower($icon['class'] ?? ''), $search_lower)
        );

        wp_send_json_success(array_values(array_slice($results, 0, 30)));
    }

    /**
     * Lógica DRY reutilizada por los endpoints de búsqueda.
     *
     * @param array<string> $post_types Tipo(s) de post a buscar.
     */
    private function ajax_search_posts(array $post_types): void
    {
        // NOTA: La acción del nonce debe coincidir con wp_create_nonce() en theme-functions/enqueue.php
        check_ajax_referer('viceunf_ajax_nonce_action', 'nonce');

        if (! current_user_can('edit_posts')) {
            wp_send_json_error('No tienes permisos suficientes para realizar esta acción.');
            return;
        }

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

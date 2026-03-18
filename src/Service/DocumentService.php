<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

class DocumentService
{

    /**
     * Obtiene la URL del archivo de un documento según su tipo de fuente.
     */
    public function get_document_url(int $post_id, string $post_type): string
    {
        $source_type = get_post_meta($post_id, "_{$post_type}_source_type_key", true);

        if ('external' === $source_type) {
            return (string) get_post_meta($post_id, "_{$post_type}_external_url_key", true);
        }

        if ('upload' === $source_type) {
            $file_id = get_post_meta($post_id, "_{$post_type}_file_id_key", true);
            return $file_id ? (wp_get_attachment_url((int) $file_id) ?: '#') : '#';
        }

        return '#';
    }

    /**
     * Obtiene los documentos filtrados por slugs específicos de categoría.
     * Retorna un array plano de documentos.
     * 
     * @return array<string, mixed>
     */
    public function get_documents(string $post_type, string $taxonomy_name, array $categoria_slugs = []): array
    {
        $resultado = [
            'is_grouped' => false,
            'is_tree'    => false,
            'data'       => []
        ];

        if (empty($categoria_slugs)) {
            return $resultado;
        }

        $args = [
            'post_type'              => $post_type,
            'posts_per_page'         => -1,
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
            'tax_query'              => [
                [
                    'taxonomy' => $taxonomy_name,
                    'field'    => 'slug',
                    'terms'    => $categoria_slugs,
                ],
            ],
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $resultado['data'][] = $this->map_document(get_the_ID(), $post_type);
            }
            wp_reset_postdata();
        }

        return $resultado;
    }

    /**
     * Construye un árbol jerárquico de categorías con sus documentos.
     *
     * @return array<string, mixed> Estructura: is_tree => true, data => [nodos raíz]
     */
    public function get_documents_tree(string $post_type, string $taxonomy_name): array
    {
        $resultado = [
            'is_grouped' => false,
            'is_tree'    => true,
            'data'       => []
        ];

        $root_terms = get_terms([
            'taxonomy'   => $taxonomy_name,
            'hide_empty' => true,
            'parent'     => 0,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);

        if (empty($root_terms) || is_wp_error($root_terms)) {
            return $resultado;
        }

        foreach ($root_terms as $term) {
            if ($term instanceof \WP_Term) {
                $node = $this->build_tree_node($term, $taxonomy_name, $post_type);
                if (! empty($node['children']) || ! empty($node['documents'])) {
                    $resultado['data'][] = $node;
                }
            }
        }

        return $resultado;
    }

    /**
     * Construye recursivamente un nodo del árbol.
     * 
     * @return array<string, mixed>
     */
    private function build_tree_node(\WP_Term $term, string $taxonomy_name, string $post_type): array
    {
        $color = get_term_meta($term->term_id, 'color', true) ?: '#1b8a4e';

        $node = [
            'term_id'     => $term->term_id,
            'term_name'   => $term->name,
            'color'       => $color,
            'children'    => [],
            'documents'   => [],
        ];

        $child_terms = get_terms([
            'taxonomy'   => $taxonomy_name,
            'hide_empty' => true,
            'parent'     => $term->term_id,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);

        if (! empty($child_terms) && ! is_wp_error($child_terms)) {
            foreach ($child_terms as $child) {
                if ($child instanceof \WP_Term) {
                    $child_node = $this->build_tree_node($child, $taxonomy_name, $post_type);
                    if (! empty($child_node['children']) || ! empty($child_node['documents'])) {
                        $node['children'][] = $child_node;
                    }
                }
            }
        }

        $args = [
            'post_type'              => $post_type,
            'posts_per_page'         => -1,
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
            'tax_query'              => [
                [
                    'taxonomy'         => $taxonomy_name,
                    'field'            => 'term_id',
                    'terms'            => $term->term_id,
                    'include_children' => false,
                ],
            ],
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $node['documents'][] = $this->map_document(get_the_ID(), $post_type);
            }
        }
        wp_reset_postdata();

        return $node;
    }

    /**
     * Cuenta recursivamente todos los documentos en un nodo y sus hijos.
     * @param array<string, mixed> $node
     */
    public function count_all_documents(array $node): int
    {
        $count = count($node['documents']);
        foreach ($node['children'] as $child) {
            $count += $this->count_all_documents($child);
        }
        return $count;
    }

    /**
     * @return array<string, mixed>
     */
    private function map_document(int $post_id, string $post_type): array
    {
        $file_url = $this->get_document_url($post_id, $post_type);
        return [
            'id'        => $post_id,
            'title'     => get_the_title($post_id),
            'permalink' => get_permalink($post_id),
            'file_url'  => $file_url,
            'has_file'  => (! empty($file_url) && '#' !== $file_url)
        ];
    }
}

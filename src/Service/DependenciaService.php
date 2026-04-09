<?php

namespace VpinUnf\Core\Service;

/**
 * Servicio de Modelo de Datos para Dependencias
 */
class DependenciaService
{
    /**
     * Obtiene el árbol jerárquico de dependencias organizativas para el Organigrama.
     * @param int $parent_id ID padre inicial (0 para root).
     * @return array Estructura de árbol del nodo raíz.
     */
    public function get_dependencia_tree(int $parent_id = 0): array
    {
        $root_node = [];
        
        if ($parent_id > 0) {
            $root_post = get_post($parent_id);
            if ($root_post && $root_post->post_type === 'dependencia') {
                $root_node = $this->build_node($root_post);
            }
        } else {
            // Buscar el padre superior (único)
            $top_level = get_posts([
                'post_type'      => 'dependencia',
                'post_parent'    => 0,
                'posts_per_page' => 1,
                'orderby'        => 'menu_order title',
                'order'          => 'ASC'
            ]);
            
            if (!empty($top_level)) {
                $root_node = $this->build_node($top_level[0]);
            }
        }
        
        return $root_node;
    }

    /**
     * Construye recursivamente un nodo y sus hijos
     */
    private function build_node(\WP_Post $post): array
    {
        $id = $post->ID;
        
        $siglas = get_post_meta($id, '_dependencia_siglas', true);
        
        $autoridad_id = get_post_meta($id, '_dependencia_autoridad_id', true);
        $autoridad = '';
        $image_url = '';
        $icon_class = '';
        
        if (!empty($autoridad_id)) {
            $autoridad = get_the_title($autoridad_id);
            // Intentar cargar la foto de la autoridad
            $autoridad_thumb = get_the_post_thumbnail_url($autoridad_id, 'thumbnail');
            if ($autoridad_thumb) {
                $image_url = $autoridad_thumb;
            }
        }
        
        // Si no hay foto de autoridad, recuperar el icono institucional
        if (empty($image_url)) {
            $icono_guardado = get_post_meta($id, '_dependencia_icono', true);
            if (!empty($icono_guardado)) {
                $icon_class = $icono_guardado;
            } else {
                // Fallback por defecto si tampoco hay icono guardado
                $icon_class = 'fas fa-sitemap';
            }
        }
        
        $linked_page_id = get_post_meta($id, '_dependencia_page_id', true);
        $permalink = $linked_page_id ? get_permalink(absint($linked_page_id)) : get_permalink($id);

        $node = [
            'id'         => $id,
            'title'      => $post->post_title,
            'siglas'     => $siglas,
            'autoridad'  => $autoridad,
            'image_url'  => $image_url,
            'icon_class' => $icon_class,
            'permalink'  => $permalink,
            'children'   => []
        ];
        
        $children_posts = get_posts([
            'post_type'      => 'dependencia',
            'post_parent'    => $id,
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC'
        ]);
        
        if (!empty($children_posts)) {
            foreach ($children_posts as $child) {
                $node['children'][] = $this->build_node($child);
            }
        }
        
        return $node;
    }
}

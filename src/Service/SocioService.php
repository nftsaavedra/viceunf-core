<?php

declare(strict_types=1);

namespace ViceUnf\Core\Service;

/**
 * Service: SocioService
 * 
 * Abstrae las consultas a la base de datos para el CPT 'socio'.
 */
class SocioService
{

    /**
     * Obtiene la lista completa de socios ordenados por Menu Order (Orden natural manual).
     */
    public function get_all_socios(): \WP_Query
    {
        $args = [
            'post_type'      => 'socio',
            'posts_per_page' => -1, // Mostrar todos
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'no_found_rows'  => true, // Optimización: no contar total
        ];

        return new \WP_Query($args);
    }
}

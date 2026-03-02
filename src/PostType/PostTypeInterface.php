<?php
declare(strict_types=1);

namespace ViceUnf\Core\PostType;

/**
 * Interface PostTypeInterface
 *
 * Define el contrato estándar que deben cumplir todos los Custom Post Types
 * registrados en el core del tema. Esto asegura OCP (Open/Closed Principle)
 * al permitir registrar nuevos CPTs dinámicamente.
 */
interface PostTypeInterface {
    
    /**
     * Obtiene el identificador (slug) del CPT.
     * @return string
     */
    public function get_slug(): string;

    /**
     * Obtiene los argumentos de configuración para register_post_type().
     * @return array<string, mixed>
     */
    public function get_args(): array;
}

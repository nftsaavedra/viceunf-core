<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

/**
 * Contrato opcional para CPTs que también registran taxonomías propias.
 * Aplica el Principio de Segregación de Interfaces (ISP).
 */
interface HasTaxonomiesInterface
{

    /**
     * Registra las taxonomías asociadas al CPT.
     */
    public function register_taxonomies(): void;
}

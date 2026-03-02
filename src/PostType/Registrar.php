<?php

declare(strict_types=1);

namespace ViceUnf\Core\PostType;

/**
 * Clase Registrar
 * 
 * Instancia las clases que implementan la interfaz PostTypeInterface
 * y las registra dinámicamente. Cumple con SRP y OCP.
 */
class Registrar
{

    /** @var PostTypeInterface[] */
    private array $cpts;

    /**
     * @param PostTypeInterface[] $cpts Lista de CPTs a registrar.
     */
    public function __construct(array $cpts)
    {
        foreach ($cpts as $cpt) {
            if (! $cpt instanceof PostTypeInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Todos los items deben implementar PostTypeInterface. Se recibió: %s', get_debug_type($cpt))
                );
            }
        }
        $this->cpts = $cpts;
    }

    /**
     * Registra todos los CPTs inyectados en WordPress.
     */
    public function register_all(): void
    {
        foreach ($this->cpts as $cpt) {
            register_post_type($cpt->get_slug(), $cpt->get_args());

            if ($cpt instanceof HasTaxonomiesInterface) {
                $cpt->register_taxonomies();
            }
        }
    }
}

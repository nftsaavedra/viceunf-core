<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Clase DependenciaMetaBox
 * 
 * Gestiona el enlace relacional entre la "Dependencia" y la "Autoridad" designada
 * para el cargo, permitiendo asignar una resolución.
 */
class DependenciaMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'dependencia';
        $this->meta_box_id    = 'dependencia_jefatura_metabox';
        $this->meta_box_title = __('Jefatura y Designación', 'viceunf-core');
        parent::__construct();
    }

    public function enqueue_admin_scripts(string $hook): void
    {
        global $post;
        if (($hook === 'post-new.php' || $hook === 'post.php') && $post && $post->post_type === $this->post_type) {
            // Cargar CSS y JS de Select2 vía CDN
            wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '4.1.0');
            wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], '4.1.0', true);

            // Inicializar Select2 en el ID del input
            $script = "
                jQuery(document).ready(function($) {
                    $('#dependencia_autoridad_id').select2({
                        width: '100%',
                        placeholder: '-- Buscar Autoridad --',
                        allowClear: true
                    });
                });
            ";
            wp_add_inline_script('select2-js', $script);
        }
    }

    protected function render_fields(\WP_Post $post): void
    {

        $jefe_asignado_id = get_post_meta($post->ID, '_dependencia_autoridad_id', true);
        $resolucion       = get_post_meta($post->ID, '_dependencia_resolucion', true);

        // Consultar todas las Autoridades para poblar el <select>
        $autoridades_query = new \WP_Query([
            'post_type'      => 'autoridad',
            'post_status'    => 'publish',
            'posts_per_page' => -1, // Obtener todas
            'orderby'        => 'title',
            'order'          => 'ASC'
        ]);

?>
        <style>
            .dep-meta-row {
                margin-bottom: 15px;
            }

            .dep-meta-row label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .dep-meta-row select,
            .dep-meta-row input[type="text"] {
                width: 100%;
                max-width: 500px;
                padding: 5px;
            }

            .dep-desc {
                color: #666;
                font-style: italic;
                display: block;
                margin-top: 4px;
            }
        </style>

        <!-- Selección de Autoridad -->
        <div class="dep-meta-row">
            <label for="dependencia_autoridad_id"><?php _e('Autoridad a Cargo (Director / Jefe):', 'viceunf-core'); ?></label>
            <select name="dependencia_autoridad_id" id="dependencia_autoridad_id">
                <option value=""><?php _e('-- Sin Asignar / Vacante --', 'viceunf-core'); ?></option>
                <?php if ($autoridades_query->have_posts()) : ?>
                    <?php while ($autoridades_query->have_posts()) : $autoridades_query->the_post(); ?>
                        <option value="<?php echo esc_attr((string)get_the_ID()); ?>" <?php selected($jefe_asignado_id, (string)get_the_ID()); ?>>
                            <?php the_title(); ?>
                        </option>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                <?php endif; ?>
            </select>
            <span class="dep-desc"><?php _e('Seleccione la autoridad designada para dirigir esta dependencia. (Debe crearla primero en el menú "Autoridades").', 'viceunf-core'); ?></span>
        </div>

        <!-- Resolución de Nombramiento -->
        <div class="dep-meta-row">
            <label for="dependencia_resolucion"><?php _e('Resolución de Designación:', 'viceunf-core'); ?></label>
            <input type="text" id="dependencia_resolucion" name="dependencia_resolucion" value="<?php echo esc_attr($resolucion); ?>" placeholder="Ej: Resolución de C.O. Nº 123-2023-UNF" />
            <span class="dep-desc"><?php _e('Documento legal vigente mediante el cual fue designado al cargo.', 'viceunf-core'); ?></span>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        // Persistir Autoridad Asignada (Validar que sea un ID numérico)
        if (isset($post_data['dependencia_autoridad_id'])) {
            $autoridad_id = sanitize_text_field($post_data['dependencia_autoridad_id']);
            if (empty($autoridad_id)) {
                delete_post_meta($post_id, '_dependencia_autoridad_id');
            } else {
                update_post_meta($post_id, '_dependencia_autoridad_id', absint($autoridad_id));
            }
        }

        // Persistir Resolución
        if (isset($post_data['dependencia_resolucion'])) {
            $resolucion_saneada = sanitize_text_field($post_data['dependencia_resolucion']);
            update_post_meta($post_id, '_dependencia_resolucion', $resolucion_saneada);
        }
    }
}

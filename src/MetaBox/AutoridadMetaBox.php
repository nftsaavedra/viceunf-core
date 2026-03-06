<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Gestiona la interfaz y persistencia de campos personalizados para el CPT "autoridad".
 */
class AutoridadMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'autoridad';
        $this->meta_box_id    = 'autoridad_datos_personales_metabox';
        $this->meta_box_title = __('Datos Profesionales e Institucionales', 'viceunf-core');
        parent::__construct();
    }

    public function enqueue_admin_scripts(string $hook): void
    {
        global $post;
        if (($hook !== 'post-new.php' && $hook !== 'post.php') || !$post || $post->post_type !== $this->post_type) {
            return;
        }

        wp_enqueue_style('viceunf-metabox-common-css', VICEUNF_CORE_URL . 'assets/admin/metabox-common.css', [], VICEUNF_CORE_VERSION);
    }

    protected function render_fields(\WP_Post $post): void
    {
        $grado_academico = get_post_meta($post->ID, '_autoridad_grado', true);
        $orcid           = get_post_meta($post->ID, '_autoridad_orcid', true);
        $cti_vitae       = get_post_meta($post->ID, '_autoridad_cti_vitae', true);
        $correo          = get_post_meta($post->ID, '_autoridad_correo', true);
?>
        <div class="viceunf-metabox-wrapper">
            <div class="viceunf-metabox-section">

                <h4 class="viceunf-metabox-subtitle"><?php _e('Grados y Filiación', 'viceunf-core'); ?></h4>
                <div class="viceunf-metabox-field">
                    <label for="autoridad_grado" class="viceunf-metabox-label"><?php _e('Grado Académico:', 'viceunf-core'); ?></label>
                    <select name="autoridad_grado" id="autoridad_grado" class="viceunf-metabox-input dt-w-100">
                        <option value=""><?php _e('-- Seleccionar --', 'viceunf-core'); ?></option>
                        <option value="Ph.D." <?php selected($grado_academico, 'Ph.D.'); ?>>Ph.D.</option>
                        <option value="Dr." <?php echo in_array($grado_academico, ['Dr.', 'Dra.']) ? 'selected="selected"' : ''; ?>>Doctor / Doctora (Dr. / Dra.)</option>
                        <option value="Mg." <?php echo in_array($grado_academico, ['Mg.', 'Mgtr.']) ? 'selected="selected"' : ''; ?>>Magíster (Mg. / Mgtr.)</option>
                        <option value="Ing." <?php selected($grado_academico, 'Ing.'); ?>>Ingeniero / Ingeniera (Ing.)</option>
                        <option value="Lic." <?php selected($grado_academico, 'Lic.'); ?>>Licenciado / Licenciada (Lic.)</option>
                        <option value="Bach." <?php selected($grado_academico, 'Bach.'); ?>>Bachiller (Bach.)</option>
                    </select>
                    <span class="viceunf-metabox-desc"><?php _e('Seleccione el grado más alto alcanzado. El formato aplica normativas RAE inclusivas.', 'viceunf-core'); ?></span>
                </div>

                <div class="viceunf-metabox-field">
                    <label for="autoridad_orcid" class="viceunf-metabox-label"><?php _e('Enlace ORCID:', 'viceunf-core'); ?></label>
                    <input type="url" id="autoridad_orcid" name="autoridad_orcid" value="<?php echo esc_url($orcid); ?>" placeholder="https://orcid.org/0000-0000-0000-0000" class="viceunf-metabox-input dt-w-100" />
                    <span class="viceunf-metabox-desc"><?php _e('URL completa del perfil público de ORCID.', 'viceunf-core'); ?></span>
                </div>

                <div class="viceunf-metabox-field">
                    <label for="autoridad_cti_vitae" class="viceunf-metabox-label"><?php _e('Enlace CTI Vitae (Concytec):', 'viceunf-core'); ?></label>
                    <input type="url" id="autoridad_cti_vitae" name="autoridad_cti_vitae" value="<?php echo esc_url($cti_vitae); ?>" placeholder="https://ctivitae.concytec.gob.pe/..." class="viceunf-metabox-input dt-w-100" />
                    <span class="viceunf-metabox-desc"><?php _e('Opcional. URL completa de la hoja de vida en CTI Vitae.', 'viceunf-core'); ?></span>
                </div>

                <div class="viceunf-metabox-field">
                    <label for="autoridad_correo" class="viceunf-metabox-label"><?php _e('Correo Electrónico Institucional:', 'viceunf-core'); ?></label>
                    <input type="email" id="autoridad_correo" name="autoridad_correo" value="<?php echo esc_attr($correo); ?>" placeholder="ejemplo@unf.edu.pe" class="viceunf-metabox-input dt-w-100" />
                    <span class="viceunf-metabox-desc"><?php _e('Este correo se mostrará públicamente.', 'viceunf-core'); ?></span>
                </div>

            </div>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        if (isset($post_data['autoridad_grado'])) {
            update_post_meta($post_id, '_autoridad_grado', sanitize_text_field($post_data['autoridad_grado']));
        }

        if (isset($post_data['autoridad_orcid'])) {
            update_post_meta($post_id, '_autoridad_orcid', esc_url_raw($post_data['autoridad_orcid']));
        }

        if (isset($post_data['autoridad_cti_vitae'])) {
            update_post_meta($post_id, '_autoridad_cti_vitae', esc_url_raw($post_data['autoridad_cti_vitae']));
        }

        if (isset($post_data['autoridad_correo'])) {
            update_post_meta($post_id, '_autoridad_correo', sanitize_email($post_data['autoridad_correo']));
        }
    }
}

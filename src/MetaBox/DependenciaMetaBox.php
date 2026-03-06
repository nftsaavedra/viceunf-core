<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Gestiona los metadatos del CPT "Dependencia":
 * - Jefatura y Designación (autoridad, resolución texto, archivo resolución upload/enlace)
 * - Datos Institucionales (siglas, correo, teléfono, ubicación, horario)
 */
class DependenciaMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'dependencia';
        $this->meta_box_id    = 'dependencia_jefatura_metabox';
        $this->meta_box_title = __('Jefatura, Designación y Datos Institucionales', 'viceunf-core');
        parent::__construct();
    }

    public function enqueue_admin_scripts(string $hook): void
    {
        global $post;
        if (($hook !== 'post-new.php' && $hook !== 'post.php') || !$post || $post->post_type !== $this->post_type) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('viceunf-metabox-common-css', VICEUNF_CORE_URL . 'assets/admin/metabox-common.css', [], VICEUNF_CORE_VERSION);
        wp_enqueue_script(
            'dependencia-metabox-js',
            VICEUNF_CORE_URL . 'assets/admin/dependencia-metabox.js',
            [],
            VICEUNF_CORE_VERSION,
            true
        );
    }

    protected function render_fields(\WP_Post $post): void
    {
        $jefe_asignado_id = get_post_meta($post->ID, '_dependencia_autoridad_id', true);
        $resolucion       = get_post_meta($post->ID, '_dependencia_resolucion', true);

        $source_type      = get_post_meta($post->ID, '_dependencia_resolucion_source_type', true) ?: 'upload';
        $file_id          = get_post_meta($post->ID, '_dependencia_resolucion_file_id', true);
        $external_url     = get_post_meta($post->ID, '_dependencia_resolucion_external_url', true);
        $file_url         = $file_id ? wp_get_attachment_url((int)$file_id) : '';
        $file_name        = $file_url ? basename($file_url) : '';

        $siglas    = get_post_meta($post->ID, '_dependencia_siglas', true);
        $correo    = get_post_meta($post->ID, '_dependencia_correo', true);
        $telefono  = get_post_meta($post->ID, '_dependencia_telefono', true);
        $ubicacion = get_post_meta($post->ID, '_dependencia_ubicacion', true);
        $horario   = get_post_meta($post->ID, '_dependencia_horario', true);


?>
        <div class="viceunf-metabox-wrapper">

            <!-- SEC 1: Jefatura y Designación -->
            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle"><?php _e('Jefatura y Designación', 'viceunf-core'); ?></h4>

                <div class="viceunf-metabox-field">
                    <?php
                    $autoridad_title = $jefe_asignado_id ? get_the_title(absint($jefe_asignado_id)) : '';
                    ?>
                    <label class="viceunf-metabox-label"><strong><?php _e('Autoridad a Cargo (Director / Jefe):', 'viceunf-core'); ?></strong></label>
                    <div class="ajax-search-wrapper" data-action="viceunf_search_autoridades">
                        <div class="selected-item-view <?php echo ($jefe_asignado_id ? 'active' : ''); ?>">
                            <span class="selected-item-title"><?php echo esc_html($autoridad_title); ?></span>
                            <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
                        </div>
                        <div class="search-input-view <?php echo ($jefe_asignado_id ? '' : 'active'); ?>">
                            <input type="text" class="viceunf-metabox-input dt-w-100 ajax-search-input" placeholder="Busca por nombre de la autoridad...">
                            <div class="ajax-search-results"></div>
                        </div>
                        <input type="hidden" class="ajax-search-hidden-id" id="dependencia_autoridad" name="dependencia_autoridad" value="<?php echo esc_attr((string)$jefe_asignado_id); ?>">
                    </div>
                    <span class="viceunf-metabox-desc"><?php _e('Busque y seleccione la autoridad designada para dirigir esta dependencia. (búsqueda dinámica)', 'viceunf-core'); ?></span>
                </div>

                <div class="viceunf-metabox-field">
                    <label for="dependencia_resolucion" class="viceunf-metabox-label"><strong><?php _e('Resolución de Designación:', 'viceunf-core'); ?></strong></label>
                    <input type="text" id="dependencia_resolucion" name="dependencia_resolucion" value="<?php echo esc_attr($resolucion); ?>" placeholder="RCU N° 123-2024-UNF" class="viceunf-metabox-input dt-w-100" />
                    <span class="viceunf-metabox-desc"><?php _e('Nombre o número del documento legal vigente.', 'viceunf-core'); ?></span>
                </div>

                <div class="viceunf-metabox-field">
                    <label class="viceunf-metabox-label"><strong><?php _e('Documento de Resolución (Archivo o Enlace):', 'viceunf-core'); ?></strong></label>

                    <div class="viceunf-radio-tabs" style="margin-bottom: 10px;">
                        <input type="radio" id="res_source_upload" name="dependencia_resolucion_source_type" value="upload" <?php checked($source_type, 'upload'); ?>>
                        <label for="res_source_upload" class="viceunf-metabox-label" style="display:inline-block; margin-right:15px; cursor:pointer;">Subir Archivo</label>

                        <input type="radio" id="res_source_external" name="dependencia_resolucion_source_type" value="external" <?php checked($source_type, 'external'); ?>>
                        <label for="res_source_external" class="viceunf-metabox-label" style="display:inline-block; cursor:pointer;">Enlace Externo</label>
                    </div>

                    <!-- UPLOAD -->
                    <div id="dependencia-upload-section" class="conditional-res-field">
                        <input type="hidden" id="dependencia_resolucion_file_id" name="dependencia_resolucion_file_id" value="<?php echo esc_attr((string)$file_id); ?>">
                        <div style="margin-bottom: 10px;">
                            <button type="button" class="button" id="upload_resolucion_button">Seleccionar o Subir Archivo</button>
                            <button type="button" class="button button-secondary" id="remove_resolucion_button" style="<?php echo empty($file_id) ? 'display:none;' : ''; ?>">Quitar Archivo</button>
                        </div>
                        <div id="dependencia-filename-display" style="padding: 10px; background: #f1f5f9; border-radius: 4px; border: 1px dashed #cbd5e1; <?php echo empty($file_url) ? 'display:none;' : ''; ?>">
                            <strong style="color: #334155;">Archivo actual:</strong>
                            <a href="<?php echo esc_url($file_url); ?>" target="_blank" style="text-decoration:none; color:#0e59a5;" id="dependencia-filename-text"><?php echo esc_html($file_name); ?></a>
                        </div>
                    </div>

                    <!-- EXTERNAL -->
                    <div id="dependencia-external-section" class="conditional-res-field" style="display:none;">
                        <input type="url" id="dependencia_resolucion_external_url" name="dependencia_resolucion_external_url" value="<?php echo esc_url($external_url); ?>" placeholder="https://busquedas.elperuano.pe/..." class="viceunf-metabox-input dt-w-100" />
                        <span class="viceunf-metabox-desc"><?php _e('URL completa del documento externo (ej. Google Drive, portal institucional).', 'viceunf-core'); ?></span>
                    </div>
                </div>
            </div>

            <!-- SEC 2: Datos Institucionales -->
            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle"><?php _e('Datos Institucionales', 'viceunf-core'); ?></h4>

                <div class="viceunf-cols">
                    <div class="viceunf-meta-row">
                        <label for="dependencia_siglas" class="viceunf-metabox-label"><?php _e('Siglas / Acrónimo:', 'viceunf-core'); ?></label>
                        <input type="text" id="dependencia_siglas" name="dependencia_siglas" value="<?php echo esc_attr($siglas); ?>" placeholder="ej. VPIN, VRAC" class="viceunf-metabox-input dt-w-100" />
                        <span class="viceunf-metabox-desc"><?php _e('Abreviatura oficial de la dependencia.', 'viceunf-core'); ?></span>
                    </div>
                    <div class="viceunf-meta-row">
                        <label for="dependencia_correo" class="viceunf-metabox-label"><?php _e('Correo Institucional:', 'viceunf-core'); ?></label>
                        <input type="email" id="dependencia_correo" name="dependencia_correo" value="<?php echo esc_attr($correo); ?>" placeholder="dependencia@unf.edu.pe" class="viceunf-metabox-input dt-w-100" />
                        <span class="viceunf-metabox-desc"><?php _e('Email oficial de contacto de la dependencia.', 'viceunf-core'); ?></span>
                    </div>
                </div>

                <div class="viceunf-cols">
                    <div class="viceunf-meta-row">
                        <label for="dependencia_telefono" class="viceunf-metabox-label"><?php _e('Teléfono / Anexo:', 'viceunf-core'); ?></label>
                        <input type="text" id="dependencia_telefono" name="dependencia_telefono" value="<?php echo esc_attr($telefono); ?>" placeholder="Ej: (073) 123456 - Anexo 123" class="viceunf-metabox-input dt-w-100" />
                        <span class="viceunf-metabox-desc"><?php _e('Número de contacto telefónico y/o anexo interno.', 'viceunf-core'); ?></span>
                    </div>
                    <div class="viceunf-meta-row">
                        <label for="dependencia_ubicacion" class="viceunf-metabox-label"><?php _e('Ubicación / Oficina:', 'viceunf-core'); ?></label>
                        <input type="text" id="dependencia_ubicacion" name="dependencia_ubicacion" value="<?php echo esc_attr($ubicacion); ?>" placeholder="Ej: Pabellón Central, 2do Piso" class="viceunf-metabox-input dt-w-100" />
                        <span class="viceunf-metabox-desc"><?php _e('Referencia física de la oficina dentro del campus.', 'viceunf-core'); ?></span>
                    </div>
                </div>

                <div class="viceunf-meta-row">
                    <label for="dependencia_horario" class="viceunf-metabox-label"><?php _e('Horario de Atención:', 'viceunf-core'); ?></label>
                    <input type="text" id="dependencia_horario" name="dependencia_horario" value="<?php echo esc_attr($horario); ?>" placeholder="Lunes - Viernes: 8:00 am - 1:00 pm y 2:00 pm - 4:00 pm" class="viceunf-metabox-input dt-w-100" />
                    <span class="viceunf-metabox-desc"><?php _e('Horario de atención al público de esta dependencia.', 'viceunf-core'); ?></span>
                </div>

            </div>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        // --- Autoridad Asignada ---
        if (isset($post_data['dependencia_autoridad'])) { // Changed from dependencia_autoridad_id
            $autoridad_id = sanitize_text_field($post_data['dependencia_autoridad']); // Changed from dependencia_autoridad_id
            if (empty($autoridad_id)) {
                delete_post_meta($post_id, '_dependencia_autoridad_id');
            } else {
                update_post_meta($post_id, '_dependencia_autoridad_id', absint($autoridad_id));
            }
        }

        // --- Resolución (texto) ---
        if (isset($post_data['dependencia_resolucion'])) {
            update_post_meta($post_id, '_dependencia_resolucion', sanitize_text_field($post_data['dependencia_resolucion']));
        }

        // --- Resolución (archivo / enlace) ---
        if (isset($post_data['dependencia_resolucion_source_type'])) {
            $source_type = sanitize_text_field($post_data['dependencia_resolucion_source_type']);
            update_post_meta($post_id, '_dependencia_resolucion_source_type', $source_type);

            if ('upload' === $source_type) {
                $file_id = isset($post_data['dependencia_resolucion_file_id']) ? sanitize_text_field($post_data['dependencia_resolucion_file_id']) : '';
                update_post_meta($post_id, '_dependencia_resolucion_file_id', $file_id);
                delete_post_meta($post_id, '_dependencia_resolucion_external_url');
            } elseif ('external' === $source_type) {
                $ext_url = isset($post_data['dependencia_resolucion_external_url']) ? esc_url_raw($post_data['dependencia_resolucion_external_url']) : '';
                update_post_meta($post_id, '_dependencia_resolucion_external_url', $ext_url);
                delete_post_meta($post_id, '_dependencia_resolucion_file_id');
            }
        }

        // --- Datos Institucionales ---
        if (isset($post_data['dependencia_siglas'])) {
            update_post_meta($post_id, '_dependencia_siglas', sanitize_text_field($post_data['dependencia_siglas']));
        }

        if (isset($post_data['dependencia_correo'])) {
            update_post_meta($post_id, '_dependencia_correo', sanitize_email($post_data['dependencia_correo']));
        }

        if (isset($post_data['dependencia_telefono'])) {
            update_post_meta($post_id, '_dependencia_telefono', sanitize_text_field($post_data['dependencia_telefono']));
        }

        if (isset($post_data['dependencia_ubicacion'])) {
            update_post_meta($post_id, '_dependencia_ubicacion', sanitize_text_field($post_data['dependencia_ubicacion']));
        }

        if (isset($post_data['dependencia_horario'])) {
            update_post_meta($post_id, '_dependencia_horario', sanitize_text_field($post_data['dependencia_horario']));
        }
    }
}

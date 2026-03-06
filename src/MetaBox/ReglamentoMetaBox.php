<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Gestión del archivo/enlace para el CPT "Reglamento".
 * Garantiza que la funcionalidad de adjuntar PDFs no se pierda al cambiar de tema.
 */
class ReglamentoMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'reglamento';
        $this->meta_box_id    = 'reglamento_file_metabox';
        $this->meta_box_title = __('Archivo del Reglamento (Obligatorio)', 'viceunf-core');
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
            'reglamento-metabox-js',
            VICEUNF_CORE_URL . 'assets/admin/reglamento-metabox.js',
            [],
            VICEUNF_CORE_VERSION,
            true
        );
    }

    protected function render_fields(\WP_Post $post): void
    {
        $source_type  = get_post_meta($post->ID, '_reglamento_source_type_key', true) ?: 'upload';
        $file_id      = get_post_meta($post->ID, '_reglamento_file_id_key', true);
        $external_url = get_post_meta($post->ID, '_reglamento_external_url_key', true);
        $file_url     = $file_id ? wp_get_attachment_url((int)$file_id) : '';
        $file_name    = $file_url ? basename($file_url) : '';
?>
        <div class="viceunf-metabox-wrapper">
            <div class="viceunf-metabox-section">

                <h4 class="viceunf-metabox-subtitle"><?php _e('Fuente del Documento', 'viceunf-core'); ?></h4>

                <div id="reglamento-source-selector" class="viceunf-metabox-field viceunf-radio-tabs">
                    <input type="radio" id="source_upload" name="reglamento_source_type" value="upload" <?php checked($source_type, 'upload'); ?>>
                    <label for="source_upload" class="viceunf-metabox-label" style="display:inline-block; margin-right:15px; cursor:pointer;">Subir Archivo</label>

                    <input type="radio" id="source_external" name="reglamento_source_type" value="external" <?php checked($source_type, 'external'); ?>>
                    <label for="source_external" class="viceunf-metabox-label" style="display:inline-block; cursor:pointer;">Enlace Externo</label>
                </div>

                <div id="reglamento-upload-section" class="viceunf-metabox-field conditional-field">
                    <p class="viceunf-metabox-desc"><?php _e('Seleccione el archivo (PDF, Word, etc.) correspondiente a este reglamento.', 'viceunf-core'); ?></p>
                    <input type="hidden" id="reglamento_file_id" name="reglamento_file_id" value="<?php echo esc_attr((string)$file_id); ?>">

                    <div style="margin-bottom: 15px;">
                        <button type="button" class="button" id="upload_reglamento_button">Seleccionar o Subir Archivo</button>
                        <button type="button" class="button button-secondary" id="remove_reglamento_button" style="<?php echo empty($file_id) ? 'display:none;' : ''; ?>">Quitar Archivo</button>
                    </div>

                    <div id="reglamento-filename-display" style="padding: 10px; background: #f1f5f9; border-radius: 4px; border: 1px dashed #cbd5e1; <?php echo empty($file_url) ? 'display:none;' : ''; ?>">
                        <strong style="color: #334155;">Archivo actual:</strong>
                        <a href="<?php echo esc_url($file_url); ?>" target="_blank" style="text-decoration:none; color:#0e59a5;" id="reglamento-filename-text"><?php echo esc_html($file_name); ?></a>
                    </div>
                </div>

                <div id="reglamento-external-section" class="viceunf-metabox-field conditional-field" style="display:none;">
                    <label for="reglamento_external_url" class="viceunf-metabox-label"><?php _e('Enlace Web Externo:', 'viceunf-core'); ?></label>
                    <input type="url" id="reglamento_external_url" name="reglamento_external_url" value="<?php echo esc_url($external_url); ?>" placeholder="https://..." class="viceunf-metabox-input dt-w-100" />
                    <span class="viceunf-metabox-desc" style="margin-top:5px; display:block;"><?php _e('Útil para reglamentos alojados en El Peruano, Google Drive, etc.', 'viceunf-core'); ?></span>
                </div>

            </div>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        if (isset($post_data['reglamento_source_type'])) {
            $source_type = sanitize_text_field($post_data['reglamento_source_type']);
            update_post_meta($post_id, '_reglamento_source_type_key', $source_type);

            if ('upload' === $source_type) {
                $file_id = isset($post_data['reglamento_file_id']) ? sanitize_text_field($post_data['reglamento_file_id']) : '';
                update_post_meta($post_id, '_reglamento_file_id_key', $file_id);
                delete_post_meta($post_id, '_reglamento_external_url_key');
            } elseif ('external' === $source_type) {
                $ext_url = isset($post_data['reglamento_external_url']) ? esc_url_raw($post_data['reglamento_external_url']) : '';
                update_post_meta($post_id, '_reglamento_external_url_key', $ext_url);
                delete_post_meta($post_id, '_reglamento_file_id_key');
            }
        }
    }
}

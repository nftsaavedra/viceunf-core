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

    protected function render_fields(\WP_Post $post): void
    {
        $source_type  = get_post_meta($post->ID, '_reglamento_source_type_key', true) ?: 'upload';
        $file_id      = get_post_meta($post->ID, '_reglamento_file_id_key', true);
        $external_url = get_post_meta($post->ID, '_reglamento_external_url_key', true);
        $file_url     = $file_id ? wp_get_attachment_url((int)$file_id) : '';
        $file_name    = $file_url ? basename($file_url) : '';
?>
        <div id="reglamento-source-selector" class="viceunf-metabox-container">
            <div class="reglamento-field radio-buttons-as-tabs" style="margin-bottom: 1em;">
                <input type="radio" id="source_upload" name="reglamento_source_type" value="upload" <?php checked($source_type, 'upload'); ?>>
                <label for="source_upload">Subir Archivo</label>
                &nbsp;&nbsp;
                <input type="radio" id="source_external" name="reglamento_source_type" value="external" <?php checked($source_type, 'external'); ?>>
                <label for="source_external">Enlace Externo</label>
            </div>

            <div id="reglamento-upload-section" class="reglamento-section-wrapper conditional-field" style="margin-bottom: 1em;">
                <p><?php _e('Seleccione el archivo (PDF, Word, etc.) correspondiente a este reglamento.', 'viceunf-core'); ?></p>
                <input type="hidden" id="reglamento_file_id" name="reglamento_file_id" value="<?php echo esc_attr((string)$file_id); ?>">
                <button type="button" class="button" id="upload_reglamento_button">Seleccionar o Subir Archivo</button>
                <button type="button" class="button button-secondary" id="remove_reglamento_button">Quitar Archivo</button>
                <div class="file-info" style="margin-top: 10px;">
                    <?php if ($file_id && $file_url) : ?>
                        Archivo actual: <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php echo esc_html($file_name); ?></a>
                    <?php else : ?>
                        <span style="color:#666; font-style:italic;">No se ha seleccionado ningún archivo.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div id="reglamento-external-section" class="reglamento-section-wrapper conditional-field" style="margin-bottom: 1em;">
                <p><?php _e('Pegue la URL completa del documento externo (ej. un PDF en Google Drive).', 'viceunf-core'); ?></p>
                <label for="reglamento_external_url"><strong>URL del Archivo:</strong></label><br>
                <input type="url" id="reglamento_external_url" name="reglamento_external_url" value="<?php echo esc_url((string)$external_url); ?>" placeholder="https://ejemplo.com/documento.pdf" class="large-text">
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

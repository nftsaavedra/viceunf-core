<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * MetaBox para el CPT "Slider"
 * Contiene campos para subtítulo, botones, alineación, y búsqueda de contenido.
 */
class SliderMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'slider';
        $this->meta_box_id    = 'slider_details_metabox';
        $this->meta_box_title = __('Datos del Slider', 'viceunf-core');
        parent::__construct();
    }

    public function enqueue_admin_scripts(string $hook): void
    {
        global $post;
        if (($hook === 'post-new.php' || $hook === 'post.php') && $post && $post->post_type === $this->post_type) {
            wp_enqueue_style('viceunf-metabox-common-css', VICEUNF_CORE_URL . 'assets/admin/metabox-common.css', [], VICEUNF_CORE_VERSION);
        }
    }

    protected function render_fields(\WP_Post $post): void
    {
        $subtitle       = get_post_meta($post->ID, '_slider_subtitle_key', true);
        $description    = get_post_meta($post->ID, '_slider_description_key', true);
        $text_alignment = get_post_meta($post->ID, '_slider_text_alignment_key', true);
        $btn1_text      = get_post_meta($post->ID, '_slider_btn1_text_key', true);
        $link_type      = get_post_meta($post->ID, '_slider_link_type_key', true);
        $link_url       = get_post_meta($post->ID, '_slider_link_url_key', true);
        $link_content_id = get_post_meta($post->ID, '_slider_link_content_id_key', true);
        $btn2_text      = get_post_meta($post->ID, '_slider_btn2_text_key', true);
        $btn2_link      = get_post_meta($post->ID, '_slider_btn2_link_key', true);
        $video_link     = get_post_meta($post->ID, '_slider_video_link_key', true);
?>
        <div class="viceunf-metabox-wrapper">
            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle">Contenido del Slider</h4>
                <div class="viceunf-metabox-field">
                    <label for="slider_subtitle" class="viceunf-metabox-label">Subtítulo</label>
                    <input type="text" id="slider_subtitle" name="slider_subtitle" value="<?php echo esc_attr((string)$subtitle); ?>" class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="viceunf-metabox-field">
                    <label for="slider_description" class="viceunf-metabox-label">Descripción</label>
                    <textarea id="slider_description" name="slider_description" rows="3" class="viceunf-metabox-input dt-w-100"><?php echo esc_textarea((string)$description); ?></textarea>
                </div>
                <div class="viceunf-metabox-field">
                    <label for="slider_text_alignment" class="viceunf-metabox-label">Alineación</label>
                    <select id="slider_text_alignment" name="slider_text_alignment" class="viceunf-metabox-input">
                        <option value="dt-text-left" <?php selected($text_alignment, 'dt-text-left'); ?>>Izquierda</option>
                        <option value="dt-text-center" <?php selected($text_alignment, 'dt-text-center'); ?>>Centro</option>
                        <option value="dt-text-right" <?php selected($text_alignment, 'dt-text-right'); ?>>Derecha</option>
                    </select>
                </div>
            </div>

            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle">Botón 1 (Principal)</h4>
                <div class="viceunf-metabox-field">
                    <label for="slider_btn1_text" class="viceunf-metabox-label">Texto Botón 1</label>
                    <input type="text" id="slider_btn1_text" name="slider_btn1_text" value="<?php echo esc_attr((string)$btn1_text); ?>" class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="viceunf-metabox-field">
                    <label for="slider_link_type" class="viceunf-metabox-label">Tipo de Enlace</label>
                    <select id="slider_link_type" name="slider_link_type" class="viceunf-metabox-input">
                        <option value="none" <?php selected($link_type, 'none'); ?>>Ninguno</option>
                        <option value="url" <?php selected($link_type, 'url'); ?>>URL Personalizada</option>
                        <option value="content" <?php selected($link_type, 'content'); ?>>Enlazar a Contenido (Buscar)</option>
                    </select>
                </div>

                <div class="viceunf-metabox-field conditional-field" id="campo_url" style="display:none">
                    <label for="slider_link_url" class="viceunf-metabox-label">URL Personalizada</label>
                    <input type="url" id="slider_link_url" name="slider_link_url" value="<?php echo esc_url((string)$link_url); ?>" placeholder="https://ejemplo.com" class="viceunf-metabox-input dt-w-100" />
                </div>

                <div class="viceunf-metabox-field conditional-field" id="campo_contenido" style="display:none">
                    <?php
                    $link_content_title = $link_content_id ? get_the_title(absint($link_content_id)) : '';
                    ?>
                    <label class="viceunf-metabox-label">Buscar Entrada o Página</label>
                    <div class="ajax-search-wrapper" data-action="viceunf_search_content">
                        <div class="selected-item-view <?php echo ($link_content_id ? 'active' : ''); ?>">
                            <span class="selected-item-title"><?php echo esc_html($link_content_title); ?></span>
                            <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
                        </div>
                        <div class="search-input-view <?php echo ($link_content_id ? '' : 'active'); ?>">
                            <input type="text" class="viceunf-metabox-input dt-w-100 ajax-search-input" placeholder="Escribe para buscar...">
                            <div class="ajax-search-results"></div>
                        </div>
                        <input type="hidden" class="ajax-search-hidden-id" id="slider_link_content_id" name="slider_link_content_id" value="<?php echo esc_attr((string)$link_content_id); ?>">
                    </div>
                </div>
            </div>

            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle">Botón 2 (Secundario)</h4>
                <div class="viceunf-metabox-field">
                    <label for="slider_btn2_text" class="viceunf-metabox-label">Texto (Opcional)</label>
                    <input type="text" id="slider_btn2_text" name="slider_btn2_text" value="<?php echo esc_attr((string)$btn2_text); ?>" class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="viceunf-metabox-field">
                    <label for="slider_btn2_link" class="viceunf-metabox-label">Enlace (Opcional)</label>
                    <input type="url" id="slider_btn2_link" name="slider_btn2_link" value="<?php echo esc_url((string)$btn2_link); ?>" class="viceunf-metabox-input dt-w-100" />
                </div>
            </div>

            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle">Botón de Video</h4>
                <div class="viceunf-metabox-field">
                    <label for="slider_video_link" class="viceunf-metabox-label">Enlace Video (Opcional)</label>
                    <input type="url" id="slider_video_link" name="slider_video_link" value="<?php echo esc_url((string)$video_link); ?>" placeholder="YouTube o Vimeo URL" class="viceunf-metabox-input dt-w-100" />
                </div>
            </div>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        $fields = [
            'slider_subtitle'        => '_slider_subtitle_key',
            'slider_description'     => '_slider_description_key',
            'slider_text_alignment'  => '_slider_text_alignment_key',
            'slider_btn1_text'       => '_slider_btn1_text_key',
            'slider_link_type'       => '_slider_link_type_key',
            'slider_link_url'        => '_slider_link_url_key',
            'slider_link_content_id' => '_slider_link_content_id_key',
            'slider_btn2_text'       => '_slider_btn2_text_key',
            'slider_btn2_link'       => '_slider_btn2_link_key',
            'slider_video_link'      => '_slider_video_link_key'
        ];

        foreach ($fields as $input => $meta_key) {
            if (isset($post_data[$input])) {
                $val = $post_data[$input];
                if (in_array($input, ['slider_link_url', 'slider_btn2_link', 'slider_video_link'])) {
                    $sanitized = esc_url_raw($val);
                } elseif ($input === 'slider_description') {
                    $sanitized = sanitize_textarea_field($val);
                } else {
                    $sanitized = sanitize_text_field($val);
                }
                update_post_meta($post_id, $meta_key, $sanitized);
            }
        }
    }
}

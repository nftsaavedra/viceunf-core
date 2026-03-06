<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * MetaBox para el CPT "socio"
 * Desacoplado del tema visual para mantener la persistencia segura.
 */
class SocioMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'socio';
        $this->meta_box_id    = 'socio_details_metabox';
        $this->meta_box_title = __('Detalles del Socio', 'viceunf-core');
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
        $socio_url = get_post_meta($post->ID, '_socio_url_key', true);
?>
        <div class="viceunf-metabox-wrapper">
            <div class="viceunf-metabox-section">
                <div class="viceunf-metabox-field">
                    <label for="socio_url" class="viceunf-metabox-label"><?php _e('Enlace Web del Socio (Opcional)', 'viceunf-core'); ?></label>
                    <input type="url" id="socio_url" name="socio_url" value="<?php echo esc_url((string)$socio_url); ?>" class="viceunf-metabox-input dt-w-100" placeholder="https://..." />
                </div>
            </div>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        if (isset($post_data['socio_url'])) {
            update_post_meta($post_id, '_socio_url_key', esc_url_raw($post_data['socio_url']));
        }
    }
}

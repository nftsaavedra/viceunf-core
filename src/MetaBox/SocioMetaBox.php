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

    protected function render_fields(\WP_Post $post): void
    {
        $socio_url = get_post_meta($post->ID, '_socio_url_key', true);
?>
        <div class="socio-field" style="margin-bottom: 1em;">
            <label for="socio_url"><strong><?php _e('Enlace Web del Socio (Opcional)', 'viceunf-core'); ?></strong></label><br>
            <input type="url" id="socio_url" name="socio_url" value="<?php echo esc_url((string)$socio_url); ?>" class="large-text" placeholder="https://..." />
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

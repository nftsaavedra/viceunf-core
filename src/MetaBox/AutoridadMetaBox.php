<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Clase AutoridadMetaBox
 * 
 * Gestiona la interfaz y persistencia de campos personalizados para el CPT "autoridad".
 * Sigue SRP, encapsulando solo el registro, renderizado y guardado de los metadatos.
 */
class AutoridadMetaBox
{
    /** @var string CPT al que aplica el metabox */
    private string $post_type = 'autoridad';

    /**
     * Registra los hooks de WordPress para los meta boxes
     */
    public function register_hooks(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box_data']);
    }

    /**
     * Agrega el meta box en la pantalla de edición del CPT "autoridad"
     */
    public function add_meta_box(string $post_type): void
    {
        if ($post_type !== $this->post_type) {
            return;
        }

        add_meta_box(
            'autoridad_datos_personales_metabox',         // ID del metabox
            __('Datos Profesionales e Institucionales', 'viceunf-core'), // Título
            [$this, 'render_meta_box_content'],           // Callback de renderizado
            $this->post_type,                             // Pantalla/CPT
            'normal',                                     // Contexto
            'high'                                        // Prioridad
        );
    }

    /**
     * Renderiza el contenido HTML del meta box
     *
     * @param \WP_Post $post Objeto del post actual
     */
    public function render_meta_box_content(\WP_Post $post): void
    {
        // 1. Agregar Nonce para validación de seguridad (OWASP CSRF protection)
        wp_nonce_field('autoridad_save_meta_box_data', 'autoridad_meta_box_nonce');

        // 2. Obtener los valores actuales (si existen)
        $grado_academico = get_post_meta($post->ID, '_autoridad_grado', true);
        $orcid           = get_post_meta($post->ID, '_autoridad_orcid', true);
        $cti_vitae       = get_post_meta($post->ID, '_autoridad_cti_vitae', true);
        $correo          = get_post_meta($post->ID, '_autoridad_correo', true);

        // 3. Estilos básicos in-line (o en admin-style.css) para el escritorio WP
?>
        <style>
            .autoridad-meta-row {
                margin-bottom: 1em;
            }

            .autoridad-meta-row label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .autoridad-meta-row input[type="text"],
            .autoridad-meta-row input[type="url"],
            .autoridad-meta-row input[type="email"],
            .autoridad-meta-row select {
                width: 100%;
                max-width: 400px;
                padding: 5px;
            }

            .autoridad-desc {
                color: #666;
                font-style: italic;
                display: block;
                margin-top: 4px;
            }
        </style>

        <!-- Campo: Grado Académico -->
        <div class="autoridad-meta-row">
            <label for="autoridad_grado"><?php _e('Grado Académico:', 'viceunf-core'); ?></label>
            <select name="autoridad_grado" id="autoridad_grado">
                <option value=""><?php _e('-- Seleccionar --', 'viceunf-core'); ?></option>
                <option value="Ph.D." <?php selected($grado_academico, 'Ph.D.'); ?>>Ph.D.</option>
                <option value="Dr." <?php selected($grado_academico, 'Dr.'); ?>>Doctor (Dr.)</option>
                <option value="Mg." <?php selected($grado_academico, 'Mg.'); ?>>Magíster (Mg.)</option>
                <option value="Ing." <?php selected($grado_academico, 'Ing.'); ?>>Ingeniero (Ing.)</option>
                <option value="Lic." <?php selected($grado_academico, 'Lic.'); ?>>Licenciado (Lic.)</option>
                <option value="Bach." <?php selected($grado_academico, 'Bach.'); ?>>Bachiller (Bach.)</option>
            </select>
            <span class="autoridad-desc"><?php _e('Seleccione el grado más alto alcanzado.', 'viceunf-core'); ?></span>
        </div>

        <!-- Campo: ORCID -->
        <div class="autoridad-meta-row">
            <label for="autoridad_orcid"><?php _e('Enlace ORCID:', 'viceunf-core'); ?></label>
            <input type="url" id="autoridad_orcid" name="autoridad_orcid" value="<?php echo esc_url($orcid); ?>" placeholder="https://orcid.org/0000-0000-0000-0000" />
            <span class="autoridad-desc"><?php _e('URL completa del perfil público de ORCID.', 'viceunf-core'); ?></span>
        </div>

        <!-- Campo: CTI Vitae -->
        <div class="autoridad-meta-row">
            <label for="autoridad_cti_vitae"><?php _e('Enlace CTI Vitae (Concytec):', 'viceunf-core'); ?></label>
            <input type="url" id="autoridad_cti_vitae" name="autoridad_cti_vitae" value="<?php echo esc_url($cti_vitae); ?>" placeholder="https://ctivitae.concytec.gob.pe/..." />
            <span class="autoridad-desc"><?php _e('Opcional. URL completa de la hoja de vida en CTI Vitae.', 'viceunf-core'); ?></span>
        </div>

        <!-- Campo: Correo Institucional -->
        <div class="autoridad-meta-row">
            <label for="autoridad_correo"><?php _e('Correo Institucional:', 'viceunf-core'); ?></label>
            <input type="email" id="autoridad_correo" name="autoridad_correo" value="<?php echo esc_attr($correo); ?>" placeholder="nombre@unf.edu.pe" />
            <span class="autoridad-desc"><?php _e('Correo electrónico oficial de contacto.', 'viceunf-core'); ?></span>
        </div>
<?php
    }

    /**
     * Sanitiza y guarda los datos de los campos personalizados.
     *
     * @param int $post_id ID del post que se está guardando.
     */
    public function save_meta_box_data(int $post_id): void
    {
        // 1. Verificación Nonce (Previene ataques CSRF)
        if (! isset($_POST['autoridad_meta_box_nonce']) || ! wp_verify_nonce($_POST['autoridad_meta_box_nonce'], 'autoridad_save_meta_box_data')) {
            return;
        }

        // 2. Verificar Autoguardado
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // 3. Verificar Permisos del Usuario (Rol y Capabilities)
        if (isset($_POST['post_type']) && 'page' === $_POST['post_type']) {
            if (! current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (! current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        // 4. Sanitizar y persistir: Grado Académico
        if (isset($_POST['autoridad_grado'])) {
            $grado_saneado = sanitize_text_field($_POST['autoridad_grado']);
            update_post_meta($post_id, '_autoridad_grado', $grado_saneado);
        }

        // 5. Sanitizar y persistir: ORCID
        if (isset($_POST['autoridad_orcid'])) {
            $orcid_saneado = esc_url_raw($_POST['autoridad_orcid']);
            update_post_meta($post_id, '_autoridad_orcid', $orcid_saneado);
        }

        // 6. Sanitizar y persistir: CTI Vitae
        if (isset($_POST['autoridad_cti_vitae'])) {
            $ctivit_saneado = esc_url_raw($_POST['autoridad_cti_vitae']);
            update_post_meta($post_id, '_autoridad_cti_vitae', $ctivit_saneado);
        }

        // 7. Sanitizar y persistir: Correo Institucional
        if (isset($_POST['autoridad_correo'])) {
            $correo_saneado = sanitize_email($_POST['autoridad_correo']);
            update_post_meta($post_id, '_autoridad_correo', $correo_saneado);
        }
    }
}

<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Clase EventoMetaBox
 * 
 * Gestiona el lugar del evento y un repetidor dinámico de horarios
 * desarrollado en Vainilla JS y PHP puro para la entidad "Evento".
 */
class EventoMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $this->post_type      = 'evento';
        $this->meta_box_id    = 'evento_detalles_metabox';
        $this->meta_box_title = __('Detalles y Horarios del Evento', 'viceunf-core');
        parent::__construct();
    }

    public function enqueue_admin_scripts(string $hook): void
    {
        global $post;
        if (($hook === 'post-new.php' || $hook === 'post.php') && $post && $post->post_type === $this->post_type) {
            wp_enqueue_style('evento-metabox-css', VICEUNF_CORE_URL . 'assets/admin/evento-metabox.css', [], VICEUNF_CORE_VERSION);
            wp_enqueue_script('evento-metabox-js', VICEUNF_CORE_URL . 'assets/admin/evento-metabox.js', [], VICEUNF_CORE_VERSION, true);
        }
    }

    protected function render_fields(\WP_Post $post): void
    {

        // Compatibilidad: intentamos leer de la llave nueva, si no está, leemos de la antigua generada por el tema ('_evento_address_key')
        $lugar = get_post_meta($post->ID, '_evento_lugar', true);
        if (empty($lugar)) {
            $lugar = get_post_meta($post->ID, '_evento_address_key', true);
        }

        $mapa_url = get_post_meta($post->ID, '_evento_mapa_url', true);
        $horarios = get_post_meta($post->ID, '_evento_horarios', true);

        // --- MIGRACION DE DATOS ANTIGUOS ---
        // Si no existen horarios repetibles usando nuestro sistema, pero hay una fecha/hora antigua guardada por el tema, 
        // la extraemos y la convertimos 'on-the-fly' a nuestro formato de repetidor
        if (empty($horarios) || !is_array($horarios)) {
            $horarios = [];
            $fecha_antigua   = get_post_meta($post->ID, '_evento_date_key', true);
            $hora_inicio_ant = get_post_meta($post->ID, '_evento_start_time_key', true);
            $hora_fin_ant    = get_post_meta($post->ID, '_evento_end_time_key', true);

            // Si encontró al menos la fecha antigua, simula nuestro bloque
            if (!empty($fecha_antigua)) {
                $horarios[] = [
                    'etiqueta' => 'Horario Migrado',
                    'fecha'    => $fecha_antigua,
                    'inicio'   => $hora_inicio_ant ?: '08:00',
                    'fin'      => $hora_fin_ant ?: '12:00'
                ];
            }
        }
?>
        <div class="viceunf-metabox-wrapper">
            <!-- Ubicación General -->
            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle"><?php _e('Ubicación General', 'viceunf-core'); ?></h4>
                <div style="display:flex; gap:20px; flex-wrap: wrap;">
                    <div class="viceunf-metabox-field" style="flex:1;">
                        <label for="evento_lugar" class="viceunf-metabox-label"><?php _e('Lugar/Sede Principal:', 'viceunf-core'); ?></label>
                        <input type="text" id="evento_lugar" name="evento_lugar" value="<?php echo esc_attr($lugar); ?>" placeholder="Ej: Auditorio Central UNF" class="viceunf-metabox-input dt-w-100" />
                    </div>
                    <div class="viceunf-metabox-field" style="flex:1;">
                        <label for="evento_mapa_url" class="viceunf-metabox-label"><?php _e('URL Ubicación en Google Maps:', 'viceunf-core'); ?></label>
                        <input type="url" id="evento_mapa_url" name="evento_mapa_url" value="<?php echo esc_url($mapa_url); ?>" placeholder="https://maps.google.com/..." class="viceunf-metabox-input dt-w-100" />
                    </div>
                </div>
            </div>

            <!-- Generador de Horarios -->
            <div class="viceunf-metabox-section">
                <h4 class="viceunf-metabox-subtitle"><?php _e('Jornadas y Horarios del Evento', 'viceunf-core'); ?></h4>
                <p class="description viceunf-metabox-desc"><?php _e('Puede agregar múltiples fechas y horas para este evento (ej. ponencias de la mañana, ponencias de la tarde).', 'viceunf-core'); ?></p>

                <div id="evento_horarios_container" class="viceunf-metabox-repeater">
                    <?php if (!empty($horarios)) : ?>
                        <?php foreach ($horarios as $index => $horario) : ?>
                            <div class="horario-row viceunf-metabox-repeater-row">
                                <div class="horario-cols" style="display:flex; gap:15px; align-items:flex-end;">
                                    <div class="col" style="flex:1.5;">
                                        <label class="viceunf-metabox-label">Etiqueta / Turno:</label>
                                        <input type="text" name="evento_horarios[<?php echo $index; ?>][etiqueta]" value="<?php echo esc_attr($horario['etiqueta'] ?? ''); ?>" class="viceunf-metabox-input dt-w-100" placeholder="Ej: Turno Mañana" />
                                    </div>
                                    <div class="col" style="flex:1;">
                                        <label class="viceunf-metabox-label">Fecha:</label>
                                        <input type="date" name="evento_horarios[<?php echo $index; ?>][fecha]" value="<?php echo esc_attr($horario['fecha'] ?? ''); ?>" required class="viceunf-metabox-input dt-w-100" />
                                    </div>
                                    <div class="col" style="flex:1;">
                                        <label class="viceunf-metabox-label">Hora Inicio:</label>
                                        <input type="time" name="evento_horarios[<?php echo $index; ?>][inicio]" value="<?php echo esc_attr($horario['inicio'] ?? ''); ?>" required class="viceunf-metabox-input dt-w-100" />
                                    </div>
                                    <div class="col" style="flex:1;">
                                        <label class="viceunf-metabox-label">Hora Fin:</label>
                                        <input type="time" name="evento_horarios[<?php echo $index; ?>][fin]" value="<?php echo esc_attr($horario['fin'] ?? ''); ?>" required class="viceunf-metabox-input dt-w-100" />
                                    </div>
                                    <div class="col col-btn">
                                        <button type="button" class="button remove-horario" style="color:#b32d2e; border-color:#b32d2e;">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 15px;">
                    <button type="button" id="add_horario_btn" class="button button-primary">
                        <?php _e('+ Añadir Nuevo Horario', 'viceunf-core'); ?>
                    </button>
                </div>
            </div>
        </div>
<?php
    }

    protected function save_fields(int $post_id, array $post_data): void
    {
        // 1. Guardar Lugar y Mapa
        if (isset($post_data['evento_lugar'])) {
            update_post_meta($post_id, '_evento_lugar', sanitize_text_field($post_data['evento_lugar']));
        }
        if (isset($post_data['evento_mapa_url'])) {
            update_post_meta($post_id, '_evento_mapa_url', esc_url_raw($post_data['evento_mapa_url']));
        }

        // 2. Guardar Horarios Iterables
        if (isset($post_data['evento_horarios']) && is_array($post_data['evento_horarios'])) {
            $horarios_saneados = [];
            foreach ($post_data['evento_horarios'] as $horario) {
                // Solo guardamos si la fecha y la hora son válidas/existen
                if (!empty($horario['fecha']) && !empty($horario['inicio'])) {
                    $horarios_saneados[] = [
                        'etiqueta' => sanitize_text_field($horario['etiqueta'] ?? ''),
                        'fecha'    => sanitize_text_field($horario['fecha']),
                        'inicio'   => sanitize_text_field($horario['inicio']),
                        'fin'      => sanitize_text_field($horario['fin'] ?? ''),
                    ];
                }
            }
            update_post_meta($post_id, '_evento_horarios', $horarios_saneados);
        } else {
            // Si eliminó todos los bloques, limpiar el meta de la base de datos
            delete_post_meta($post_id, '_evento_horarios');
        }
    }
}

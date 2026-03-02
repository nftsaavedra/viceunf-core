<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Clase EventoMetaBox
 * 
 * Gestiona el lugar del evento y un repetidor dinámico de horarios
 * desarrollado en Vainilla JS y PHP puro para la entidad "Evento".
 */
class EventoMetaBox
{
    private string $post_type = 'evento';

    public function register_hooks(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box_data']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts(string $hook): void
    {
        global $post;
        if (($hook === 'post-new.php' || $hook === 'post.php') && $post && $post->post_type === $this->post_type) {
            // Script nativo para manejar el repetidor de horarios
            $script = "
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('evento_horarios_container');
                    const addBtn = document.getElementById('add_horario_btn');
                    if(!container || !addBtn) return;
                    
                    let counter = container.querySelectorAll('.horario-row').length;

                    addBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const row = document.createElement('div');
                        row.className = 'horario-row';
                        row.innerHTML = `
                            <div class=\"horario-cols\">
                                <div class=\"col\">
                                    <label>Etiqueta / Turno:</label>
                                    <input type=\"text\" name=\"evento_horarios[\${counter}][etiqueta]\" placeholder=\"Ej: Turno Mañana\" />
                                </div>
                                <div class=\"col\">
                                    <label>Fecha:</label>
                                    <input type=\"date\" name=\"evento_horarios[\${counter}][fecha]\" required />
                                </div>
                                <div class=\"col\">
                                    <label>Hora Inicio:</label>
                                    <input type=\"time\" name=\"evento_horarios[\${counter}][inicio]\" required />
                                </div>
                                <div class=\"col\">
                                    <label>Hora Fin:</label>
                                    <input type=\"time\" name=\"evento_horarios[\${counter}][fin]\" required />
                                </div>
                                <div class=\"col col-btn\">
                                    <button type=\"button\" class=\"button remove-horario\">Eliminar</button>
                                </div>
                            </div>
                        `;
                        container.appendChild(row);
                        counter++;
                    });

                    container.addEventListener('click', function(e) {
                        if (e.target && e.target.classList.contains('remove-horario')) {
                            e.preventDefault();
                            e.target.closest('.horario-row').remove();
                        }
                    });
                });
            ";
            wp_add_inline_script('jquery', $script);
        }
    }

    public function add_meta_box(string $post_type): void
    {
        if ($post_type !== $this->post_type) {
            return;
        }

        add_meta_box(
            'evento_detalles_metabox',
            __('Detalles y Horarios del Evento', 'viceunf-core'),
            [$this, 'render_meta_box_content'],
            $this->post_type,
            'normal',
            'high'
        );
    }

    public function render_meta_box_content(\WP_Post $post): void
    {
        wp_nonce_field('evento_save_meta_box_data', 'evento_meta_box_nonce');

        // Compatibilidad: intentamos leer de la llave nueva, si no está, leemos de una genérica antigua (acf / temas antiguos suelen usar 'fecha_evento' o 'lugar_evento')
        $lugar = get_post_meta($post->ID, '_evento_lugar', true);
        if (empty($lugar)) {
            $lugar = get_post_meta($post->ID, 'lugar_evento', true) ?: get_post_meta($post->ID, 'evento_lugar', true);
        }

        $mapa_url = get_post_meta($post->ID, '_evento_mapa_url', true);
        $horarios = get_post_meta($post->ID, '_evento_horarios', true);

        // --- MIGRACION DE DATOS ANTIGUOS ---
        // Si no existen horarios repetibles usando nuestro sistema, pero hay una fecha/hora antigua guardada en el post, 
        // la extraemos y la convertimos 'on-the-fly' a nuestro formato de repetidor para que el usuario no pierda el dato antiguo
        if (empty($horarios) || !is_array($horarios)) {
            $horarios = [];
            $fecha_antigua = get_post_meta($post->ID, 'fecha_evento', true) ?: get_post_meta($post->ID, 'evento_fecha', true);
            $hora_inicio_ant   = get_post_meta($post->ID, 'hora_inicio', true);
            $hora_fin_ant      = get_post_meta($post->ID, 'hora_fin', true);

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
        <style>
            .evento-meta-row {
                margin-bottom: 20px;
            }

            .evento-meta-row label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .evento-meta-row input[type="text"] {
                width: 100%;
                max-width: 500px;
                padding: 5px;
            }

            .horario-row {
                background: #f9f9f9;
                border: 1px solid #ccd0d4;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 4px;
            }

            .horario-cols {
                display: flex;
                gap: 15px;
                align-items: flex-end;
                flex-wrap: wrap;
            }

            .horario-cols .col {
                flex: 1;
                min-width: 120px;
            }

            .horario-cols .col label {
                font-size: 12px;
                margin-bottom: 4px;
            }

            .horario-cols .col input {
                width: 100%;
                padding: 4px;
            }

            .horario-cols .col-btn {
                flex: 0 0 auto;
            }

            .remove-horario {
                color: #b32d2e;
                border-color: #b32d2e;
            }

            .remove-horario:hover {
                background: #b32d2e;
                color: #fff;
            }
        </style>

        <!-- Ubicación General -->
        <div class="evento-meta-row" style="display:flex; gap:20px;">
            <div style="flex:1;">
                <label for="evento_lugar"><?php _e('Lugar/Sede Principal:', 'viceunf-core'); ?></label>
                <input type="text" id="evento_lugar" name="evento_lugar" value="<?php echo esc_attr($lugar); ?>" placeholder="Ej: Auditorio Central UNF" />
            </div>
            <div style="flex:1;">
                <label for="evento_mapa_url"><?php _e('URL Ubicación en Google Maps:', 'viceunf-core'); ?></label>
                <input type="url" id="evento_mapa_url" name="evento_mapa_url" value="<?php echo esc_url($mapa_url); ?>" placeholder="https://maps.google.com/..." />
            </div>
        </div>

        <hr style="margin: 20px 0;">

        <!-- Generador de Horarios -->
        <div class="evento-meta-row">
            <label><?php _e('Jornadas y Horarios del Evento:', 'viceunf-core'); ?></label>
            <p class="description"><?php _e('Puede agregar múltiples fechas y horas para este evento (ej. ponencias de la mañana, ponencias de la tarde).', 'viceunf-core'); ?></p>

            <div id="evento_horarios_container">
                <?php if (!empty($horarios)) : ?>
                    <?php foreach ($horarios as $index => $horario) : ?>
                        <div class="horario-row">
                            <div class="horario-cols">
                                <div class="col">
                                    <label>Etiqueta / Turno:</label>
                                    <input type="text" name="evento_horarios[<?php echo $index; ?>][etiqueta]" value="<?php echo esc_attr($horario['etiqueta'] ?? ''); ?>" />
                                </div>
                                <div class="col">
                                    <label>Fecha:</label>
                                    <input type="date" name="evento_horarios[<?php echo $index; ?>][fecha]" value="<?php echo esc_attr($horario['fecha'] ?? ''); ?>" required />
                                </div>
                                <div class="col">
                                    <label>Hora Inicio:</label>
                                    <input type="time" name="evento_horarios[<?php echo $index; ?>][inicio]" value="<?php echo esc_attr($horario['inicio'] ?? ''); ?>" required />
                                </div>
                                <div class="col">
                                    <label>Hora Fin:</label>
                                    <input type="time" name="evento_horarios[<?php echo $index; ?>][fin]" value="<?php echo esc_attr($horario['fin'] ?? ''); ?>" required />
                                </div>
                                <div class="col col-btn">
                                    <button type="button" class="button remove-horario">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" id="add_horario_btn" class="button button-primary" style="margin-top: 10px;">
                <?php _e('+ Añadir Nuevo Horario', 'viceunf-core'); ?>
            </button>
        </div>
<?php
    }

    public function save_meta_box_data(int $post_id): void
    {
        if (!isset($_POST['evento_meta_box_nonce']) || !wp_verify_nonce($_POST['evento_meta_box_nonce'], 'evento_save_meta_box_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // 1. Guardar Lugar y Mapa
        if (isset($_POST['evento_lugar'])) {
            update_post_meta($post_id, '_evento_lugar', sanitize_text_field($_POST['evento_lugar']));
        }
        if (isset($_POST['evento_mapa_url'])) {
            update_post_meta($post_id, '_evento_mapa_url', esc_url_raw($_POST['evento_mapa_url']));
        }

        // 2. Guardar Horarios Iterables
        if (isset($_POST['evento_horarios']) && is_array($_POST['evento_horarios'])) {
            $horarios_saneados = [];
            foreach ($_POST['evento_horarios'] as $horario) {
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

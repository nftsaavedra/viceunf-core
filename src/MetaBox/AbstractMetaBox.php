<?php

declare(strict_types=1);

namespace ViceUnf\Core\MetaBox;

/**
 * Clase base abstracta para MetaBoxes, provee lógica reutilizable para 
 * registro, seguridad y validación de guardado (Resolviendo Violaciones DRY).
 */
abstract class AbstractMetaBox
{
    /**
     * @var string El nombre del Custom Post Type al que aplica este metabox.
     */
    protected string $post_type;

    /**
     * @var string Identificador único del metabox.
     */
    protected string $meta_box_id;

    /**
     * @var string Título visible del metabox.
     */
    protected string $meta_box_title;

    /**
     * @var string Contexto donde se mostrará ('normal', 'side', 'advanced').
     */
    protected string $context = 'normal';

    /**
     * @var string Prioridad ('high', 'core', 'default', 'low').
     */
    protected string $priority = 'high';

    /**
     * @var string Acción del nonce para validación.
     */
    protected string $nonce_action;

    /**
     * @var string Nombre del campo nonce correspondiente.
     */
    protected string $nonce_name;

    public function __construct()
    {
        // Define dinámicamente las firmas de seguridad basadas en el post type hijo
        $this->nonce_action = "{$this->post_type}_save_meta_box_data";
        $this->nonce_name   = "{$this->post_type}_meta_box_nonce";
    }

    /**
     * Registra los hooks nativos de WordPress de forma centralizada.
     */
    public function register_hooks(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box_data']);

        // Si el hijo necesita encolar assets, enlazamos el hook
        if (method_exists($this, 'enqueue_admin_scripts')) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        }
    }

    public function add_meta_box(string $post_type): void
    {
        if ($post_type !== $this->post_type) {
            return;
        }

        add_meta_box(
            $this->meta_box_id,
            $this->meta_box_title,
            [$this, 'render_wrapper'],
            $this->post_type,
            $this->context,
            $this->priority
        );
    }

    /**
     * Wrapper principal que inyecta la seguridad (Nonce) y luego llama a la vista hija.
     */
    public function render_wrapper(\WP_Post $post): void
    {
        wp_nonce_field($this->nonce_action, $this->nonce_name);
        $this->render_fields($post);
    }

    /**
     * Lógica de guardado principal con validaciones de seguridad estandarizadas (DRY).
     */
    public function save_meta_box_data(int $post_id): void
    {
        // 1. Verificación contra Ataques CSRF
        $nonce = isset($_POST[$this->nonce_name])
            ? sanitize_text_field(wp_unslash($_POST[$this->nonce_name]))
            : '';

        if (! wp_verify_nonce($nonce, $this->nonce_action)) {
            return;
        }

        // 2. Prevenir iteración en Autosaves
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // 3. Verificación de Permisos (Por defecto usa el map_meta_cap nativo)
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        // Una vez saneadas las variables de entorno básico, delegamos la persistencia al hijo
        $this->save_fields($post_id, wp_unslash($_POST));
    }

    /**
     * Renderiza los campos HTML del metabox. Implementado por la clase hija.
     */
    abstract protected function render_fields(\WP_Post $post): void;

    /**
     * Persiste los datos del dominio asegurando sanitización. Implementado por la clase hija.
     */
    abstract protected function save_fields(int $post_id, array $post_data): void;
}

# VpinUnf Core Plugin

Plugin de dominio para el sitio web de la **Vice-Rectoría de Investigación de la UNF**.

Registra la capa de datos del proyecto: Custom Post Types, Taxonomías, Endpoints REST API y Ajax.

## 📁 Estructura

```
vpinunf-core/
├── src/
│   ├── Api/            # Endpoints REST y Ajax handlers
│   ├── MetaBox/        # MetaBoxes para cada CPT
│   ├── PostType/       # Definición e interfaz de los CPTs
│   └── Service/        # Servicios de consulta y caché
├── assets/admin/       # CSS y JS del panel de administración
└── vpinunf-core.php    # Bootstrap y autoloader del plugin
```

## 🧩 Custom Post Types registrados

- `slider` — Diapositivas del hero principal
- `evento` — Eventos institucionales con horarios múltiples
- `socio` — Socios estratégicos
- `reglamento` — Documentos reglamentarios (PDF / enlace externo)
- `autoridad` — Autoridades universitarias con datos académicos
- `dependencia` — Dependencias y oficinas institucionales

## 🔌 Requisitos

- WordPress 6.0+
- PHP 8.1+
- Tema activo: `vpinunf`

---

**Plugin de dominio puro — sin dependencias externas de Node.js ni Composer.**

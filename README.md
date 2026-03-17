# ViceUnf Plugin Build System

## 📦 Build Local para Plugin

Este sistema de build está **DENTRO** de la carpeta del plugin, no afecta a la instalación completa.

## 🚀 Uso

### Instalación de dependencias:
```bash
cd wp-content/plugins/viceunf-core
npm install
```

### Build para producción:
```bash
npm run build
```

### Comandos disponibles:
```bash
npm run clean          # Limpiar build/
npm run optimize        # Optimizar assets
npm run optimize:css    # Optimizar CSS
npm run optimize:js     # Optimizar JS
npm run package         # Crear ZIP
npm run test:production # Tests de producción
```

## 📁 Estructura Generada

```
viceunf-core/
├── build/                    # Archivos de producción
│   ├── assets/admin/        # Assets optimizados
│   ├── src/                 # Código fuente
│   ├── viceunf-core.php    # Plugin principal
│   ├── production-config.json # Config producción
│   └── (demás archivos)
├── viceunf-plugin-v1.1.0-prod.zip  # Paquete para deploy
└── package.json             # Configuración del build
```

## ✅ Optimizaciones Aplicadas

- **CSS**: Minificación + Autoprefixer
- **JS**: Compresión + Mangling
- **PHP**: Limpieza de comentarios
- **Config**: Producción habilitada

## 🎯 Deployment

1. Ejecutar `npm run build`
2. Subir `viceunf-plugin-v1.1.0-prod.zip` vía WordPress Admin
3. Activar plugin
4. Configurar en Settings > Image Optimizer

---

**Build system local y aislado - NO afecta otros componentes.**

# Código Nativo Connect

Plugin de WordPress para conectar tu sitio con el sistema de gestión de Código Nativo.

## Descripción

Este plugin permite establecer una conexión segura entre tu sitio WordPress y el sistema de gestión de proyectos de Código Nativo, facilitando:

- Monitoreo de plugins instalados
- Información del estado del sitio
- Conexión API segura mediante tokens
- Sincronización de datos

## Instalación

### Instalación Manual

1. Descarga el plugin y descomprime el archivo
2. Sube la carpeta `plugin-codigo-nativo` al directorio `/wp-content/plugins/`
3. Activa el plugin desde el menú 'Plugins' en WordPress
4. Ve a **Código Nativo** en el menú de administración para configurar

### Instalación desde ZIP

1. Ve a **Plugins > Añadir nuevo** en tu WordPress
2. Haz clic en **Subir plugin**
3. Selecciona el archivo ZIP del plugin
4. Haz clic en **Instalar ahora**
5. Activa el plugin

## Configuración

1. Una vez activado, ve al menú **Código Nativo** en el panel de administración
2. Copia el token API que aparece en pantalla
3. Ve a tu dashboard de Código Nativo
4. Al crear un nuevo proyecto WordPress, pega el token en el campo correspondiente
5. Haz clic en **Validar** para establecer la conexión

## Características

### Gestión de Token

- **Token Seguro**: Se genera automáticamente un token de 64 caracteres
- **Regeneración**: Puedes regenerar el token en cualquier momento
- **Copia Rápida**: Botón para copiar el token al portapapeles

### API REST

El plugin expone los siguientes endpoints:

#### POST `/wp-json/codigo-nativo/v1/validate`
Valida el token de conexión.

**Parámetros:**
```json
{
  "token": "tu-token-aqui"
}
```

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Token válido",
  "site_url": "https://tu-sitio.com",
  "site_name": "Nombre del Sitio"
}
```

#### GET `/wp-json/codigo-nativo/v1/plugins`
Obtiene la lista de plugins instalados.

**Headers requeridos:**
```
X-CN-Token: tu-token-aqui
```

**Respuesta:**
```json
{
  "success": true,
  "plugins": [
    {
      "name": "Nombre del Plugin",
      "version": "1.0.0",
      "author": "Autor",
      "active": true,
      "file": "plugin-folder/plugin-file.php"
    }
  ]
}
```

#### GET `/wp-json/codigo-nativo/v1/site-info`
Obtiene información general del sitio.

**Headers requeridos:**
```
X-CN-Token: tu-token-aqui
```

**Respuesta:**
```json
{
  "success": true,
  "site": {
    "name": "Nombre del Sitio",
    "url": "https://tu-sitio.com",
    "description": "Descripción del sitio",
    "wp_version": "6.4.2",
    "theme": {
      "name": "Twenty Twenty-Four",
      "version": "1.0",
      "author": "WordPress.org"
    },
    "plugins_count": 15,
    "language": "es_ES"
  }
}
```

## Seguridad

- ✅ Tokens generados con `random_bytes()` de alta seguridad
- ✅ Comparación de tokens usando `hash_equals()` para prevenir timing attacks
- ✅ Verificación de nonces en peticiones AJAX
- ✅ Verificación de capacidades de usuario
- ✅ Sin uso de `eval()` o funciones inseguras (compatible con CSP)
- ✅ Escape y sanitización de datos

## Requisitos

- WordPress 5.6 o superior
- PHP 7.4 o superior

## Preguntas Frecuentes

### ¿Es seguro este plugin?

Sí, el plugin utiliza las mejores prácticas de seguridad de WordPress:
- Tokens criptográficamente seguros
- Verificación de permisos en todos los endpoints
- Protección contra timing attacks
- Sin código evaluado dinámicamente

### ¿Puedo usar el mismo token en múltiples sitios?

No, cada sitio WordPress debe tener su propio token único. Esto garantiza la seguridad y permite identificar correctamente cada sitio.

### ¿Qué pasa si regenero el token?

Al regenerar el token, el anterior deja de funcionar. Deberás actualizar el token en tu dashboard de Código Nativo para restablecer la conexión.

### ¿Funciona con sitios en mantenimiento?

Sí, el plugin funciona incluso si tu sitio está en modo mantenimiento, siempre que la API REST de WordPress esté accesible.

## Soporte

Si tienes problemas o preguntas:

- 📧 Email: soporte@codigonativo.com
- 🌐 Web: https://codigonativo.com
- 📖 Documentación: https://docs.codigonativo.com

## Changelog

### 1.0.0
- Versión inicial
- Gestión de tokens de API
- Endpoints REST para plugins y información del sitio
- Panel de administración
- Compatible con Content Security Policy (CSP)

## Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## Créditos

Desarrollado por [Código Nativo](https://codigonativo.com)

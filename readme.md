# Código Nativo HUB Plugin

Este plugin conecta tu sitio WordPress con el panel dashboard de Código Nativo, permitiendo monitoreo y comunicación segura.

## Instalación
1. Sube los archivos del plugin a `/wp-content/plugins/codigo-nativo/`.
2. Activa el plugin desde el panel de administración de WordPress.
3. Ve a **Código Nativo** en el menú admin para configurar el token API.

## Configuración
- **Token API**: Genera un token seguro en el panel de admin. Incluye expiración para mayor seguridad.
- **HTTPS Obligatorio**: Asegúrate de que tu sitio use HTTPS para conexiones seguras.

## Endpoints API
- `GET /wp-json/codigonativo/v1/status`: Obtiene el estado del sitio (requiere autenticación).
- `POST /wp-json/codigonativo/v1/logs`: Envía logs al dashboard (requiere autenticación).

### Ejemplo de Uso
```bash
# Obtener estado
curl -H "Authorization: Bearer TU_TOKEN" https://tusitio.com/wp-json/codigonativo/v1/status

# Enviar logs
curl -X POST -H "Authorization: Bearer TU_TOKEN" -H "Content-Type: application/json" \
  -d '{"logs": ["Mensaje de log"]}' https://tusitio.com/wp-json/codigonativo/v1/logs
```

## Seguridad
- Autenticación con tokens hasheados y expiración.
- Rate limiting para prevenir abusos.
- Logging de intentos fallidos.
- Solo conexiones HTTPS permitidas.

## Desarrollo
- Funciones prefijadas con `cn_`.
- Usa `WP_Error` para manejo de errores.
- Compatible con WordPress 5.0+.

Para más información, contacta a Código Nativo.
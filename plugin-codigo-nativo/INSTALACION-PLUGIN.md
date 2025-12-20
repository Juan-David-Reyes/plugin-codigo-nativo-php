# 🚀 Instrucciones de Instalación - Plugin Código Nativo

## Instalación Rápida

### Opción 1: Instalación Manual (Desarrollo)

1. Copia la carpeta `plugin-codigo-nativo` completa a:
   ```
   /wp-content/plugins/plugin-codigo-nativo/
   ```

2. Ve a WordPress Admin → Plugins → Plugins instalados

3. Busca "Código Nativo Connect" y haz clic en **Activar**

### Opción 2: Instalación desde ZIP (Recomendado)

1. Genera el archivo ZIP ejecutando:
   ```bash
   bash build-plugin.sh
   ```

2. Ve a WordPress Admin → Plugins → Añadir nuevo

3. Haz clic en **Subir plugin**

4. Selecciona el archivo `dist/codigo-nativo-connect-1.0.0.zip`

5. Haz clic en **Instalar ahora**

6. Una vez instalado, haz clic en **Activar plugin**

## Configuración

1. Ve a **Código Nativo** en el menú lateral de WordPress

2. Copia el **Token API** que aparece

3. Ve a tu Dashboard de Código Nativo

4. Crea un nuevo proyecto:
   - Selecciona CMS: **WordPress**
   - Pega la URL de tu WordPress
   - Pega el token copiado
   - Haz clic en **Validar**

5. Si la conexión es exitosa, verás el estado **Conectado** en verde

## Solución de Problemas

### Error: "No se pudo conectar con WordPress"

✅ Verifica que:
- La URL de WordPress sea correcta (incluye https://)
- El sitio WordPress esté accesible desde internet
- El plugin esté activo en WordPress
- No hay un firewall bloqueando las peticiones

### Error: "Token inválido"

✅ Verifica que:
- Hayas copiado el token completo sin espacios
- No hayas regenerado el token sin actualizar en el dashboard
- El plugin esté activo

### Error CSP (Content Security Policy)

✅ Este plugin NO usa `eval()` ni funciones inseguras
✅ Es 100% compatible con políticas CSP estrictas
✅ Si tienes problemas de CSP, son de otro código, no del plugin

### Error de CORS

Si WordPress está en un dominio diferente:

1. Añade esto a tu `.htaccess` de WordPress:
```apache
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-CN-Token"
</IfModule>
```

O añade esto a `wp-config.php`:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CN-Token');
```

## Verificación Manual

Puedes probar la API directamente con cURL:

```bash
# Validar token
curl -X POST https://tu-wordpress.com/wp-json/codigo-nativo/v1/validate \
  -H "Content-Type: application/json" \
  -d '{"token":"TU_TOKEN_AQUI"}'

# Obtener plugins
curl https://tu-wordpress.com/wp-json/codigo-nativo/v1/plugins?token=TU_TOKEN_AQUI

# Obtener info del sitio
curl https://tu-wordpress.com/wp-json/codigo-nativo/v1/site-info?token=TU_TOKEN_AQUI
```

## Características

- ✅ Sin uso de eval() (compatible con CSP)
- ✅ Tokens criptográficamente seguros
- ✅ API REST moderna de WordPress
- ✅ Compatible con WordPress 5.6+
- ✅ Compatible con PHP 7.4+
- ✅ Sin dependencias externas
- ✅ Interfaz de administración intuitiva

## Soporte

📧 soporte@codigonativo.com
🌐 https://codigonativo.com

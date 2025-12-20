# 🔧 Configuración del Servidor para el Plugin

## Problema: 403 Forbidden en la API REST

Si recibes un error 403 al intentar validar la conexión, es porque el servidor está bloqueando las peticiones POST a la API REST de WordPress.

## Soluciones

### Opción 1: Agregar reglas al .htaccess de WordPress

Agrega esto al archivo `.htaccess` en la **raíz de tu WordPress**:

```apache
# Permitir peticiones a la API REST de WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^wp-json/codigo-nativo/v1/(.*)$ index.php?rest_route=/codigo-nativo/v1/$1 [L]
</IfModule>

# Habilitar CORS para la API
<IfModule mod_headers.c>
    <FilesMatch "\.(php)$">
        Header set Access-Control-Allow-Origin "*"
        Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
        Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-CN-Token"
        Header set Access-Control-Allow-Credentials "true"
    </FilesMatch>
</IfModule>

# Permitir OPTIONS para preflight CORS
<IfModule mod_rewrite.c>
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ $1 [R=200,L]
</IfModule>
```

### Opción 2: Configurar en wp-config.php

Agrega esto al inicio de `wp-config.php` (después de la línea `<?php`):

```php
// Habilitar CORS para la API REST
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CN-Token');
header('Access-Control-Allow-Credentials: true');

// Manejar OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    status_header(200);
    exit;
}
```

### Opción 3: Desactivar ModSecurity (si está activo)

Si tu hosting tiene ModSecurity, puede estar bloqueando las peticiones. Contacta a tu proveedor de hosting o agrega esto al `.htaccess`:

```apache
<IfModule mod_security.c>
    SecFilterEngine Off
    SecFilterScanPOST Off
</IfModule>
```

### Opción 4: Cloudflare (si lo usas)

Si usas Cloudflare:

1. Ve a **Firewall** → **Tools**
2. En **Rate Limiting**, asegúrate de no estar limitando `/wp-json/*`
3. En **WAF**, revisa las reglas bloqueadas
4. Considera agregar una regla que permita tu dominio del dashboard

### Opción 5: Whitelist en el Firewall del Hosting

Muchos hostings (como SiteGround, Hostinger, etc.) tienen firewalls propios:

1. Accede al panel de control de tu hosting
2. Busca la sección de **Seguridad** o **Firewall**
3. Agrega una excepción para `/wp-json/codigo-nativo/v1/*`
4. O agrega la IP del servidor de tu dashboard a la whitelist

## Verificar que el Plugin está Instalado

Antes de todo, verifica que el plugin esté activo:

```bash
# Accede por SSH a tu servidor
cd /ruta/a/wordpress/wp-content/plugins/
ls -la | grep codigo-nativo
```

Deberías ver la carpeta `plugin-codigo-nativo/`

También puedes probar directamente con cURL:

```bash
curl -X POST https://codigonativo.com/wp-json/codigo-nativo/v1/validate \
  -H "Content-Type: application/json" \
  -d '{"token":"tu-token-aqui"}' \
  -v
```

Si funciona con cURL desde el servidor pero no desde el navegador, es un problema de CORS/Firewall.

## Contactar al Hosting

Si nada funciona, contacta a tu proveedor de hosting con este mensaje:

```
Hola,

Necesito habilitar peticiones POST a la API REST de WordPress en mi sitio.
Específicamente a esta ruta: /wp-json/codigo-nativo/v1/validate

Actualmente recibo un error 403 Forbidden.

¿Pueden ayudarme a:
1. Desactivar ModSecurity para esta ruta
2. Permitir peticiones CORS desde cualquier origen
3. Habilitar el método OPTIONS (preflight)

Gracias.
```

## Verificación Final

Una vez aplicada alguna solución, prueba nuevamente:

1. Ve a http://tu-dashboard.test/test-api.html
2. Genera un token
3. Pega el token en WordPress
4. Haz clic en "Validar Conexión"
5. Debería funcionar ✅

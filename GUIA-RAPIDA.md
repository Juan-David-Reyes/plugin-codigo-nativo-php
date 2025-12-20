# ⚡ Guía Rápida - Conectar WordPress con tu Dashboard

## 🎯 Pasos Rápidos (5 minutos)

### 1️⃣ Instalar Plugin en WordPress

**Opción A: Subir ZIP**
1. Descarga: `codigo-nativo-connect.zip` (está en la raíz del proyecto)
2. Ve a tu WordPress: **Plugins > Añadir nuevo > Subir plugin**
3. Selecciona el ZIP
4. **Instalar** → **Activar**

**Opción B: Copiar carpeta**
```bash
cp -r plugin-codigo-nativo /ruta/wordpress/wp-content/plugins/
```
Luego activa desde WordPress Admin.

### 2️⃣ Generar Token en el Dashboard

1. Abre tu dashboard: `/new-project`
2. Llena los datos básicos del cliente
3. En **"CMS"**, selecciona **"WordPress"**
4. Ingresa la **URL de tu WordPress** (ej: `https://mi-sitio.com`)
5. Haz clic en **"Generar Token"**
6. Se generará un **token de 32 caracteres**
7. **Copia el token** generado

### 3️⃣ Configurar Token en WordPress

1. En WordPress, ve al menú **"Código Nativo"** (lateral izquierdo)
2. **Pega el token** copiado del dashboard en el campo "Token del Dashboard"
3. Haz clic en **"Guardar y Validar"**
4. Deberías ver: **"✓ Conectado"** (verde)

### 4️⃣ Validar Conexión en el Dashboard

1. De vuelta en tu dashboard
2. Haz clic en **"Validar"**

✅ **Éxito:** El estado cambiará a **"Conectado"** (verde)
✅ **Éxito:** Se cargarán los plugins de WordPress
✅ **Éxito:** El botón "Crear proyecto" se activará

### 5️⃣ Crear el Proyecto

1. Completa el resto del formulario (servicios, fechas, etc.)
2. Haz clic en **"Crear Proyecto"**
3. ¡Listo! WordPress conectado exitosamente

---

## 🔧 Problemas Comunes

### ❌ "No se pudo conectar con WordPress"

**Verifica:**
- [ ] URL correcta (con `https://` o `http://`)
- [ ] Plugin activo en WordPress
- [ ] WordPress accesible desde internet
- [ ] No hay firewall bloqueando

**Prueba manual:**
```bash
curl https://tu-wordpress.com/wp-json/
```
Debe devolver JSON. Si no, hay problema de configuración en WordPress.

### ❌ "Token inválido"

**Verifica:**
- [ ] Token copiado completo (sin espacios)
- [ ] Plugin activo
- [ ] No regeneraste el token sin actualizar

**Solución:** Copia el token de nuevo desde WordPress

### ❌ Error CSP en Dashboard

**Solución:** Ya está resuelto en el código actual.

Si sigues viendo el error:
1. Limpia caché del navegador: `Ctrl + Shift + R`
2. Cierra todas las pestañas del dashboard
3. Abre de nuevo

---

## 🧪 Prueba la API Manualmente

Si la validación falla, prueba con cURL:

```bash
# Reemplaza TU_WORDPRESS y TU_TOKEN con tus datos reales
curl -X POST https://TU_WORDPRESS.com/wp-json/codigo-nativo/v1/validate \
  -H "Content-Type: application/json" \
  -d '{"token":"TU_TOKEN_AQUI"}'
```

**Respuesta esperada (éxito):**
```json
{
  "success": true,
  "message": "Token válido",
  "site_url": "https://tu-wordpress.com",
  "site_name": "Nombre de tu Sitio"
}
```

**Respuesta de error:**
```json
{
  "success": false,
  "message": "Token inválido"
}
```

---

## 📂 Archivos Importantes

| Archivo | Ubicación | Para qué |
|---------|-----------|----------|
| Plugin ZIP | `codigo-nativo-connect.zip` | Instalar en WordPress |
| Carpeta plugin | `plugin-codigo-nativo/` | Desarrollo del plugin |
| Guía completa | `INSTALACION-PLUGIN.md` | Instrucciones detalladas |
| Solución CSP | `PROBLEMA-CSP-RESUELTO.md` | Si hay errores CSP |
| Diagnóstico | `DIAGNOSTICO-CSP.md` | Debug avanzado |

---

## 🎬 Video Mental del Proceso

```
WordPress → Plugins → Añadir nuevo → Subir ZIP → Activar
    ↓
WordPress → Código Nativo (menú) → Copiar Token
    ↓
Dashboard → Nuevo Proyecto → WordPress → Pegar Token → Validar
    ↓
✅ ¡Conectado!
```

---

## 💡 Datos Técnicos

**Endpoints de la API del plugin:**

1. **Validar token:**  
   `POST /wp-json/codigo-nativo/v1/validate`

2. **Listar plugins:**  
   `GET /wp-json/codigo-nativo/v1/plugins?token=XXX`

3. **Info del sitio:**  
   `GET /wp-json/codigo-nativo/v1/site-info?token=XXX`

**Seguridad implementada:**
- ✅ Tokens de 64 caracteres
- ✅ Sin eval() ni código inseguro
- ✅ Compatible con CSP
- ✅ Hash-equals para prevenir timing attacks

---

## 📞 Si Todo Falla

1. Verifica que `/wp-json/` funcione en tu WordPress:
   ```
   https://tu-wordpress.com/wp-json/
   ```
   Debe mostrar JSON con información de la API.

2. Prueba el endpoint de validación con cURL (comando arriba)

3. Revisa logs de errores:
   - WordPress: `wp-content/debug.log`
   - Dashboard: Consola del navegador (F12)

4. Verifica versiones:
   - WordPress: 5.6 o superior
   - PHP: 7.4 o superior

---

## ✅ Checklist Final

- [ ] Plugin instalado y activo en WordPress
- [ ] Token copiado de WordPress
- [ ] URL de WordPress correcta (con https://)
- [ ] Token pegado en dashboard
- [ ] Clic en "Validar"
- [ ] Estado cambió a "Conectado" (verde)
- [ ] Botón "Crear proyecto" habilitado

**Si completaste todos → ¡Listo para crear proyectos!** 🎉

---

¿Necesitas más ayuda? Lee los archivos de documentación completa:
- 📖 [INSTALACION-PLUGIN.md](INSTALACION-PLUGIN.md)
- 🔍 [DIAGNOSTICO-CSP.md](DIAGNOSTICO-CSP.md)
- ✅ [PROBLEMA-CSP-RESUELTO.md](PROBLEMA-CSP-RESUELTO.md)

#!/bin/bash

# Script para empaquetar el plugin Código Nativo Connect

PLUGIN_NAME="codigo-nativo-connect"
VERSION="1.0.0"
# Directorio de salida (absoluto, relativo al script)
BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
OUTPUT_DIR="$BASE_DIR/dist"

# Si `PLUGIN_DIR` no está definido, usar el directorio del script (raíz del repo)
PLUGIN_DIR="${PLUGIN_DIR:-$BASE_DIR}"

echo "📦 Empaquetando plugin ${PLUGIN_NAME} v${VERSION}..."

# Crear directorio de salida si no existe
mkdir -p "$OUTPUT_DIR"

# Nombre del archivo ZIP (ruta absoluta)
ZIP_FILE="$OUTPUT_DIR/${PLUGIN_NAME}-${VERSION}.zip"

# Eliminar ZIP anterior si existe
if [ -f "$ZIP_FILE" ]; then
    rm "$ZIP_FILE"
    echo "🗑️  Eliminado ZIP anterior"
fi


# Empaquetar desde el directorio del plugin
pushd "$PLUGIN_DIR" > /dev/null || { echo "Directorio de plugin no encontrado: $PLUGIN_DIR"; exit 1; }

# Nombre base del directorio de salida para exclusiones (ej: "dist")
OUTPUT_BASENAME="$(basename "$OUTPUT_DIR")"

# Excluir carpetas/archivos innecesarios: .git, dist, zips previos, metadatos de macOS
zip -r "$ZIP_FILE" . \
    -x ".git/*" \
    -x "${OUTPUT_BASENAME}/*" \
    -x "*.zip" \
    -x "*.DS_Store" \
    -x "__MACOSX*"

popd > /dev/null

echo "✅ Plugin empaquetado correctamente: ${ZIP_FILE}"
echo "📤 Listo para subir a WordPress"

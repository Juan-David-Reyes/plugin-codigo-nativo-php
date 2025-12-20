#!/bin/bash

# Script para empaquetar el plugin Código Nativo Connect

PLUGIN_NAME="codigo-nativo-connect"
VERSION="1.0.0"
OUTPUT_DIR="dist"
PLUGIN_DIR="plugin-codigo-nativo"

echo "📦 Empaquetando plugin ${PLUGIN_NAME} v${VERSION}..."

# Crear directorio de salida si no existe
mkdir -p "$OUTPUT_DIR"

# Nombre del archivo ZIP
ZIP_FILE="${OUTPUT_DIR}/${PLUGIN_NAME}-${VERSION}.zip"

# Eliminar ZIP anterior si existe
if [ -f "$ZIP_FILE" ]; then
    rm "$ZIP_FILE"
    echo "🗑️  Eliminado ZIP anterior"
fi

# Crear ZIP
cd "$PLUGIN_DIR" || exit
zip -r "../${ZIP_FILE}" . -x "*.git*" -x "*.DS_Store" -x "__MACOSX*"
cd ..

echo "✅ Plugin empaquetado correctamente: ${ZIP_FILE}"
echo "📤 Listo para subir a WordPress"

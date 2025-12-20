#!/bin/bash

# 🧪 Script para probar la API REST de WordPress

echo "🔍 Probando conexión a la API de WordPress..."
echo ""

# Configuración
SITE_URL="https://codigonativo.com"
TOKEN="tu-token-aqui"

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "📋 Configuración:"
echo "   URL: $SITE_URL"
echo "   Token: ${TOKEN:0:10}..."
echo ""

# Test 1: Verificar que el endpoint existe
echo "1️⃣  Verificando que la API REST esté disponible..."
API_ROOT=$(curl -s "$SITE_URL/wp-json/")
if [ -z "$API_ROOT" ]; then
    echo -e "${RED}❌ No se puede acceder a la API REST${NC}"
    echo "   El sitio puede tener la API REST deshabilitada"
else
    echo -e "${GREEN}✓ API REST disponible${NC}"
fi
echo ""

# Test 2: Verificar el endpoint específico
echo "2️⃣  Probando endpoint de validación..."
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$SITE_URL/wp-json/codigo-nativo/v1/validate" \
    -H "Content-Type: application/json" \
    -d "{\"token\":\"$TOKEN\"}")

HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | sed '$d')

echo "   HTTP Status: $HTTP_CODE"
echo "   Response:"
echo "$BODY" | python3 -m json.tool 2>/dev/null || echo "$BODY"
echo ""

# Análisis del resultado
if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✅ Conexión exitosa!${NC}"
elif [ "$HTTP_CODE" = "403" ]; then
    echo -e "${RED}❌ Error 403 Forbidden${NC}"
    echo ""
    echo "🔧 Posibles causas:"
    echo "   • ModSecurity bloqueando la petición"
    echo "   • Firewall del hosting bloqueando POST"
    echo "   • Cloudflare WAF activo"
    echo "   • .htaccess con reglas restrictivas"
    echo ""
    echo "📖 Consulta: plugin-codigo-nativo/README-INSTALACION-SERVIDOR.md"
elif [ "$HTTP_CODE" = "404" ]; then
    echo -e "${RED}❌ Error 404 Not Found${NC}"
    echo ""
    echo "🔧 Posibles causas:"
    echo "   • Plugin no instalado o no activado"
    echo "   • Permalinks no configurados"
    echo ""
    echo "Solución: Ve a WordPress → Ajustes → Enlaces permanentes → Guardar"
elif [ "$HTTP_CODE" = "401" ]; then
    echo -e "${YELLOW}⚠️  Token inválido${NC}"
    echo "   El token no coincide con el configurado en WordPress"
else
    echo -e "${RED}❌ Error desconocido (HTTP $HTTP_CODE)${NC}"
fi

echo ""
echo "💡 Para probar con tu token:"
echo "   bash test-wp-api.sh"
echo "   (Edita el archivo y cambia TOKEN por tu token real)"

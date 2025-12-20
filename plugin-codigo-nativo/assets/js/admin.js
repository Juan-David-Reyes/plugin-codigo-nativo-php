/**
 * JavaScript para el panel de administración de Código Nativo
 */

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Elementos del DOM
    const testButton = document.getElementById('cn-test-connection');
    const tokenField = document.getElementById('cn-token-field');
    const messageDiv = document.getElementById('cn-message');
    
    /**
     * Mostrar mensaje al usuario
     */
    function showMessage(message, type) {
        if (!messageDiv) return;
        
        messageDiv.style.display = 'block';
        messageDiv.className = 'notice notice-' + type;
        messageDiv.querySelector('p').textContent = message;
        
        // Ocultar después de 5 segundos
        setTimeout(function() {
            messageDiv.style.display = 'none';
        }, 5000);
    }
    
    /**
     * Probar conexión
     */
    if (testButton) {
        testButton.addEventListener('click', function() {
            // Deshabilitar botón mientras se procesa
            testButton.disabled = true;
            testButton.textContent = 'Probando...';
            
            const token = tokenField.value;
            
            if (!token) {
                showMessage('Por favor, ingresa un token primero', 'error');
                testButton.disabled = false;
                testButton.textContent = 'Probar Conexión';
                return;
            }
            
            // Hacer petición a la API de validación (GET)
            fetch(cnData.apiUrl + 'validate?token=' + encodeURIComponent(token), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    showMessage('✓ Conexión exitosa. El token es válido.', 'success');
                } else {
                    showMessage('✗ ' + (data.message || 'Token inválido'), 'error');
                }
            })
            .catch(function() {
                showMessage('✗ Error al probar la conexión', 'error');
            })
            .finally(function() {
                // Re-habilitar botón
                testButton.disabled = false;
                testButton.textContent = 'Probar Conexión';
            });
        });
    }
});

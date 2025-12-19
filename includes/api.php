<?php

add_action('rest_api_init', function () {
  register_rest_route('codigonativo/v1', '/status', [
    'methods'  => 'GET',
    'callback' => 'cn_get_site_status',
    'permission_callback' => 'cn_check_auth'
  ]);

  register_rest_route('codigonativo/v1', '/logs', [
    'methods' => 'POST',
    'callback' => 'cn_send_logs',
    'permission_callback' => 'cn_check_auth'
  ]);
});

function cn_check_auth($request) {
  // Forzar HTTPS
  if (!is_ssl()) {
    return new WP_Error('insecure', 'Conexión debe ser HTTPS', ['status' => 403]);
  }

  $token = sanitize_text_field($request->get_header('authorization'));
  if (!$token) {
    error_log('Intento de autenticación sin token desde IP: ' . $_SERVER['REMOTE_ADDR']);
    return new WP_Error('missing_token', 'Token requerido', ['status' => 401]);
  }

  $saved_hash = get_option('cn_api_token_hash');
  $expiration = get_option('cn_api_token_expiration');

  if (time() > $expiration) {
    error_log('Token expirado, intento desde IP: ' . $_SERVER['REMOTE_ADDR']);
    return new WP_Error('expired_token', 'Token expirado', ['status' => 401]);
  }

  // Rate limiting: máximo 5 intentos fallidos por hora por IP
  $ip = $_SERVER['REMOTE_ADDR'];
  $transient_key = 'cn_rate_limit_' . md5($ip);
  $attempts = get_transient($transient_key) ?: 0;
  if ($attempts >= 5) {
    error_log('Rate limit excedido desde IP: ' . $ip);
    return new WP_Error('rate_limit', 'Demasiados intentos fallidos', ['status' => 429]);
  }

  $clean_token = str_replace('Bearer ', '', $token);
  if (!wp_check_password($clean_token, $saved_hash)) {
    set_transient($transient_key, $attempts + 1, HOUR_IN_SECONDS);
    error_log('Token inválido desde IP: ' . $ip);
    return new WP_Error('invalid_token', 'Token inválido', ['status' => 401]);
  }

  // Reset attempts on success
  delete_transient($transient_key);
  return true;
}

function cn_get_site_status($request) {
  return new WP_REST_Response([
    'status' => 'active',
    'site_url' => get_site_url(),
    'wp_version' => get_bloginfo('version'),
    'timestamp' => time()
  ], 200);
}

function cn_send_logs($request) {
  $logs = $request->get_json_params();
  if (empty($logs) || !is_array($logs)) {
    return new WP_Error('invalid_logs', 'Logs inválidos', ['status' => 400]);
  }

  // Aquí podrías enviar logs al dashboard de Código Nativo
  // Por ahora, solo loguear localmente
  error_log('Logs recibidos: ' . json_encode($logs));

  return new WP_REST_Response(['status' => 'logs processed'], 200);
}

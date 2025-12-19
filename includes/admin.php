<?php

add_action('admin_menu', 'cn_add_admin_menu');

function cn_add_admin_menu() {
  add_menu_page(
    'Código Nativo Settings',
    'Código Nativo',
    'manage_options',
    'cn-settings',
    'cn_settings_page',
    'dashicons-admin-network'
  );
}

function cn_settings_page() {
  if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos para acceder a esta página.'));
  }

  if (isset($_POST['cn_token']) && wp_verify_nonce($_POST['cn_nonce'], 'cn_update_token')) {
    $new_token = sanitize_text_field($_POST['cn_token']);
    $expiration_hours = intval($_POST['cn_expiration']);
    if ($new_token && $expiration_hours > 0) {
      update_option('cn_api_token_hash', wp_hash_password($new_token));
      update_option('cn_api_token_expiration', time() + ($expiration_hours * HOUR_IN_SECONDS));
      echo '<div class="notice notice-success"><p>Token actualizado exitosamente.</p></div>';
    } else {
      echo '<div class="notice notice-error"><p>Error: Token o expiración inválidos.</p></div>';
    }
  }

  $current_expiration = get_option('cn_api_token_expiration', 0);
  $remaining_time = $current_expiration - time();
  $remaining_hours = $remaining_time > 0 ? round($remaining_time / HOUR_IN_SECONDS) : 0;

  ?>
  <div class="wrap">
    <h1>Configuración de Código Nativo HUB</h1>
    <form method="post">
      <?php wp_nonce_field('cn_update_token', 'cn_nonce'); ?>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="cn_token">Nuevo Token API</label></th>
          <td><input type="password" name="cn_token" id="cn_token" class="regular-text" required></td>
        </tr>
        <tr>
          <th scope="row"><label for="cn_expiration">Expiración (horas)</label></th>
          <td><input type="number" name="cn_expiration" id="cn_expiration" value="24" min="1" required></td>
        </tr>
      </table>
      <p>Token actual expira en: <?php echo $remaining_hours; ?> horas.</p>
      <?php submit_button('Guardar Token'); ?>
    </form>
  </div>
  <?php
}
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
    if ($new_token) {
      update_option('cn_api_token', $new_token);
      echo '<div class="notice notice-success"><p>Token actualizado exitosamente.</p></div>';
    } else {
      echo '<div class="notice notice-error"><p>Error: Token inválido.</p></div>';
    }
  }

  ?>
  <div class="wrap">
    <h1>Configuración de Código Nativo HUB</h1>
    <form method="post">
      <?php wp_nonce_field('cn_update_token', 'cn_nonce'); ?>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="cn_token">Token API</label></th>
          <td><input type="text" name="cn_token" id="cn_token" class="regular-text" value="<?php echo esc_attr(get_option('cn_api_token', '')); ?>" required></td>
        </tr>
      </table>
      <?php submit_button('Guardar Token'); ?>
    </form>
  </div>
  <?php
}
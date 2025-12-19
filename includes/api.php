<?php

add_action('rest_api_init', function () {
  register_rest_route('codigonativo/v1', '/status', [
    'methods'  => 'GET',
    'callback' => 'cn_get_site_status',
    'permission_callback' => 'cn_check_auth'
  ]);
});

function cn_check_auth($request) {
  $token = $request->get_header('authorization');
  $saved_token = get_option('cn_api_token');

  return $token === 'Bearer ' . $saved_token;
}

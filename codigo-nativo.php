<?php
/**
 * Plugin Name: HUB Código Nativo
 * Plugin URI: hhttps://codigonativo.com/servicios/mantenimiento-web/
 * Conecta WordPress con el HUB de Código Nativo para mantenimiento, monitoreo, backups y métricas clave del sitio.
 * Version: 1.0.0
 * Requires PHP: 8.0
 * Author: Código Nativo
 * Author URI: https://www.codigonativo.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Purpose: Integración SaaS
 * Category: Monitoring & performance
 * Internal: true
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/api.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';

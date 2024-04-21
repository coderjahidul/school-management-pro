<?php
defined( 'ABSPATH' ) || die();
global $wpdb;
$school_id = $current_school['id'];
$school_name = $current_school['name'];
$assessment_types = '';

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
$assessment_type_list = WLSM_Helper::assessment_type_list();

require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/global.php';
?>

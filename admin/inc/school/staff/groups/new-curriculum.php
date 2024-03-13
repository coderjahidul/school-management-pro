<style>
    body{
        background: #fff;
        margin-right:20px;
    }
    .wlsm .wlsm-main-header {
        border-radius: 15px;
        background-color: #f5f6ff;
        border: 0;
    }
    .wlsm .col {
        max-width: 100%;
    }
    .card {
        text-align: center;
        padding:20px 0;
    }
    .wlsm .wlsm-section-heading-block {
        background-color: #007bff;
        color: #fff;
        padding: 0.5rem 0.5rem;
        margin-top: 1.4rem;
        margin-bottom: 1.4rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
        text-align: center;

    }
    .wlsm .wlsm-section-heading {
        font-size: 1.4rem;
        font-weight: 600;
       
    }
</style>
<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/global.php';

// $page_url_exams                  = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAMS );
// $page_url_exam_admit_cards       = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAM_ADMIT_CARDS );
// $page_url_exam_results           = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAM_RESULTS );
// $page_url_results_assessment     = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAM_ASSESSMENT );
?>
<div class="wlsm container-fluid">
	<?php
	require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/staff/partials/header.php';
	?>

	<div class="row">
		<div class="col-md-12">
			<div class="text-center wlsm-section-heading-block">
				<span class="wlsm-section-heading">
					<i class="fas fa-clock"></i>
					<?php esc_html_e( 'Examination', 'school-management' ); ?>
				</span>
			</div>
		</div>
	</div>
</div>

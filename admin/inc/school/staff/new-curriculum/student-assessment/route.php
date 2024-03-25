<style>
    .nc-subject-card {
        text-align: center;
    }
    .nc-subject-card img {
        width: 100px;
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
					<?php esc_html_e( 'Assessment', 'school-management' ); ?>
				</span>
			</div>
		</div>
	</div>
    <?php
        global $wpdb;
        $subject_type = 'subjective';
        $subjects = $wpdb->get_results($wpdb->prepare(
            "SELECT label , code  FROM {$wpdb->prefix}wlsm_subjects WHERE type = %s",
            $subject_type
        ));
    ?>

    <div class="row mt-3 mb-3">
		<?php if ( WLSM_M_Role::check_permission( array( 'manage_exams' ), $current_school['permissions'] ) ) { ?>
        <?php foreach ($subjects as $subject) { ?>
            <div class="col-md-3 col-sm-6">
                <div class="wlsm-group nc-subject-card">
                    <img src="https://play-lh.googleusercontent.com/sim64EUhIT9b0_16zsEVzxzTGaM0CK4iM4ZiorJaAmsgdMrw1xecBIQJsmhdnFKIOfw=w600-h300-pc0xffffff-pd" alt="new-curriculum-image">
                    <span class="wlsm-group-title"><?php echo esc_html( $subject->label ); ?></span>
                    <!-- <div class="wlsm-group-actions">
                        <a href="<?php //echo esc_url( $page_url_exams ); ?>" class="btn btn-sm btn-primary">
                            <?php //esc_html_e( 'View Exams', 'school-management' ); ?>
                        </a>
                        <a href="<?php //echo esc_url( $page_url_exams . '&action=save' ); ?>" class="btn btn-sm btn-outline-primary">
                            <?php //esc_html_e( 'Add New Exam', 'school-management' ); ?>
                        </a>
                    </div> -->
                </div>
            </div>
        <?php
        }?>
		

		<?php } ?>
	</div>
</div>
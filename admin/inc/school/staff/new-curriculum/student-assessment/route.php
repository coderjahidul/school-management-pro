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

        // Get current user
        $get_current_user_id = get_current_user_id();
        $get_current_user_roles = wp_get_current_user()->roles;
        
        echo '<pre>';
        print_r($get_current_user_roles[0]);
        echo '</pre>';
        // Get current staff
        $get_current_staff_id = $wpdb->get_results($wpdb->prepare(
            "SELECT ID FROM {$wpdb->prefix}wlsm_staff WHERE user_id = %d",
            $get_current_user_id
        ));
        $get_current_staff_id = $get_current_staff_id[0]->ID;

        // Get Current
        $get_current_admin = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}wlsm_admins WHERE staff_id = %d",
            $get_current_staff_id
        ));
        
        if($get_current_user_roles[0] == 'administrator'){
            $subject_types = array('subjective', 'practical');
            foreach ($subject_types as $subject_type) {
                $subjects = $wpdb->get_results($wpdb->prepare(
                    "SELECT label, code FROM {$wpdb->prefix}wlsm_subjects WHERE type = %s",
                    $subject_type
                ));

                if ($subjects) {
                    ?>
                    <div class="row mt-3 mb-3">
                        <?php foreach ($subjects as $subject) { ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="wlsm-group nc-subject-card">
                                    <img src="<?php echo esc_url( WLSM_PLUGIN_URL . '/assets/images/new-curriculum.png' ); ?>" alt="New Curriculum">
                                    <span class="wlsm-group-title"><?php echo esc_html( $subject->label ); ?></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                } else {
                    echo "No subjects found for type: $subject_type";
                }
            }
        }else{
            $teacher_assign_subjects = $wpdb->get_results($wpdb->prepare(
                "SELECT subject_id FROM {$wpdb->prefix}wlsm_admin_subject WHERE admin_id = %d",
                $get_current_admin[0]->ID
            ));
            if($teacher_assign_subjects){
                foreach($teacher_assign_subjects as $teacher_assign_subject){
                    $subject_id = $teacher_assign_subject->subject_id;
                    $subjects = $wpdb->get_results($wpdb->prepare(
                        "SELECT label , code  FROM {$wpdb->prefix}wlsm_subjects WHERE ID = %d",
                        $subject_id
                    ));
                    ?>
                    <div class="row mt-3 mb-3">
                        <?php foreach ($subjects as $subject) { ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="wlsm-group nc-subject-card">
                                    <img src="<?php echo esc_url( WLSM_PLUGIN_URL . '/assets/images/new-curriculum.png' ); ?>" alt="New Curriculum">
        
                                    <span class="wlsm-group-title"><?php echo esc_html( $subject->label ); ?></span>
                                    <span><?php echo esc_html("Teacher Name: " . $get_current_admin[0]->name); ?></span>
                                    <br>
                                    <span><?php echo esc_html($get_current_admin[0]->designation); ?></span>
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
                    </div>
        
                    <?php
                }
            }
        }

        
        
    ?>

    
</div>
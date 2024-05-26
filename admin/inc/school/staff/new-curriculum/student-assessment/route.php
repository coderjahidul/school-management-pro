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
$subject_based_assessment = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_SUBJECT_BASED_ASSESSMENT );
$behavioral_assessment = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_BEHAVIORAL_ASSESSMENT );
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
        $roles_users = array('administrator', 'author', 'editor');
        $role_matched = false;
        foreach($get_current_user_roles as $role){
            if(in_array($role, $roles_users)){
                $role_matched = true;
                break;
            }
        }
        if($role_matched){
            $subject_types = array('subjective', 'practical');
            foreach ($subject_types as $subject_type) {
                $subjects = $wpdb->get_results($wpdb->prepare(
                    "SELECT ID, label, code, class_school_id FROM {$wpdb->prefix}wlsm_subjects WHERE type = %s",
                    $subject_type
                ));?>
                <div class="row mt-3 mb-3">
                        <?php foreach ($subjects as $subject) { 
                            $class_school_id = $subject->class_school_id;
                            $subject_id = $subject->ID;
                            $get_class_id = $wpdb->get_results($wpdb->prepare(
                                "SELECT class_id FROM {$wpdb->prefix}wlsm_class_school WHERE ID = %d",
                                $class_school_id
                            ));
                            $get_class_lable = $wpdb->get_results($wpdb->prepare(
                                "SELECT label FROM {$wpdb->prefix}wlsm_classes WHERE ID = %d",
                                $get_class_id[0]->class_id
                            ));
                            ?>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="wlsm-group nc-subject-card">
                                    <img src="<?php echo esc_url( WLSM_PLUGIN_URL . '/assets/images/new-curriculum.png' ); ?>" alt="New Curriculum">
                                    <br>
                                    <span><?php echo esc_html( "Code: " . $subject->code ); ?></span>
                                    <br>
                                    <span><?php echo esc_html( $subject->label ); ?></span>
                                    <br>
                                    <?php foreach($get_class_lable as $class_lable) {?>
                                    <span><?php echo esc_html( "Class: " . $class_lable->label ); ?></span>
                                    <?php } ?>
                                    <br>
                                    <span><?php //echo esc_html("Teacher Name: " . $get_current_admin[0]->name); ?></span>
                                    <div class="wlsm-group-actions">
                                        <a href="<?php echo esc_url( $subject_based_assessment . '&subject_id=' . $subject_id); ?>" class="btn btn-sm btn-primary">
                                            <?php esc_html_e( 'Subject Based', 'school-management' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( $behavioral_assessment . '&subject_id=' . $subject_id); ?>" class="btn btn-sm btn-outline-primary">
                                            <?php esc_html_e( 'Behavioral Based', 'school-management' ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <?php
                }
        }else{
            $teacher_assign_subjects = $wpdb->get_results($wpdb->prepare(
                "SELECT subject_id FROM {$wpdb->prefix}wlsm_admin_subject WHERE admin_id = %d",
                $get_current_admin[0]->ID
            ));
            if($teacher_assign_subjects){?>
                <div class="row mt-3 mb-3">
                <?php
                    foreach($teacher_assign_subjects as $teacher_assign_subject){
                        $subject_id = $teacher_assign_subject->subject_id;
                        $subjects = $wpdb->get_results($wpdb->prepare(
                            "SELECT label , code, class_school_id  FROM {$wpdb->prefix}wlsm_subjects WHERE ID = %d",
                            $subject_id
                        ));
                        ?>
                        <?php foreach ($subjects as $subject) { 
                            $class_school_id = $subject->class_school_id;
                            $get_class_id = $wpdb->get_results($wpdb->prepare(
                                "SELECT class_id FROM {$wpdb->prefix}wlsm_class_school WHERE ID = %d",
                                $class_school_id
                            ));
                            $get_class_lable = $wpdb->get_results($wpdb->prepare(
                                "SELECT label FROM {$wpdb->prefix}wlsm_classes WHERE ID = %d",
                                $get_class_id[0]->class_id
                            ));
                            ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="wlsm-group nc-subject-card">
                                    <img src="<?php echo esc_url( WLSM_PLUGIN_URL . '/assets/images/new-curriculum.png' ); ?>" alt="New Curriculum">
                                    <br>
                                    <span><?php echo esc_html( "Code: " . $subject->code ); ?></span>
                                    <br>
                                    <span><?php echo esc_html( $subject->label ); ?></span>
                                    <br>
                                    <?php foreach($get_class_lable as $class_lable) {?>
                                    <span><?php echo esc_html( "Class: " . $class_lable->label ); ?></span>
                                    <?php } ?>
                                    <br>
                                    <span><?php echo esc_html("Teacher: " . $get_current_admin[0]->name); ?></span>
                                    <br>
                                    <span><?php echo esc_html($get_current_admin[0]->designation); ?></span>
                                    <div class="wlsm-group-actions">
                                        <a href="<?php echo esc_url( $subject_based_assessment . '&subject_id=' . $subject_id);?>" class="btn btn-sm btn-primary">
                                            <?php esc_html_e( 'Subject Based', 'school-management' ); ?>
                                        </a>
                                        <a href="<?php echo esc_url( $behavioral_assessment . '&subject_id=' . $subject_id); ?>" class="btn btn-sm btn-outline-primary">
                                            <?php esc_html_e( 'Behavioral Based', 'school-management' ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }?>
                        <?php
                    }?>
                </div>
                <?php
            }
        } 
    ?>

    
</div>
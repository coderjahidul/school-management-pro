<style>
    .student-list{
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        box-shadow: 0px 0px 5px 0px #ddd;
    }
</style>
<?php
defined( 'ABSPATH' ) || die();

$school_id = $current_school['id'];
$assessment_types = '';

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
global $wpdb;
$class_schools_id = $wpdb->get_results( $wpdb->prepare("SELECT ID FROM {$wpdb->prefix}wlsm_class_school WHERE school_id = %d", $school_id) );

// Check if $class_schools_id is not empty before accessing its first element
if (!empty($class_schools_id)) {
    $subjects = $wpdb->get_results( $wpdb->prepare("SELECT ID, label FROM {$wpdb->prefix}wlsm_subjects WHERE class_school_id = %d AND type != 'mcq'", $class_schools_id[0]->ID) );

    // Proceed with further processing
} else {
    // Handle case when $class_schools_id is empty
    echo 'No class schools found for the specified school ID.';
}

$assessment_type_list = WLSM_Helper::assessment_type_list();

require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/global.php';
?>

<div class="wlsm container-fluid">
    <?php
    require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/staff/partials/header.php';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="mt-2 text-center wlsm-section-heading-block">
                <span class="wlsm-section-heading">
                    <i class="fas fa-id-card"></i>
                    <?php esc_html_e( 'Student Transcript', 'school-management' ); ?>
                </span>
            </div>
            <div class="wlsm-students-block wlsm-form-section">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" id="wlsm-student-transcript-form" class="mb-3">
                            <div class="pt-2">
                                <div class="row">
                                    <div class="col-md-8 mb-1">
                                        <div class="h6">
                                            <span class="text-secondary border-bottom">
                                                <?php esc_html_e( 'Search Students By Class', 'school-management' ); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="wlsm_class" class="wlsm-font-bold">
                                            <?php esc_html_e( 'Class', 'school-management' ); ?>:
                                        </label>
                                        <select name="class_id" class="form-control selectpicker" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" id="wlsm_class" data-live-search="true">
                                            <option value=""><?php esc_html_e( 'Select Class', 'school-management' ); ?></option>
                                            <?php foreach ( $classes as $class ) { ?>
                                                <option value="<?php echo esc_attr( $class->ID ); ?>">
                                                    <?php echo esc_html( WLSM_M_Class::get_label_text( $class->label ) ); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="wlsm_class_group" class="wlsm-font-bold">
                                            <?php esc_html_e( 'Class Group', 'school-management' ); ?>:
                                        </label>
                                        <select name="class_group" class="form-control selectpicker" id="wlsm_class_group">
                                            <option value=""><?php esc_html_e( 'Select Class Group', 'school-management' ); ?></option>
                                            <option value="common" selected>Common</option>
                                            <option value="science">Science</option>
                                            <option value="commerce">Commerce</option>
                                            <option value="humanity">Humanity</option>
                                            <option value="vocational">Vocational</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="wlsm_section" class="wlsm-font-bold">
                                            <?php esc_html_e( 'Section', 'school-management' ); ?>:
                                        </label>
                                        <select name="section_id" class="form-control selectpicker wlsm_section_excel_bulk" id="wlsm_section" data-live-search="true" title="<?php esc_attr_e( 'All Sections', 'school-management' ); ?>" data-all-sections="1">
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="wlsm_subject" class="wlsm-font-bold">
                                            <?php esc_html_e( 'Subject', 'school-management' ); ?>:
                                        </label>
                                        <select name="subject_id" class="form-control selectpicker" id="wlsm_subject" data-live-search="true">
                                            <option value=""><?php esc_html_e( 'Select Subject', 'school-management' ); ?></option>
                                            <?php foreach ( $subjects as $subject ) { ?>
                                                <option value="<?php echo esc_attr( $subject->ID ); ?>">
                                                    <?php echo esc_html( WLSM_M_Class::get_label_text( $subject->label ) ); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="wlsm-print-transcript-btn">
                                        <i class="fas fa-print"></i>&nbsp;
                                        <?php esc_html_e( 'Print Transcript', 'school-management' ); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <?php 
                            // Display student names and IDs based on selected parameters
                            if ( isset($_POST['class_group']) && isset($_POST['section_id'])) {
                                // Fetch selected values from the form
                                $class_group = sanitize_text_field($_POST['class_group']);
                                $section_id = absint($_POST['section_id']);

                                // Perform SQL query to fetch student records based on selected parameters
                                global $wpdb;
                                $table_name = $wpdb->prefix . 'wlsm_student_records';
                                $get_student_records = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM $table_name WHERE note = %s AND section_id = %d", $class_group, $section_id
                                ));

                                // Display student names and IDs
                                if ($get_student_records) {
                                    echo '<div class="wlsm-student-list">';
                                    foreach ($get_student_records as $student_record) {
                                        $student_name = $student_record->name;
                                        $student_roll = $student_record->roll_number;
                                        ?>
                                        <div class="student-list">
                                            <span class="student-name"><?php  echo esc_html__("Student Name: " . $student_name); ?></span>
                                            <br>
                                            <span class="student-roll"><?php echo esc_html__("Student Roll: " . $student_roll); ?></span>
                                        </div>
                                        <?php
                                    }
                                    echo '</div>';
                                } else {
                                    echo '<p>!No students found.</p>';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="class-id"></div>
    <div id="class-group"></div>
    <div id="section-id"></div>

    <?php
    
    ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('#wlsm-print-transcript-btn').on('click', function (e) {
            e.preventDefault();

            // Get selected values
            var classId = $('#wlsm_class').val();
            var classGroup = $('#wlsm_class_group').val();
            var sectionId = $('#wlsm_section').val();

            // Print selected values (you can modify this part as needed)
            // console.log('Class ID: ' + classId);
            // console.log('Class Group: ' + classGroup);
            // console.log('Section ID: ' + sectionId);
            // document.getElementById('class-id').innerHTML = "Class Id: " + classId;
            // document.getElementById('class-group').innerHTML = "Class Group: " + classGroup;
            // document.getElementById('section-id').innerHTML = "Class Section: " + sectionId;

            // Perform further actions here, such as submitting the form or processing the data
            // Here, we can submit the form to fetch and display student names and IDs
            $('#wlsm-student-transcript-form').submit();
        });
    });
</script>


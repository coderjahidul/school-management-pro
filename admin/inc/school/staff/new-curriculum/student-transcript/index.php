<?php
defined( 'ABSPATH' ) || die();

$school_id = $current_school['id'];
$assessment_types = '';

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
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
                        <form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="wlsm-print-bulk-result-form" class="mb-3">
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
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="wlsm-print-bulk-result-btn">
                                        <i class="fas fa-print"></i>&nbsp;
                                        <?php esc_html_e( 'Print Transcript', 'school-management' ); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="class-id"></div>
    <div id="class-group"></div>
    <div id="section-id"></div>

    <?php
    // Display student names and IDs based on selected parameters
    if (isset($_POST['class_id']) && isset($_POST['class_group']) && isset($_POST['section_id'])) {
        // Fetch selected values from the form
        $class_id = absint($_POST['class_id']);
        $class_group = sanitize_text_field($_POST['class_group']);
        $section_id = absint($_POST['section_id']);

        // Perform SQL query to fetch student records based on selected parameters
        global $wpdb;
        $table_name = $wpdb->prefix . 'wlsm_student_records';
        $query = $wpdb->prepare("
            SELECT student_id, student_name
            FROM $table_name
            WHERE class_id = %d AND class_group = %s AND section_id = %d
        ", $class_id, $class_group, $section_id);
        $students = $wpdb->get_results($query);

        // Display student names and IDs
        if ($students) {
            echo '<div class="wlsm-student-list">';
            foreach ($students as $student) {
                echo '<p>Student ID: ' . esc_html($student->student_id) . ', Name: ' . esc_html($student->student_name) . '</p>';
            }
            echo '</div>';
        } else {
            echo '<p>No students found.</p>';
        }
    }
    ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('#wlsm-print-bulk-result-btn').on('click', function (e) {
            e.preventDefault();

            // Get selected values
            var classId = $('#wlsm_class').val();
            var classGroup = $('#wlsm_class_group').val();
            var sectionId = $('#wlsm_section').val();

            // Print selected values (you can modify this part as needed)
            // console.log('Class ID: ' + classId);
            // console.log('Class Group: ' + classGroup);
            // console.log('Section ID: ' + sectionId);
            document.getElementById('class-id').innerHTML = "Class Id: " + classId;
            document.getElementById('class-group').innerHTML = "Class Group: " + classGroup;
            document.getElementById('section-id').innerHTML = "Class Section: " + sectionId;

            // Perform further actions here, such as submitting the form or processing the data
            // Here, we can submit the form to fetch and display student names and IDs
            $('#wlsm-print-bulk-result-form').submit();
        });
    });
</script

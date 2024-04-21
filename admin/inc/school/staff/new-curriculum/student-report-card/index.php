<style>
    .wlsm-student-list .card {
        max-width: 100%;
        margin-top: 10px;
        padding: 0px;
    }
    .wlsm-student-list .card .card-header .btn-link {
        color: #000;
        font-size: 16px;
        font-weight: 600;
        text-align: left;
    }
    .wlsm-student-list .card .btn-link:hover {
        text-decoration: none;
        color: #0078F9;
    }
    .wlsm-student-list .card .collapse {
        border-top: 1px solid #ddd;
    }
    .chapter-not-found {
        text-align: center;
        margin-top: 200px;
    }
    .card-header {
        padding: 0px;
    }
    .collapse .card-body {
        padding: 0px;
    }
    .collapse .card-body .btn-link {
        font-size: 16px;
        font-weight: 500;
        color: #000;
        text-decoration: none;
        margin-top: 10px;
        max-width: 100%;
        width: 100%;
        text-align: left;
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }
    .modal .modal-content {
        margin-top: 50px;
    }
    .student-info {
        display: flex;
        border: 1px solid #ddd;
    }
    .student-info span{
        font-weight: 500;
    }
    .student-info-one,
    .student-info-two {
        width:50%;
        padding: 10px;
    }
    .student-info-one {
        border-right: 1px solid #ddd;
    }

    .modal-body table tr td,
    .modal-body table tr th {
        padding: 5px !important;
    }
    .modal-body table tr td {
        text-align: left !important;
    }
    .marking p {
        font-size: 14px !important;
    }

    .modal-body table tr td .active{
        color: #007bff !important;
    }
    
</style>
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

<div class="wlsm container-fluid">
    <?php
    require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/staff/partials/header.php';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="mt-2 text-center wlsm-section-heading-block">
                <span class="wlsm-section-heading">
                    <i class="fas fa-id-card"></i>
                    <?php esc_html_e( 'Student Report Card', 'school-management' ); ?>
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
                                            <span class="wlsm-important">*</span> <?php esc_html_e( 'Class', 'school-management' ); ?>:
                                        </label>
                                        <select name="class_id" class="form-control selectpicker wlsm_class_subjects" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" data-nonce-subjects="<?php echo esc_attr( wp_create_nonce( 'get-class-subjects' ) ); ?>" id="wlsm_class" data-live-search="true">
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
                                            <span class="wlsm-important">*</span> <?php esc_html_e( 'Section', 'school-management' ); ?>:
                                        </label>
                                        <select name="section_id" class="form-control selectpicker" id="wlsm_section" data-live-search="true" title="<?php esc_attr_e( 'Select Section', 'school-management' ); ?>" data-actions-box="true">
                                            <?php foreach ( $sections as $section ) { ?>
                                                <option value="<?php echo esc_attr( $section->ID ); ?>" <?php selected( in_array( $section->ID, $homework_sections ), true, true ); ?>>
                                                    <?php echo esc_html( WLSM_M_Staff_Class::get_section_label_text( $section->label ) ); ?>
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
                                $class_id = absint($_POST['class_id']);
                                $class_group = sanitize_text_field($_POST['class_group']);
                                $section_id = absint($_POST['section_id']);
                                // $subject_id = absint($_POST['subject_id']);
                                $class_label = isset($class->label) ? $class->label : '';

                                $section_label = $wpdb->get_results($wpdb->prepare("SELECT label FROM {$wpdb->prefix}wlsm_sections WHERE ID = %d", $section_id));
                                $section_label = isset($section_label[0]->label) ? $section_label[0]->label : '';

                                // Perform SQL query to fetch student records based on selected parameters
                                $table_name = $wpdb->prefix . 'wlsm_student_records';
                                $get_student_records = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM $table_name WHERE note = %s AND section_id = %d order by roll_number", $class_group, $section_id
                                ));
                                // $subject_label = $wpdb->get_results($wpdb->prepare("SELECT label FROM {$wpdb->prefix}wlsm_subjects WHERE ID = %d", $subject_id));
                                // $subject_label = isset($subject_label[0]->label) ? $subject_label[0]->label : '';


                                // Display student names and IDs
                                if ($get_student_records) {
                                    echo '<div class="wlsm-student-list">';
                                    foreach ($get_student_records as $student_record) {
                                        $student_name = $student_record->name;
                                        $student_roll = $student_record->roll_number;
                                        $student_record_id = $student_record->ID;
                                        ?>
                                            <div class="card-body">
                                                <?php
                                                    ?>
                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-link " data-toggle="modal" data-target="#student_report_card<?php echo $student_record_id;?>">
                                                        <?php echo $student_roll . ' - ' . $student_name; ?>
                                                    </button>
                                                    <!-- Assessment During Learning Modal -->
                                                    <div class="modal fade" id="student_report_card<?php echo $student_record_id;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <?php 
                                                            $assessment_types = "assessment_during_learning";
                                                            $assessment_label = "Assessment During Learning";
                                                            $subject_woys_result = new_curriculum_subject_ways_result_print($wpdb, $student_record_id, $student_roll, $student_name, $class_id, $class_group, $section_label, $subject_id, $subject_label, $assessment_types, $school_name, $class_label, $assessment_label);
                                                        ?>
                                                    </div>
                                                    <?php
                                                ?>
                                            </div>
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

    <?php
    
    ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('#wlsm-print-transcript-btn').on('click', function (e) {
            e.preventDefault();

            // Get selected values
            let classId = $('#wlsm_class').val();
            let classGroup = $('#wlsm_class_group').val();
            let sectionId = $('#wlsm_section').val();
            $('#wlsm-student-transcript-form').submit();
        });
        
    });

</script>



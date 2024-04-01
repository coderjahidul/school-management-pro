<style>
.nav-link.active {
    color: #fff !important;
    background-color: #007BFF !important;
	border: 1px solid #007BFF !important;
}
.nav-tabs li a {
	color: #000;
	font-size: 18px;
	font-weight: 600;
}
.tab-content .card {
    max-width: 100%;
    margin-top: 10px;
    padding: 0px;
}
.tab-content .card .card-header .btn-link {
    color: #000;
    font-size: 16px;
    font-weight: 600;
}
.tab-content .card .btn-link:hover {
    text-decoration: none;
    color: #0078F9;
}
.tab-content .card .collapse {
    border-top: 1px solid #ddd;
}
.chapter-not-found {
    text-align: center;
    margin-top: 200px;
}
.collapse .card-body .btn-link {
    font-size: 16px;
    font-weight: 500;
    color: #000;
    text-decoration: none;
}
/* popup */
.modal .modal-title {
    font-size: 16px;
    font-weight: 500;
}
.modal .close span {
    font-size: 34px;
    font-weight: 400;
}
.modal .modal-content {
    margin-top: 50px;
}

.result-assessment {
    display: flex;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 5px;
}
.student-list {
    width: 20%;
    padding: 10px;
}
.student-list img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
}
.student-result {
    display: flex;
    width: 80%;
}
.student-result div {
    width: 33.33%;
    padding: 10px;
    display: flex;
    border-left: 1px solid #ddd;
}
.student-result div span {
    font-size: 20px;
    font-weight: 600;
    color: #000;
    margin-right: 10px;
}
</style>
<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/global.php';

// Define page URLs if needed
// $page_url_exams                  = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAMS );
// $page_url_exam_admit_cards       = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAM_ADMIT_CARDS );
// $page_url_exam_results           = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAM_RESULTS );
// $page_url_results_assessment     = admin_url( 'admin.php?page=' . WLSM_MENU_STAFF_EXAM_ASSESSMENT );

$subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;
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
                    <?php esc_html_e( 'Behavioral Assessment', 'school-management' ); ?>
                </span>
            </div>

            <!-- Tab navigation -->
            <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true"><?php echo esc_html__("Quarterly Behavioral Assessment", "school-management");?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><?php echo esc_html__("Annual Behavioral Assessment", "school-management");?></a>
                </li>
            </ul>

            
            <div class="tab-content" id="myTabContent">
                <!-- Start Quarterly Behavioral Assessment -->
                <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    <!-- Accordion -->
                    <div id="accordion">
                        <?php 
                            global $wpdb;
                            $get_subject_chapters = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}wlsm_chapter WHERE subject_id = %d AND assessment_types = 'quarterly_behavioral_assessment'",
                                $subject_id,
                            ));
                            if(!empty($get_subject_chapters)){
                                foreach($get_subject_chapters as $chapter){
                                    $chapter_id = $chapter->ID;
                                    $chapter_label = $chapter->title; ?>
                                    <div class="card">
                                        <div class="card-header" id="heading<?php echo $chapter_id; ?>">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?php echo $chapter_id; ?>" aria-expanded="true" aria-controls="collapse<?php echo $chapter_id; ?>">
                                                    <span><?php echo $chapter_label; ?></span>
                                                </button>
                                            </h5>
                                        </div>
                                        <?php 
                                            $get_subject_lessons = $wpdb->get_results($wpdb->prepare(
                                                "SELECT * FROM {$wpdb->prefix}wlsm_lecture WHERE chapter_id = %d",
                                                $chapter_id
                                            ));
                                            foreach($get_subject_lessons as $lesson){
                                                $lesson_id = $lesson->ID;
                                                $lesson_code = $lesson->code;
                                                $class_id = $lesson->class_id;
                                                $lesson_label = $lesson->title;
                                                $square_des = $lesson->square_description;
                                                $circle_des = $lesson->circle_description;
                                                $triangle_des = $lesson->triangle_description;
                                                
                                                ?>
                                                <div id="collapse<?php echo $chapter_id; ?>" class="collapse" aria-labelledby="heading<?php echo $chapter_id; ?>" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#extraLargeModal<?php echo $lesson_id;?>"><span><?php echo $lesson_code . " - " . $lesson_label; ?></span></button>
                                                    </div>
                                                    <div class="modal fade" id="extraLargeModal<?php echo $lesson_id;?>" tabindex="-1" role="dialog" aria-labelledby="extraLargeModalLabel<?php echo $lesson_id;?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="extraLargeModalLabel<?php echo $lesson_id;?>"><?php echo $lesson_code . " - " . $lesson_label; ?></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <!-- Modal content goes here -->
                                                                    <?php 
                                                                        $get_class_school_id = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT ID FROM {$wpdb->prefix}wlsm_class_school WHERE class_id = %d",
                                                                            $class_id
                                                                        ));
                                                                        $get_section_id = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT ID FROM {$wpdb->prefix}wlsm_sections WHERE class_school_id = %d",
                                                                            $get_class_school_id[0]->ID
                                                                        ));
                                                                        $get_student_records = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT * FROM {$wpdb->prefix}wlsm_student_records WHERE section_id = %d ORDER BY roll_number ASC",
                                                                            $get_section_id[0]->ID
                                                                        ));
                                                                        foreach($get_student_records as $student_record){
                                                                            ?>
                                                                            <div class="result-assessment">
                                                                                <div class="student-list">
                                                                                    <img src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/user.png');?>" alt="student image">
                                                                                    <br>
                                                                                    <span><?php echo $student_record->name; ?></span>
                                                                                    <br>
                                                                                    <span><?php echo esc_html__("Roll No: " . $student_record->roll_number); ?></span>
                                                                                </div>
                                                                                <div class="student-result">
                                                                                    <div class="square-description">
                                                                                        <span class="square-icon">&#9634;</span>
                                                                                        <?php echo $square_des; ?>
                                                                                    </div>
                                                                                    <div class="circle-description">
                                                                                        <span class="circle-icon">&#11096;</span>
                                                                                        <?php echo $circle_des; ?>
                                                                                    </div>
                                                                                    <div class="triangle-description">
                                                                                        <span class="triangle-icon">&#128710;</span>
                                                                                        <?php echo $triangle_des; ?>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                            
                                                                            
                                                                            <?php
                                                                            // echo "Student Class: " .$student_class = $student_record->class_id;
                                                                        }
                                                                    ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                                    <!-- Additional buttons can be added here -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><?php
                                            } 
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("(BI) Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Quarterly Behavioral Assessment -->
                <!-- Start Annual Behavioral Assessment -->
                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    <!-- Accordion -->
                    <div id="accordion">
                        <?php 
                            global $wpdb;
                            $get_subject_chapters = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}wlsm_chapter WHERE subject_id = %d AND assessment_types = 'annual_behavioral_assessment'",
                                $subject_id,
                            ));
                            if(!empty($get_subject_chapters)){
                                foreach($get_subject_chapters as $chapter){
                                    $chapter_id = $chapter->ID;
                                    $chapter_label = $chapter->title; ?>
                                    <div class="card">
                                        <div class="card-header" id="heading<?php echo $chapter_id; ?>">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?php echo $chapter_id; ?>" aria-expanded="true" aria-controls="collapse<?php echo $chapter_id; ?>">
                                                    <span><?php echo $chapter_label; ?></span>
                                                </button>
                                            </h5>
                                        </div>
                                        <?php 
                                            $get_subject_lessons = $wpdb->get_results($wpdb->prepare(
                                                "SELECT * FROM {$wpdb->prefix}wlsm_lecture WHERE chapter_id = %d",
                                                $chapter_id
                                            ));
                                            foreach($get_subject_lessons as $lesson){
                                                $lesson_id = $lesson->ID;
                                                $lesson_code = $lesson->code;
                                                $class_id = $lesson->class_id;
                                                $lesson_label = $lesson->title;
                                                $square_des = $lesson->square_description;
                                                $circle_des = $lesson->circle_description;
                                                $triangle_des = $lesson->triangle_description;
                                                
                                                ?>
                                                <div id="collapse<?php echo $chapter_id; ?>" class="collapse" aria-labelledby="heading<?php echo $chapter_id; ?>" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#extraLargeModal<?php echo $lesson_id;?>"><span><?php echo $lesson_code . " - " . $lesson_label; ?></span></button>
                                                    </div>
                                                    <div class="modal fade" id="extraLargeModal<?php echo $lesson_id;?>" tabindex="-1" role="dialog" aria-labelledby="extraLargeModalLabel<?php echo $lesson_id;?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="extraLargeModalLabel<?php echo $lesson_id;?>"><?php echo $lesson_code . " - " . $lesson_label; ?></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <!-- Modal content goes here -->
                                                                    <?php 
                                                                        $get_class_school_id = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT ID FROM {$wpdb->prefix}wlsm_class_school WHERE class_id = %d",
                                                                            $class_id
                                                                        ));
                                                                        $get_section_id = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT ID FROM {$wpdb->prefix}wlsm_sections WHERE class_school_id = %d",
                                                                            $get_class_school_id[0]->ID
                                                                        ));
                                                                        $get_student_records = $wpdb->get_results($wpdb->prepare(
                                                                            "SELECT * FROM {$wpdb->prefix}wlsm_student_records WHERE section_id = %d ORDER BY roll_number ASC",
                                                                            $get_section_id[0]->ID
                                                                        ));
                                                                        foreach($get_student_records as $student_record){
                                                                            ?>
                                                                            <div class="result-assessment">
                                                                                <div class="student-list">
                                                                                    <img src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/user.png');?>" alt="student image">
                                                                                    <br>
                                                                                    <span><?php echo $student_record->name; ?></span>
                                                                                    <br>
                                                                                    <span><?php echo esc_html__("Roll No: " . $student_record->roll_number); ?></span>
                                                                                </div>
                                                                                <div class="student-result">
                                                                                    <div class="square-description">
                                                                                        <span class="square-icon">&#9634;</span>
                                                                                        <?php echo $square_des; ?>
                                                                                    </div>
                                                                                    <div class="circle-description">
                                                                                        <span class="circle-icon">&#11096;</span>
                                                                                        <?php echo $circle_des; ?>
                                                                                    </div>
                                                                                    <div class="triangle-description">
                                                                                        <span class="triangle-icon">&#128710;</span>
                                                                                        <?php echo $triangle_des; ?>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                            
                                                                            
                                                                            <?php
                                                                            // echo "Student Class: " .$student_class = $student_record->class_id;
                                                                        }
                                                                    ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                                    <!-- Additional buttons can be added here -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><?php
                                            }  
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("(BI) Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Annual Behavioral Assessment -->
            </div>
            <!-- student list modal popup -->
            
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



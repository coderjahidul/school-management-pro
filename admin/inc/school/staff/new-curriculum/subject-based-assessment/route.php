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
.tab-content .card .btn-link {
    color: #000;
    font-size: 16px;
    font-weight: 600;
}
.tab-content .card .btn-link:hover {
    text-decoration: none;
    color: #0078F9;
}
.tab-content .card .collapse {
    border: 1px solid #ddd;
    border-left: 0;
    border-right: 0;
    margin: 5px 0;
}
.chapter-not-found {
    text-align: center;
    margin-top: 200px;
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
                    <?php esc_html_e( 'Subject Based Assessment', 'school-management' ); ?>
                </span>
            </div>

            <!-- Tab navigation -->
            <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true"><?php echo esc_html__("Assessment During Learning", "school-management");?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><?php echo esc_html__("Quarterly Summative Assessment", "school-management");?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false"><?php echo esc_html__("Annual Summative Assessment", "school-management");?></a>
                </li>
            </ul>

            
            <div class="tab-content" id="myTabContent">
                <!-- Start Assessment During Learning -->
                <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    <!-- Accordion -->
                    <div id="accordion">
                        <?php 
                            global $wpdb;
                            $get_subject_chapters = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}wlsm_chapter WHERE subject_id = %d AND assessment_types = 'assessment_during_learning'",
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
                                                $lesson_label = $lesson->title;?>
                                                <div id="collapse<?php echo $chapter_id; ?>" class="collapse" aria-labelledby="heading<?php echo $chapter_id; ?>" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <span><?php echo $lesson_code . " - " . $lesson_label; ?></span>
                                                    </div>
                                                </div><?php
                                            } 
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("(PI) Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Assessment During Learning -->
                <!-- Start Quarterly Summative Assessment -->
                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    <!-- Accordion -->
                    <div id="accordion">
                        <?php 
                            global $wpdb;
                            $get_subject_chapters = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}wlsm_chapter WHERE subject_id = %d AND assessment_types = 'quarterly_summative_assessment'",
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
                                                $lesson_label = $lesson->title;?>
                                                <div id="collapse<?php echo $chapter_id; ?>" class="collapse" aria-labelledby="heading<?php echo $chapter_id; ?>" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <span><?php echo $lesson_code . " - " . $lesson_label; ?></span>
                                                    </div>
                                                </div><?php
                                            } 
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("(PI) Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Quarterly Summative Assessment -->
                <!-- Start Annual Summative Assessment -->
                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    <!-- Accordion -->
                    <div id="accordion">
                        <?php 
                            global $wpdb;
                            $get_subject_chapters = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}wlsm_chapter WHERE subject_id = %d AND assessment_types = 'annular_summative_assessment'",
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
                                                $lesson_label = $lesson->title;?>
                                                <div id="collapse<?php echo $chapter_id; ?>" class="collapse" aria-labelledby="heading<?php echo $chapter_id; ?>" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <span><?php echo $lesson_code . " - " . $lesson_label; ?></span>
                                                    </div>
                                                </div><?php
                                            } 
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("(PI) Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Annual Summative Assessment -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



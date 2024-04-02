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
.student-result label {
    width: 33.33%;
    padding: 10px;
    display: flex;
    border-left: 1px solid #ddd;
    margin-bottom: 0;
}
.student-result label span {
    font-size: 20px;
    font-weight: 600;
    color: #000;
    margin-right: 10px;
}
.selected {
    color: #007BFF !important;
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
                               // call new curriculum marks template
                               $subject_chapters = chapters_function_template($get_subject_chapters, $wpdb);
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
                                // call new curriculum marks template
                                $subject_chapters = chapters_function_template($get_subject_chapters, $wpdb); 
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
<script src="//unpkg.com/alpinejs" defer></script>



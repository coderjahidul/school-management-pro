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
                    <a class="nav-link active" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><?php echo esc_html__("Quarterly Behavioral Assessment", "school-management");?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false"><?php echo esc_html__("Annual Behavioral Assessment", "school-management");?></a>
                </li>
            </ul>

            
            <div class="tab-content" id="myTabContent">
                <!-- Start Quarterly Behavioral Assessment -->
                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
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
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
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
                                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <span><?php echo $lesson_code . " - " . $lesson_label; ?></span>
                                                    </div>
                                                </div><?php
                                            } 
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Quarterly Behavioral Assessment -->
                <!-- Start Annual Behavioral Assessment -->
                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    <!-- Accordion -->
                    <div id="accordion">
                        <?php 
                            global $wpdb;
                            $get_subject_chapters = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}wlsm_chapter WHERE subject_id = %d AND assessment_types = 'annular_behavioral_assessment'",
                                $subject_id,
                            ));
                            if(!empty($get_subject_chapters)){
                                foreach($get_subject_chapters as $chapter){
                                    $chapter_id = $chapter->ID;
                                    $chapter_label = $chapter->title; ?>
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
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
                                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <span><?php echo $lesson_code . " - " . $lesson_label; ?></span>
                                                    </div>
                                                </div><?php
                                            } 
                                        ?>
                                    </div><?php
                                } 
                            }else {
                                ?><h3 class="chapter-not-found"><?php echo esc_html__("Chapter Not Found", "school-management"); ?></h3><?php
                            }
                            
                        ?>

                        <!-- Add more accordion items as needed -->

                    </div>
                    <!-- End Accordion -->
                </div>
                <!-- End Annual Behavioral Assessment -->
            </div>
		</div>
	</div>
</div>
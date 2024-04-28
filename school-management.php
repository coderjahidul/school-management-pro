<?php
/*
 * Plugin Name: School Management
 * Plugin URI: https://weblizar.com/plugins/school-management/
 * Description: School Management is a WordPress plugin to manage multiple schools and their entities such as classes, sections, students, exams, ID cards, admit cards, teachers, staff, fees, invoices, income, expense, noticeboard, study materials and much more.
 * Version: 10.2.7
 * Author: Grocoder
 * Author URI: https://grocoder.com
 * Text Domain: school-management
*/

defined( 'ABSPATH' ) || die();

if ( ! defined( 'WLSM_PLUGIN_URL' ) ) {
	define( 'WLSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WLSM_PLUGIN_DIR_PATH' ) ) {
	define( 'WLSM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

final class WLSM_School_Management {
	private static $instance = NULL;

	private function __construct() {
		$this->initialize_hooks();
		$this->setup_database();
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function initialize_hooks() {
		if ( is_admin() ) {
			require_once WLSM_PLUGIN_DIR_PATH . 'admin/admin.php';
		}
		require_once WLSM_PLUGIN_DIR_PATH . 'public/public.php';
	}

	private function setup_database() {
		require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/WLSM_Database.php';
		register_activation_hook( __FILE__, array( 'WLSM_Database', 'activation' ) );
		register_deactivation_hook( __FILE__, array( 'WLSM_Database', 'deactivation' ) );
		register_uninstall_hook( __FILE__, array( 'WLSM_Database', 'uninstall' ) );
	}
}
WLSM_School_Management::get_instance();

// required custom functions
if( file_exists(__DIR__ . '/custom-functions.php') ){
	require_once(__DIR__ . '/custom-functions.php');
}
// required codestar framework
if( file_exists(__DIR__ . './includes/codestar-framework/codestar-framework.php') ){
	require_once(__DIR__ . './includes/codestar-framework/codestar-framework.php');
}
if( file_exists(__DIR__ . '/metabox.php') ){
	require_once(__DIR__ . '/metabox.php');
}



// new curriculum marks template 
function chapters_function_template($get_subject_chapters, $wpdb){
	// Submit student new curriculum results marks
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_ids'], $_POST['lesson_id'])) {
        global $wpdb;
    
        // Sanitize and validate lesson_id
        $lesson_id = intval($_POST['lesson_id']);
    
        // Retrieve the submitted data
        $student_ids = $_POST['student_ids'];
    
        // Check if records already exist for the given lesson_id
        $existing_records = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT student_record_id FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE lecture_id = %d",
                $lesson_id
            ),
            ARRAY_A
        );
    
        // Iterate through each student ID and insert or update the data into the database
        foreach ($student_ids as $student_id) {
            // Sanitize the student ID
            $student_id = intval($student_id);
    
            // Get the selected mark for the student
            $selected_mark_key = 'mark_' . $student_id;
            if (isset($_POST[$selected_mark_key])) {
                $selected_mark = sanitize_text_field($_POST[$selected_mark_key]);
    
                // Check if record exists for the current student_id and lesson_id combination
                $existing_record_index = array_search($student_id, array_column($existing_records, 'student_record_id'));
    
                if ($existing_record_index !== false) {
                    // Update existing record
                    $wpdb->update(
                        $wpdb->prefix . 'wlsm_new_curriculum_results',
                        array(
                            'new_curriculum_marks' => $selected_mark
                        ),
                        array(
                            'student_record_id' => $student_id,
                            'lecture_id' => $lesson_id
                        ),
                        array(
                            '%s' // new_curriculum_marks format
                        ),
                        array(
                            '%d', // student_id format
                            '%d'  // lecture_id format
                        )
                    );
                } else {
                    // Insert new record
                    $wpdb->insert(
                        $wpdb->prefix . 'wlsm_new_curriculum_results',
                        array(
                            'student_record_id' => $student_id,
                            'lecture_id' => $lesson_id,
                            'new_curriculum_marks' => $selected_mark
                        ),
                        array(
                            '%d', // student_id format
                            '%d', // lecture_id format
                            '%s'  // new_curriculum_marks format
                        )
                    );
                }
            }
        }
    }

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
											));?>
											<form action="" method="post"><?php
												foreach($get_student_records as $student_record):
													$student_id = $student_record->ID;
													$get_new_curriculum_results = $wpdb->get_results($wpdb->prepare(
														"SELECT new_curriculum_marks FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE student_record_id = %d AND lecture_id = %d", 
														$student_record->ID,
														$lesson_id
													));
													$result = isset($get_new_curriculum_results[0]->new_curriculum_marks) ? $get_new_curriculum_results[0]->new_curriculum_marks : '';
													?>
													<div class="result-assessment">
														<div class="student-list">
															<img src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/user.png');?>" alt="student image">
															<br>
															<span><?php echo $student_record->name; ?></span>
															<br>
															<span><?php echo esc_html__("Roll No: " . $student_record->roll_number); ?></span>
														</div>
														<input type="hidden" name="student_ids[]" value="<?php echo $student_record->ID; ?>">
														<input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">   
														<div class="student-result" x-data="{ selected: '<?php echo $result;?>' }">
															<label class="square-description">
																<input @click="selected = 'square'" style="display:none;" type="radio" name="mark_<?php echo $student_record->ID; ?>" value="square">
																<span class="square-icon" x-bind:class="{ 'selected': selected === 'square' }">&#9634;</span>
																<?php echo $square_des; ?>
															</label>
															<label class="circle-description">
																<input @click="selected = 'circle'" style="display:none;" type="radio" name="mark_<?php echo $student_record->ID; ?>" value="circle">
																<span class="circle-icon" x-bind:class="{ 'selected': selected === 'circle' }">&#11096;</span>
																<?php echo $circle_des; ?>
															</label>
															<label class="triangle-description">
																<input @click="selected = 'triangle'" style="display:none;" type="radio" name="mark_<?php echo $student_record->ID; ?>" value="triangle">
																<span class="triangle-icon" x-bind:class="{ 'selected': selected === 'triangle' }">&#128710;</span>
																<?php echo $triangle_des; ?>
															</label>
														</div>
													</div>                                                                               
													<?php
													// echo "Student Class: " .$student_class = $student_record->class_id;
												endforeach;
											?>
											<div class="modal-footer">
												<button class="btn btn-primary text-end" type="submit">Submit</button>
											</div>
											
										</form>
										<?php
										?>
									</div>
									
								</div>
							</div>
						</div>
					</div><?php
				} 
			?>
		</div><?php
	} 
}

// new curriculum subject woys result print function
function new_curriculum_subject_ways_result_print($wpdb, $student_record_id, $student_roll, $student_name, $class_id, $class_group, $section_label, $subject_id, $subject_label, $assessment_types, $school_name, $class_label, $assessment_label) {?>
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo esc_html__('Student Transcript', 'school-management');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="transcript-content<?php echo $assessment_types . $student_record_id;?>">
				<table border="1" style="border-collapse: collapse;">
					<tbody>
						<tr>
							<th colspan="2" align="left" style="padding: 5px; text-align: left">
								<?php echo esc_html__('School Name: ' . $school_name); ?>
								<br>
								<?php echo esc_html__("Assessment Types: " . $assessment_label);?>
								<br>
								<?php echo esc_html__('Student Name: ' . $student_name); ?>
								<br>
								<?php echo esc_html__('Student Roll: ' . $student_roll); ?>
							</th>
							<th colspan="2" align="left" style="padding: 5px; text-align: left">
								<?php echo esc_html__('Class: ' . $class_label); ?>
								<br>
								<?php echo esc_html__('Group: ' . $class_group); ?>
								<br>
								<?php echo esc_html__('Section: ' . $section_label); ?>
								<br>
								<?php echo esc_html__('Subject: ' . $subject_label); ?>
							</th>
						</tr>
						<tr>
							<th colspan = "1" style="padding: 5px;"> 
								<?php echo esc_html__('Proficiency index', 'school-management');?>
							</th>
							<th colspan = "3" style="padding: 5px;">
								<?php echo esc_html__('Level of proficiency', 'school-management');?>
							</th>
						</tr>
					</tbody>
					<?php 
						
						$chapter_ids = $wpdb->get_results($wpdb->prepare(
							"SELECT ID FROM {$wpdb->prefix}wlsm_chapter WHERE class_id = %d AND subject_id = %d AND assessment_types = %s", $class_id, $subject_id, $assessment_types
						));
						foreach($chapter_ids as $chapter_id) {
							$chapter_id = $chapter_id->ID;
							$get_subject_lesson = $wpdb->get_results($wpdb->prepare(
								"SELECT * FROM {$wpdb->prefix}wlsm_lecture WHERE chapter_id = %d", $chapter_id
							));
							
							foreach ($get_subject_lesson as $subject_lesson) {
								$lesson_id = $subject_lesson->ID;
								$lesson_code = $subject_lesson->code;
								$lesson_title = $subject_lesson->title;
								$square_des = $subject_lesson->square_description;
								$circle_des = $subject_lesson->circle_description;
								$triangle_des = $subject_lesson->triangle_description;
								?>

									<tbody>
										<td style="padding: 5px;">
											<p><?php echo $lesson_code . ' - ' . $lesson_title; ?></p>
										</td> 
										<?php 
											$new_curriculum_results = $wpdb->get_results($wpdb->prepare(
												"SELECT * FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE student_record_id = %d AND lecture_id = %d", $student_record_id, $lesson_id
											));
											foreach($new_curriculum_results as $new_curriculum_result) {
												$marks = $new_curriculum_result->new_curriculum_marks;
												?>
													<!-- <div class="transcript-result"> -->
														<td style="padding: 5px;">
															<div class="marking" style="display: flex;justify-content: left;">
																<?php if($marks == "square"){?>
																	<span class="square-icon active" style="color: #007bff; font-weight: 600; padding-right: 5px;">&#9634;</span>
																<?php } else {
																	?>
																		<span class="square-icon" style="padding-right: 5px;">&#9634;</span>
																	<?php
																}
																echo $square_des;?>
															</div>
														</td>
														<td style="padding: 5px;">
															<div class="marking" style="display: flex;justify-content: left;">
																<?php if($marks == "circle"){?>
																	<span class="circle-icon active" style="color: #007bff; font-weight: 600; padding-right: 5px;">&#11096;</span>
																<?php } else {
																	?>
																		<span class="circle-icon" style="padding-right: 5px;">&#11096;</span>
																	<?php
																}
																echo $circle_des; ?>
															</div>
														</td>
														<td style="padding: 5px;"> 
															<div class="marking" style="display: flex;justify-content: left;">
																<?php if($marks == "triangle"){?>
																	<span class="triangle-icon active" style="color: #007bff; font-weight: 600; padding-right: 5px;">&#128710;</span>
																<?php } else {
																	?>
																		<span class="triangle-icon" style="padding-right: 5px;">&#128710;</span>
																	<?php
																}
																echo $triangle_des; ?>
															</div>
														</td>
													<!-- </div> -->
												<?php
											}
										?> 
										
									</tbody>

								<?php
							}
						}
						
					?>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="print-transcript<?php echo $assessment_types . $student_record_id;?>"><?php echo esc_html__('Print Transcript', 'school-management');?></button>
			</div>
			<script>
				jQuery(document).ready(function ($) {
					$("#print-transcript<?php echo $assessment_types . $student_record_id;?>").click(function(){
						let content = $("#transcript-content<?php echo $assessment_types . $student_record_id;?>").html();
						let printWindow = window.open('', '', 'resizable=yes, scrollbars=yes');

						printWindow.document.write('<html><head><title><?php echo esc_html__('Print ' . $student_name . "-" . $assessment_label . 'Transcript'); ?></title></head><body>');
						printWindow.document.write(content);
						printWindow.document.write('</body></html>');
						printWindow.document.close();
						printWindow.print();
					});
				});
			</script>
		</div>
	</div><?php
}

// Student Report Card Function
function student_report_card($wpdb, $student_record_id, $student_roll, $student_name, $student_session, $class_id, $class_group, $section_label, $assessment_types, $school_name, $school_logo, $class_label, $assessment_label) {?>
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo esc_html__('Student Report Card', 'school-management');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="report-content<?php echo $student_record_id;?>">
				<table border="1" style="border-collapse: collapse;">
					<tbody>
						<tr>
							<th>
								<div class="school-banner text-center">
									<img style="width: 70%; padding: 20px 0" src="<?php echo esc_url(WLSM_PLUGIN_URL . "assets/images/student-report-card-banner.png"); ?>" alt="student-report-card-banner">
								</div>
							</th>
						</tr>
						<tr>
							<th>
								<div class="school-logo text-center">
									<?php 
										if(!empty($school_logo)) {
											?>
												<img style="border-radius: 50%; padding: 10px 0;" src="<?php echo esc_url(wp_get_attachment_url($school_logo));?>" alt="school logo">
											<?php
										}
									?>
								</div>
							</th>
						</tr>
						<tr>
							<th colspan="2" align="left" style="padding: 5px; text-align: left">
								<?php echo esc_html__('School Name: ' . $school_name); ?>
								<br>
								<?php echo esc_html__('Student Name: ' . $student_name);?>
								<br>
								<?php echo esc_html__('Student Roll: ' . $student_roll);?>
								<br>
								<?php echo esc_html__('Class: ' . $class_label);?>
								<br>
								<?php echo esc_html__('Session: ' . $student_session[0]->label);?>
							</th>
						</tr>
						<tr>
							<th colspan="2" align="center" style="padding: 5px; text-align: center">
								<h4 class="border-bottom pb-1"><?php echo esc_html__("Subjects", "school-management");?></h4>
								<div align="left" class="subject-list text-left" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));">
									<?php 
										$class_school_id = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}wlsm_class_school WHERE class_id = %d", $class_id));
										$subject_list = $wpdb->get_results($wpdb->prepare("SELECT ID, label FROM {$wpdb->prefix}wlsm_subjects WHERE class_school_id = %d AND type IN ('subjective', 'practical')", $class_school_id[0]->ID));
										foreach($subject_list as $subject) {
											$subject_label = $subject->label;
											?>
												<div class="subject"><img style="width: 35px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . "assets/images/new-curriculum.png"); ?>" alt=""><?php echo esc_html($subject_label);?></div>
											<?php
										}
									?>
								</div>
								
							</th>			
						</tr>
						<tr>
							<th colspan="2" align="center" style="padding: 5px; text-align: center">
								<?php 
									foreach($subject_list as $subject){
										$subject_label = $subject->label;?>

											<h5 class="bg-light font-size-18 font-weight-500" style="padding: 10px 0; margin: 10px 0;"><?php echo esc_html($subject_label);?></h5>
											<table style="margin-top: 0 !important;">
												<tr>
													<td style="padding: 0 !important;">
														<table border="1" style="border-collapse: collapse; margin-top: 0 !important;">
															<tr>
																<td colspan="7" style="padding: 5px !important; text-align: center;">
																	<?php echo esc_html("Contact", "school-management");?>
																</td>
															</tr>
															<tr>
																<td colspan="7" style="padding: 5px !important; text-align: center;">
																	<?php echo esc_html("ব্যক্তির সাথে সম্পর্কের ধরন অনুযায়ী মর্যাদাপূর্ণ শারীরিক ভাষা প্রয়োগ করতে পারছে", "school-management");?>
																</td>
															</tr>
															<tr>
																<td style="padding: 5px !important; text-align: center;">1</td>
																<td style="padding: 5px !important; text-align: center;">2</td>
																<td style="padding: 5px !important; text-align: center;">3</td>
																<td style="padding: 5px !important; text-align: center;">4</td>
																<td style="padding: 5px !important; text-align: center;">5</td>
																<td style="padding: 5px !important; text-align: center;">6</td>
																<td style="padding: 5px !important; text-align: center;">7</td>
															</tr>
														</table>
													</td>
													<td style="padding: 0 !important;">
														<table border="1" style="border-collapse: collapse; margin-top: 0 !important;">
															<tr>
																<td colspan="7" style="padding: 5px !important; text-align: center;">
																	<?php echo esc_html("Contact", "school-management");?>
																</td>
															</tr>
															<tr>
																<td colspan="7" style="padding: 5px !important; text-align: center;">
																	<?php echo esc_html("ব্যক্তির সাথে সম্পর্কের ধরন অনুযায়ী মর্যাদাপূর্ণ শারীরিক ভাষা প্রয়োগ করতে পারছে", "school-management");?>
																</td>
															</tr>
															<tr>
																<td style="padding: 5px !important; text-align: center;">1</td>
																<td style="padding: 5px !important; text-align: center;">2</td>
																<td style="padding: 5px !important; text-align: center;">3</td>
																<td style="padding: 5px !important; text-align: center;">4</td>
																<td style="padding: 5px !important; text-align: center;">5</td>
																<td style="padding: 5px !important; text-align: center;">6</td>
																<td style="padding: 5px !important; text-align: center;">7</td>
															</tr>
														</table>
													</td>
													<td style="padding: 0 !important;">
														<table border="1" style="border-collapse: collapse; margin-top: 0 !important;">
															<tr>
																<td colspan="7" style="padding: 5px !important; text-align: center;">
																	<?php echo esc_html("Contact", "school-management");?>
																</td>
															</tr>
															<tr>
																<td colspan="7" style="padding: 5px !important; text-align: center;">
																	<?php echo esc_html("ব্যক্তির সাথে সম্পর্কের ধরন অনুযায়ী মর্যাদাপূর্ণ শারীরিক ভাষা প্রয়োগ করতে পারছে", "school-management");?>
																</td>
															</tr>
															<tr>
																<td style="padding: 5px !important; text-align: center;">1</td>
																<td style="padding: 5px !important; text-align: center;">2</td>
																<td style="padding: 5px !important; text-align: center;">3</td>
																<td style="padding: 5px !important; text-align: center;">4</td>
																<td style="padding: 5px !important; text-align: center;">5</td>
																<td style="padding: 5px !important; text-align: center;">6</td>
																<td style="padding: 5px !important; text-align: center;">7</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>

										<?php
									}
								?>
							</th>
						</tr>
						
					</tbody>
				
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="print-report-card<?php echo $student_record_id;?>"><?php echo esc_html__('Print Report Card', 'school-management');?></button>
			</div>
			<script>
				jQuery(document).ready(function ($) {
					$("#print-report-card<?php echo $student_record_id;?>").click(function(){
						let content = $("#report-content<?php echo $student_record_id;?>").html();
						let printWindow = window.open('', '', 'resizable=yes, scrollbars=yes');

						printWindow.document.write('<html><head><title><?php echo esc_html__('Print ' . $student_name  . 'Report Card'); ?></title></head><body>');
						printWindow.document.write(content);
						printWindow.document.write('</body></html>');
						printWindow.document.close();
						printWindow.print();
					});
				});
			</script>
		</div>
	</div><?php
}



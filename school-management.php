<?php
/*
 * Plugin Name: School Management
 * Plugin URI: https://weblizar.com/plugins/school-management/
 * Description: School Management is a WordPress plugin to manage multiple schools and their entities such as classes, sections, students, exams, ID cards, admit cards, teachers, staff, fees, invoices, income, expense, noticeboard, study materials and much more.
 * Version: 10.3.1
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
function chapters_function_template($get_subject_chapters, $wpdb) {
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
		$chapter_label = $chapter->title;
		$chapter_subject_id = $chapter->subject_id; ?>
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
													$student_religion = $student_record->religion;
													$student_id = $student_record->ID;
													// get subject religion whre subject id = $chapter_subject_id
													$get_subject_religion = $wpdb->get_results($wpdb->prepare(
														"SELECT religion FROM {$wpdb->prefix}wlsm_subjects WHERE ID = %d",
														$chapter_subject_id
													));
													$subject_religion = isset($get_subject_religion[0]->religion) ? $get_subject_religion[0]->religion : '';
													
									
													if($subject_religion ==  $student_religion || $subject_religion == "common") {
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
																	<span class="triangle-icon" x-bind:class="{ 'selected': selected === 'triangle' }">🛆</span>
																	<?php echo $triangle_des; ?>
																</label>
															</div>
														</div>                                                                               
														<?php
													}
													
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
function new_curriculum_subject_ways_result_print($wpdb, $student_record_id, $student_roll, $student_name, $class_id, $class_group, $section_label, $subject_id, $subject_label, $assessment_types, $school_name, $school_logo, $active_schools_id, $class_label, $assessment_label, $lecture_ids) {?>
	<div class="modal-dialog modal-xl" role="document">
		
		<?php 
			global $wpdb; // Declare the global $wpdb object
			$table_name = $wpdb->prefix . 'wlsm_schools';
			$active_schools_id = $active_schools_id[0]->ID;
			// Prepare and execute the SQL query to get the school's address and email by school ID
			$school = $wpdb->get_row($wpdb->prepare("SELECT address, email FROM $table_name WHERE id = %d", $active_schools_id));
			
			// Extract the address from the result object and assign it to $school_address
			$school_address = $school->address;
			
			// Extract the email from the result object and assign it to $school_email
			$school_email = $school->email;
		?>
		<table border="1" style="border-collapse: collapse;">
			<tbody>
				<tr style="border: none;">
					<th colspan="4" style="border: none;">
						<div class="schools-info" style="display: flex; align-items: center;">
							<div class="logo" style="text-align: right; padding-right: 20px;">
								<img style="border-radius: 50%; width: 20%; align-items: center;" src="<?php echo esc_url(wp_get_attachment_url($school_logo));?>" alt="school logo">
							</div>
							<div class="info">
								<h3><?php echo esc_html($school_name);?></h3>
								<?php if(!empty($school_address)) { ?>
									<h5><?php echo esc_html($school_address);?></h5>
								<?php } ?>
								<?php if(!empty($school_email)) { ?>
									<p style="margin: 0; padding: 0;"><?php echo esc_html("Email: " . $school_email);?></p>
								<?php } ?>
							</div>
						</div>
					</th>
				</tr>


				<tr>
					<th colspan="2" align="left" style="padding: 5px; text-align: left">
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__("Assessment Types: " . $assessment_label);?></span>
						<br>
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__('Student Name: ' . $student_name); ?></span>
						<br>
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__('Student Roll: ' . $student_roll); ?></span>
					</th>
					<th colspan="2" align="left" style="padding: 5px; text-align: left">
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__('Class: ' . $class_label); ?></span>
						<br>
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__('Group: ' . $class_group); ?></span>
						<br>
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__('Section: ' . $section_label); ?></span>
						<br>
						<span style="font-width: bold; color: #3399ff;"><?php echo esc_html__('Subject: ' . $subject_label); ?></span>
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
					

					foreach($lecture_ids as $lecture_id) {
						$lecture_id = intval($lecture_id);
						$get_subject_lesson = $wpdb->get_results($wpdb->prepare(
							"SELECT * FROM {$wpdb->prefix}wlsm_lecture WHERE chapter_id = %d AND ID = %d", $chapter_id, $lecture_id
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
								<?php 
									// Initialize $rlesson_id
									$rlesson_id = null;

									// Fetch lesson IDs from the database
									$results_lesson_id = $wpdb->get_results(
										$wpdb->prepare("SELECT lecture_id FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE student_record_id = %d AND lecture_id = %d", $student_record_id, $lesson_id)
									);

									// Check if there are any results
									if (!empty($results_lesson_id)) {
										foreach ($results_lesson_id as $r_lesson_id) {
											// Get the lesson ID from the current result
											$rlesson_id = $r_lesson_id->lecture_id;
											// Break the loop since we only need the first match
											break;
										}
									}

									// Check if $rlesson_id is set and equal to $lesson_id
									if ($lesson_id == $rlesson_id) { ?>
										<td style="padding: 5px;">
											<p><?php echo $lesson_code . ' - ' . $lesson_title; ?></p>
										</td>
									<?php } ?>

									<?php 
										$new_curriculum_results = $wpdb->get_results($wpdb->prepare(
											"SELECT * FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE student_record_id = %d AND lecture_id = %d", $student_record_id, $lesson_id
										));
										foreach($new_curriculum_results as $new_curriculum_result) {
											$marks = $new_curriculum_result->new_curriculum_marks;
											?>
												<td style="padding: 5px;">
													<div class="marking" style="display: flex;justify-content: left;">
														<?php if($marks == "square"){?>
															<span class="square-icon active" style=" padding-right: 10px;"><img style="width: 17px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/square-black.png');?>" alt=""></span>
														<?php } else {
															?>
																<span class="square-icon" style="padding-right: 10px;"><img style="width: 17px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/square-white.png');?>" alt=""></span>
															<?php
														}
														echo $square_des;?>
													</div>
												</td>
												<td style="padding: 5px;">
													<div class="marking" style="display: flex;justify-content: left;">
														<?php if($marks == "circle"){?>
															<span class="circle-icon active" style="padding-right: 10px;"><img style="width: 15px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/circle-black.png');?>" alt=""></span>
														<?php } else {
															?>
																<span class="circle-icon" style="padding-right: 10px;"><img style="width: 15px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/circle-white.png');?>" alt=""></span>
															<?php
														}
														echo $circle_des; ?>
													</div>
												</td>
												<td style="padding: 5px;"> 
													<div class="marking" style="display: flex;justify-content: left;">
														<?php if($marks == "triangle"){?>
															<span class="triangle-icon active" style="padding-right: 10px;"><img style="width: 15px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/triangle-black.png');?>" alt=""></span>
														<?php } else {
															?>
																<span class="triangle-icon" style="padding-right: 10px;"><img style="width: 15px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . '/assets/images/triangle-white.png');?>" alt=""></span>
															<?php
														}
														echo $triangle_des; ?>
													</div>
												</td>
											<?php
										}
									?> 
									
								</tbody>

							<?php
						}
					}
					
					
				}
				
			?>
			<td colspan="2" style="text-align: center !important; border: 0;">
				<?php echo esc_html__('', 'school-management'); ?><br>
				<?php echo esc_html__('____________________________', 'school-management'); ?><br>
				<?php echo esc_html__('Subject Teacher', 'school-management'); ?>
				</td>
				<td colspan="2" style="text-align: center !important; border: 0;">
				<?php echo esc_html__('', 'school-management'); ?><br>
				<?php echo esc_html__('____________________________', 'school-management'); ?><br>
				<?php echo esc_html__('Head Teacher', 'school-management'); ?>
			</td>

		</table>
			
	</div>
	<?php
}

// Student Report Card Function
function student_report_card($wpdb, $student_record_id, $student_roll, $student_name, $student_session, $student_religion, $class_id, $class_group, $section_label, $school_name, $school_logo, $class_label) {?>
	
	<!-- <tbody id="report-content"> -->
		<tr>
			<th colspan="5">
				<div class="school-banner text-center">
					<img style="width: 70%; padding: 20px 0" src="<?php echo esc_url(WLSM_PLUGIN_URL . "assets/images/student-report-card-banner.png"); ?>" alt="student-report-card-banner">
				</div>
			</th>
		</tr>
		<tr>
			<th colspan="5">
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
			<th colspan="5" align="left" style="padding: 5px; text-align: left">
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
			<th colspan="5" align="center" style="padding: 5px; text-align: center">
				<h4 class="border-bottom pb-1"><?php echo esc_html__("Subjects", "school-management");?></h4>
				<div align="left" class="subject-list text-left" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));">
					<?php 
						$class_school_id = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}wlsm_class_school WHERE class_id = %d", $class_id));
						$subject_list = $wpdb->get_results($wpdb->prepare("SELECT ID, label, religion FROM {$wpdb->prefix}wlsm_subjects WHERE class_school_id = %d AND type IN ('subjective', 'practical')", $class_school_id[0]->ID));
						foreach($subject_list as $subject) {
							if($subject->religion ==  $student_religion || $subject->religion == "common") {
								$subject_label = $subject->label;
								?>
									<div class="subject"><img style="width: 35px;" src="<?php echo esc_url(WLSM_PLUGIN_URL . "assets/images/new-curriculum.png"); ?>" alt=""><?php echo esc_html($subject_label);?></div>
								<?php
							}
						}
					?>
				</div>
				
			</th>			
		</tr>
		<tr>
			<th colspan="5" align="center" style="padding: 5px; text-align: center">
				<?php 
					foreach($subject_list as $subject){
						if($subject->religion ==  $student_religion || $subject->religion == "common") {
							$subject_label = $subject->label;?>

							<h5 class="bg-light font-size-18 font-weight-500" style="padding: 10px 0; margin: 10px 0;"><?php echo esc_html($subject_label);?></h5>
							<table>
								<tr>
									<?php 
									$get_area_of_expertise = $wpdb->get_results($wpdb->prepare("SELECT ID, title, description, lecture_ids FROM {$wpdb->prefix}wlsm_area_of_expertise WHERE class_id = %d AND subject_id = %d", $class_id, $subject->ID));

									$column_count = 0;

									foreach ($get_area_of_expertise as $area_of_expertise) {
										$area_of_expertise_title = $area_of_expertise->title;
										$area_of_expertise_description = $area_of_expertise->description;
										$lecture_ids = $area_of_expertise->lecture_ids;


										if ($column_count % 3 == 0 && $column_count != 0) {
											echo '</tr><tr>';
										}

										$column_count++;
										?>
										<td style="padding: 0 !important; width: 33%;">
											<table border="1" style="height: 100%; border-collapse: collapse; margin-top: 0 !important;">
												<tr>
													<td colspan="7" style="padding: 5px !important; text-align: center;">
														<?php echo esc_html($area_of_expertise_title, "school-management");?>
													</td>
												</tr>
												<tr>
													<td border="1"  colspan="7" style="border-collapse: collapse; padding: 5px !important; text-align: center;">
														<?php echo esc_html($area_of_expertise_description, "school-management");?>
													</td>
												</tr>
												<tr>
													<?php 
														// Get all exam results for the area of expertise
														$lecture_ids = explode(",", $lecture_ids);
														$total_lectures = count($lecture_ids);
														$student_record_id = intval($student_record_id);

														// Initialize counters for each shape
														$total_squares = 0;
														$total_circles = 0;
														$total_triangles = 0;
														
														foreach ($lecture_ids as $lecture_id) {
															$lesson_codes = $wpdb->get_results($wpdb->prepare(
																"SELECT code FROM {$wpdb->prefix}wlsm_lecture WHERE ID = %d", $lecture_id
															));
															// Loop through each lesson code
															foreach ($lesson_codes as $lesson_code) {
																$lecture_code = $lesson_code->code;
																$all_exam_results = $wpdb->get_results($wpdb->prepare(
																	"SELECT * FROM {$wpdb->prefix}wlsm_lecture WHERE code = %s", $lecture_code
																));
																$square = $circle = $triangle = NULL;
																// Loop through each exam result
																foreach ($all_exam_results as $all_exam_result) {
																	$all_exm_lecture_id = $all_exam_result->ID;
																	$new_curriculum_results = $wpdb->get_results($wpdb->prepare(
																		"SELECT * FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE student_record_id = %d AND lecture_id = %d",
																		$student_record_id, $all_exm_lecture_id
																	));
																	// Loop through each new curriculum result
																	foreach ($new_curriculum_results as $new_curriculum_result) {
																		$result_shapes = $new_curriculum_result->new_curriculum_marks;
																		if ($result_shapes == "square") {
																			$square = "square";
																		} elseif ($result_shapes == "circle") {
																			$circle = "circle";
																		} elseif ($result_shapes == "triangle") {
																			$triangle = "triangle";
																		}
																	}
																}
																// Count the number of each shape
																if ($square && $circle && $triangle || $triangle) {
																	$triangle = "triangle";
																	$total_triangles++;
																} elseif ($square && $circle || $circle) {
																	$circle = "circle";
																	$total_circles++;
																} else {
																	$square = "square";
																	$total_squares++;
																}
															}
														}
														// Calculate the percentage of each shape
														$total_pi = $total_squares + $total_circles + $total_triangles;
														$sub_pi = $total_triangles - $total_squares;
														
														if($sub_pi == 0) {
															$mark = 0;
														}else{
															$mark = 100 * ($sub_pi / $total_pi);
														}
														$mark = round($mark);
														// echo "Mark: " . $mark;
														// Display the percentage of each shape
														if($mark >= 81 ) {
															for ($i = 0; $i < 7; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														} elseif($mark >= 41) {
															for ($i = 0; $i < 6; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
															for($i = 0; $i < 1; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #fff;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														} elseif($mark >= 21) {
															for($i = 0; $i< 5; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
															for($i = 0; $i < 2; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #fff;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														} elseif($mark >= 0) {
															for($i = 0; $i <4; $i++){
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
															for($i = 0; $i < 3; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #fff;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														} elseif( $mark >= -20) {
															for($i = 0; $i < 3; $i++){
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
															for($i = 0; $i < 4; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #fff;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														} elseif($mark >= -40) {
															for($i = 0; $i < 2; $i++){
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
															for($i = 0; $i < 5; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #fff;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														} else {
															for($i = 0; $i < 1; $i++){
																echo '<td style="padding: 5px !important; text-align: center; background: #454444;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
															for($i = 0; $i < 6; $i++) {
																echo '<td style="padding: 5px !important; text-align: center; background: #fff;"><span style="opacity: 0;">' . ($i + 1) . '</span></td>';
															}
														}
													?>
													
												</tr>
											</table>
										</td>
										<?php
									}
									?>
								</tr>
							</table>

						<?php
						}
					}
				?>
			</th>
		</tr>
		<tr>
			<th colspan="21" align="center" style="padding: 5px; text-align: center">
				<h5 class="bg-light font-size-18 font-weight-500" style="padding: 10px 0; margin: 10px 0;"><?php echo esc_html("Behavioral indicators", "school-management");?></h5>
				<table style="margin-top: 0 !important; width: 100%;">
					<tr>
						<td style="padding: 0 !important; width: 33.33%;">
							<table border="1" style="border-collapse: collapse; margin-top: 0 !important; width: 100%;">
								<!-- Your table content here -->
								<tr>
									<td colspan="7" style="padding: 5px !important; text-align: center;">
										<?php echo esc_html("অংশগ্রহণ ও যোগাযোগ", "school-management");?>
									</td>
								</tr>
								<tr>
									<?php 
										for($i = 0; $i < 7; $i++) {
											echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
										}
									?>
								</tr>
							</table>
						</td>
						<td style="padding: 0 !important; width: 33.33%;">
							<table border="1" style="border-collapse: collapse; margin-top: 0 !important; width: 100%;">
								<!-- Your table content here -->
								<tr>
									<td colspan="7" style="padding: 5px !important; text-align: center;">
										<?php echo esc_html("নিষ্ঠা ও সততা", "school-management");?>
									</td>
								</tr>
								<tr>
									<?php 
										for($i = 0; $i < 7; $i++) {
											echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
										}
									?>
								</tr>
							</table>
						</td>
						<td style="padding: 0 !important; width: 33.33%;">
							<table border="1" style="border-collapse: collapse; margin-top: 0 !important; width: 100%;">
								<!-- Your table content here -->
								<tr>
									<td colspan="7" style="padding: 5px !important; text-align: center;">
										<?php echo esc_html("পারস্পরিক শ্রদ্ধা এবং সহযোগিতা", "school-management");?>
									</td>
								</tr>
								<tr>
									<?php 
										for($i = 0; $i < 7; $i++) {
											echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
										}
									?>
								</tr>
							</table>
						</td>
					</tr>
				</table> 
			</th>
		</tr>
		<tr>
			<th colspan="5">
			<h5 class="bg-light font-size-18 font-weight-500" style="padding: 10px 0; margin: 10px 0; text-align: left;"><?php echo esc_html("Evaluation Scale", "school-management");?></h5>
			</th>		
		</tr>
		<tr>
			<td colspan="2" style="width: 40%;">
				<table border="1" style="border-collapse: collapse; margin-top: 0 !important; width: 100%;">
					<tr>
						<?php 
							for($i = 0; $i < 7; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
					<tr>
						<?php 
							for($i = 0; $i < 6; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
							for($i = 0; $i < 1; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #fff;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
					<tr>
						<?php 
							for($i = 0; $i < 5; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
							for($i = 0; $i < 2; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #fff;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
					<tr>
						<?php 
							for($i = 0; $i < 4; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
							for($i = 0; $i < 3; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #fff;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
					<tr>
						<?php 
							for($i = 0; $i < 3; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
							for($i = 0; $i < 4; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #fff;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
					<tr>
						<?php 
							for($i = 0; $i < 2; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
							for($i = 0; $i < 5; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #fff;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
					<tr>
						<?php 
							for($i = 0; $i < 1; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #454444;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
							for($i = 0; $i < 6; $i++) {
								echo '<td style="padding: 5px !important; text-align: center; background-color: #fff;"><span style="opacity: 0;">'.($i+1).'</span></td>';
							}
						?>
					</tr>
				</table>
			</td>
			<td colspan="1" style="width: 20%;">
				<table border="1" style="border-collapse: collapse; margin-top: 0 !important; width: 100%;">
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Upgrading", "school-management");?></td></tr>
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Achieving", "school-management");?></td></tr>
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Advancing", "school-management");?></td></tr>
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Activating", "school-management");?></td></tr>
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Exploring", "school-management");?></td></tr>
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Developing", "school-management");?></td></tr>
					<tr><td border="0" style="text-align: left; padding: 5px;"><?php echo esc_html("Elementary", "school-management");?></td></tr>
				</table>
			</td>
			<td colspan="2" style="width: 40%;" style="padding: 10px !important;">
					<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('উপস্থিতির হার:..............................%', 'school-management'); ?></p>
					<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('শ্রেণী শিক্ষকের মন্তব্য:..........................................................................', 'school-management'); ?></p>
					<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
					<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
					<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 10px;">
				<h5 class="font-size-18 font-weight-500" style="text-align: left;"><?php echo esc_html("শিক্ষার্থীর মন্তব্য:", "school-management");?></h5>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('যে কাজটি সবচেয়ে ভালোভাবে করতে পেরেছি:', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('আরো উন্নতির জন্য যা যা করতে চাই:', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p><p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
			</td>
			<td></td>
			<td colspan="2" style="padding: 10px;">
				<h5 class="font-size-18 font-weight-500" style="text-align: left;"><?php echo esc_html("অভিভাবকের মন্তব্য:", "school-management");?></h5>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('যে কাজটি সবচেয়ে ভালোভাবে করতে পেরেছি:', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('আরো উন্নতির জন্য যা যা করতে চাই:', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p><p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
				<p style="text-align: left; font-size: 14px;"><?php echo esc_html__('..............................................................................................................................', 'school-management'); ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p style="padding-top: 20px; margin-bottom: 0px;"><?php echo esc_html__('...............................................', 'school-management');?></p>
				<p style="margin-bottom: 0px;"><?php echo esc_html__('শ্রেণী শিক্ষকের স্বাক্ষর', 'school-management');?></p>
				<p style="margin-bottom: 0px;"><?php echo esc_html__('তারিখ:', 'school-management');?></p>
			</td>
			<td>
				<p style="padding-top: 20px; margin-bottom: 0px;"><?php echo esc_html__('...............................................', 'school-management');?></p>
				<p style="margin-bottom: 0px;"><?php echo esc_html__('প্রধান শিক্ষকের স্বাক্ষর', 'school-management');?></p>
				<p style="margin-bottom: 0px;"><?php echo esc_html__('তারিখ:', 'school-management');?></p>
			</td>
			<td colspan="2">
				<p style="padding-top: 20px; margin-bottom: 0px;"><?php echo esc_html__('...............................................', 'school-management');?></p>
				<p style="margin-bottom: 0px;"><?php echo esc_html__('অভিভাবকের স্বাক্ষর', 'school-management');?></p>
				<p style="margin-bottom: 0px;"><?php echo esc_html__('তারিখ:', 'school-management');?></p>
			</td>
		</tr>
		
	<!-- </tbody> -->
	<?php
}



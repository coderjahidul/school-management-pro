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
			<div class="modal-body" id="transcript-content">
				<div class="student-info">
					<div class="student-info-one">
						<span><?php echo esc_html__("Assessment Types: " . $assessment_label);?></span>
						<br>
						<span><?php echo esc_html__('School Name: ' . $school_name); ?></span>
						<br>
						<span><?php echo esc_html__('Student Name: ' . $student_name); ?></span>
						<br>
						<span><?php echo esc_html__('Student Roll: ' . $student_roll); ?></span>
						
					</div>
					<div class="student-info-two">
						<span><?php echo esc_html__('Class: ' . $class_label); ?></span>
						<br>
						<span><?php echo esc_html__('Group: ' . $class_group); ?></span>
						<br>
						<span><?php echo esc_html__('Section: ' . $section_label); ?></span>
						<br>
						<span><?php echo esc_html__('Subject: ' . $subject_label); ?></span>
					</div>
				</div>
				<div class="result-assessment-title">
					<div class="transcript-lesson">
						<?php echo esc_html__('Proficiency index', 'school-management');?>
					</div>  
					<div class="transcript-result">
						<?php echo esc_html__('Level of proficiency', 'school-management');?>
					</div>
				</div>
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

								<div class="result-assessment">
									<div class="transcript-lesson">
										<p><?php echo $lesson_code . ' - ' . $lesson_title; ?></p>
									</div> 
									<?php 
										$new_curriculum_results = $wpdb->get_results($wpdb->prepare(
											"SELECT * FROM {$wpdb->prefix}wlsm_new_curriculum_results WHERE student_record_id = %d AND lecture_id = %d", $student_record_id, $lesson_id
										));
										foreach($new_curriculum_results as $new_curriculum_result) {
											$marks = $new_curriculum_result->new_curriculum_marks;
											?>
												<div class="transcript-result">
													<div class="square-description">
														<?php if($marks == "square"){?>
														<span class="square-icon active">&#9634;</span>
														<?php } else {
															?>
																<span class="square-icon">&#9634;</span>
															<?php
														}
														echo $square_des; ?>
													</div>
													<div class="circle-description">
													<?php if($marks == "circle"){?>
														<span class="circle-icon active">&#11096;</span>
														<?php } else {
															?>
																<span class="circle-icon">&#11096;</span>
															<?php
														}
														echo $circle_des; ?>
													</div>
													<div class="triangle-description">
													<?php if($marks == "triangle"){?>
														<span class="triangle-icon active">&#128710;</span>
														<?php } else {
															?>
																<span class="triangle-icon">&#128710;</span>
															<?php
														}
														echo $triangle_des; ?>
													</div>
												</div>
											<?php
										}
									?> 
									
								</div>

							<?php
						}
					}
					
				?>
						
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="print-transcript"><?php echo esc_html__('Print Transcript', 'school-management');?></button>
			</div>
		</div>
	</div><?php
}



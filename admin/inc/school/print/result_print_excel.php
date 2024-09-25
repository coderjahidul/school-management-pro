<?php
defined('ABSPATH') || die();



require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

if (isset($from_front)) {
	$print_button_classes = 'button btn-sm btn-success';
} else {
	$print_button_classes = 'btn btn-sm btn-success';
}

$grade_criteria = WLSM_Config::get_default_grade_criteria($exam->grade_criteria);

$enable_overall_grade = $grade_criteria['enable_overall_grade'];
$marks_grades = $grade_criteria['marks_grades'];
?>
<center class="excel_result_box" id="wlsm-print-result-excel">

	<?php

	global $wpdb;

	// a query to get class school id from exam_id
	// $query = $wpdb->prepare("SELECT class_school_id FROM {$wpdb->prefix}wlsm_class_school WHERE class_id = %d AND school_id = %d", $class_id, $school_id);
	
	$query = $wpdb->prepare(
		"
    SELECT cse.class_school_id 
    FROM {$wpdb->prefix}wlsm_class_school_exam AS cse
    JOIN {$wpdb->prefix}wlsm_class_school AS cs ON cs.ID= cse.class_school_id
    WHERE cs.class_id = %d AND cs.school_id = %d AND cse.exam_id = %d",
		$class_id,
		$school_id,
		$exam_id
	);

	$class_school_id = $wpdb->get_var($query);
	$class_label = $wpdb->get_results($wpdb->prepare("SELECT label FROM {$wpdb->prefix}wlsm_classes WHERE ID = %d", $class_id));
	$labelToInt = array(
		"One" => 1,
		"Two" => 2,
		"Three" => 3,
		"Four" => 4,
		"Five" => 5,
		"Six" => 6,
		"Seven" => 7,
		"Eight" => 8,
		"Nine" => 9,
		"Ten" => 10
	);
	foreach($class_label as $class){
		if(isset($labelToInt[$class->label])){
			$class_label_id = $labelToInt[$class->label];
		}
	}


	// a query to get all section ids from the class_school_id
	$query2 = $wpdb->prepare("SELECT ID FROM {$wpdb->prefix}wlsm_sections WHERE class_school_id = %d", $class_school_id);
	$section_ids = $wpdb->get_results($query2);

	// get the sections in an array using the selection of section
	$sections = [];
	if ($section_id == 0) {
		foreach ($section_ids as $single_section) {
			$sections[] = $single_section->ID;
		}
	} else {
		$sections[] = $section_id;
	}
	// a loop in the sections to get all the students from that section 
	$students = [];
	foreach ($sections as $section) {
		$admit_card_querys = $wpdb->get_results($wpdb->prepare("SELECT student_record_id FROM {$wpdb->prefix}wlsm_admit_cards WHERE exam_id = %d", $exam_id));
		
		foreach ($admit_card_querys as $key => $admit_card_query) {
			$student_id = $admit_card_query->student_record_id;
			$student_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wlsm_student_records WHERE section_id = %d AND note = %s AND ID = %d ORDER BY roll_number ASC", $section, $class_group, $student_id);
			
			$student_records = $wpdb->get_results($student_query);
			
			foreach ($student_records as $key => $single_student) {
				$students[$single_student->ID]["ID"] = $single_student->ID;
				$students[$single_student->ID]["name"] = $single_student->name;
				$students[$single_student->ID]["roll_number"] = $single_student->roll_number;
				$students[$single_student->ID]["religion"] = $single_student->religion;

				
			}
		}
	}

	// get all subjects related to the exam
	$exam_subjects = array();
	$subjects_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wlsm_exam_papers WHERE exam_id = %d", $exam_id);
	$subjects = $wpdb->get_results($subjects_query);

	$fresh_subjects = array();
	foreach ($subjects as $subject) {
		$code = $subject->paper_code;

		$subject_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wlsm_subjects WHERE code = %d AND  class_school_id = %d", $code, $class_school_id);
		$subject_array = $wpdb->get_results($subject_query);
		// $fresh_subjects[] = $subject_array[0];
		$unserializeData = unserialize($subject_array[0]->subject_group)['subject_group'];
		if (in_array($class_group, $unserializeData)) {
			$fresh_subjects[] = $subject_array[0];
		}

	}



	$filtered_subjects = array();
	$parent_subject_map = array();

	// Assuming you have retrieved the new subjects from the database into $new_subjects_array
	$new_subjects_array = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}wlsm_subjects"
		)
	);

	foreach ($fresh_subjects as $subject) {
		// Check if the subject_cat is "sub" and parent_subject is set
		if ($subject->subject_cat === 'sub' && isset($subject->parent_subject)) {
			$parent_subject = $subject->parent_subject;

			// If this parent_subject is not yet encountered, add it to the filtered array
			if (!isset($parent_subject_map[$parent_subject])) {
				$parent_subject_map[$parent_subject] = true;

				// Find the new subject from $new_subjects_array based on parent_subject ID
				foreach ($new_subjects_array as $new_subject) {
					if ($new_subject->ID == $parent_subject) {
						$filtered_subjects[] = $new_subject; // Replace with new subject
						break;
					}
				}
			}
		} else {
			// Keep non-"sub" subjects or those without a parent_subject as they are
			$filtered_subjects[] = $subject;
		}
	}

	$subjects = $filtered_subjects;

	usort($subjects, function ($a, $b) {
		$created_at_a = strtotime($a->created_at);
		$created_at_b = strtotime($b->created_at);

		if ($created_at_a === $created_at_b) {
			return 0;
		}

		return ($created_at_a > $created_at_b) ? -1 : 1;
	});

	$subjects = array_reverse($subjects);

	// a function to get the grade of the student
	function getGrade($number, $exam_id = 1)
	{
		global $wpdb;
		// get the grading system 
		$grading_system_query = $wpdb->prepare("SELECT grade_criteria FROM {$wpdb->prefix}wlsm_exams WHERE ID = %d", $exam_id);
		$grading_system = $wpdb->get_var($grading_system_query);

		$grading_data = unserialize($grading_system);
		foreach ($grading_data['marks_grades'] as $grade_data) {
			if ($number >= $grade_data['min'] && $number <= $grade_data['max']) {
				return $grade_data['grade'];
			}
		}
		return 'N/A'; // Return 'N/A' if no matching grade is found
	}

	// a function to get the gpa of the student
	// function calculateGPA($letter_grade) {
	// 	$grade_scale = array(
	// 		'A+' => 5.00,
	// 		'A'  => 4.00,
	// 		'A-' => 3.50,
	// 		'B'  => 3.00,
	// 		'C'  => 2.00,
	// 		'D'  => 1.00,
	// 		'F'  => 0.00
	// 	);
	
	// 	// Convert the input grade to uppercase to ensure case-insensitivity
	// 	$upper_case_grade = strtoupper($letter_grade);
	
	// 	// Check if the input grade exists in the grade scale
	// 	if (array_key_exists($upper_case_grade, $grade_scale)) {
	// 		return $grade_scale[$upper_case_grade];
	// 	} else {
	// 		return 'N/A'; // Return 'N/A' for invalid or unrecognized grades
	// 	}
	// }
	$type = "objective";
	$optional_paper_codes = $wpdb->get_results($wpdb->prepare(
		"SELECT code FROM {$wpdb->prefix}wlsm_subjects WHERE type = %s",
		$type
	)
	);
	$exam_papers = $wpdb->get_results($wpdb->prepare(
		"SELECT subject_label, subject_type, paper_code, maximum_marks, religion FROM {$wpdb->prefix}wlsm_exam_papers WHERE exam_id = %d",
		$exam_id
	));
	foreach ($students as $student):
		$thirdfiltered = array_filter($subjects);
		foreach ($thirdfiltered as $subject) {
			foreach ($exam_papers as $subject) {
				$subject_label = $subject->subject_label;
				$paper_code = $subject->paper_code;
				$subject_type = $subject->subject_type;
				$subject_maximum_marks = $subject->maximum_marks;
				$subject_religion = $subject->religion;


				// if ($subject_type === 'subjective' || $subject_type === 'objective') {
				$class_school_id = $wpdb->get_var($wpdb->prepare(
					"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
					$exam_id
				)
				);


				$exam_paper_id = $wpdb->get_var($wpdb->prepare(
					"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %s AND subject_label = %s AND subject_type = %s AND exam_id=%d",
					$paper_code,
					$subject_label,
					$subject_type,
					$exam_id
				)
				);
				$admit_card_query = $wpdb->prepare(
					"SELECT ID FROM {$wpdb->prefix}wlsm_admit_cards WHERE student_record_id = %d AND exam_id = %d",
					$student['ID'],
					$exam_id
				);
				$admit_card_id = $wpdb->get_var($admit_card_query);
				$get_obtained_marks = $wpdb->get_var($wpdb->prepare(
					"SELECT obtained_marks FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d",
					$exam_paper_id,
					$admit_card_id
				)
				);

				if ($paper_code == 102) {
				?>
				<!-- Bangla second cq marks-->
				<?php
				if ($subject_type == "subjective") { 
					$bangla_second_marks = $get_obtained_marks;
				} ?>
				<!-- Bangla second mcq marks-->
				<?php
				} elseif ($paper_code == 107) {
				

				} elseif ($paper_code == 108) {
				?>
				<!-- English second cq-->
				<?php
				if ($subject_type == "subjective") { ?>
					<?php
					$english_second_marks = $get_obtained_marks;
				} ?>

				<?php
				} 
			}

		}
	endforeach;
	?>

	<div class="table-container excel_result_box" style="width:100%;overflow-x: auto;" id="wlsm-print-result-excel">
		<table>
			<thead>
				<tr>
					<h4 style="text-align: center;margin:10px 0px">Class :
						<?php echo $class_label[0]->label; ?> & Section :
						<?php
						$section_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM {$wpdb->prefix}wlsm_sections WHERE class_school_id = %d AND ID =%d", $class_school_id, $section_id));
						$exam_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM {$wpdb->prefix}wlsm_exams WHERE ID = %d", $exam_id));
						if ($section_label == "") {
							echo "All";
							echo "<br>";
							echo $exam_label . "(" . $session_label . ")";
						} else {
							echo $section_label;
							echo "<br>";
							echo $exam_label . "(" . $session_label . ")";
						}
						?>
					</h4>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th rowspan="2">
						<?php esc_html_e("R.NO", "school-management"); ?>
					</th>
					<th rowspan="2">
						<?php esc_html_e("Student Name", "school-management"); ?>
					</th>
					<?php
					// $exam_papers = $wpdb->get_results($wpdb->prepare(
					// 	"SELECT subject_label, subject_type, paper_code, maximum_marks, religion FROM {$wpdb->prefix}wlsm_exam_papers WHERE exam_id = %d",
					// 	$exam_id
					// ));

					
						foreach ($exam_papers as $subject) {
							$subject_label = $subject->subject_label;
							$paper_code = $subject->paper_code;
							$subject_type = $subject->subject_type;
							$maximum_mark = $subject->maximum_marks;

							// Process or display subject details only for subjective and objective types
							if($class_label_id <= 5){
								$word = explode(' ', $subject_label);
								if ($paper_code == 101) {
									echo "<th colspan='4'> $word[0]</th>";
								} elseif ($paper_code == 102) {
	
								} elseif ($paper_code == 107) {
									echo "<th colspan='4'> $word[0]</th>";
								} elseif ($paper_code == 108) {
	
								} elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) {
									echo "<th colspan='4'> $subject_label</th>";
								} elseif ($subject_type == "objective") {
									echo "<th colspan='4'> $subject_label</th>";
								} else {
									echo "<th colspan='4'> $subject_label</th>";
								}
							}else{
								if ($subject_type === 'subjective' || $subject_type === 'objective') {
									$word = explode(' ', $subject_label);
									if($bangla_second_marks != null || $english_second_marks != null){
										if ($paper_code == 101) {
											echo "<th colspan='7'> $word[0]</th>";
										} elseif ($paper_code == 102) {
			
										} elseif ($paper_code == 107) {
											echo "<th colspan='5'> $word[0]</th>";
										} elseif ($paper_code == 108) {
			
										} elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) {
											echo "<th colspan='6'> $subject_label</th>";
										} elseif ($subject_type == "objective") {
											echo "<th colspan='6'> $subject_label</th>";
										} else {
											echo "<th colspan='5'> $subject_label</th>";
										}
									}else{
										if ($paper_code == 101) {
											echo "<th colspan='5'> $word[0]</th>";
										} elseif ($paper_code == 102) {
			
										} elseif ($paper_code == 107) {
											echo "<th colspan='4'> $word[0]</th>";
										} elseif ($paper_code == 108) {
			
										} elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) {
											echo "<th colspan='6'> $subject_label</th>";
										} elseif ($subject_type == "objective") {
											echo "<th colspan='6'> $subject_label</th>";
										} else {
											echo "<th colspan='5'> $subject_label</th>";
										}
									}
								}
							}
							


						}

					?>

					<th rowspan="2">
						<?php esc_html_e("Total Mark", "school-management") ?>
					</th>
					<th rowspan="2">
						<?php esc_html_e("LG Without Ad. Sub", "school-management") ?>
					</th>
					<th rowspan="2">
						<?php esc_html_e("CGPA Without Ad. Sub", "school-management") ?>
					</th>
					<th rowspan="2">
						<?php esc_html_e("LG", "school-management") ?>
					</th>
					<th rowspan="2">
						<?php esc_html_e("CGPA", "school-management") ?>
					</th>
				</tr>
				<tr>
					<?php
					foreach ($exam_papers as $subject) {
						$subject_label = $subject->subject_label;
						$paper_code = $subject->paper_code;
						$subject_type = $subject->subject_type;

						// Check if this subject has been processed
						if($class_label_id <= 5){
							if ($subject_type === 'subjective' || $subject_type === 'objective') {
								if ($paper_code == 101) { ?>
									<!-- Bangla -->
									<th>
										<?php esc_html_e("Mark", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("Total", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("LG", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("GP", "school-management") ?>
									</th>
								<?php } elseif ($paper_code == 102) { ?>
									<!-- Nathing -->
								<?php } elseif ($paper_code == 107) { ?>
									<!-- English -->
									<th>
										<?php esc_html_e("Mark", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("Total", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("LG", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("GP", "school-management") ?>
									</th>
								<?php } elseif ($paper_code == 108) { ?>
									<!-- Nathing -->
								<?php } elseif ($subject_type == "objective") { ?>
									<!-- other subject -->
									<th>
										<?php esc_html_e("Mark", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("Total", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("LG", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("GP", "school-management") ?>
									</th>
								<?php } else { ?>
									<!-- other subject -->
									<th>
										<?php esc_html_e("Mark", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("Total", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("LG", "school-management") ?>
									</th>
									<th>
										<?php esc_html_e("GP", "school-management") ?>
									</th>
								<?php }
							}
						}else{
							if($bangla_second_marks != null || $english_second_marks != null){
								if ($subject_type === 'subjective' || $subject_type === 'objective') {
									if ($paper_code == 101) { ?>
										<!-- Bangla -->
										<th>
											<?php esc_html_e("1st paper (CQ)", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("1st Paper (MCQ)", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("2nd Paper (CQ)", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("2nd Paper (MCQ)", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } elseif ($paper_code == 102) { ?>
										<!-- Nathing -->
									<?php } elseif ($paper_code == 107) { ?>
										<!-- English -->
										<th>
											<?php esc_html_e("1st paper (CQ)", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("2nd Paper (CQ)", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } elseif ($paper_code == 108) { ?>
										<!-- Nathing -->
									<?php } elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) { ?>
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Practical", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } elseif ($subject_type == "objective") { ?>
										<!-- other subject -->
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Practical", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } else { ?>
										<!-- other subject -->
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php }
								}
							}else{
								if ($subject_type === 'subjective' || $subject_type === 'objective') {
									if ($paper_code == 101) { ?>
										<!-- Bangla -->
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } elseif ($paper_code == 102) { ?>
										<!-- Nathing -->
									<?php } elseif ($paper_code == 107) { ?>
										<!-- English -->
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } elseif ($paper_code == 108) { ?>
										<!-- Nathing -->
									<?php } elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) { ?>
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Practical", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } elseif ($subject_type == "objective") { ?>
										<!-- other subject -->
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Practical", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php } else { ?>
										<!-- other subject -->
										<th>
											<?php esc_html_e("CQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("MCQ", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("Total", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("LG", "school-management") ?>
										</th>
										<th>
											<?php esc_html_e("GP", "school-management") ?>
										</th>
									<?php }
								}
							}
						}
						
						
					}
					?>

				</tr>
				<?php

				foreach ($students as $student): ?>
					<?php
					$letter_grade_bangla_fail = 0;
					$total_failde_subject = 0;
					$count_letter_grade_bangla_f = 0;
					$count_letter_grade_english_f = 0;
					$count_group_subject_letter_grade_f = 0;
					$count_other_subject_letter_grade_f = 0;

					$totla_group_subject_maxmarks = 0;
					$total_other_subject_maxmarks = 0;

					$group_subject_total_marks = 0;
					$other_subject_total_marks = 0;
					?>
					<tr>
						<td>
							<?php echo $student['roll_number']; ?>
						</td>
						<td>
							<?php echo $student['name']; ?>
						</td>
						<?php
						$thirdfilteredArray = array_filter($subjects);
						if($class_label_id <= 5){
							foreach ($thirdfilteredArray as $subject) {
								foreach ($exam_papers as $subject) {
									$subject_label = $subject->subject_label;
									$paper_code = $subject->paper_code;
									$subject_type = $subject->subject_type;
									$subject_maximum_marks = $subject->maximum_marks;
									$subject_religion = $subject->religion;
	
	
									// if ($subject_type === 'subjective' || $subject_type === 'objective') {
									$class_school_id = $wpdb->get_var($wpdb->prepare(
										"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
										$exam_id
									)
									);
	
	
									$exam_paper_id = $wpdb->get_var($wpdb->prepare(
										"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %s AND subject_label = %s AND subject_type = %s AND exam_id=%d",
										$paper_code,
										$subject_label,
										$subject_type,
										$exam_id
									)
									);
									$admit_card_query = $wpdb->prepare(
										"SELECT ID FROM {$wpdb->prefix}wlsm_admit_cards WHERE student_record_id = %d AND exam_id = %d",
										$student['ID'],
										$exam_id
									);
									$admit_card_id = $wpdb->get_var($admit_card_query);
									$get_obtained_marks = $wpdb->get_var($wpdb->prepare(
										"SELECT obtained_marks FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d",
										$exam_paper_id,
										$admit_card_id
									)
									);
	
									if ($paper_code == 101) { ?>
										<!-- Bangla first cq marks-->
										<?php
										if ($subject_type == "subjective") { ?>
											<td>
												<?php
												$bangla_first_cq_marks = $get_obtained_marks;
												$bangla_first_cq_maxmarks = $subject_maximum_marks;
												echo esc_html($bangla_first_cq_marks);
	
												?>
											</td>
											<td>
												<?php
												$bangla_total_cq_marks = $bangla_first_cq_marks;
												$bangla_total_mcq_marks = $bangla_first_mcq_marks;
												$total_bangla_mark = $bangla_total_cq_marks + $bangla_total_mcq_marks;
												echo $total_bangla_mark;
												?>
											</td>
											<td>
												<?php
												$bangla_total_cq_maxmarks = $bangla_first_cq_maxmarks;
												$bangla_subject_maxmarks = $bangla_total_cq_maxmarks;
	
												$minimam_cq_fash_marks = floor($bangla_total_cq_maxmarks / 3);
	
												$divide_bangla_mark = $total_bangla_mark / 1;
												if ($bangla_first_cq_marks >= 1) {
													if ($bangla_total_cq_marks >= $minimam_cq_fash_marks) {
														$letter_grade_bangla = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_bangla_mark));
														echo $letter_grade_bangla;
	
													} else {
														echo $letter_grade_bangla = "F";
													}
												} else {
													echo $letter_grade_bangla = "F";
												}
												if ($letter_grade_bangla === 'F') {
													$count_letter_grade_bangla_f++;
												}
												?>
											</td>
											<td>
												<?php
												if ($bangla_first_cq_marks >= 1) {
													if ($bangla_total_cq_marks >= $minimam_cq_fash_marks) {
														echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_bangla), 2);
													} else {
														echo "0.00";
													}
												} else {
													echo "0.00";
												}
	
												?>
											</td>
										<?php }
									} elseif ($paper_code == 107) {
										?>
										<!-- English first cq-->
										<?php
										if ($subject_type == "subjective") { ?>
											<td>
												<?php
												$english_first_cq_marks = $get_obtained_marks;
												$english_first_cq_maxmarks = $subject_maximum_marks;
												echo esc_html($english_first_cq_marks);
												?>
											</td>
											<td>
												<?php
												$english_total_marks = $english_first_cq_marks;
												echo $english_total_marks;
												?>
											</td>
											<td>
												<?php
												$english_subject_maxmarks = $english_first_cq_maxmarks;
												$divide_english_mark = $english_total_marks / 1;
												if ($english_first_cq_marks >= 1) {
													$letter_grade_english = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_english_mark));
													echo $letter_grade_english;
												} else {
													echo $letter_grade_english = "F";
												}
												if ($letter_grade_english === 'F') {
													$count_letter_grade_english_f++;
												}
												?>
											</td>
											<td>
												<?php
												if ($english_first_cq_marks >= 1) {
													echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_english), 2);
												} else {
													echo "0.00";
												}
												?>
											</td>
										<?php }
	
									} else {
										?>
										<!-- other subject cq -->
										<?php
										if ($subject_type == "subjective") { ?>
											<td>
												<?php
												$other_subject_cq_marks = $get_obtained_marks;
												$other_subject_cq_maxmarks = $subject_maximum_marks;
												echo esc_html($other_subject_cq_marks);
												?>
											</td>
											<td>
												<?php
												if ($other_subject_cq_marks >= 1) {
													$other_subject_marks = $other_subject_cq_marks;
													echo $other_subject_marks;
													$other_subject_total_marks += $other_subject_marks;
												} else {
													// Nathing	
												}
												?>
											</td>
											<td>
												<?php
	
												if ($other_subject_cq_marks >= 1) {
													$other_subject_maximam_marks = $other_subject_cq_maxmarks;
													$total_other_subject_maxmarks += $other_subject_maximam_marks;
													$grades_percentage = 100 / $other_subject_maximam_marks;
													$other_subject_marks *= $grades_percentage;
	
													$minimam_other_subject_cq_fash_mark = floor($other_subject_cq_maxmarks / 3);
	
													if ($other_subject_cq_marks >= $minimam_other_subject_cq_fash_mark) {
														$other_subject_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $other_subject_marks));
														echo $other_subject_letter_grade;
													} else {
														echo $other_subject_letter_grade = "F";
													}
	
													if ($other_subject_letter_grade === 'F') {
														$count_other_subject_letter_grade_f++;
													}
												} else {
													// Nathing	
												}
												?>
											</td>
											<td>
												<?php
												if ($other_subject_cq_marks >= 1) {
													if ($other_subject_cq_marks >= $minimam_other_subject_cq_fash_mark) {
														echo number_format(WLSM_M_Setting::calculateGPA($other_subject_letter_grade), 2);
													} else {
														echo "0.00";
													}
												} else {
													// Nathing	
												}
												?>
											</td>
										<?php }
									}
	
	
									// }
						
								}
	
								break;
							}
						}else{
							if($bangla_second_marks !=null || $english_second_marks != null){
								foreach ($thirdfilteredArray as $subject) {
									foreach ($exam_papers as $subject) {
										$subject_label = $subject->subject_label;
										$paper_code = $subject->paper_code;
										$subject_type = $subject->subject_type;
										$subject_maximum_marks = $subject->maximum_marks;
										$subject_religion = $subject->religion;
		
		
										// if ($subject_type === 'subjective' || $subject_type === 'objective') {
										$class_school_id = $wpdb->get_var($wpdb->prepare(
											"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
											$exam_id
										)
										);
		
		
										$exam_paper_id = $wpdb->get_var($wpdb->prepare(
											"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %s AND subject_label = %s AND subject_type = %s AND exam_id=%d",
											$paper_code,
											$subject_label,
											$subject_type,
											$exam_id
										)
										);
										$admit_card_query = $wpdb->prepare(
											"SELECT ID FROM {$wpdb->prefix}wlsm_admit_cards WHERE student_record_id = %d AND exam_id = %d",
											$student['ID'],
											$exam_id
										);
										$admit_card_id = $wpdb->get_var($admit_card_query);
										$get_obtained_marks = $wpdb->get_var($wpdb->prepare(
											"SELECT obtained_marks FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d",
											$exam_paper_id,
											$admit_card_id
										)
										);
		
										if ($paper_code == 101) { ?>
											<!-- Bangla first cq marks-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$bangla_first_cq_marks = $get_obtained_marks;
													$bangla_first_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($bangla_first_cq_marks);
		
													?>
												</td>
											<?php } ?>
											<!-- Bangla first cq marks-->
											<?php
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$bangla_first_mcq_marks = $get_obtained_marks;
													$bangla_first_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($bangla_first_mcq_marks);
													?>
												</td>
											<?php } ?>
											<?php
										} elseif ($paper_code == 102) {
											?>
											<!-- Bangla second cq marks-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$bangla_second_cq_marks = $get_obtained_marks;
													$bangla_second_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($bangla_second_cq_marks);
													?>
												</td>
											<?php } else {
											} ?>
											<!-- Bangla second mcq marks-->
											<?php
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$bangla_second_mcq_marks = $get_obtained_marks;
													$bangla_second_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($bangla_second_mcq_marks);
													?>
												</td>
												<td>
													<?php
													$bangla_total_cq_marks = $bangla_first_cq_marks + $bangla_second_cq_marks;
													$bangla_total_mcq_marks = $bangla_first_mcq_marks + $bangla_second_mcq_marks;
													$total_bangla_mark = $bangla_total_cq_marks + $bangla_total_mcq_marks;
													echo $total_bangla_mark;
													?>
												</td>
												<td>
													<?php
													$bangla_total_cq_maxmarks = $bangla_first_cq_maxmarks + $bangla_second_cq_maxmarks;
													$bangla_total_mcq_maxmarks = $bangla_first_mcq_maxmarks + $bangla_second_mcq_maxmarks;
													$bangla_subject_maxmarks = $bangla_total_cq_maxmarks + $bangla_total_mcq_maxmarks;
		
													$minimam_cq_fash_marks = floor($bangla_total_cq_maxmarks / 3);
													$minimam_mcq_fash_marks = floor($bangla_total_mcq_maxmarks / 3);
		
													$divide_bangla_mark = floor($total_bangla_mark / 2);
													if ($bangla_first_cq_marks >= 1 && $bangla_second_cq_marks >= 1 && $bangla_first_mcq_marks >= 1 && $bangla_second_mcq_marks >= 1) {
														if ($bangla_total_cq_marks >= $minimam_cq_fash_marks && $bangla_total_mcq_marks >= $minimam_mcq_fash_marks) {
															$letter_grade_bangla = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_bangla_mark));
															echo $letter_grade_bangla;
		
														} else {
															echo $letter_grade_bangla = "F";
														}
													} else {
														echo $letter_grade_bangla = "F";
													}
													if ($letter_grade_bangla === 'F') {
														$count_letter_grade_bangla_f++;
													}
													?>
												</td>
												<td>
													<?php
													if ($bangla_first_cq_marks >= 1 && $bangla_second_cq_marks >= 1 && $bangla_first_mcq_marks >= 1 && $bangla_second_mcq_marks >= 1) {
														if ($bangla_total_cq_marks >= $minimam_cq_fash_marks && $bangla_total_mcq_marks >= $minimam_mcq_fash_marks) {
															echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_bangla), 2);
														} else {
															echo "0.00";
														}
													} else {
														echo "0.00";
													}
		
													?>
												</td>
											<?php } else {
											} ?>
		
											<?php
										} elseif ($paper_code == 107) {
											?>
											<!-- English first cq-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$english_first_cq_marks = $get_obtained_marks;
													$english_first_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($english_first_cq_marks);
													?>
												</td>
											<?php }
		
										} elseif ($paper_code == 108) {
											?>
											<!-- English second cq-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$english_second_cq_marks = $get_obtained_marks;
													$english_second_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($english_second_cq_marks);
													?>
												</td>
												<td>
													<?php
													$english_total_marks = $english_first_cq_marks + $english_second_cq_marks;
													echo $english_total_marks;
													?>
												</td>
												<td>
													<?php
													$english_subject_maxmarks = $english_first_cq_maxmarks + $english_second_cq_maxmarks;
													$divide_english_mark = floor($english_total_marks / 2);
													if ($english_first_cq_marks >= 1 && $english_second_cq_marks >= 1) {
														$letter_grade_english = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_english_mark));
														echo $letter_grade_english;
													} else {
														echo $letter_grade_english = "F";
													}
													if ($letter_grade_english === 'F') {
														$count_letter_grade_english_f++;
													}
													?>
												</td>
												<td>
													<?php
													if ($english_first_cq_marks >= 1 && $english_second_cq_marks >= 1) {
														echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_english), 2);
													} else {
														echo "0.00";
													}
													?>
												</td>
											<?php } ?>
		
										<?php
										} elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) {
											// group subject
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$group_subject_cq_marks = $get_obtained_marks;
													$group_subject_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($group_subject_cq_marks);
													?>
												</td>
												<?php
											}
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$group_subject_mcq_marks = $get_obtained_marks;
													$group_subject_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($group_subject_mcq_marks);
													?>
												</td>
												<?php
											}
											if ($subject_type == "practical") { ?>
												<td>
													<?php
													$group_subject_practical_marks = $get_obtained_marks;
													$group_subject_practical_maxmarks = $subject_maximum_marks;
													echo esc_html($group_subject_practical_marks);
													?>
												</td>
												<td>
													<?php
													if ($group_subject_cq_marks >= 1 || $group_subject_mcq_marks >= 1 || $group_subject_practical_marks >= 1) {
														$group_subject_marks = $group_subject_cq_marks + $group_subject_mcq_marks + $group_subject_practical_marks;
														echo $group_subject_marks;
														$group_subject_total_marks += $group_subject_marks;
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
		
													if ($group_subject_cq_marks >= 1 || $group_subject_mcq_marks >= 1 || $group_subject_practical_marks >= 1) {
														$group_subject_maximam_marks = $group_subject_cq_maxmarks + $group_subject_mcq_maxmarks + $group_subject_practical_maxmarks;
														$totla_group_subject_maxmarks += $group_subject_maximam_marks;
														$grades_percentage = 100 / $group_subject_maximam_marks;
														$group_subject_marks *= $grades_percentage;
		
														$minimam_group_subject_cq_fash_mark = floor($group_subject_cq_maxmarks / 3);
														$minimam_group_subject_mcq_fash_mark = floor($group_subject_mcq_maxmarks / 3);
														$minimam_group_subject_practical_fash_mark = floor($group_subject_practical_maxmarks / 3);
		
														if ($group_subject_cq_marks >= $minimam_group_subject_cq_fash_mark && $group_subject_mcq_marks >= $minimam_group_subject_mcq_fash_mark && $group_subject_practical_marks >= $minimam_group_subject_practical_fash_mark) {
															$group_subject_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $group_subject_marks));
															echo $group_subject_letter_grade;
														} else {
															echo $group_subject_letter_grade = "F";
														}
		
														if ($group_subject_letter_grade === 'F') {
															$count_group_subject_letter_grade_f++;
														}
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
													if ($group_subject_cq_marks >= 1 || $group_subject_mcq_marks >= 1 || $group_subject_practical_marks >= 1) {
														if ($group_subject_cq_marks >= $minimam_group_subject_cq_fash_mark && $group_subject_mcq_marks >= $minimam_group_subject_mcq_fash_mark && $group_subject_practical_marks >= $minimam_group_subject_practical_fash_mark) {
															echo number_format(WLSM_M_Setting::calculateGPA($group_subject_letter_grade), 2);
														} else {
															echo "0.00";
														}
													} else {
														//Nathing
													}
													?>
												</td>
												<?php
											}
										} elseif ($paper_code == "126" || $paper_code == "134") {
											// objective subject cq
											if ($subject_type == "objective") { ?>
												<td>
													<?php
													$objective_cq_marks = $get_obtained_marks;
													$objective_cq_maxmarks = $subject_maximum_marks;
													if ($objective_cq_marks >= 1) {
														echo esc_html($objective_cq_marks);
													} {
														//Nathing
													}
													?>
												</td>
											<?php }
											// objective subject mcq
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$objective_mcq_marks = $get_obtained_marks;
													$objective_mcq_maxmarks = $subject_maximum_marks;
													if ($objective_mcq_marks >= 1) {
														echo esc_html($objective_mcq_marks);
													} {
														//Nathing
													}
													?>
												</td>
											<?php }
											// objective subject practical
											if ($subject_type == "practical") { ?>
												<td>
													<?php
													$objective_practical_marks = $get_obtained_marks;
													$objective_practical_maxmarks = $subject_maximum_marks;
													if ($objective_practical_marks >= 1) {
														echo esc_html($objective_practical_marks);
													} {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
													if ($objective_cq_marks >= 1 || $objective_mcq_marks >= 1 || $objective_practical_marks >= 1) {
														$objective_marks = $objective_cq_marks + $objective_mcq_marks + $objective_practical_marks;
														echo $objective_marks;
														$objective_total_marks = $objective_marks;
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
		
													if ($objective_cq_marks >= 1 || $objective_mcq_marks >= 1 || $objective_practical_marks >= 1) {
														$objective_maximam_marks = $objective_cq_maxmarks + $objective_mcq_maxmarks + $objective_practical_maxmarks;
														$grades_percentage = 100 / $objective_maximam_marks;
														$objective_marks *= $grades_percentage;
		
														$minimam_objective_cq_fash_mark = floor($objective_cq_maxmarks / 3);
														$minimam_objective_mcq_fash_mark = floor($objective_mcq_maxmarks / 3);
														$minimam_objective_practical_fash_mark = floor($objective_practical_maxmarks / 3);
		
														if ($objective_cq_marks >= $minimam_objective_cq_fash_mark && $objective_mcq_marks >= $minimam_objective_mcq_fash_mark && $objective_practical_marks >= $minimam_objective_practical_fash_mark) {
															$objective_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $objective_marks));
															echo $objective_letter_grade;
														} else {
															echo $objective_letter_grade = "F";
														}
		
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
													if ($objective_cq_marks >= 1 || $objective_mcq_marks >= 1 || $objective_practical_marks >= 1) {
														if ($objective_cq_marks >= $minimam_objective_cq_fash_mark && $objective_mcq_marks >= $minimam_objective_mcq_fash_mark && $objective_practical_marks >= $minimam_objective_practical_fash_mark) {
															echo number_format(WLSM_M_Setting::optional_calculateGPA($objective_letter_grade), 2);
														} else {
															echo "0.00";
														}
													} else {
														//Nathing
													}
													?>
												</td>
											<?php }
										} else {
											?>
											<!-- other subject cq -->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$other_subject_cq_marks = $get_obtained_marks;
													$other_subject_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($other_subject_cq_marks);
													?>
												</td>
											<?php }
											// Other subject mcq
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$other_subject_mcq_marks = $get_obtained_marks;
													$other_subject_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($other_subject_mcq_marks);
													?>
												</td>
												<td>
													<?php
													if ($other_subject_cq_marks >= 1 || $other_subject_mcq_marks >= 1) {
														$other_subject_marks = $other_subject_cq_marks + $other_subject_mcq_marks;
														echo $other_subject_marks;
														$other_subject_total_marks += $other_subject_marks;
													} else {
														// Nathing	
													}
													?>
												</td>
												<td>
													<?php
		
													if ($other_subject_cq_marks >= 1 || $other_subject_mcq_marks >= 1) {
														$other_subject_maximam_marks = $other_subject_cq_maxmarks + $other_subject_mcq_maxmarks;
														$total_other_subject_maxmarks += $other_subject_maximam_marks;
														$grades_percentage = 100 / $other_subject_maximam_marks;
														$other_subject_marks *= $grades_percentage;
		
														$minimam_other_subject_cq_fash_mark = floor($other_subject_cq_maxmarks / 3);
														$minimam_other_subject_mcq_fash_mark = floor($other_subject_mcq_maxmarks / 3);
		
														if ($other_subject_cq_marks >= $minimam_other_subject_cq_fash_mark && $other_subject_mcq_marks >= $minimam_other_subject_mcq_fash_mark) {
															$other_subject_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $other_subject_marks));
															echo $other_subject_letter_grade;
														} else {
															echo $other_subject_letter_grade = "F";
														}
		
														if ($other_subject_letter_grade === 'F') {
															$count_other_subject_letter_grade_f++;
														}
													} else {
														// Nathing	
													}
													?>
												</td>
												<td>
													<?php
													if ($other_subject_cq_marks >= 1 || $other_subject_mcq_marks >= 1) {
														if ($other_subject_cq_marks >= $minimam_other_subject_cq_fash_mark && $other_subject_mcq_marks >= $minimam_other_subject_mcq_fash_mark) {
															echo number_format(WLSM_M_Setting::calculateGPA($other_subject_letter_grade), 2);
														} else {
															echo "0.00";
														}
													} else {
														// Nathing	
													}
													?>
												</td>
											<?php }
										}
		
		
										// }
							
									}
		
									break;
								}
							}else{
								foreach ($thirdfilteredArray as $subject) {
									foreach ($exam_papers as $subject) {
										$subject_label = $subject->subject_label;
										$paper_code = $subject->paper_code;
										$subject_type = $subject->subject_type;
										$subject_maximum_marks = $subject->maximum_marks;
										$subject_religion = $subject->religion;
		
		
										// if ($subject_type === 'subjective' || $subject_type === 'objective') {
										$class_school_id = $wpdb->get_var($wpdb->prepare(
											"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
											$exam_id
										)
										);
		
		
										$exam_paper_id = $wpdb->get_var($wpdb->prepare(
											"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %s AND subject_label = %s AND subject_type = %s AND exam_id=%d",
											$paper_code,
											$subject_label,
											$subject_type,
											$exam_id
										)
										);
										$admit_card_query = $wpdb->prepare(
											"SELECT ID FROM {$wpdb->prefix}wlsm_admit_cards WHERE student_record_id = %d AND exam_id = %d",
											$student['ID'],
											$exam_id
										);
										$admit_card_id = $wpdb->get_var($admit_card_query);
										$get_obtained_marks = $wpdb->get_var($wpdb->prepare(
											"SELECT obtained_marks FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d",
											$exam_paper_id,
											$admit_card_id
										)
										);
		
										if ($paper_code == 101) { ?>
											<!-- Bangla first cq marks-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$bangla_first_cq_marks = $get_obtained_marks;
													$bangla_first_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($bangla_first_cq_marks);
		
													?>
												</td>
											<?php } ?>
											<!-- Bangla first cq marks-->
											<?php
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$bangla_first_mcq_marks = $get_obtained_marks;
													$bangla_first_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($bangla_first_mcq_marks);
													?>
												</td>
												<td>
													<?php
													$bangla_total_cq_marks = $bangla_first_cq_marks;
													$bangla_total_mcq_marks = $bangla_first_mcq_marks;
													$total_bangla_mark = $bangla_total_cq_marks + $bangla_total_mcq_marks;
													echo $total_bangla_mark;
													?>
												</td>
												<td>
													<?php
													$bangla_total_cq_maxmarks = $bangla_first_cq_maxmarks;
													$bangla_total_mcq_maxmarks = $bangla_first_mcq_maxmarks;
													$bangla_subject_maxmarks = $bangla_total_cq_maxmarks + $bangla_total_mcq_maxmarks;
		
													$minimam_cq_fash_marks = floor($bangla_total_cq_maxmarks / 3);
													$minimam_mcq_fash_marks = floor($bangla_total_mcq_maxmarks / 3);
		
													$divide_bangla_mark = $total_bangla_mark / 1;
													if ($bangla_first_cq_marks >= 1 && $bangla_first_mcq_marks >= 1) {
														if ($bangla_total_cq_marks >= $minimam_cq_fash_marks && $bangla_total_mcq_marks >= $minimam_mcq_fash_marks) {
															$letter_grade_bangla = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_bangla_mark));
															echo $letter_grade_bangla;
		
														} else {
															echo $letter_grade_bangla = "F";
														}
													} else {
														echo $letter_grade_bangla = "F";
													}
													if ($letter_grade_bangla === 'F') {
														$count_letter_grade_bangla_f++;
													}
													?>
												</td>
												<td>
													<?php
													if ($bangla_first_cq_marks >= 1 && $bangla_first_mcq_marks >= 1) {
														if ($bangla_total_cq_marks >= $minimam_cq_fash_marks && $bangla_total_mcq_marks >= $minimam_mcq_fash_marks) {
															echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_bangla), 2);
														} else {
															echo "0.00";
														}
													} else {
														echo "0.00";
													}
		
													?>
												</td>
											<?php } ?>
											<?php
										} elseif ($paper_code == 107) {
											?>
											<!-- English first cq-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$english_first_cq_marks = $get_obtained_marks;
													$english_first_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($english_first_cq_marks);
													?>
												</td>
												<td>
													<?php
													$english_total_marks = $english_first_cq_marks;
													echo $english_total_marks;
													?>
												</td>
												<td>
													<?php
													$english_subject_maxmarks = $english_first_cq_maxmarks;
													$divide_english_mark = $english_total_marks / 1;
													if ($english_first_cq_marks >= 1) {
														$letter_grade_english = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_english_mark));
														echo $letter_grade_english;
													} else {
														echo $letter_grade_english = "F";
													}
													if ($letter_grade_english === 'F') {
														$count_letter_grade_english_f++;
													}
													?>
												</td>
												<td>
													<?php
													if ($english_first_cq_marks >= 1) {
														echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_english), 2);
													} else {
														echo "0.00";
													}
													?>
												</td>
											<?php }
		
										} elseif ($paper_code == 108) {
											?>
											<!-- English second cq-->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$english_second_cq_marks = $get_obtained_marks;
													$english_second_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($english_second_cq_marks);
													?>
												</td>
												<td>
													<?php
													$english_total_marks = $english_first_cq_marks + $english_second_cq_marks;
													echo $english_total_marks;
													?>
												</td>
												<td>
													<?php
													$english_subject_maxmarks = $english_first_cq_maxmarks + $english_second_cq_maxmarks;
													$divide_english_mark = floor($english_total_marks / 2);
													if ($english_first_cq_marks >= 1 && $english_second_cq_marks >= 1) {
														$letter_grade_english = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_english_mark));
														echo $letter_grade_english;
													} else {
														echo $letter_grade_english = "F";
													}
													if ($letter_grade_english === 'F') {
														$count_letter_grade_english_f++;
													}
													?>
												</td>
												<td>
													<?php
													if ($english_first_cq_marks >= 1 && $english_second_cq_marks >= 1) {
														echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_english), 2);
													} else {
														echo "0.00";
													}
													?>
												</td>
											<?php } ?>
		
										<?php
										} elseif ($paper_code == 136 || $paper_code == 137 || $paper_code == 138) {
											// group subject
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$group_subject_cq_marks = $get_obtained_marks;
													$group_subject_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($group_subject_cq_marks);
													?>
												</td>
												<?php
											}
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$group_subject_mcq_marks = $get_obtained_marks;
													$group_subject_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($group_subject_mcq_marks);
													?>
												</td>
												<?php
											}
											if ($subject_type == "practical") { ?>
												<td>
													<?php
													$group_subject_practical_marks = $get_obtained_marks;
													$group_subject_practical_maxmarks = $subject_maximum_marks;
													echo esc_html($group_subject_practical_marks);
													?>
												</td>
												<td>
													<?php
													if ($group_subject_cq_marks >= 1 || $group_subject_mcq_marks >= 1 || $group_subject_practical_marks >= 1) {
														$group_subject_marks = $group_subject_cq_marks + $group_subject_mcq_marks + $group_subject_practical_marks;
														echo $group_subject_marks;
														$group_subject_total_marks += $group_subject_marks;
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
		
													if ($group_subject_cq_marks >= 1 || $group_subject_mcq_marks >= 1 || $group_subject_practical_marks >= 1) {
														$group_subject_maximam_marks = $group_subject_cq_maxmarks + $group_subject_mcq_maxmarks + $group_subject_practical_maxmarks;
														$totla_group_subject_maxmarks += $group_subject_maximam_marks;
														$grades_percentage = 100 / $group_subject_maximam_marks;
														$group_subject_marks *= $grades_percentage;
		
														$minimam_group_subject_cq_fash_mark = floor($group_subject_cq_maxmarks / 3);
														$minimam_group_subject_mcq_fash_mark = floor($group_subject_mcq_maxmarks / 3);
														$minimam_group_subject_practical_fash_mark = floor($group_subject_practical_maxmarks / 3);
		
														if ($group_subject_cq_marks >= $minimam_group_subject_cq_fash_mark && $group_subject_mcq_marks >= $minimam_group_subject_mcq_fash_mark && $group_subject_practical_marks >= $minimam_group_subject_practical_fash_mark) {
															$group_subject_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $group_subject_marks));
															echo $group_subject_letter_grade;
														} else {
															echo $group_subject_letter_grade = "F";
														}
		
														if ($group_subject_letter_grade === 'F') {
															$count_group_subject_letter_grade_f++;
														}
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
													if ($group_subject_cq_marks >= 1 || $group_subject_mcq_marks >= 1 || $group_subject_practical_marks >= 1) {
														if ($group_subject_cq_marks >= $minimam_group_subject_cq_fash_mark && $group_subject_mcq_marks >= $minimam_group_subject_mcq_fash_mark && $group_subject_practical_marks >= $minimam_group_subject_practical_fash_mark) {
															echo number_format(WLSM_M_Setting::calculateGPA($group_subject_letter_grade), 2);
														} else {
															echo "0.00";
														}
													} else {
														//Nathing
													}
													?>
												</td>
												<?php
											}
										} elseif ($paper_code == "126" || $paper_code == "134") {
											// objective subject cq
											if ($subject_type == "objective") { ?>
												<td>
													<?php
													$objective_cq_marks = $get_obtained_marks;
													$objective_cq_maxmarks = $subject_maximum_marks;
													if ($objective_cq_marks >= 1) {
														echo esc_html($objective_cq_marks);
													} {
														//Nathing
													}
													?>
												</td>
											<?php }
											// objective subject mcq
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$objective_mcq_marks = $get_obtained_marks;
													$objective_mcq_maxmarks = $subject_maximum_marks;
													if ($objective_mcq_marks >= 1) {
														echo esc_html($objective_mcq_marks);
													} {
														//Nathing
													}
													?>
												</td>
											<?php }
											// objective subject practical
											if ($subject_type == "practical") { ?>
												<td>
													<?php
													$objective_practical_marks = $get_obtained_marks;
													$objective_practical_maxmarks = $subject_maximum_marks;
													if ($objective_practical_marks >= 1) {
														echo esc_html($objective_practical_marks);
													} {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
													if ($objective_cq_marks >= 1 || $objective_mcq_marks >= 1 || $objective_practical_marks >= 1) {
														$objective_marks = $objective_cq_marks + $objective_mcq_marks + $objective_practical_marks;
														echo $objective_marks;
														$objective_total_marks = $objective_marks;
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
		
													if ($objective_cq_marks >= 1 || $objective_mcq_marks >= 1 || $objective_practical_marks >= 1) {
														$objective_maximam_marks = $objective_cq_maxmarks + $objective_mcq_maxmarks + $objective_practical_maxmarks;
														$grades_percentage = 100 / $objective_maximam_marks;
														$objective_marks *= $grades_percentage;
		
														$minimam_objective_cq_fash_mark = floor($objective_cq_maxmarks / 3);
														$minimam_objective_mcq_fash_mark = floor($objective_mcq_maxmarks / 3);
														$minimam_objective_practical_fash_mark = floor($objective_practical_maxmarks / 3);
		
														if ($objective_cq_marks >= $minimam_objective_cq_fash_mark && $objective_mcq_marks >= $minimam_objective_mcq_fash_mark && $objective_practical_marks >= $minimam_objective_practical_fash_mark) {
															$objective_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $objective_marks));
															echo $objective_letter_grade;
														} else {
															echo $objective_letter_grade = "F";
														}
		
													} else {
														//Nathing
													}
													?>
												</td>
												<td>
													<?php
													if ($objective_cq_marks >= 1 || $objective_mcq_marks >= 1 || $objective_practical_marks >= 1) {
														if ($objective_cq_marks >= $minimam_objective_cq_fash_mark && $objective_mcq_marks >= $minimam_objective_mcq_fash_mark && $objective_practical_marks >= $minimam_objective_practical_fash_mark) {
															$optional_gap = number_format(WLSM_M_Setting::optional_calculateGPA($objective_letter_grade), 2);
															echo $optional_gap;
														} else {
															$optional_gap = "0.00";
															echo $optional_gap;
														}
													} else {
														//Nathing
													}
													?>
												</td>
											<?php }
										} else {
											?>
											<!-- other subject cq -->
											<?php
											if ($subject_type == "subjective") { ?>
												<td>
													<?php
													$other_subject_cq_marks = $get_obtained_marks;
													$other_subject_cq_maxmarks = $subject_maximum_marks;
													echo esc_html($other_subject_cq_marks);
													?>
												</td>
											<?php }
											// Other subject mcq
											if ($subject_type == "mcq") { ?>
												<td>
													<?php
													$other_subject_mcq_marks = $get_obtained_marks;
													$other_subject_mcq_maxmarks = $subject_maximum_marks;
													echo esc_html($other_subject_mcq_marks);
													?>
												</td>
												<td>
													<?php
													if ($other_subject_cq_marks >= 1 || $other_subject_mcq_marks >= 1) {
														$other_subject_marks = $other_subject_cq_marks + $other_subject_mcq_marks;
														echo $other_subject_marks;
														$other_subject_total_marks += $other_subject_marks;
													} else {
														// Nathing	
													}
													?>
												</td>
												<td>
													<?php
		
													if ($other_subject_cq_marks >= 1 || $other_subject_mcq_marks >= 1) {
														$other_subject_maximam_marks = $other_subject_cq_maxmarks + $other_subject_mcq_maxmarks;
														$total_other_subject_maxmarks += $other_subject_maximam_marks;
														$grades_percentage = 100 / $other_subject_maximam_marks;
														$other_subject_marks *= $grades_percentage;
		
														$minimam_other_subject_cq_fash_mark = floor($other_subject_cq_maxmarks / 3);
														$minimam_other_subject_mcq_fash_mark = floor($other_subject_mcq_maxmarks / 3);
		
														if ($other_subject_cq_marks >= $minimam_other_subject_cq_fash_mark && $other_subject_mcq_marks >= $minimam_other_subject_mcq_fash_mark) {
															$other_subject_letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $other_subject_marks));
															echo $other_subject_letter_grade;
														} else {
															echo $other_subject_letter_grade = "F";
														}
		
														if ($other_subject_letter_grade === 'F') {
															$count_other_subject_letter_grade_f++;
														}
													} else {
														// Nathing	
													}
													?>
												</td>
												<td>
													<?php
													if ($other_subject_cq_marks >= 1 || $other_subject_mcq_marks >= 1) {
														if ($other_subject_cq_marks >= $minimam_other_subject_cq_fash_mark && $other_subject_mcq_marks >= $minimam_other_subject_mcq_fash_mark) {
															echo number_format(WLSM_M_Setting::calculateGPA($other_subject_letter_grade), 2);
														} else {
															echo "0.00";
														}
													} else {
														// Nathing	
													}
													?>
												</td>
											<?php }
										}
		
		
										// }
							
									}
		
									break;
								}
							}
							
						}
						
						?>
						<td>
							<!-- Student Total Marks -->
							<?php
							$total_subject_marks = $total_bangla_mark + $english_total_marks + $group_subject_total_marks + $other_subject_total_marks;
							$total_objective_and_subject_marks = $total_subject_marks + $objective_total_marks;
							echo $total_objective_and_subject_marks;
							?>
						</td>
						<td>
							<!-- Student LG Without Ad. Sub -->
							<?php
							$totla_subject_maxmarks = $bangla_subject_maxmarks + $english_subject_maxmarks + $totla_group_subject_maxmarks + $total_other_subject_maxmarks;
							$total_objective_and_subject_maxmarks = $bangla_subject_maxmarks + $english_subject_maxmarks + $totla_group_subject_maxmarks + $objective_maximam_marks + $total_other_subject_maxmarks;

							$total_failde_subject = $count_letter_grade_bangla_f + $count_letter_grade_english_f + $count_group_subject_letter_grade_f + $count_other_subject_letter_grade_f;

							// echo "Total failde subject: " . $total_failde_subject;
						
							$without_objective_total_mark_percentage = esc_html(WLSM_Config::get_percentage_text($totla_subject_maxmarks, $total_subject_marks));

							if($without_objective_total_mark_percentage <= 100 && $objective_total_marks >= 40 && $optional_gap != "0.00"){
								// less the 40 marks in optional subject
								$objective_total_marks -= 40;
								// add marks students main subject and optional subject mark
								$total_student_marks = $total_subject_marks + $objective_total_marks;
							}else{
								$total_student_marks = $total_subject_marks;
							}

							
							if ($total_failde_subject == 0) {
								$gpa_result = number_format(WLSM_M_Setting::calculatePreciseGPA($without_objective_total_mark_percentage), 2);
							} else {
								$gpa_result = "0.00";
							}
							

							if ($total_failde_subject == 0) {
								$without_objective_letter_grade = esc_html(WLSM_M_Setting::calcuateGPAToLetterGrade($gpa_result));
								echo $without_objective_letter_grade;
							} else {
								echo "F";
							}
							?>
						</td>
						<td>
							<!-- Student CGPA Without Ad. Sub -->
							<?php
								echo $gpa_result;
							?>
						</td>
						<td>
							<?php
								$total_mark_percentage = esc_html(WLSM_Config::get_percentage_text($totla_subject_maxmarks, $total_student_marks));
								// Create CGPA function
								if ($total_failde_subject == 0) {
									$_gpa_result = number_format(WLSM_M_Setting::calculatePreciseGPA($total_mark_percentage), 2);
									
								} else {
									$_gpa_result = "0.00";
								}
							?>
							<!-- Student LG -->
							<?php

							if ($total_failde_subject == 0) {
								$_final_grade = esc_html(WLSM_M_Setting::calcuateGPAToLetterGrade($_gpa_result));
								echo $_final_grade;
							} else {
								echo "F";
							}
							?>
						</td>
						<td>
							<!-- show Student CGPA -->
							<?php
							echo $_gpa_result;
							?>
						</td>
					</tr>


				<?php endforeach; ?>
				<!-- need exam_paper_id and admit_card_id to get the mark of the student and the subject  -->

			</tbody>
		</table>
	</div>



</center>


<div class="wlsm-container d-flex mb-2">
	<div class="col-md-12 wlsm-text-center">
		<br>
		<button type="button" class="<?php echo esc_attr($print_button_classes); ?>" id="wlsm-print-result-excel-btn"
			data-styles='["<?php echo esc_url(WLSM_PLUGIN_URL . 'assets/css/bootstrap.min.css'); ?>","<?php echo esc_url(WLSM_PLUGIN_URL . 'assets/css/result_print_in_excel.css'); ?>"]'>
			<?php esc_html_e('Print Result Excel', 'school-management'); ?>
		</button>
	</div>
</div>
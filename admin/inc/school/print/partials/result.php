<?php

if ($i != 0) {
	echo '<div class="page-break"></div>';
}
?>

<?php
defined('ABSPATH') || die();

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

if (isset($from_front)) {
	$print_button_classes = 'button btn-sm btn-success';
} else {
	$print_button_classes = 'btn btn-sm btn-success';
}

$grade_criteria = WLSM_Config::sanitize_grade_criteria($exam->grade_criteria);

$enable_overall_grade = $grade_criteria['enable_overall_grade'];
$marks_grades = $grade_criteria['marks_grades'];

$settings_dashboard = WLSM_M_Setting::get_settings_dashboard($school_id);
$school_enrollment_number = $settings_dashboard['school_enrollment_number'];
$school_admission_number = $settings_dashboard['school_admission_number'];

$settings_url = WLSM_M_Setting::get_settings_certificate_qcode_url($school_id);
$school_result_url = $settings_url['result_url'];


// function subject_total_mark(){
// 	$subject_totla_marks = $written_mark + $mcq_mark + $practical_mark;
// 	echo $subject_totla_marks;
// }



?>

<!-- Print exam results section. -->
<style type="text/css">
	.wlsm-print-exam-results-container {
		padding: 40px;
		border-image-repeat: round;
		border-image-slice: 160;
		border-image-width: 60px;
	}
</style>

<div class="wlsm-container wlsm" id="wlsm-print-exam-results">
	<div class="wlsm-print-exam-results-container"
		style="border-image-source:url('<?php echo WLSM_PLUGIN_URL . "assets/images/result-border.png"; ?>');">


		<?php

		// school header section. ---------------------------------------
		$school = WLSM_M_School::fetch_school($school_id);
		$settings_general = WLSM_M_Setting::get_settings_general($school_id);
		$school_logo = $settings_general['school_logo'];
		$school_signature = $settings_general['school_signature'];


		?>

		<!-- School header -->
		<div class="container-fluid">
			<div class="row wlsm-school-header justify-content-center">
				<div class="col-3 text-right">
					<?php if (!empty($school_logo)) { ?>
						<img src="<?php echo esc_url(wp_get_attachment_url($school_logo)); ?>"
							class="wlsm-print-school-logo">
					<?php } ?>
				</div>
				<div class="col-6">
					<div class="wlsm-print-school-label">
						<?php echo esc_html(WLSM_M_School::get_label_text($school->label)); ?>
					</div>
					<div class="wlsm-print-school-contact">
						<?php if ($school->phone) { ?>
							<span class="wlsm-print-school-phone">
								<span class="wlsm-font-bold">
									<?php esc_html_e('Phone:', 'school-management'); ?>
								</span>
								<span>
									<?php echo esc_html(WLSM_M_School::get_label_text($school->phone)); ?>
								</span>
							</span>
						<?php } ?>
						<?php if ($school->email) { ?>
							<span class="wlsm-print-school-email">
								<span class="wlsm-font-bold">
									|
									<?php esc_html_e('Email:', 'school-management'); ?>
								</span>
								<span>
									<?php echo esc_html(WLSM_M_School::get_phone_text($school->email)); ?>
								</span>
							</span>
							<br>
						<?php } ?>
						<?php if ($school->address) { ?>
							<span class="wlsm-print-school-address">
								<span class="wlsm-font-bold">
									<?php esc_html_e('Address:', 'school-management'); ?>
								</span>
								<span>
									<?php echo esc_html(WLSM_M_School::get_email_text($school->address)); ?>
								</span>
							</span>
						<?php } ?>
					</div>
				</div>
				<div class="col-3 text-right">
					<?php if (!empty($school_result_url)) { ?>
						<?php
						$qr_code_url = $school_result_url . '?exam_roll_number=' . WLSM_M_Staff_Class::get_roll_no_text($result->roll_number) . '&id=' . $result->exam_id;
						$field_output = esc_url('https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($qr_code_url) . '&choe=UTF-8');
						?>
						<?php if ($school_result_url): ?>
							<img src="<?php echo esc_url($field_output); ?>" width="120px">
						<?php endif ?>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="wlsm-heading wlsm-exam-results-heading h5 wlsm-text-center">
			<div class="wlsm-exam-results-table-heading">
				<?php
				// echo "<pre>";
				// print_r($new_common_subject);
				// echo "</pre>";
				// printf(
				// 	wp_kses(
				// 		/* translators: 1: exam title, 2: start date, 3: end date */
				// 		__( '<span class="wlsm-font-bold">Exam: </span> %1$s (%2$s - %3$s)', 'school-management' ),
				// 		array(
				// 			'span' => array( 'class' => array() )
				// 		)
				// 	),
				// 	esc_html( WLSM_M_Staff_Examination::get_exam_label_text( $exam_title ) ),
				// 	esc_html( WLSM_Config::get_date_text( $start_date ) ),
				// 	esc_html( WLSM_Config::get_date_text( $end_date ) )
				// );
				?>
			</div>
		</div>

		<div class="row wlsm-student-details">
			<div class="col-md-6" style="width: 70%">
				<ul class="wlsm-list-group">
					<li>

						<span class="wlsm-font-bold">
							<?php esc_html_e('Student Name', 'school-management'); ?>:
						</span>
						<span>
							<?php echo esc_html(WLSM_M_Staff_Class::get_name_text($result->name)); ?>
						</span>
					</li>
					<?php if ($school_enrollment_number): ?>
						<!-- <li>
							<span class="wlsm-font-bold"><? php // esc_html_e( 'Enrollment Number', 'school-management' ); ?>:</span>
							<span><?php //echo esc_html( WLSM_M_Staff_Class::get_roll_no_text( $result->enrollment_number ) ); ?></span>
						</li> -->
					<?php endif ?>
					<?php if ($school_admission_number): ?>
						<li>
							<span class="wlsm-font-bold">
								<?php esc_html_e('Admission Number', 'school-management'); ?>:
							</span>
							<span>
								<?php echo esc_html(WLSM_M_Staff_Class::get_roll_no_text($result->admission_number)); ?>
							</span>
						</li>
					<?php endif ?>
					<li>
						<span class="wlsm-font-bold">
							<?php esc_html_e('Session', 'school-management'); ?>:
						</span>
						<span>
							<?php
							$session_id = $result->session_id;
							global $wpdb;
							$table_name = WLSM_SESSIONS;
							$session_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM {$table_name} WHERE ID =%d", $session_id));
							echo $session_label;
							// echo esc_html( WLSM_M_Session::get_label_text( ) );
							?>
						</span>
					</li>
					<li>
						<span class="wlsm-pr-3 pr-3">
							<span class="wlsm-font-bold">
								<?php esc_html_e('Class', 'school-management'); ?>:
							</span>
							<span>
								<?php echo esc_html(WLSM_M_Class::get_label_text($result->class_label)); ?>
							</span>
						</span>
						<span class="wlsm-pl-3 pl-3">
							<span class="wlsm-font-bold">
								<?php esc_html_e('Section', 'school-management'); ?>:
							</span>
							<span>
								<?php echo esc_html(WLSM_M_Class::get_label_text($result->section_label)); ?>
							</span>
						</span>
					</li>
					<li>
						<span class="wlsm-pr-3 pr-3">
							<span class="wlsm-font-bold">
								<?php esc_html_e('Exam Roll Number', 'school-management'); ?>:
							</span>
							<span>
								<?php echo esc_html(WLSM_M_Staff_Class::get_roll_no_text($result->roll_number)); ?>
							</span>
						</span>
						<!-- <span class="wlsm-pr-3 pr-3">
							<span class="wlsm-font-bold"><?php esc_html_e('Father\'s Name', 'school-management'); ?>:</span>
							<span><?php echo esc_html($result->father_name); ?></span>
						</span> -->
					</li>
				</ul>
			</div>
			<?php
			$optional_subject = WLSM_M_Staff_Class::optional_subject_code($admission_number = $result->admission_number);
			$optional_subject_code = $optional_subject[0]->optional_subject_code;
			?>
			<div class="col-md-6" style="width: 30%">
				<style>
					.marks-distribution {
						margin-top: 0;
					}

					.marks-distribution span {
						font-size: 10px !important;
					}

					.marks-distribution td,
					.marks-distribution th {
						font-size: 10px !important;
					}

					.marks-distribution td,
					.marks-distribution th {
						padding: 0 !important;
					}
				</style>
				<table class="marks-distribution" border="1"
					style="font-size: 12px !important;  text-align: left !important;">
					<tr align="center">
						<th style="padding: 5px !important;"><span>
								<?php esc_html_e('Class Interval', 'school-management'); ?>
							</span></th>
						<th style="padding: 5px !important;"><span>
								<?php esc_html_e('Letter Grade', 'school-management'); ?>
							</span></th>
						<th style="padding: 5px !important;"><span>
								<?php esc_html_e('Grade Point', 'school-management'); ?>
							</span></th>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('80-100', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('A+', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('5', 'school-management'); ?>
							</span></td>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('70-79', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('A', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('4', 'school-management'); ?>
							</span></td>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('60-69', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('A-', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('3.5', 'school-management'); ?>
							</span></td>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('50-59', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('B', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('3', 'school-management'); ?>
							</span></td>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('40-49', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('C', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('2', 'school-management'); ?>
							</span></td>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('33-39', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('D', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('1', 'school-management'); ?>
							</span></td>
					</tr>
					<tr align="center">
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('0-32', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('F', 'school-management'); ?>
							</span></td>
						<td style="padding: 3px !important;"><span>
								<?php esc_html_e('0', 'school-management'); ?>
							</span></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="wlsm-exam-results-table-heading">
					<?php
					// printf(
					// 	wp_kses(
					// 		/* translators: 1: exam title, 2: start date, 3: end date */
					// 		__( '<span class="wlsm-font-bold">Exam Result:</span> %1$s (%2$s - %3$s)', 'school-management' ),
					// 		array(
					// 			'span' => array( 'class' => array() )
					// 		)
					// 	),
					// 	esc_html( WLSM_M_Staff_Examination::get_exam_label_text( $exam_title ) ),
					// 	esc_html( WLSM_Config::get_date_text( $start_date ) ),
					// 	esc_html( WLSM_Config::get_date_text( $end_date ) )
					// );
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive w-100">
					<table class="table table-bordered wlsm-view-exam-results-table">
						<?php
						$show_marks_grades = count($marks_grades);


						?>
						<thead>
							<tr>
								<th>
									<?php esc_html_e('Code', 'school-management'); ?>
								</th>
								<th>
									<?php esc_html_e('Subject Name', 'school-management'); ?>
								</th>
								<!-- <th><?php //esc_html_e( 'Subject Type', 'school-management' ); ?></th> -->
								<!-- <th><?php //esc_html_e( 'Maximum Marks', 'school-management' ); ?></th> -->
								<th>
									<?php esc_html_e('CQ', 'school-management'); ?>
								</th>
								<th>
									<?php esc_html_e('MCQ', 'school-management'); ?>
								</th>
								<th>
									<?php esc_html_e('Practical', 'school-management'); ?>
								</th>
								<th>
									<?php esc_html_e('Total', 'school-management'); ?>
								</th>
								<!-- <th><?php //esc_html_e( 'Total', 'school-management' ); ?></th> -->
								<?php if ($show_marks_grades) { ?>
									<th>
										<?php esc_html_e('LG', 'school-management'); ?>
									</th>
								<?php } ?>
								<th>
									<?php esc_html_e('GP', 'school-management'); ?>
								</th>

							</tr>
						</thead>
						<tbody>
							<?php

							$total_maximum_marks = 0;
							$total_mcq_maximum_marks = 0;
							$total_practical_maximum_marks = 0;
							$total_obtained_marks = 0;
							$total_mcq_marks = 0;
							$total_practical_marks = 0;
							$count_letter_grade_f = 0;
							$count_letter_grade_bangla_f = 0;
							$count_letter_grade_english_f = 0;
							$letter_grade_bangla_fail = 0;
							$maximum_marks = 0;
							$optional_total_practical_maximum_marks = 0;
							$optional_total_mcq_maximum_marks = 0;
							$optional_total_obtained_marks = 0;
							$optional_total_mcq_marks = 0;
							$optional_total_practical_marks = 0;

							$new_all_subject_array = array();
							$sub_subject_parent_ids = array();
							$parent_sub_ids = array();
							foreach ($exam_papers as $key => $single_paper) {
								$parent_subject = $single_paper->parent_subject;
								$sub_subject_parent_ids[$single_paper->ID] = $parent_subject;

								if ($parent_subject == false) {
									$parent_sub_ids[$single_paper->ID] = $single_paper->subject_id;
								}

							}

							$all_subject_ids = array_filter(array_merge(array_unique($sub_subject_parent_ids), $parent_sub_ids));




							$all_subject_info_array = array();
							foreach ($all_subject_ids as $subject_id) {
								$subject_info = $wpdb->get_results($wpdb->prepare(
									"SELECT ID as subject_id, label as subject_label , code as paper_code, `type` as subject_type FROM {$wpdb->prefix}wlsm_subjects WHERE ID = %d ",
									$subject_id,
								)
								);
								$all_subject_info_array[] = $subject_info;
							}
							$array_unique = array_merge($all_subject_info_array, $exam_papers);


							$all_filter_subject = array();

							$student_optional_subject = $all_subject_info_array;
							if ($exam_ontained_mark == "" || $exam_ontained_mark != 0) {
								$filtered_subjects = array_filter($student_optional_subject, function ($subject) {
									return $subject[0]->subject_type !== "subjective";
								});

								foreach ($filtered_subjects as $subject) {
									if ($optional_subject_code == $subject[0]->paper_code) {
										break;
									}
								}
							}

							foreach ($all_subject_info_array as $sub_info) {
								$exam_paper_id = $wpdb->get_var($wpdb->prepare(
									"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE subject_label =%s AND paper_code = %d AND exam_id = %d",
									$sub_info[0]->subject_label,
									$sub_info[0]->paper_code,
									$exam_id
								)
								);

								$exam_ontained_mark = $wpdb->get_var($wpdb->prepare(
									"SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
									$exam_paper_id,
									$admit_card_id
								)
								);

								// if($exam_ontained_mark == "" || $exam_ontained_mark != 0){
								// 	$all_filter_subject[] = $sub_info ;
								// }
							
								// $all_filter_subject[] = $sub_info ;
							
								if ($exam_ontained_mark == "" || $exam_ontained_mark != 0) {
									if ($sub_info[0]->subject_type != "objective") {
										$all_filter_subject[] = $sub_info;
									}
								}



							}


							// $student_rank = WLSM_M_Staff_Examination::calculate_exam_ranks($school_id, $exam_id, $total_failde_subject, array(), $result->admit_card_id , $result->note);
							
							$students_ranks = array_filter($rankedStudents, function ($subArray) use ($result) {
								return $subArray['id'] == $result->admit_card_id;
							});
							$students_rank = reset($students_ranks);


							$is_fail = false;
							$previous_code = null;

							foreach ($all_filter_subject as $key => $exam_paper) {
								$total_fail = false;
								$written_mark = 0;
								$practical_mark = 0;
								$mcq_mark = 0;
								$subje_maximam_marks = 0;
								$practical_maximum_marks = 0;

								if ($result && isset($exam_results[$exam_paper->ID])) {
									$exam_result = $exam_results[$exam_paper->ID];
									$obtained_marks = $exam_result->obtained_marks;

								} else {
									$obtained_marks = '';
								}

								$teacher_remark = $exam_result->teacher_remark;
								$school_remark = $exam_result->school_remark;
								$p_scale = $exam_result->scale;

								$new_code = $exam_paper[0]->paper_code;

								if ($previous_code == $new_code) {
									continue;
								} else {
									$previous_code = $new_code;
								}


								$main_paper_code = $exam_paper[0]->paper_code;

								?>

								<tr>
									<td>
										<?php
										$papers_codes = esc_html($exam_paper[0]->paper_code);
										echo $papers_codes;

										?>
									</td>
									<td calspan=><?php echo esc_html($exam_paper[0]->subject_label); ?></td>
									<!-- <td><?php //echo esc_html( WLSM_Helper::get_subject_type_text( $exam_paper[0]->subject_type ) ); ?></td> -->
									<td>
										<?php
										$maximum_marks = $wpdb->get_var($wpdb->prepare(
											"SELECT maximum_marks  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %d AND subject_label = %s AND exam_id=%d",
											$exam_paper[0]->paper_code,
											$exam_paper[0]->subject_label,
											$exam_id
										)
										);


										if ($maximum_marks == null) {
											$class_school_id = $wpdb->get_var($wpdb->prepare(
												"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
												$exam_id
											)
											);
											$sub_subjects = $wpdb->get_results($wpdb->prepare(
												"SELECT label , code  FROM {$wpdb->prefix}wlsm_subjects WHERE parent_subject = %d AND class_school_id=%d",
												$exam_paper[0]->subject_id,
												$class_school_id
											)
											);
											$max_mark = 0;
											foreach ($sub_subjects as $sub_subject) {
												$get_sub_maximum_marks = $wpdb->get_var($wpdb->prepare(
													"SELECT maximum_marks  FROM {$wpdb->prefix}wlsm_exam_papers WHERE subject_label =%s AND paper_code = %d AND exam_id = %d",
													$sub_subject->label,
													$sub_subject->code,
													$exam_id
												)
												);
												$max_mark = $max_mark + $get_sub_maximum_marks;
											}
											$maximum_marks = $max_mark / count($sub_subjects);

											// echo $maximum_marks ;
									


										} else {
											// echo $maximum_marks; 
									
										}


										// CQ
									

										$class_school_id = $wpdb->get_var($wpdb->prepare(
											"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
											$exam_id
										)
										);

										$exam_paper_id = $wpdb->get_var($wpdb->prepare(
											"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %s AND subject_label = %s AND subject_type = %s AND exam_id=%d",
											$exam_paper[0]->paper_code,
											$exam_paper[0]->subject_label,
											$exam_paper[0]->subject_type,
											$exam_id
										)
										);

										$get_obtained_marks = $wpdb->get_var($wpdb->prepare(
											"SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
											$exam_paper_id,
											$admit_card_id
										)
										);


										if ($get_obtained_marks == null) {

											$sub_subjects_new = $wpdb->get_results($wpdb->prepare(
												"SELECT label , code  FROM {$wpdb->prefix}wlsm_subjects WHERE parent_subject = %d AND class_school_id=%d",
												$exam_paper[0]->subject_id,
												$class_school_id
											)
											);
											$get_marks = 0;
											foreach ($sub_subjects_new as $sub_subject) {
												$new_exam_paper_id = $wpdb->get_var($wpdb->prepare(
													"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE subject_label =%s AND paper_code = %d AND exam_id = %d",
													$sub_subject->label,
													$sub_subject->code,
													$exam_id
												)
												);

												$exam_ontained_mark = $wpdb->get_var($wpdb->prepare(
													"SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
													$new_exam_paper_id,
													$admit_card_id
												)
												);
												$get_marks = $get_marks + $exam_ontained_mark;
											}

											$new_obtained_marks = $get_marks / 2;



											echo $new_obtained_marks;
											$get_obtained_marks = $new_obtained_marks;
											$total_obtained_marks += WLSM_Config::sanitize_marks($get_obtained_marks);
										} else {

											$written_mark = $get_obtained_marks;
											echo esc_html($get_obtained_marks);
											$total_fail = $get_obtained_marks == 0 ? true : false;
											$total_obtained_marks += WLSM_Config::sanitize_marks($get_obtained_marks);


										}

										?>
									</td>

									<td>
										<?php

										$main_exam_paper = array_filter($exam_papers, function ($obj) use ($main_paper_code) {
											return $obj->paper_code == $main_paper_code && $obj->subject_type == 'mcq';
										});

										if (!empty($main_exam_paper)) {
											$foundObject = reset($main_exam_paper); // Get the first matching object
									
											$subject_id = reset($main_exam_paper)->ID;

											$mcq_mark = isset($exam_results[$subject_id]->obtained_marks) && $exam_results[$subject_id]->obtained_marks != null && $exam_results[$subject_id]->obtained_marks != "" ? $exam_results[$subject_id]->obtained_marks : 0; // comment uthaisi
									
											$mcq_maximum_marks = $foundObject->maximum_marks;

											$total_mcq_marks += $mcq_mark;
											$total_mcq_maximum_marks += $mcq_maximum_marks;

											echo $mcq_mark;
											$total_fail = $mcq_mark == 0 ? true : false;

										} else {
											// do nothing
										}

										?>
									</td>
									<td>
										<?php
										$main_exam_paper = array_filter($exam_papers, function ($obj) use ($main_paper_code) {
											return $obj->paper_code == $main_paper_code && $obj->subject_type == 'practical';
										});

										if (!empty($main_exam_paper)) {
											$foundObject = reset($main_exam_paper); // Get the first matching object
									
											$subject_id = reset($main_exam_paper)->ID;

											// $practical_mark = $exam_results[$subject_id]->obtained_marks; // commend uthaisi 
											$practical_mark = isset($exam_results[$subject_id]->obtained_marks) && $exam_results[$subject_id]->obtained_marks != null && $exam_results[$subject_id]->obtained_marks != "" ? $exam_results[$subject_id]->obtained_marks : 0;
											$practical_maximum_marks = $foundObject->maximum_marks;

											$total_practical_marks += $practical_mark;
											$total_practical_maximum_marks += $practical_maximum_marks;

											echo $practical_mark;

											$total_fail = $practical_mark == 0 ? true : false;

										} else {
											// do nothing
										}

										?>
									</td>
									<?php if ($show_marks_grades) { ?>
										<?php
										$subject_totla_marks = $written_mark + $mcq_mark + $practical_mark;

										if ($exam_paper[0]->paper_code == 101) {
											$exam_id = $result->exam_id;
											$first_paper_code = $main_paper_code;
											$second_paper_code = $first_paper_code + 1;

											$enrollment_number = $result->enrollment_number;
											$admission_number = $result->admission_number;
											$session_id = $result->session_id;
											$section_id = get_section_id($enrollment_number, $admission_number, $session_id);
											$bangla_first_subjective_mark = get_mark_by_paper_code($exam_id, $first_paper_code, "subjective", $admission_number, $section_id);
											$bangla_second_subjective_mark = get_mark_by_paper_code($exam_id, $second_paper_code, "subjective", $admission_number, $section_id);

											$first_mcq_mark = get_mark_by_paper_code($exam_id, $first_paper_code, "mcq", $admission_number, $section_id);
											$second_mcq_mark = get_mark_by_paper_code($exam_id, $second_paper_code, "mcq", $admission_number, $section_id);

											$total_subjective_mark = $bangla_first_subjective_mark + $bangla_second_subjective_mark;
											$total_mcq_mark = $first_mcq_mark + $second_mcq_mark;

											$total_bangla_mark = $total_subjective_mark + $total_mcq_mark;

											if ($bangla_first_subjective_mark == 0 && $first_mcq_mark == 0 || $bangla_second_subjective_mark == 0 && $second_mcq_mark == 0 || $bangla_first_subjective_mark == NULL && $first_mcq_mark == NULL || $bangla_second_subjective_mark == NULL && $second_mcq_mark == NULL) {
												echo '<td>';
											} else {
												echo '<td rowspan="2" style="vertical-align: middle !important;"
											>';
											}

											echo $total_bangla_mark;

											echo '</td>';
										} elseif ($exam_paper[0]->paper_code == 107) {
											$exam_id = $result->exam_id;
											$first_paper_code = $main_paper_code;
											$second_paper_code = $first_paper_code + 1;

											$enrollment_number = $result->enrollment_number;
											$admission_number = $result->admission_number;
											$session_id = $result->session_id;
											$section_id = get_section_id($enrollment_number, $admission_number, $session_id);
											$english_first_subjective_mark = get_mark_by_paper_code($exam_id, $first_paper_code, "subjective", $admission_number, $section_id);
											$english_second_subjective_mark = get_mark_by_paper_code($exam_id, $second_paper_code, "subjective", $admission_number, $section_id);

											$total_english_mark = $english_first_subjective_mark + $english_second_subjective_mark;


											if ($english_first_subjective_mark == 0 || $english_second_subjective_mark == 0 || $english_first_subjective_mark == NULL || $english_second_subjective_mark == NULL) {
												echo '<td>';
											} else {
												echo '<td rowspan="2" style="vertical-align: middle !important;">';
											}
											echo $total_english_mark;
											echo '</td>';
										} elseif ($exam_paper[0]->paper_code == 102 || $exam_paper[0]->paper_code == 108) {

										} else {
											echo '<td>';
											$subject_totla_marks = $written_mark + $mcq_mark + $practical_mark;
											echo $subject_totla_marks;
											echo '</td>';
										}
										?>
										<?php
										if ($exam_paper[0]->paper_code == 101) {
											if ($maximum_marks == 50) {
												$get_obtained_marks = round(($get_obtained_marks / $maximum_marks) * 100);
											}
											$bangla_cq_maximum_mark = $maximum_marks * 2;
											$bangla_mcq_maximum_mark = $mcq_maximum_marks * 2;

											$minimam_cq_fash_mark = $bangla_cq_maximum_mark / 3;
											$minimam_mcq_fash_mark = $bangla_mcq_maximum_mark / 3;
											if ($bangla_second_subjective_mark != null) {
												$divide_bangla_mark = $total_bangla_mark / 2;
											} else {
												$divide_bangla_mark = $total_bangla_mark / 1;
											}


											if ($bangla_first_subjective_mark == 0 && $first_mcq_mark == 0 || $bangla_second_subjective_mark == 0 && $second_mcq_mark == 0 || $bangla_first_subjective_mark == NULL && $first_mcq_mark == NULL || $bangla_second_subjective_mark == NULL && $second_mcq_mark == NULL) {
												echo '<td>';
											} else {
												echo '<td rowspan="2" style="vertical-align: middle !important;"
										>';
											}

											if ($bangla_second_subjective_mark != null) {
												if ($bangla_first_subjective_mark >= 1 && $bangla_second_subjective_mark >= 1 && $first_mcq_mark >= 1 && $second_mcq_mark >= 1) {
													if ($total_subjective_mark >= $minimam_cq_fash_mark && $total_mcq_mark >= $minimam_mcq_fash_mark) {
														$letter_grade_bangla = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_bangla_mark));
														echo $letter_grade_bangla;

													} else {
														echo $letter_grade_bangla = "F";
													}
												} else {
													echo $letter_grade_bangla = "F";
												}
											} else {
												if ($total_subjective_mark >= $minimam_cq_fash_mark && $total_mcq_mark >= $minimam_mcq_fash_mark) {
													$letter_grade_bangla = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_bangla_mark));
													echo $letter_grade_bangla;

												} else {
													echo $letter_grade_bangla = "F";
												}
											}


											if ($letter_grade_bangla === 'F') {
												$count_letter_grade_bangla_f++;
											}
											echo "</td>";


											$cq_percentage = WLSM_Config::sanitize_percentage($maximum_marks, $written_mark) . ",";
											// echo $cq_percentage;
											$mcq_percentage = WLSM_Config::sanitize_percentage($mcq_maximum_marks, $mcq_mark) . ",";
											// echo $mcq_percentage;
											$practical_percentage = WLSM_Config::sanitize_percentage($practical_maximum_marks, $practical_mark) . "," . "<br>";
											// echo $practical_percentage;
								
											$overall_fail = 0;

											if ((int) $cq_percentage < 33) {
												$overall_fail = 1;
											}

											if ((int) $mcq_percentage < 33 && $mcq_mark != null) {
												$overall_fail = 1;
											}



											$percentage = WLSM_Config::sanitize_percentage($maximum_marks, WLSM_Config::sanitize_marks($get_obtained_marks));
											// newly added code 
											if ($is_fail == false) {
												if (esc_html(WLSM_Helper::calculate_grade($marks_grades, $get_obtained_marks)) == "F" && $exam_paper[0]->subject_type != "objective") {
													$is_fail = true;
												} else {
													$is_fail = false;
												}
											}


										} elseif ($exam_paper[0]->paper_code == 107) {
											if ($maximum_marks == 50) {
												$get_obtained_marks = round(($get_obtained_marks / $maximum_marks) * 100);
											}
											if ($english_second_subjective_mark != null) {
												$divide_english_mark = $total_english_mark / 2;
											} else {
												$divide_english_mark = $total_english_mark / 1;
											}
											if ($english_first_subjective_mark == 0 || $english_second_subjective_mark == 0 || $english_first_subjective_mark == NULL || $english_second_subjective_mark == NULL) {
												echo '<td>';
											} else {
												echo '<td rowspan="2" style="vertical-align: middle !important;">';
											}
											if ($english_second_subjective_mark != null) {
												if ($english_first_subjective_mark >= 1 && $english_second_subjective_mark >= 1) {
													$letter_grade_eng = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_english_mark));
													echo $letter_grade_eng;

												} else {
													echo $letter_grade_eng = "F";
												}
											} else {
												$letter_grade_eng = esc_html(WLSM_Helper::calculate_grade($marks_grades, $divide_english_mark));
												echo $letter_grade_eng;
											}

											if ($letter_grade_english === 'F' || $letter_grade_eng === 'F') {
												$count_letter_grade_english_f++;
											}

											echo '</td>';

											$cq_percentage = WLSM_Config::sanitize_percentage($maximum_marks, $written_mark) . ",";
											// echo $cq_percentage;
											$mcq_percentage = WLSM_Config::sanitize_percentage($mcq_maximum_marks, $mcq_mark) . ",";
											// echo $mcq_percentage;
											$practical_percentage = WLSM_Config::sanitize_percentage($practical_maximum_marks, $practical_mark) . "," . "<br>";
											// echo $practical_percentage;
								
											$overall_fail = 0;

											if ((int) $cq_percentage < 33) {
												$overall_fail = 1;
											}

											if ((int) $mcq_percentage < 33 && $mcq_mark != null) {
												$overall_fail = 1;
											}



											$percentage = WLSM_Config::sanitize_percentage($maximum_marks, WLSM_Config::sanitize_marks($get_obtained_marks));
											// newly added code 
											if ($is_fail == false) {
												if (esc_html(WLSM_Helper::calculate_grade($marks_grades, $get_obtained_marks)) == "F" && $exam_paper[0]->subject_type != "objective") {
													$is_fail = true;
												} else {
													$is_fail = false;
												}
											}

										} elseif ($exam_paper[0]->paper_code == 102 || $exam_paper[0]->paper_code == 108) {

										} else {
											echo "<td>";
											if ($get_obtained_marks == 0) {

											} else {
												$subje_maximam_marks = $maximum_marks + $mcq_maximum_marks + $practical_maximum_marks;
												$grades_percentage = 100 / $subje_maximam_marks;
												$subject_totla_marks *= $grades_percentage;



												if ($maximum_marks == 50) {
													$get_obtained_marks = round(($get_obtained_marks / $maximum_marks) * 100);
												}
												$minimam_cq_fash_mark = $maximum_marks / 3;
												$minimam_mcq_fash_mark = $mcq_maximum_marks / 3;
												$minimam_practical_fash_mark = $practical_maximum_marks / 3;
												if ($written_mark >= $minimam_cq_fash_mark && $mcq_mark >= $minimam_mcq_fash_mark && $practical_mark >= $minimam_practical_fash_mark) {
													$letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $subject_totla_marks));
													echo $letter_grade;
												} else {
													echo $letter_grade = "F";
												}

												$cq_percentage = WLSM_Config::sanitize_percentage($maximum_marks, $written_mark) . ",";
												// echo $cq_percentage;
												$mcq_percentage = WLSM_Config::sanitize_percentage($mcq_maximum_marks, $mcq_mark) . ",";
												// echo $mcq_percentage;
												$practical_percentage = WLSM_Config::sanitize_percentage($practical_maximum_marks, $practical_mark) . "," . "<br>";
												// echo $practical_percentage;
								
												$overall_fail = 0;

												if ((int) $cq_percentage < 33) {
													$overall_fail = 1;
												}

												if ((int) $mcq_percentage < 33 && $mcq_mark != null) {
													$overall_fail = 1;
												}

												$percentage = WLSM_Config::sanitize_percentage($maximum_marks, WLSM_Config::sanitize_marks($get_obtained_marks));


												// newly added code 
												if ($is_fail == false) {
													if (esc_html(WLSM_Helper::calculate_grade($marks_grades, $get_obtained_marks)) == "F" && $exam_paper[0]->subject_type != "objective") {
														$is_fail = true;
													} else {
														$is_fail = false;
													}
												}

											}
											if ($letter_grade === 'F') {
												$count_letter_grade_f++;
											}
											echo "</td>";

										}
										// echo $maximum_marks + $mcq_maximum_marks + $practical_maximum_marks;
										$total_maximum_marks += $maximum_marks;
										?>
									<?php } ?>
									<?php
									if ($exam_paper[0]->paper_code == 101) {

										if ($bangla_first_subjective_mark == 0 && $first_mcq_mark == 0 || $bangla_second_subjective_mark == 0 && $second_mcq_mark == 0 || $bangla_first_subjective_mark == NULL && $first_mcq_mark == NULL || $bangla_second_subjective_mark == NULL && $second_mcq_mark == NULL) {
											echo '<td>';
										} else {
											echo '<td rowspan="2" style="vertical-align: middle !important;"
										>';
										}
										echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_bangla), 2);

										echo '</td>';
									} elseif ($exam_paper[0]->paper_code == 107) {
										if ($english_first_subjective_mark == 0 || $english_second_subjective_mark == 0 || $english_first_subjective_mark == NULL || $english_second_subjective_mark == NULL) {
											echo '<td>';
										} else {
											echo '<td rowspan="2" style="vertical-align: middle !important;">';
										}

										echo number_format(WLSM_M_Setting::calculateGPA($letter_grade_eng), 2);

										echo '</td>';

									} elseif ($exam_paper[0]->paper_code == 102 || $exam_paper[0]->paper_code == 108) {

									} else {
										echo '<td>';
										if ($written_mark >= $minimam_cq_fash_mark && $mcq_mark >= $minimam_mcq_fash_mark && $practical_mark >= $minimam_practical_fash_mark) {
											echo number_format(WLSM_M_Setting::calculateGPA($letter_grade), 2);
										} else {
											echo "0.00";
										}
										echo '</td>';

									}
									?>

								</tr>
								<?php


							}



							?>
							<tr>
								<th colspan="8">
									<?php esc_html_e('4th Subject (Above 2)', "school-management"); ?>
								</th>
							</tr>
							<tr>
								<td>
									<?php $optional_papre_code = esc_html($subject[0]->paper_code);
									echo $optional_papre_code;
									?>
								</td>
								<td>
									<?php echo esc_html($subject[0]->subject_label); ?>
								</td>
								<td>
									<?php

									$maximum_marks = $wpdb->get_var($wpdb->prepare(
										"SELECT maximum_marks  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %d AND subject_label = %s AND exam_id=%d",
										$subject[0]->paper_code,
										$subject[0]->subject_label,
										$exam_id
									)
									);


									if ($maximum_marks == null) {
										$class_school_id = $wpdb->get_var($wpdb->prepare(
											"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
											$exam_id
										)
										);
										$sub_subjects = $wpdb->get_results($wpdb->prepare(
											"SELECT label , code  FROM {$wpdb->prefix}wlsm_subjects WHERE parent_subject = %d AND class_school_id=%d",
											$subject[0]->subject_id,
											$class_school_id
										)
										);
										$max_mark = 0;
										foreach ($sub_subjects as $sub_subject) {
											$get_sub_maximum_marks = $wpdb->get_var($wpdb->prepare(
												"SELECT maximum_marks  FROM {$wpdb->prefix}wlsm_exam_papers WHERE subject_label =%s AND paper_code = %d AND exam_id = %d",
												$sub_subject->label,
												$sub_subject->code,
												$exam_id
											)
											);
											$max_mark = $max_mark + $get_sub_maximum_marks;
										}
										$maximum_marks = $max_mark / count($sub_subjects);

										// echo $maximum_marks ;
									


									} else {
										// echo $maximum_marks; 
									
									}


									// CQ
									
									$class_school_id = $wpdb->get_var($wpdb->prepare(
										"SELECT class_school_id  FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id=%d",
										$exam_id
									)
									);

									$exam_paper_id = $wpdb->get_var($wpdb->prepare(
										"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE paper_code = %s AND subject_label = %s AND subject_type = %s AND exam_id=%d",
										$subject[0]->paper_code,
										$subject[0]->subject_label,
										$subject[0]->subject_type,
										$exam_id
									)
									);

									$get_obtained_marks = $wpdb->get_var($wpdb->prepare(
										"SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
										$exam_paper_id,
										$admit_card_id
									)
									);


									if ($get_obtained_marks == null) {

										$sub_subjects_new = $wpdb->get_results($wpdb->prepare(
											"SELECT label , code  FROM {$wpdb->prefix}wlsm_subjects WHERE parent_subject = %d AND class_school_id=%d",
											$subject[0]->subject_id,
											$class_school_id
										)
										);
										$get_marks = 0;
										foreach ($sub_subjects_new as $sub_subject) {
											$new_exam_paper_id = $wpdb->get_var($wpdb->prepare(
												"SELECT ID  FROM {$wpdb->prefix}wlsm_exam_papers WHERE subject_label =%s AND paper_code = %d AND exam_id = %d",
												$sub_subject->label,
												$sub_subject->code,
												$exam_id
											)
											);

											$exam_ontained_mark = $wpdb->get_var($wpdb->prepare(
												"SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
												$new_exam_paper_id,
												$admit_card_id
											)
											);
											$get_marks = $get_marks + $exam_ontained_mark;
										}

										$new_obtained_marks = $get_marks / 2;



										echo $new_obtained_marks;
										$get_obtained_marks = $new_obtained_marks;
										$optional_total_obtained_marks += WLSM_Config::sanitize_marks($get_obtained_marks);
									} else {

										$written_mark = $get_obtained_marks;
										echo esc_html($get_obtained_marks);
										$total_fail = $get_obtained_marks == 0 ? true : false;
										$optional_total_obtained_marks += WLSM_Config::sanitize_marks($get_obtained_marks);


									}

									?>
								</td>
								<td>
									<?php
									$main_exam_paper = array_filter($exam_papers, function ($obj) use ($optional_papre_code) {
										return $obj->paper_code == $optional_papre_code && $obj->subject_type == 'mcq';
									});

									if (!empty($main_exam_paper)) {
										$foundObject = reset($main_exam_paper); // Get the first matching object
									
										$subject_id = reset($main_exam_paper)->ID;

										$mcq_mark = isset($exam_results[$subject_id]->obtained_marks) && $exam_results[$subject_id]->obtained_marks != null && $exam_results[$subject_id]->obtained_marks != "" ? $exam_results[$subject_id]->obtained_marks : 0; // comment uthaisi
									
										$mcq_maximum_marks = $foundObject->maximum_marks;

										$optional_total_mcq_marks += $mcq_mark;
										$optional_total_mcq_maximum_marks += $mcq_maximum_marks;

										echo $mcq_mark;
										$total_fail = $mcq_mark == 0 ? true : false;

									} else {
										// do nothing
									}
									?>
								</td>
								<td>
									<?php
									$main_exam_paper = array_filter($exam_papers, function ($obj) use ($optional_papre_code) {
										return $obj->paper_code == $optional_papre_code && $obj->subject_type == 'practical';
									});

									if (!empty($main_exam_paper)) {
										$foundObject = reset($main_exam_paper); // Get the first matching object
									
										$subject_id = reset($main_exam_paper)->ID;

										// $practical_mark = $exam_results[$subject_id]->obtained_marks; // commend uthaisi 
										$practical_mark = isset($exam_results[$subject_id]->obtained_marks) && $exam_results[$subject_id]->obtained_marks != null && $exam_results[$subject_id]->obtained_marks != "" ? $exam_results[$subject_id]->obtained_marks : 0;
										$practical_maximum_marks = $foundObject->maximum_marks;

										$optional_total_practical_marks += $practical_mark;
										$optional_total_practical_maximum_marks += $practical_maximum_marks;

										echo $practical_mark;

										$total_fail = $practical_mark == 0 ? true : false;

									} else {
										// do nothing
									}
									?>
								</td>
								<td>
									<?php
									$subject_totla_marks = $written_mark + $mcq_mark + $practical_mark;
									echo $subject_totla_marks;
									?>
								</td>
								<td>
									<?php

									$minimam_objective_cq_fash_mark = $maximum_marks / 3;
									$minimam_objective_mcq_fash_mark = $optional_total_mcq_maximum_marks / 3;
									$minimam_objective_practical_fash_mark = $optional_total_practical_maximum_marks / 3;
									if ($written_mark >= $minimam_objective_cq_fash_mark && $mcq_mark >= $minimam_objective_mcq_fash_mark && $practical_mark >= $minimam_objective_practical_fash_mark) {
										$letter_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $subject_totla_marks));
										echo $letter_grade;
									} else {
										echo "F";
									}
									?>
								</td>
								<td>
									<?php
									if ($written_mark >= $minimam_objective_cq_fash_mark && $mcq_mark >= $minimam_objective_mcq_fash_mark && $practical_mark >= $minimam_objective_practical_fash_mark) {
										echo number_format(WLSM_M_Setting::optional_calculateGPA($letter_grade), 2);
									} else {
										echo "0.00";
									}
									?>
								</td>
							</tr>
							<?php
							// $total_percentage = WLSM_Config::sanitize_percentage( $total_maximum_marks, $total_obtained_marks );
							//poblem jahidul
							// $p_scale = unserialize($p_scale);
							$total_marks = $total_obtained_marks + $total_mcq_marks + $total_practical_marks;
							// echo $total_maximum_marks . "<br>";
							$total_max_marks = $total_maximum_marks + $total_practical_maximum_marks + $total_mcq_maximum_marks;
							$optional_max_mark = $maximum_marks + $optional_total_mcq_maximum_marks + $optional_total_practical_maximum_marks;


							$optional_subject_totla_mark = $optional_total_obtained_marks + $optional_total_mcq_marks + $optional_total_practical_marks;


							// $optional_and_mainsubject_max_mark = $total_max_marks + $optional_max_mark;
							$optional_and_mainsubject_totol_mark = $total_marks + $optional_subject_totla_mark;

							// echo $total_max_marks;
							$total_failde_subject = $count_letter_grade_f + $count_letter_grade_bangla_f + $count_letter_grade_english_f; ?>
							<tr>
								<th colspan="3">
									<?php esc_html_e('Total', 'school-management'); ?>
								</th>
								<!-- <th><?php //echo esc_html( $total_max_marks ); ?></th> -->

								<th colspan="3">
									<?php echo esc_html($optional_and_mainsubject_totol_mark); ?>
								</th>
								<?php if ($show_marks_grades) { ?>

								<?php } ?>
								<th></th>
								<th></th>
							</tr>
							<tr>
								<td></td>
								<td>
									<?php
									$total_mark_percentage = esc_html(WLSM_Config::get_percentage_text($total_max_marks, $total_marks));
									// echo $total_mark_percentage;
									?>
								</td>
								<td></td>
								<th colspan="3">
									<?php esc_html_e('GPA Without Ad. Sub', 'school-management'); ?>
								</th>
								<?php if ($show_marks_grades) { ?>
									<td>
										<?php
										if ($total_failde_subject == 0) {
											$final_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $total_mark_percentage));
											echo $final_grade;
										} else {
											echo "F";
										}
										?>
									</td>
									<td>
										<?php
										if ($total_failde_subject == 0) {
											$gpa_result = number_format(WLSM_M_Setting::calculateGPA($final_grade), 2);
											echo $gpa_result;
										} else {
											echo "0.00";
										}
										?>
									</td>
								<?php } ?>
							</tr>
							<tr>
								<th colspan="3">
									<?php //esc_html_e( 'Percentage', 'school-management' ); ?>
								</th>
								<th colspan="3">
									<?php
									$opti_and_main_sub_total_mark_percentage = esc_html(WLSM_Config::get_percentage_text($total_max_marks, $optional_and_mainsubject_totol_mark));
									// echo $opti_and_main_sub_total_mark_percentage;
									esc_html_e('GPA', 'school-management');
									?>
								</th>
								<?php if ($show_marks_grades) { ?>
									<th>
										<?php
										if ($total_failde_subject == 0) {
											$final_grade = esc_html(WLSM_Helper::calculate_grade($marks_grades, $opti_and_main_sub_total_mark_percentage));
											echo $final_grade;
										} else {
											echo "F";
										}
										?>
									</th>
									<th>
										<?php
										if ($total_failde_subject == 0) {
											$gpa_result = number_format(WLSM_M_Setting::calculateGPA($final_grade), 2);
											echo $gpa_result;
										} else {
											echo "0.00";
										}
										?>
									</th>

								<?php } ?>
							</tr>
							<?php if ($show_rank === '1') { ?>
								<tr>
									<th colspan="2">
										<?php esc_html_e('Rank', 'school-management'); ?>
									</th>
									<?php

									?>
									<th colspan="<?php echo esc_html($show_marks_grades ? '1' : '1'); ?>">
										<?php
										echo $students_rank['rank'];

										?>
									</th>

									<th colspan="3">
										<?php esc_html_e('Failed of Subject', 'school-management'); ?>
									</th>
									<th colspan="2">
										<?php
										echo $total_failde_subject;
										?>
									</th>
								</tr>
							<?php } ?>

							<?php if ($show_eremark === '1') { ?>
								<tr>
									<td colspan="2"><strong>
											<?php esc_html_e('Hadteacher :', 'school-management'); ?>
										</strong>
										<?php echo $teacher_remark; ?>
									</td>
									<td colspan="2"></td>

									<td colspan="2"><strong>
											<?php esc_html_e('Principal :', 'school-management'); ?>
										</strong>
										<?php echo $school_remark; ?>
									</td>
									<td colspan="2"></td>

								</tr>
							<?php } ?>

							<?php if ($psychomotor_enable === '1'): ?>

								<table class="table table-bordered wlsm-view-exam-results-table">
									<thead>
										<tr>
											<th colspan="10">
												<?php esc_html_e('Psychomotor Analysis', 'school-management'); ?>
											</th>
										</tr>

									</thead>

									<tbody>
										<tr>
											<?php foreach ($psychomotor['psych'] as $key => $value): ?>
												<td>
													<?php echo $value; ?>
												</td>
											<?php endforeach ?>
										</tr>
										<tr>
											<?php foreach ($p_scale as $value): ?>
												<td>
													<?php echo $value; ?>
												</td>
											<?php endforeach ?>
										</tr>
									</tbody>
								</table>

							<?php endif ?>
						</tbody>
					</table>
					<?php if ($psychomotor_enable === '1'): ?>
						<table class="table table-bordered wlsm-view-exam-results-table">
							<thead>
								<tr>
									<th scope="col">
										<?php esc_html_e('Scale', 'school-management'); ?>
									</th>
									<th scope="col">
										<?php esc_html_e('Defination', 'school-management'); ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php $s = 1; ?>
								<?php foreach ($psychomotor['def'] as $key => $value): ?>
									<tr>
										<th scope="row">
											<?php echo $s++; ?>
										</th>
										<td>
											<?php echo $value; ?>
										</td>
									</tr>
								<?php endforeach ?>

							</tbody>
						</table>
					<?php endif ?>


				</div>
			</div>
		</div>

	</div>
</div>

<?php
$i++;
?>
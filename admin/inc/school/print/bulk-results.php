<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

if ( isset( $from_front ) ) {
    $print_button_classes = 'button btn-sm btn-success';
} else {
    $print_button_classes = 'btn btn-sm btn-success';
}

$exam_title = $exam->exam_title;
$start_date = $exam->start_date;
$end_date   = $exam->end_date;

?>

<!-- Print Admit Cards. -->
<div class="wlsm-container d-flex mb-2">
    <div class="col-md-12 wlsm-text-center">
        <br>
        <button type="button" class="<?php echo esc_attr( $print_button_classes ); ?> mt-2" id="wlsm-print-exam-admit-card-btn" data-styles='["<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/bootstrap.min.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/wlsm-school-header.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/print/wlsm-exam-admit-card-two.css' ); ?>"]' data-title="<?php
            printf(
                /* translators: 1: exam title, 2: start date, 3: end date, 4: exam classes */
                esc_attr__( 'Results: %1$s (%2$s - %3$s), Class: %4$s', 'school-management' ),
                esc_html( WLSM_M_Staff_Examination::get_exam_label_text( $exam_title ) ),
                esc_html( WLSM_Config::get_date_text( $start_date ) ),
                esc_html( WLSM_Config::get_date_text( $end_date ) ),
                esc_html( $class_names )
            );
            ?>"><?php esc_html_e( 'Print Results', 'school-management' ); ?>
        </button>
    </div>
</div>

<!-- Print Admit Cards section. -->
<div class="wlsm-container wlsm wlsm-form-section wlsm-print-exam-admit-card" id="wlsm-print-exam-admit-card">
    <div class="wlsm-print-exam-admit-card-container">
        <!-- Print Admit Cards section. -->
        <?php

		$new_result_array = [];

        foreach ( $results as $result ) {
			
            $class_school_id = $wpdb->get_var( $wpdb->prepare( 'SELECT class_school_id FROM ' . WLSM_CLASS_SCHOOL_EXAM . ' WHERE exam_id = %s', $result->exam_id ) );

            $exam_common_papers = WLSM_M_Staff_Examination::get_exam_papers_by_admit_card( $school_id, $result->admit_card_id, $class_school_id );

            $new_common_subject = array();
            foreach ( $exam_common_papers as $key => $single_subject ) {
                $subject_group_array = unserialize( $single_subject->subject_group )["subject_group"];

                if ( in_array( $classGroup, $subject_group_array ) ) {
                    $new_common_subject[] = $single_subject;
                }
            }

            $exam_religion_papers = WLSM_M_Staff_Examination::get_religion_exam_papers_by_admit_card( $school_id, $result->admit_card_id, $class_school_id );
            $exam_results         = WLSM_M_Staff_Examination::get_exam_results_by_admit_card( $school_id, $result->admit_card_id );
            $exam_papers          = array_merge( $new_common_subject, $exam_religion_papers );
            $exam                 = WLSM_M_Staff_Examination::fetch_exam( $school_id, $result->exam_id );
            $admit_card_id        = $result->admit_card_id;
            $exam_id              = $exam->ID;
            $show_rank            = $exam->show_rank;
            $show_remark          = $exam->show_remark;
            $show_eremark         = $exam->show_eremark;
            $psychomotor_enable   = $exam->psychomotor_analysis;
            $psychomotor          = WLSM_Config::sanitize_psychomotor( $exam->psychomotor );
            ?>
            <?php require WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/print/partials/result2.php'; ?>
            <div class="page-break"></div>
        <?php
        }

		function student_ranks($students_marks){
			// Create a new array to store ranked students
			$rankedStudents = [];
			// Sort the original array by total marks in descending order
			usort($students_marks, function($a, $b) {
				return $b["total_marks"] - $a["total_marks"];
			});
			// Assign ranks to students in the new array
			$rank = 1;
			foreach ($students_marks as $student) {
				if ($student["total_failed_subjects"] == 0) {
					// Create a new array with rank and push it to the new array
					$rankedStudents[] = array_merge($student, ["rank" => $rank]);
					$rank++;
				} else {
					// Add student with rank as null
					$rankedStudents[] = array_merge($student, ["rank" => null]);
				}
			}

			return $rankedStudents;
		}

		// echo "<pre>";
		// print_r($new_result_array);
		// echo "</pre>";
		
		
		$rankedStudents = student_ranks($new_result_array);
		

		foreach ( $results as $result ) {
			
            $class_school_id = $wpdb->get_var( $wpdb->prepare( 'SELECT class_school_id FROM ' . WLSM_CLASS_SCHOOL_EXAM . ' WHERE exam_id = %s', $result->exam_id ) );

            $exam_common_papers = WLSM_M_Staff_Examination::get_exam_papers_by_admit_card( $school_id, $result->admit_card_id, $class_school_id );

            $new_common_subject = array();
            foreach ( $exam_common_papers as $key => $single_subject ) {
                $subject_group_array = unserialize( $single_subject->subject_group )["subject_group"];

                if ( in_array( $classGroup, $subject_group_array ) ) {
                    $new_common_subject[] = $single_subject;
                }
            }

            $exam_religion_papers = WLSM_M_Staff_Examination::get_religion_exam_papers_by_admit_card( $school_id, $result->admit_card_id, $class_school_id );
            $exam_results         = WLSM_M_Staff_Examination::get_exam_results_by_admit_card( $school_id, $result->admit_card_id );
            $exam_papers          = array_merge( $new_common_subject, $exam_religion_papers );
            $exam                 = WLSM_M_Staff_Examination::fetch_exam( $school_id, $result->exam_id );
            $admit_card_id        = $result->admit_card_id;
            $exam_id              = $exam->ID;
            $show_rank            = $exam->show_rank;
            $show_remark          = $exam->show_remark;
            $show_eremark         = $exam->show_eremark;
            $psychomotor_enable   = $exam->psychomotor_analysis;
            $psychomotor          = WLSM_Config::sanitize_psychomotor( $exam->psychomotor );
            ?>
            <?php require WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/print/partials/result.php'; ?>
            <div class="page-break"></div>
        <?php
        }
        ?>
    </div>
</div>

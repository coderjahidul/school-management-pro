<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

if ( isset( $from_front ) ) {
	$print_button_classes = 'button btn-sm btn-success';
} else {
	$print_button_classes = 'btn btn-sm btn-success';
}

$grade_criteria = WLSM_Config::sanitize_grade_criteria( $exam->grade_criteria );

$enable_overall_grade = $grade_criteria['enable_overall_grade'];
$marks_grades         = $grade_criteria['marks_grades'];

$settings_dashboard       = WLSM_M_Setting::get_settings_dashboard( $school_id );
$school_enrollment_number = $settings_dashboard['school_enrollment_number'];
$school_admission_number  = $settings_dashboard['school_admission_number'];

$settings_url      = WLSM_M_Setting::get_settings_certificate_qcode_url( $school_id );
$school_result_url = $settings_url['result_url'];
?>

<!-- Print exam results. -->
<div class="wlsm-container d-flex mb-2">
	<div class="col-md-12 wlsm-text-center">
		<br>
		<button type="button"
			class="<?php echo esc_attr( $print_button_classes ); ?>"
			id="wlsm-print-exam-results-btn"
			data-styles='["<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/bootstrap.min.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/wlsm.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/wlsm-school-header.css' ); ?>"]'
			data-title="">
			<?php esc_html_e( 'Print Exam Results', 'school-management' ); ?>
		</button>
	</div>
</div>

<!-- Print exam results section. -->
<?php
global $wpdb;

// ── 1. Resolve class_school_id ───────────────────────────────────────────────
if ( ! isset( $class_school_id ) || ! $class_school_id ) {
	$class_school_id = $wpdb->get_var( $wpdb->prepare(
		'SELECT class_school_id FROM ' . WLSM_CLASS_SCHOOL_EXAM . ' WHERE exam_id = %s',
		$exam_id
	) );
}
if ( ! $class_school_id ) {
	$class_school_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT se.class_school_id FROM " . WLSM_ADMIT_CARDS . " as ac
		 JOIN " . WLSM_STUDENT_RECORDS . " as sr ON sr.ID = ac.student_record_id
		 JOIN " . WLSM_SECTIONS . " as se ON se.ID = sr.section_id
		 WHERE ac.ID = %d",
		$admit_card->ID
	) );
}

// ── 2. Ensure admit_card has note and session_id ─────────────────────────────
if ( ! isset( $admit_card->note ) || ! isset( $admit_card->session_id ) ) {
	$student_info = $wpdb->get_row( $wpdb->prepare(
		"SELECT note, session_id FROM {$wpdb->prefix}wlsm_student_records WHERE ID = %d",
		$admit_card->student_id
	) );
	if ( $student_info ) {
		$admit_card->note       = $student_info->note;
		$admit_card->session_id = $student_info->session_id;
	}
}

// Ensure the student's class roll number (sr.roll_number) is available for result.php.
if ( ! isset( $admit_card->student_class_roll_number ) && isset( $admit_card->student_id ) ) {
	$admit_card->student_class_roll_number = $wpdb->get_var( $wpdb->prepare(
		"SELECT roll_number FROM {$wpdb->prefix}wlsm_student_records WHERE ID = %d",
		$admit_card->student_id
	) );
}

// admit_card_id alias used inside result.php at line ~552
$admit_card->admit_card_id = $admit_card->ID;
$admit_card_id             = $admit_card->ID;

// ── 3. Fetch & filter papers by student group (mirrors bulk-results.php) ─────
$classGroup         = isset( $admit_card->note ) ? $admit_card->note : '';
$exam_common_papers = WLSM_M_Staff_Examination::get_exam_papers_by_admit_card( $school_id, $admit_card->ID, $class_school_id );
$new_common_subject = array();
foreach ( $exam_common_papers as $single_subject ) {
	$subject_group_raw   = $single_subject->subject_group;
	$subject_group_array = $subject_group_raw ? @unserialize( $subject_group_raw ) : array();
	if ( ! empty( $classGroup ) && is_array( $subject_group_array ) && isset( $subject_group_array['subject_group'] ) ) {
		if ( in_array( $classGroup, $subject_group_array['subject_group'] ) ) {
			$new_common_subject[] = $single_subject;
		}
	} else {
		// No group filtering — include all subjects
		$new_common_subject[] = $single_subject;
	}
}

$exam_religion_papers = WLSM_M_Staff_Examination::get_religion_exam_papers_by_admit_card( $school_id, $admit_card->ID, $class_school_id );
$exam_results         = WLSM_M_Staff_Examination::get_exam_results_by_admit_card( $school_id, $admit_card->ID );
$exam_papers          = array_merge( $new_common_subject, $exam_religion_papers );

// ── 4. Build rankedStudents array (single student) ────────────────────────────
$student_rank   = WLSM_M_Staff_Examination::calculate_exam_ranks( $school_id, $exam_id, array(), $admit_card->ID, $admit_card->note );
$rankedStudents = array(
	array(
		'id'   => $admit_card->ID,
		'rank' => $student_rank,
	),
);

// ── 5. Set $result alias and exam variables expected by result.php ───────────
$result             = $admit_card;
$exam_title         = $exam->exam_title;
$start_date         = $exam->start_date;
$end_date           = $exam->end_date;
$show_rank          = $exam->show_rank;
$show_remark        = $exam->show_remark;
$show_eremark       = $exam->show_eremark;
$psychomotor_enable = $exam->psychomotor_analysis;
$psychomotor        = WLSM_Config::sanitize_psychomotor( $exam->psychomotor );

// ── 6. Include the premium result card template ──────────────────────────────
$i = 0;
require WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/print/partials/result.php';
?>

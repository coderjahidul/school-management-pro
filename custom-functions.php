<?php 


add_shortcode('test-the-query', 'get_mark_by_paper_code_shortcode');

function get_mark_by_paper_code_shortcode(){

	global $wpdb;

	$exam_id = 23;
	$paper_code = 101;
	$subject_type = "mcq";
	$admission_number = 7000;
	$section_id = 3;

	$student_record_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_student_records WHERE admission_number = %d AND section_id = %d ",
		$admission_number,
		$section_id
	));

	$exam_paper_id = $wpdb->get_var($wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_exam_papers WHERE exam_id = %d AND paper_code = %d AND subject_type = %s",
		$exam_id,
		$paper_code,
		$subject_type
	));


	$admit_card_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_admit_cards WHERE exam_id = %d AND student_record_id = %d ",
		$exam_id,
		$student_record_id
	));

    $exam_ontained_mark =$wpdb->get_var( $wpdb->prepare(
        "SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
        $exam_paper_id ,
        $admit_card_id
    ));
    
    return $exam_ontained_mark;

}



// get mark by paper code
function get_mark_by_paper_code($exam_id, $paper_code, $subject_type, $admission_number, $section_id){

	global $wpdb;

	// $exam_id = 23;
	// $paper_code = 101;
	// $subject_type = "subjective";
	// $admission_number = 7000;
	// $section_id = 3;

	$student_record_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_student_records WHERE admission_number = %d AND section_id = %d ",
		$admission_number,
		$section_id
	));

    

	$exam_paper_id = $wpdb->get_var($wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_exam_papers WHERE exam_id = %d AND paper_code = %d AND subject_type = %s",
		$exam_id,
		$paper_code,
		$subject_type
	));

	$admit_card_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_admit_cards WHERE exam_id = %d AND student_record_id = %d ",
		$exam_id,
		$student_record_id
	));

    $exam_ontained_mark =$wpdb->get_var( $wpdb->prepare(
        "SELECT obtained_marks  FROM {$wpdb->prefix}wlsm_exam_results WHERE exam_paper_id = %d AND admit_card_id = %d ",
        $exam_paper_id ,
        $admit_card_id
    ));
    
    return $exam_ontained_mark;

}

// get subject maximum mark by paper code 
function get_subject_maximum_mark_by_paper_code($exam_id, $paper_code, $subject_type){

	global $wpdb;

	$exam_paper_id = $wpdb->get_var($wpdb->prepare(
		"SELECT ID FROM {$wpdb->prefix}wlsm_exam_papers WHERE exam_id = %d AND paper_code = %d AND subject_type = %s",
		$exam_id,
		$paper_code,
		$subject_type
	));

	$maximum_marks = $wpdb->get_var( $wpdb->prepare(	
		"SELECT maximum_marks  FROM {$wpdb->prefix}wlsm_exam_papers WHERE ID = %d ",
		$exam_paper_id
	));

	return $maximum_marks;
}


function get_section_id($enrollment_number, $admission_number, $session_id){
    global $wpdb;

    $section_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT section_id FROM {$wpdb->prefix}wlsm_student_records WHERE enrollment_number = %d AND admission_number = %d AND  session_id = %d ",
        $enrollment_number,
        $admission_number,
        $session_id
    ));

    return $section_id;
}

// add_shortcode('jahidul', 'subject_list');

function put_program_logs( $data ) {

	// Ensure the directory for logs exists
	$directory = plugin_dir_path( __FILE__ ) . '/program_logs/';
	if ( !file_exists( $directory ) ) {
		mkdir( $directory, 0777, true );
	}

	// Construct the log file path
	$file_name = $directory . 'program_logs.log';

	// Append the current datetime to the log entry
	$current_datetime = date( 'Y-m-d H:i:s' );
	$data             = $data . ' - ' . $current_datetime;

	// Write the log entry to the file
	if ( file_put_contents( $file_name, var_dump( $data ) . "\n\n", FILE_APPEND | LOCK_EX ) !== false ) {
		return "Data appended to file successfully.";
	} else {
		return "Failed to append data to file.";
	}
}

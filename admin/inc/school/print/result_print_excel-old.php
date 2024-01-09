<?php
defined( 'ABSPATH' ) || die();



require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

if ( isset( $from_front ) ) {
	$print_button_classes = 'button btn-sm btn-success';
} else {
	$print_button_classes = 'btn btn-sm btn-success';
}  
?>





							
		<!-- <center class="excel_result_box" id="wlsm-print-result-excel">

				<?php 

					// global $wpdb;

					// a query to get class scroll id from exam_id
					// $query = $wpdb->prepare("SELECT class_school_id FROM {$wpdb->prefix}wlsm_class_school_exam WHERE exam_id = %d", $exam_id);
					// $class_school_id = $wpdb->get_var($query);

					// a query to get all section ids from the class_school_id
					// $query2 = $wpdb->prepare("SELECT ID FROM {$wpdb->prefix}wlsm_sections WHERE class_school_id = %d", $class_school_id);
					// $section_ids = $wpdb->get_results($query2);

					// get the sections in an array using the selection of section
					// $sections = [];
					// if($section_id == 0){
					// 	foreach($section_ids as $single_section){
					// 		$sections[] = $single_section->ID;
					// 	}
					// }else {
					// 	$sections[] = $section_id;
					// }

					// a loop in the sections to get all the students from that section 

					// $students = [];

					// foreach($sections as $section){
					// 	$student_query = $wpdb->prepare("SELECT ID, name, roll_number FROM {$wpdb->prefix}wlsm_student_records WHERE section_id = %d", $section);

					// 	$student_records = $wpdb->get_results($student_query);

					// 	foreach($student_records as $key => $single_student){
					// 		$students[$single_student->ID]["ID"] = $single_student->ID;
					// 		$students[$single_student->ID]["name"] = $single_student->name;
					// 		$students[$single_student->ID]["roll_number"] = $single_student->roll_number;
					// 	}
					// }





				?>


		
			<table>
				
			
				<?php
				
					$mergedArray = [];
					foreach($results as $key=>$res){
						$admitCardId = $res->admit_card_id;

						if (!isset($mergedArray[$admitCardId])) {
							$mergedArray[$admitCardId] = [];
						}
					
						$mergedArray[$admitCardId][] = $res;
						
						
					}

			
				

					$outputArray = array();

					foreach ($mergedArray as $admitCardID => $subjects) {
						$subjectData = $subjects[0]; // Extracting common data from the first subject entry
						
						$outputArray[$admitCardID] = array(
							'admit_card_id' => $subjectData->admit_card_id,
							'exam_id' => $subjectData->exam_id,
							'roll_number' => $subjectData->roll_number,
							'class_label' => $subjectData->class_label,
							'section_label' => $subjectData->section_label,
							'name' => $subjectData->name,
							'enrollment_number' => $subjectData->enrollment_number,
							'religion' => $subjectData->religion,
							'subjects' => array()
						);

						foreach ($subjects as $subject) {
							$outputArray[$admitCardID]['subjects'][] = $subject;
						}
					}

					// Re-index the array to have sequential keys
					$outputArray = array_values($outputArray);

				
					$papercodeIds = [];
					foreach($outputArray as $single_student){
							foreach($single_student['subjects'] as $student_data){
								$papercodeIds[$student_data->paper_code] = $student_data->subject_label ;
							}

					}

					echo '<tr>';
					echo '<th rowspan="2">R.NO</th>';
					echo '<th style="width:15%;" rowspan="2">Student Name</th>';
					echo '<th colspan="'. count($papercodeIds) .'">Subject Name</th>';
					echo '<th rowspan="2">Total Marks</th>';
					echo '</tr>';
					echo "<tr>";
						foreach($papercodeIds as $paper){
						echo '<th style="width:8%;">'. $paper .'</th>';
						}
					echo "</tr>";
							
					foreach($outputArray as $single_student){
						echo "<tr>";
							echo "<td>" . $single_student['roll_number'] . "</td>";
							echo "<td>" . $single_student['name'] . "</td>";
							$totalObtainerMark = 0 ;
							foreach($single_student['subjects'] as $student_data){
								echo "<td>" . $student_data->obtained_marks . "</td>";
								$totalObtainerMark = $totalObtainerMark + $student_data->obtained_marks ;
							}
							echo "<td>" . $totalObtainerMark . "</td>";
						echo "</tr>";
						
					}
					

					
				?>




			</table>
		</center> -->

		<center >
    <table>

	<?php
				
				$mergedArray = [];
				foreach($results as $key=>$res){
					$admitCardId = $res->admit_card_id;

					if (!isset($mergedArray[$admitCardId])) {
						$mergedArray[$admitCardId] = [];
					}
				
					$mergedArray[$admitCardId][] = $res;
					
					
				}

		
			

				$outputArray = array();

				foreach ($mergedArray as $admitCardID => $subjects) {
					$subjectData = $subjects[0]; // Extracting common data from the first subject entry
					
					$outputArray[$admitCardID] = array(
						'admit_card_id' => $subjectData->admit_card_id,
						'exam_id' => $subjectData->exam_id,
						'roll_number' => $subjectData->roll_number,
						'class_label' => $subjectData->class_label,
						'section_label' => $subjectData->section_label,
						'name' => $subjectData->name,
						'enrollment_number' => $subjectData->enrollment_number,
						'religion' => $subjectData->religion,
						'subjects' => array()
					);

					foreach ($subjects as $subject) {
						$outputArray[$admitCardID]['subjects'][] = $subject;
					}
				}

				// Re-index the array to have sequential keys
				// $outputArray = array_values($outputArray);
				// $papercodeIds = [];
				// foreach($outputArray as $single_student){
				// 		foreach($single_student['subjects'] as $student_data){
				// 			$papercodeIds[$student_data->subject_label] = $student_data->parent_subject ;
							
				// 		}
				// }
				// $parent_subject_name = array_unique($papercodeIds);
				// echo "<pre>";
				// print_r($outputArray);
				// echo "</pre>";

				// echo '<tr>';
				// echo '<th rowspan="2">R.NO</th>';
				// echo '<th style="width:15%;" rowspan="2">Student Name</th>';
				// echo '<th colspan="'. count($papercodeIds) .'">Subject Name</th>';
				// echo '<th rowspan="2">Total Marks</th>';
				// echo '</tr>';
				// echo "<tr>";
				// 	foreach($papercodeIds as $paper){
				// 	echo '<th style="width:8%;">'. $paper .'</th>';
				// 	}
				// echo "</tr>";
						
				// foreach($outputArray as $single_student){
				// 	echo "<tr>";
				// 		echo "<td>" . $single_student['roll_number'] . "</td>";
				// 		echo "<td>" . $single_student['name'] . "</td>";
				// 		$totalObtainerMark = 0 ;
				// 		foreach($single_student['subjects'] as $student_data){
				// 			echo "<td>" . $student_data->obtained_marks . "</td>";
				// 			$totalObtainerMark = $totalObtainerMark + $student_data->obtained_marks ;
				// 		}
				// 		echo "<td>" . $totalObtainerMark . "</td>";
				// 	echo "</tr>";
					
				// }
				

				
			?>
       
        <tr>
            <th rowspan="2">R.NO</th>
            <th  rowspan="2">Student Name</th>
			<?php
				foreach($parent_subject_name as $key=>$paper){

					if($key == $paper){
						$colSpan = 3 ;
					}else{
						$colSpan = 5 ;
					}
				
					echo '<th  colspan="'. $colSpan .'" style="width:8%;">'. $paper .'</th>';
					}
			?>
            <th rowspan="2">Total Marks</th>
   
        </tr>
        <tr>
			<?php
			foreach($parent_subject_name as $key=>$paper){

				if($key == $paper){
					echo "<th >Total</th>";
					echo "<th >LG</th>";
					echo "<th >GP</th>";
				}else{
					echo "<th >1st Paper</th>";
					echo "<th >2nd Paper</th>";
					echo "<th >Total</th>";
					echo "<th >LG</th>";
					echo "<th >GP</th>";
				}
				}
			
			?>
        </tr>

        <tr>
            <td  >101</td>
            <td  >John Doe</td>
            <td>92</td>
			<td>88</td>
			<td>180</td>
			<td>5.00</td>
			<td>A+</td>
            <td>85</td>
			<td>87</td>
			<td>172</td>
			<td>4.50</td>
			<td>A</td>
            <td>88</td>
			<td>5.00</td>
			<td>A+</td>
            <td>82</td>
			<td>4.00</td>
			<td>A</td>
            <td>90</td>
			<td>5.00</td>
			<td>A+</td>
            <td>87</td>
			<td>5.00</td>
			<td>A+</td>
            <td>85</td>

        </tr>


   
    </table>
</center>
		
		<br>
		<br>
<div class="wlsm-container d-flex mb-2">
	<div class="col-md-12 wlsm-text-center">
		<br>
		<button type="button" class="<?php echo esc_attr( $print_button_classes ); ?>" id="wlsm-print-result-excel-btn" 
		data-styles='["<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/bootstrap.min.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/result_print_in_excel.css' ); ?>"]'>
			<?php esc_html_e( 'Print Result Excel', 'school-management' ); ?>
		</button>
	</div>
</div>

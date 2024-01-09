<?php 
	global $wpdb;
	

foreach ($allStudent as $key=>$dataInfo) :
    $studentValu = reset($dataInfo);

    // echo "<h2>". $studentValu->religion ."</h1>";
    // echo "<h2>".print_r($studentValu)."</h1>";

    $subject_information =$wpdb->get_results( $wpdb->prepare(
        'SELECT exp.subject_label , exp.subject_type, exp.paper_code, exp.maximum_marks, exp.religion 
        FROM ' . WLSM_EXAM_PAPERS . ' AS exp
        WHERE exp.ID = %d AND exp.exam_id= %d', $examPaperid , $examId
    ));

    $data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . WLSM_ADMIT_CARDS . ' WHERE exam_id = %d AND student_record_id = %d', $examId, $studentValu->ID ));


    $obtain_mark = $wpdb->get_var( $wpdb->prepare( 'SELECT obtained_marks FROM ' . WLSM_EXAM_RESULTS . ' WHERE exam_paper_id = %d AND admit_card_id = %d', $examPaperid, $data[0]->ID ));

    $unique_optional_subjects = $wpdb->get_results(
        $wpdb->prepare(
            'SELECT DISTINCT code FROM ' . WLSM_SUBJECTS . ' WHERE type = "objective"'
        )
    );
    
    $optional_subject_codes = array_column($unique_optional_subjects, 'code');
    $valueToRemove = $studentValu->optional_subject_code;
    
    // Find the index of the value to remove
    $key = array_search($valueToRemove, $optional_subject_codes);
    
    // If the value is found, remove it
    if ($key !== false) {
        unset($optional_subject_codes[$key]);
        // Reindex the array
        $optional_subject_codes = array_values($optional_subject_codes);

        $not_taken_subject_code = $optional_subject_codes[0];
    }
    
   
    if( ($studentValu->religion == $subject_information[0]->religion || $studentValu->optional_subject_code == $subject_information[0]->paper_code || $subject_information[0]->religion == "common") && ($subject_information[0]->paper_code != $not_taken_subject_code)) :

    
    ?>		
    <tr>
        <td><?php echo $studentValu->roll_number  ?></td>
        <td><?php echo $studentValu->name  ?></td>
        <td><?php echo  $subject_information[0]->maximum_marks; ?></td>
        <td><input type="number" id="" step="any" min="0" max="100" name="obtained_marks[]" class="form-control obtained_mark_input" value="<?php echo $obtain_mark; ?>"></td>
        <td><input type="text"  name="remark[]" class="form-control" value="">
        <input type="hidden" name="exam_paper_id" value="<?php echo $examPaperid ?>">
        <input type="hidden" name="admit_card_id[]" value="<?php echo $data[0]->ID;  ?>">
    
    </td>
    </tr>
    <?php 

   
    endif;

 endforeach;
 
?>




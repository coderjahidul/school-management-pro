<?php
global $wpdb;

// Sort the $allStudent array by roll_number before looping
usort($allStudent, function ($a, $b) {
    return $a[0]->roll_number <=> $b[0]->roll_number;
});

foreach ($allStudent as $key => $dataInfo) :
    $studentValu = reset($dataInfo);

    $subject_information = $wpdb->get_results($wpdb->prepare(
        'SELECT exp.subject_label, exp.subject_type, exp.paper_code, exp.maximum_marks, exp.religion 
        FROM ' . WLSM_EXAM_PAPERS . ' AS exp
        WHERE exp.ID = %d AND exp.exam_id = %d',
        $examPaperid,
        $examId
    ));

    $data = $wpdb->get_results($wpdb->prepare(
        'SELECT * FROM ' . WLSM_ADMIT_CARDS . ' WHERE exam_id = %d AND student_record_id = %d',
        $examId,
        $studentValu->ID
    ));

    $obtain_mark = $wpdb->get_var($wpdb->prepare(
        'SELECT obtained_marks FROM ' . WLSM_EXAM_RESULTS . ' WHERE exam_paper_id = %d AND admit_card_id = %d',
        $examPaperid,
        $data[0]->ID
    ));

    $unique_optional_subjects = $wpdb->get_results(
        $wpdb->prepare(
            'SELECT DISTINCT code FROM ' . WLSM_SUBJECTS . ' WHERE type = "objective"'
        )
    );


    $optional_subject_codes = array_column($unique_optional_subjects, 'code');
    $valueToRemove = $studentValu->optional_subject_code;

    // Remove the specific subject code if found
    if (($key = array_search($valueToRemove, $optional_subject_codes)) !== false) {
        unset($optional_subject_codes[$key]);
    }

    // Reindex the array to avoid missing keys
    $optional_subject_codes = array_values($optional_subject_codes);

    // Check if current paper code is NOT in the remaining optional subjects
    $not_taken = !in_array($subject_information[0]->paper_code, $optional_subject_codes);

    if (
        ($studentValu->religion == $subject_information[0]->religion
            || $studentValu->optional_subject_code == $subject_information[0]->paper_code
            || $subject_information[0]->religion == "common")
        && $not_taken
    ):
?>
        <tr>
            <td><?php echo $studentValu->roll_number; ?></td>
            <td><?php echo $studentValu->name; ?></td>
            <td><?php echo esc_html( $subject_information[0]->maximum_marks ); ?></td>
            <td><input type="number" step="any" min="0" max="<?php echo esc_attr( $subject_information[0]->maximum_marks ); ?>" name="obtained_marks[]" class="form-control obtained_mark_input" data-maximum-marks="<?php echo esc_attr( $subject_information[0]->maximum_marks ); ?>" value="<?php echo esc_attr( $obtain_mark ); ?>"></td>
            <td>
                <input type="text" name="remark[]" class="form-control" value="">
                <input type="hidden" name="exam_paper_id" value="<?php echo $examPaperid; ?>">
                <input type="hidden" name="admit_card_id[]" value="<?php echo $data[0]->ID; ?>">
            </td>
        </tr>
<?php
    endif;
endforeach;
?>
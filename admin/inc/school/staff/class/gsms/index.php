
<?php
defined( 'ABSPATH' ) || die();
global $wpdb;
$school_id = $current_school['id'];

// school header section. ---------------------------------------
$school = WLSM_M_School::fetch_school($school_id);
$settings_general = WLSM_M_Setting::get_settings_general($school_id);
$school_logo = $settings_general['school_logo'];
$school_signature = $settings_general['school_signature'];

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
$assessment_type_list = WLSM_Helper::assessment_type_list();

require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/global.php';
?>

<div class="wlsm container-fluid">
    <?php
    require_once WLSM_PLUGIN_DIR_PATH . 'admin/inc/school/staff/partials/header.php';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="mt-2 text-center wlsm-section-heading-block">
                <span class="wlsm-section-heading">
                    <i class="fas fa-id-card"></i>
                    <?php esc_html_e( 'Student Report Card', 'school-management' ); ?>
                </span>
            </div>
            <div class="wlsm-students-block wlsm-form-section">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" id="wlsm-student-transcript-form" class="mb-3">
                            <div class="pt-2">
                                <div class="row">
                                    <div class="col-md-8 mb-1">
                                        <div class="h6">
                                            <span class="text-secondary border-bottom">
                                                <?php esc_html_e( 'Search Students By Class', 'school-management' ); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="wlsm_class" class="wlsm-font-bold">
                                            <span class="wlsm-important">*</span> <?php esc_html_e( 'Class', 'school-management' ); ?>:
                                        </label>
                                        <select name="class_id" class="form-control selectpicker wlsm_class_subjects" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" data-nonce-subjects="<?php echo esc_attr( wp_create_nonce( 'get-class-subjects' ) ); ?>" id="wlsm_class" data-live-search="true">
                                            <option value=""><?php esc_html_e( 'Select Class', 'school-management' ); ?></option>
                                            <?php foreach ( $classes as $class ) { ?>
                                                <option value="<?php echo esc_attr( $class->ID ); ?>">
                                                    <?php echo esc_html( WLSM_M_Class::get_label_text( $class->label ) ); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="wlsm_class_group" class="wlsm-font-bold">
                                            <?php esc_html_e( 'Class Group', 'school-management' ); ?>:
                                        </label>
                                        <select name="class_group" class="form-control selectpicker" id="wlsm_class_group">
                                            <option value=""><?php esc_html_e( 'Select Class Group', 'school-management' ); ?></option>
                                            <option value="common" selected>Common</option>
                                            <option value="science">Science</option>
                                            <option value="commerce">Commerce</option>
                                            <option value="humanity">Humanity</option>
                                            <option value="vocational">Vocational</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="wlsm_section" class="wlsm-font-bold">
                                            <span class="wlsm-important">*</span> <?php esc_html_e( 'Section', 'school-management' ); ?>:
                                        </label>
                                        <select name="section_id" class="form-control selectpicker" id="wlsm_section" data-live-search="true" title="<?php esc_attr_e( 'Select Section', 'school-management' ); ?>" data-actions-box="true">
                                            <?php foreach ( $sections as $section ) { ?>
                                                <option value="<?php echo esc_attr( $section->ID ); ?>" <?php selected( in_array( $section->ID, $homework_sections ), true, true ); ?>>
                                                    <?php echo esc_html( WLSM_M_Staff_Class::get_section_label_text( $section->label ) ); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="wlsm-print-transcript-btn">
                                        <i class="fas fa-print"></i>&nbsp;
                                        <?php esc_html_e( 'Print Transcript', 'school-management' ); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <?php 
                            // Display student names and IDs based on selected parameters
                            if ( isset($_POST['class_group']) && isset($_POST['section_id'])) {
                                // Fetch selected values from the form
                                $class_id = absint($_POST['class_id']);
                                $class_group = sanitize_text_field($_POST['class_group']);
                                $section_id = absint($_POST['section_id']);
                                $class_label = isset($class->label) ? $class->label : '';

                                $section_label = $wpdb->get_results($wpdb->prepare("SELECT label FROM {$wpdb->prefix}wlsm_sections WHERE ID = %d", $section_id));
                                $section_label = isset($section_label[0]->label) ? $section_label[0]->label : '';

                                // Perform SQL query to fetch student records based on selected parameters
                                $table_name = $wpdb->prefix . 'wlsm_student_records';
                                $get_student_records = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM $table_name WHERE note = %s AND section_id = %d order by roll_number", $class_group, $section_id
                                ));

                                $bulksmsbd_apikey = $wpdb->get_results($wpdb->prepare("SELECT setting_value FROM {$wpdb->prefix}wlsm_settings WHERE setting_key = %s", 'bulksmsbd'));

                                foreach ($bulksmsbd_apikey as $value) {
                                    $serialized_data = $value->setting_value;
                                    $decoded_data = unserialize($serialized_data);

                                    // Check if $decoded_data is an array
                                    if (is_array($decoded_data)) {
                                        // Check if 'api_key' and 'sender' keys exist in the array
                                        if (isset($decoded_data['api_key']) && isset($decoded_data['sender'])) {
                                            // Access the values directly
                                            $api_key = $decoded_data['api_key'] . "<br>";
                                            $sender = $decoded_data['sender'] . "<br>";

                                            // Display student names and IDs
                                            if ($get_student_records) {
                                                echo '<div class="wlsm-student-list">';
                                                define('BULKSMSBD_API_URL', 'http://bulksmsbd.net/api/smsapi');
                                                define('BULKSMSBD_API_KEY', $api_key);
                                                define('BULKSMSBD_SENDER_ID', $sender);
                                                
                                                // Function to send SMS message using BulkSMSBD API
                                                function send_sms($to, $message) {
                                                    $url = BULKSMSBD_API_URL;
                                                    $api_key = BULKSMSBD_API_KEY;
                                                    $sender_id = BULKSMSBD_SENDER_ID;
                                                
                                                    // Construct URL with parameters
                                                    $url .= '?api_key=' . $api_key;
                                                    $url .= '&type=text';
                                                    $url .= '&number=' . urlencode($to);
                                                    $url .= '&senderid=' . $sender_id;
                                                    $url .= '&message=' . urlencode($message);
                                                
                                                    // Send request using cURL for better control and error handling
                                                    $ch = curl_init($url);
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                    $response = curl_exec($ch);
                                                    curl_close($ch);
                                                
                                                    // Check response
                                                    if ($response) {
                                                        // Check the response from BulkSMSBD API if needed
                                                        // For example, you might parse the JSON response to check for success or error messages
                                                        return true;
                                                    } else {
                                                        return false;
                                                    }
                                                }
                                                
                                                // Handle SMS submission when the form is submitted
                                                function handle_sms_submission() {
                                                    if (isset($_POST['send_sms'])) {
                                                        // Sanitize phone number and message
                                                        $to = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
                                                        $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
                                                
                                                        if (!empty($to) && !empty($message)) {
                                                            // Send SMS
                                                            $result = send_sms($to, $message);
                                                
                                                            if ($result) {
                                                                echo '<div class="updated"><p>SMS sent successfully!</p></div>';
                                                            } else {
                                                                echo '<div class="error"><p>Failed to send SMS. Please try again later.</p></div>';
                                                            }
                                                        } else {
                                                            echo '<div class="error"><p>Phone number and message are required.</p></div>';
                                                        }
                                                    }
                                                }
                                                // add_action('admin_init', 'handle_sms_submission');
                                                echo handle_sms_submission();
                                                ?>
                                                
                                                <div class="wrap">
                                                    <h1><?php esc_html_e('Send SMS', 'sms-sender');?></h1>
                                                    <form method="post" action="">
                                                        <div class="form-group">
                                                            <label for="phone_number">Phone Number:</label>
                                                            <input type="text" id="phone_number" name="phone_number" value="<?php foreach($get_student_records as $student){ echo $student->phone . ', '; }?>" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="message">Message:</label>
                                                            <textarea id="message" name="message" class="form-control" rows="10" required></textarea>
                                                        </div>
                                                        <button type="submit" name="send_sms" class="btn btn-primary">Send SMS</button>
                                                    </form>
                                                </div>
                                                <?php
                                                
                                                echo '</div>';
                                            } else {
                                                echo '<p>!No students found.</p>';
                                            }
                                        } else {
                                            echo "Error: 'api_key' or 'sender' key is missing from the array.<br>";
                                        }
                                    } else {
                                        // Handle the case where $decoded_data is not an array
                                        echo "Error: Decoded data is not an array.<br>";
                                    }
                                }



                                
                            }else {
                                $bulksmsbd_apikey = $wpdb->get_results($wpdb->prepare("SELECT setting_value FROM {$wpdb->prefix}wlsm_settings WHERE setting_key = %s", 'bulksmsbd'));

                                foreach ($bulksmsbd_apikey as $value) {
                                    $serialized_data = $value->setting_value;
                                    $decoded_data = unserialize($serialized_data);

                                    // Check if $decoded_data is an array
                                    if (is_array($decoded_data)) {
                                        // Check if 'api_key' and 'sender' keys exist in the array
                                        if (isset($decoded_data['api_key']) && isset($decoded_data['sender'])) {
                                            // Access the values directly
                                            $api_key = $decoded_data['api_key'];
                                            $sender = $decoded_data['sender'];
                                            define('BULKSMSBD_API_URL', 'http://bulksmsbd.net/api/smsapi');
                                            define('BULKSMSBD_API_KEY', $api_key);
                                            define('BULKSMSBD_SENDER_ID', $sender);
                                            
                                            // Function to send SMS message using BulkSMSBD API
                                            function send_sms($to, $message) {
                                                $url = BULKSMSBD_API_URL;
                                                $api_key = BULKSMSBD_API_KEY;
                                                $sender_id = BULKSMSBD_SENDER_ID;
                                            
                                                // Construct URL with parameters
                                                $url .= '?api_key=' . $api_key;
                                                $url .= '&type=text';
                                                $url .= '&number=' . urlencode($to);
                                                $url .= '&senderid=' . $sender_id;
                                                $url .= '&message=' . urlencode($message);
                                            
                                                // Send request using cURL for better control and error handling
                                                $ch = curl_init($url);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $response = curl_exec($ch);
                                                curl_close($ch);
                                            
                                                // Check response
                                                if ($response) {
                                                    // Check the response from BulkSMSBD API if needed
                                                    // For example, you might parse the JSON response to check for success or error messages
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            }
                                            
                                            // Handle SMS submission when the form is submitted
                                            function handle_sms_submission() {
                                                if (isset($_POST['send_sms'])) {
                                                    // Sanitize phone number and message
                                                    $to = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
                                                    $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
                                            
                                                    if (!empty($to) && !empty($message)) {
                                                        // Send SMS
                                                        $result = send_sms($to, $message);
                                            
                                                        if ($result) {
                                                            echo '<div class="updated"><p>SMS sent successfully!</p></div>';
                                                        } else {
                                                            echo '<div class="error"><p>Failed to send SMS. Please try again later.</p></div>';
                                                        }
                                                    } else {
                                                        echo '<div class="error"><p>Phone number and message are required.</p></div>';
                                                    }
                                                }
                                            }
                                            // add_action('admin_init', 'handle_sms_submission');
                                            echo handle_sms_submission();
                                            ?>
                                            
                                            <div class="wrap">
                                                <h1><?php esc_html_e('Send SMS', 'sms-sender');?></h1>
                                                <form method="post" action="">
                                                    <div class="form-group">
                                                        <label for="phone_number">Phone Number:</label>
                                                        <input type="text" id="phone_number" name="phone_number" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="message">Message:</label>
                                                        <textarea id="message" name="message" class="form-control" rows="10" required></textarea>
                                                    </div>
                                                    <button type="submit" name="send_sms" class="btn btn-primary">Send SMS</button>
                                                </form>
                                            </div>
                                        <?php  
                                        } else {
                                            echo "Error: 'api_key' or 'sender' key is missing from the array.<br>";
                                        }
                                    } else {
                                        // Handle the case where $decoded_data is not an array
                                        echo "Error: Decoded data is not an array.<br>";
                                    }
                                }

                                   
                            }
                            
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    
    ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('#wlsm-print-transcript-btn').on('click', function (e) {
            e.preventDefault();

            // Get selected values
            let classId = $('#wlsm_class').val();
            let classGroup = $('#wlsm_class_group').val();
            let sectionId = $('#wlsm_section').val();
            $('#wlsm-student-transcript-form').submit();
        });
        
    });

</script>



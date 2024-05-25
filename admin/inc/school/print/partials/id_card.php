<?php
defined( 'ABSPATH' ) || die();

// Registration settings.
$settings_registration           = WLSM_M_Setting::get_settings_registration( $school_id );
$school_registration_blood_group = $settings_registration['blood_group'];

$settings_dashboard       = WLSM_M_Setting::get_settings_dashboard( $school_id );
$school_enrollment_number = $settings_dashboard['school_enrollment_number'];
$school_admission_number  = $settings_dashboard['school_admission_number'];


require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

$school           = WLSM_M_School::fetch_school( $school_id );
$settings_general = WLSM_M_Setting::get_settings_general( $school_id );
$school_logo      = $settings_general['school_logo'];
$school_signature = $settings_general['school_signature'];
?>
<div class="wlsm-print-id-card-container">

	<div class="id_card">
        <div class="first-box">
     
            <div class="font-header" style="margin-top: 20px; background-image:url('<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/images/solid-color-image.png' ); ?>');" >
			<h3><?php echo esc_html( WLSM_M_School::get_label_text( $school->label ) ); ?></h3>
   
                
            </div>
            <div class="font-card" >
                
                <div class="images">
				<?php if ( ! empty( $photo_id ) ) { ?>
				<img style="object-fit:cover ;" src="<?php echo esc_url( wp_get_attachment_url( $photo_id ) ); ?>" class="wlsm-print-id-card-photo">
			<?php } ?>
                    <h1><?php 
                   
                    echo esc_html( WLSM_M_Staff_Class::get_name_text( $student->student_name ) ); 
                    ?></h1>
                    <p>Student ID: <?php echo esc_html( WLSM_M_Staff_Class::get_roll_no_text( $student->admission_number ) ); ?></p>
                </div>
                <div class="info">
                    <p><strong>Roll Number:</strong> <?php echo esc_html( $student->roll_number ); ?></p>
                    <p><strong>Class:</strong> <?php echo esc_html( WLSM_M_Class::get_label_text( $student->class_label ) ); ?></p>
                    <p><strong>Section:</strong> <?php echo esc_html( WLSM_M_Class::get_label_text( $student->section_label ) ); ?></p>
                    <p><strong>Blood Group:</strong> <?php echo esc_html( $student->blood_group ); ?></p>
                    <p style="font-size:12px;" ><strong>Father's Name:</strong> <?php echo esc_html( WLSM_M_Staff_Class::get_name_text( $student->father_name ) ); ?></p>
                    <p style="font-size:13px;"><strong>Address: </strong><?php echo esc_html( $student->address);?></p>
                    
                </div>
                
            </div>
            <!-- <div class="font-footer" style="background-image:url('<?php //echo esc_url( WLSM_PLUGIN_URL . 'assets/images/solid-color-image.png' ); ?>');">
                <h4><?php //echo esc_html( WLSM_M_School::get_label_text( $school->label ) ); ?></h4>
                <p><?php //echo esc_html( WLSM_M_School::get_email_text( $school->address ) ); ?></p>
            </div> -->
        </div>
        
        <!-- back side -->
        <div class="second-box ">
            <div class="font-header" style="margin-top:20px; background-image:url('<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/images/solid-color-image.png' ); ?>');">
                <h3><?php echo "If Found Please Return the Card"; ?></h3>
            </div>
            <div class="back-card">
                    <!-- <h5><?php //echo "If Found Please Return the Card" ?></h5> -->
					<div class="images">
						<?php if ( ! empty ( $school_logo ) ) { ?>
                            <img src="<?php echo esc_url( wp_get_attachment_url( $school_logo ) ); ?>" class="wlsm-print-school-logo">
                        <?php } ?>
					</div>
                    <div class="info">
                        <h5><?php echo esc_html( WLSM_M_School::get_label_text( $school->label ) ); ?></h5>
                        <p style="font-size: 13px;" ><?php echo home_url();?></p>
                        <p style="font-size: 13px;" ><strong>Email: </strong><?php echo esc_html( WLSM_M_School::get_email_text( $school->email ) );?></p>

                    
                        <span><strong>Phone: +88<?php echo esc_html( WLSM_M_School::get_label_text( $school->phone ) ); ?></strong> </span>
                        <img style="width: 35%;" src="<?php echo esc_url(wp_get_attachment_url($school_signature))?>" alt="school signature">
                        <p><?php echo esc_html("Authorized Signature");?></p>
                    </div>
                
            </div>
            <!-- <div class="font-footer" style="background-image:url('<?php //echo esc_url( WLSM_PLUGIN_URL . 'assets/images/solid-color-image.png' ); ?>');">
                <h4><?php //echo esc_html( WLSM_M_School::get_label_text( $school->label ) ); ?></h4>
                <p><?php //echo esc_html( WLSM_M_School::get_email_text( $school->address ) ); ?></p> -->
            </div>
        </div>
    </div>

</div>

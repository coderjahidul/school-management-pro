<?php
defined( 'ABSPATH' ) || die();


require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';


$settings_general = WLSM_M_Setting::get_settings_general( $school_id );
$school_logo      = $settings_general['school_logo'];
$school_signature      = $settings_general['school_signature'];

if ( isset( $from_front ) ) {
	$print_button_classes = 'button btn-sm btn-success';
} else {
	$print_button_classes = 'btn btn-sm btn-success';
}  
?>



<!-- Print ID cards. -->
<div class="wlsm-container d-flex mb-2">
	<div class="col-md-12 wlsm-text-center">
		<br>
		<button type="button" class="<?php echo esc_attr( $print_button_classes ); ?>" id="wlsm-print-testimonials-btn" 
		data-styles='["<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/bootstrap.min.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/wlsm_testimonials.css' ); ?>"]'
			data-title="<?php esc_attr__( 'Testimonials Print' , 'school-management' ); ?>">
			<?php esc_html_e( 'Print Testimonials', 'school-management' ); ?>
		</button>
	</div>
</div>

<!-- Print ID cards section. -->
    <div class="school-form" id="wlsm-print-testimonials"  >
    <style>

.container::before{
    content: '';
    position: absolute;
    top: 30px;
    left: 0;
    background-size: 600px;
    background-image: url('<?php  echo WLSM_PLUGIN_URL . "assets/images/education_bangla.png" ?>');
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    height: 100%;
    opacity: .2;
    z-index: 2;
}
    
</style> 
        <div class="container" style="border-image-source: url('<?php  echo WLSM_PLUGIN_URL . "assets/images/out.png" ?>');">
        <img style="position:absolute;height:60px;" src="<?php echo esc_url( wp_get_attachment_url( $school_logo ) ); ?>" class="wlsm-print-school-logo school_logo_style">
            <div class="school-info">
                <h1 id="school-name"><?php echo $school_name ;?></h1>
                <p><?php echo $school_location; ?></p>
                <h5><?php echo $school_established ;?></h5>
            </div>
            <div class="center-container">
                <span class="appre"><?php echo $testimonials_name ; ?></span>
            </div>
            <div class="date-time">
                <div class="kromik-num">ক্রমিক নং:<span class="kromik_number testimonials" ><?php echo $kromik_number; ?></span> </div>
                <div class="tarik-no ">তারিখ: <span class="date testimonials"><?php echo $today_date; ?></span></div>
            </div>
            <div class="content">
                <div class="description">
                    <p>এতদ্বারা অভিজ্ঞান পত্র দেওয়া যাইতেছে যে,<span class="certified testimonials"><?php echo ($students[0]->name == "")? $student_name : $students[0]->name ; ?></span><br>পিতা নাম :<span class="father testimonials"><?php echo ($students[0]->father_name == "")? $father_name : $students[0]->father_name ; ?></span> মাতা নাম:<span class="mother testimonials"><?php echo ($students[0]->mother_name == "")? $mother_name : $students[0]->mother_name ; ?></span>রোল নং<span class="villag testimonials"><?php echo $board_roll ; ?></span>রেজি নং<span class="post testimonials"><?php echo $registration_number ;?></span> সেশন<span class="upazila testimonials"><?php echo ($students[0]->session_id == "")? $session : $students[0]->session_id ; ?></span>বোর্ড<span class="zilla testimonials"><?php echo $board ; ?></span> হইতে<span class="pass-date testimonials"><?php echo $passing_date ; ?></span>সনে মাধ্যমিক/উচ্চ মাধ্যমিক/স্নাতক থেকে মানবিক/ব্যবসায় শিক্ষা/বিজ্ঞান/কারিগরি শাখায় পরীক্ষা দিয়া<span class="roll testimonials"><?php echo $cgpa ; ?></span>বিভাগে পাশ করিয়াছে। <br>তাহার নৈতিক চরিত্র ভালো। এই স্কুল/কলেজ/বিশ্ববিদ্যালয় অধ্যয়ন কালে সে প্রতিষ্ঠানের এবং রাষ্টের আইন শৃঙ্খলার পরিপন্থী কোন রকম কাজে লিপ্ত ছিল বলিয়া আমার জানা নেই।</p>
                    <p>আমি তাহার উজ্জ্বল ভবিষ্যৎ কামনা করছি।</p>
                </div>
            </div>
            <div class="date-time">
                <div class="kromik-num"></div>
               
            </div>
            <div class="footer">
            <img style="transform: translateX(22px);height:60px ; width:120px;" src="<?php echo esc_url( wp_get_attachment_url( $school_signature ) ); ?>" class="wlsm-print-school-logo">
                <p>প্রধান শিক্ষক/অধ্যক্ষ</p>
                
            </div>
        </div>
    </div>


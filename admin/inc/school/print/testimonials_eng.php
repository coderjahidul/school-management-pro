<?php
defined( 'ABSPATH' ) || die();



require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';
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
<style type="text/css">
    /*first section*/
.content span.certified.testimonials::after {
  content: "";
  position: absolute;
  bottom: -7px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.certified.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 73% !important;
  position: relative;
  height: 16px;
}


.content span.father.testimonials::after {
    content: "";
    position: absolute;
    bottom: -7px;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 2px dotted #000;
}
.content span.father.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 32%;
  position: relative;
  height: 16px;
}
.content span.mother.testimonials::after {
  content: "";
  position: absolute;
  bottom: -7px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.mother.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 32%;
  position: relative;
  height: 16px;
}
.content span.villag.testimonials::after {
  content: "";
  position: absolute;
  bottom: -7px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.villag.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 33%;
  position: relative;
  padding-left: 5px;
  height: 16px;
}
.content span.post.testimonials::after {
  content: "";
  position: absolute;
  bottom: -7px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.post.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 33%;
  position: relative;
  padding-left: 5px;
  height: 16px;
}
.content span.upazila.testimonials::after {
  content: "";
  position: absolute;
  bottom: -7px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.upazila.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 24%;
  position: relative;
  height: 16px;
}
.content span.zilla.testimonials::after {
  content: "";
  position: absolute;
  bottom: -7px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.zilla.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 29%;
  position: relative;
  height: 16px;
}
.tarik-no span.date.testimonials {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 120px;
  position: relative;
  height: 15px;
}
.tarik-no span.date.testimonials::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.kromik-num span.kromik_number.testimonials::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}

</style>
<!-- Print ID cards. -->
<div class="wlsm-container d-flex mb-2">
	<div class="col-md-12 wlsm-text-center">
		<br>
		<button type="button" class="<?php echo esc_attr( $print_button_classes ); ?>" id="wlsm-print-testimonials-btn" 
		data-styles='["<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/bootstrap.min.css' ); ?>","<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/css/wlsm_new_testimonials_eng.css' ); ?>"]'
			data-title="<?php esc_attr__( 'Testimonials Print' , 'school-management' ); ?>">
			<?php esc_html_e( 'Print Testimonials', 'school-management' ); ?>
		</button>
	</div>
</div>

<!-- Print ID cards section. -->
    <div class="school-form" id="wlsm-print-testimonials" >
    <style>

.container::before{
    content: '';
    position: absolute;
    top: 30px;
    left: 0;
    background-size: 600px;
    background-image: url('<?php  echo WLSM_PLUGIN_URL . "assets/images/education_english.png" ?>');
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    height: 100%;
    opacity: .2;
    z-index: 2;
}
    
</style> 
        <div class="container" style="border-image-source: url('<?php echo WLSM_PLUGIN_URL . "assets/images/out.png" ?>');">
        <img style="position:absolute;height:60px;" src="<?php echo esc_url( wp_get_attachment_url( $school_logo ) ); ?>" class="wlsm-print-school-logo school_logo_style">
            <div class="school-info">
                <h1 id="school-name"><?php echo $school_name; ?></h1>
                <p><?php echo $school_location; ?></p>
                <h5><?php echo $school_established; ?></h5>
            </div>
            <div class="center-container">
                <span class="appre">Testimonials</span>
            </div>
            <div class="date-time">
                <div class="kromik-num">Serial No: <span class="kromik_number testimonials"><?php echo $kromik_number; ?></span> </div>
                <div class="tarik-no">Date: <span class="date testimonials"><?php echo $today_date; ?></span></div>
            </div>
            <div class="content">
                <div class="description">
                    <p>This is to certify that, <span class="certified testimonials"><?php echo ($students[0]->name == "") ? $student_name : $students[0]->name; ?></span><br>Father's Name: <span class="father testimonials"><?php echo ($students[0]->father_name == "") ? $father_name : $students[0]->father_name; ?></span> Mother's Name: <span class="mother testimonials"><?php echo ($students[0]->mother_name == "") ? $mother_name : $students[0]->mother_name; ?></span> Roll No: <span class="villag testimonials"><?php echo $board_roll; ?></span> Registration No: <span class="post testimonials"><?php echo $registration_number; ?></span> Session: <span class="upazila testimonials"><?php echo ($students[0]->session_id == "") ? $session : $students[0]->session_id; ?></span> Board: <span class="zilla testimonials"><?php echo $board; ?></span> Passed <span class="pass-date testimonials"><?php echo $passing_date; ?></span> in the year of <span class="roll testimonials"><?php echo $cgpa; ?></span> division in the field of humanities/commerce/science/technical/vocational branch from this school/college/university. He/She was not involved in any activities contrary to the discipline of this institution and the country's law during his/her period of study.<br>I wish him/her a bright future.</p>
                    
                </div>
            </div>
            <div class="date-time">
                <div class="kromik-num"></div>
            </div>
            <div class="footer">
            <img style="transform: translateX(22px);height:60px ; width:120px;" src="<?php echo esc_url( wp_get_attachment_url( $school_signature ) ); ?>" class="wlsm-print-school-logo">
                <p>Head Teacher/Principal</p>
                <h6 style="text-align:right;"><?php echo $second_school_name; ?></h6>
            </div>
        </div>
    </div>
    


<?php
defined( 'ABSPATH' ) || die();

$school_id = $current_school['id'];

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';

$settings_general = WLSM_M_Setting::get_settings_general( $school_id );
$school_logo      = $settings_general['school_logo'];
$school_signature      = $settings_general['school_signature'];
$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );

?>
<style>

/*first section*/
.content span.certified {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 87%;
  position: relative;
}
.content span.certified::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.tarik-no span.date {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 120px;
  position: relative;
}
.kromik-num span.kromik_number {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 120px;
  position: relative;
}
.tarik-no span.date::after {
  content: "";
  position: absolute;
  bottom: 6px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.kromik-num span.kromik_number::after {
  content: "";
  position: absolute;
  bottom: 6px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.father {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 38%;
  position: relative;
}
.content span.father::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.mother {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 43%;
  position: relative;
}
.content span.mother::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.villag {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 17%;
  position: relative;
}
.content span.villag::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}

/*first section*/
.content span.post {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 17%;
  position: relative;
}
.content span.post::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.upazila {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 20%;
  position: relative;
}
.content span.upazila::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.zilla {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 20%;
  position: relative;
}
.content span.zilla::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.pass-date {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 18%;
  position: relative;
}
.content span.pass-date::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.division {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 20%;
  position: relative;
}
.content span.division::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
/*first section*/
.content span.roll {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 18%;
  position: relative;
}
.content span.roll::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}
.content span.birth {
  font-size: 14px;
  display: inline-block;
  text-align: center;
  width: 18%;
  position: relative;
}
.content span.birth::after {
  content: "";
  position: absolute;
  bottom: 5px;
  left: 0;
  width: 100%;
  height: 1px;
  border-bottom: 2px dotted #000;
}



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
    z-index: 0;
}
    


</style>
<div class="row">
	<div class="col-md-12">
		<div class="mt-2 text-center wlsm-section-heading-block">
			<span class="wlsm-section-heading">
				<i class="fas fa-id-card"></i>
				<?php esc_html_e( 'Print Testimonials', 'school-management' ); ?>
			</span>
		</div>
		<div class="wlsm-students-block wlsm-form-section">
			<div class="row">
				<div class="col-md-12">
					<form  id="wlsm-print-testimonials-form" class="mb-3">
						<?php
						$nonce_action = 'print-id-cards';
						?>
						<?php $nonce = wp_create_nonce( $nonce_action ); ?>
						<input type="hidden" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( $nonce ); ?>">

						<input type="hidden" name="action" value="new_testimonials_eng">

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
										<?php esc_html_e( 'Class', 'school-management' ); ?>:
									</label>
									<select name="class_id" class="form-control selectpicker" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" id="wlsm_class" data-live-search="true">
										<option value=""><?php esc_html_e( 'Select Class', 'school-management' ); ?></option>
										<?php foreach ( $classes as $class ) { ?>
										<option value="<?php echo esc_attr( $class->ID ); ?>">
											<?php echo esc_html( WLSM_M_Class::get_label_text( $class->label ) ); ?>
										</option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label for="wlsm_section" class="wlsm-font-bold">
										<?php esc_html_e( 'Section', 'school-management' ); ?>:
									</label>
									<select name="section_id" class="form-control selectpicker" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get_all_section_student' ) ); ?>" id="wlsm_section" data-live-search="true" title="<?php esc_attr_e( 'All Sections', 'school-management' ); ?>"  data-all-sections="1">
									</select>
								</div>
								<div class="form-group col-md-4">
									<label for="wlsm_only_active" class="wlsm-font-bold">
										<?php esc_html_e( 'Students', 'school-management' ); ?>:
									</label>
									<select name="section_student" class="form-control selectpicker"  id="wlsm_student" >
									
										<option value=""><?php esc_html_e( 'Select Students', 'school-management' ); ?></option>
										
										
									</select>
								</div>
							</div>
						</div>
            <?php
            global $wpdb;
            $school_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wlsm_schools WHERE ID=%d", $school_id));
            // print_r($school_info);
            ?>

            <div class="form-row">
              <div class="school-form" >
                <div class="container" style="border-image-source: url('<?php echo WLSM_PLUGIN_URL . "assets/images/out.png" ?>');">
                <img style="position:absolute;" src="<?php echo esc_url( wp_get_attachment_url( $school_logo ) ); ?>" class="wlsm-print-school-logo">
                  <div class="school-info">
                  <input type="hidden" name="school_id" value ="<?php echo $school_id  ?>">
                    <h1 id="school-name"><input style="font-size:24px;" type="text" name="school_name" id="" value=" <?php echo (!empty($school_info->label) ? $school_info->label : "Mohonagor School and College") ;?>"></h1>
                    <p><input style="font-size:14px;" type="text" name="school_location" id="" value="<?php echo (!empty($school_info->address) ? $school_info->address : "Mohonagor, Netrokona ") ;?>"></p>
                    <h5 ><input style="font-size:20px;"  type="text" name="school_established" id="" value="Established:<?php echo (!empty($school_info->established_date) ? $school_info->established_date : "1919") ;?>"></h5>
                  </div>
                  <div class="center-container">
                    <span class="appre">Testimonials</span>
                  </div>
                  <div class="date-time">
                    <div class="kromik-num">Serial No: <span class="kromik_number"><input type="text" name="kromik_number" id="" value=""></span> </div>
                    <div class="tarik-no">Date: <span class="date"><input type="text" name="today_date" id=""></span></div>
                  </div>
                  <div class="content">
                    <div class="description color-jahid">
                      <p>This is to certify that, <span class="certified"><input type="text" name="student_name" id="name"  value=""></span><br>Father's Name: <span class="father"><input type="text" name="father_name" id="father_name"></span> Mother's Name: <span class="mother"><input type="text" name="mother_name" id="mother_name"></span> Roll No: <span class="villag"><input type="text" name="board_roll" id="board_roll"></span> Registration No: <span class="post"><input type="text" name="registration_number" id="board_reg"></span> Session: <span class="upazila"><input type="text" name="session" id="session"></span> Board: <span class="zilla"><input type="text" name="board" id="board"></span> Passed <span class="pass-date"><input type="text" name="passing_date" id="pass_date"></span> in the year of <span class="roll"><input type="text" name="cgpa" id="cgpa"></span> division in the field of humanities/commerce/science/technical/vocational branch from this school/college/university. He/She was not involved in any activities contrary to the discipline of this institution and the country's law during his/her period of study.<br>I wish him/her a bright future.</p>
                      
                    </div>
                  </div>
                  <div class="date-time">
                    <div class="kromik-num"></div>
             
                  </div>
                  <div class="footer">
                  <img src="<?php echo esc_url( wp_get_attachment_url( $school_signature ) ); ?>" class="wlsm-print-school-logo">
                    <p>Head Teacher / Principal</p>
                    
                  </div>
                </div>
              </div>
            </div>

						<div class="form-row">
							<div class="col-md-12">
								<button type="submit" class="btn btn-sm btn-outline-primary" id="wlsm_print_testimonialsBtn">
									<i class="fas fa-print"></i>&nbsp;
									<?php esc_html_e( 'Print Testimonials', 'school-management' ); ?>
								</button>

								
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<script>

(function ($) {
	$(document).on('change', '#wlsm_section', function() {
			var classId = $('#wlsm_class').val();
			var sectionId = $(this).val();
			var nonce = $(this).data('nonce');

			console.log(sectionId);


			if(classId && nonce) {
				var data = 'action=get_all_section_student&nonce=' + nonce + '&section_id=' + sectionId + '&class_id=' + classId;
			
				$.ajax({
					data: data,
					url: ajaxurl,
					type: 'POST',
					success: function(res) {
						console.log(res);
						var options = [];
						res.forEach(function(item) {
							var option = '<option value="' + item.ID + '">' + item.name  + '</option>';
							options.push(option);
						});
						var newOption = '<option value="">Select Here</option>';
						options.unshift(newOption);
						$('#wlsm_student').html(options);
						$('#wlsm_student').selectpicker('refresh');
					},
					error:function(){
						console.log("error found");
					}
				});
			}
		});


	$(document).on('change', '#wlsm_student', function() {
			var sectionId = $('#wlsm_section').val();
			var studentId = $(this).val();
			
			

			console.log(sectionId);
			console.log(studentId);


			if(sectionId && studentId) {
				var data = 'action=fetch_student_data_testimonials_cards&section_id=' + sectionId + '&section_student=' + studentId;
			
				$.ajax({
					data: data,
					url: ajaxurl,
					type: 'POST',
					success: function(res) {

						var data = JSON.parse(res.data.json);
						let dataObject = data[0];
						$("#name").val(dataObject.name);
						$("#father_name").val(dataObject.father_name);
						$("#mother_name").val(dataObject.mother_name);
						$("#session").val(dataObject.session_id);
						
					},
					error:function(){
						console.log("error found");
					}
				});
			}
		});


})(jQuery);

</script>


<?php





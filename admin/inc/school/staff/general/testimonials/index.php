<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_M_Setting.php';
$school_id = $current_school['id'];
$settings_general = WLSM_M_Setting::get_settings_general( $school_id );
$school_logo      = $settings_general['school_logo'];
$school_signature = $settings_general['school_signature'];

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );

?>

<style>

.school-form::before{
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

						<input type="hidden" name="action" value="print_testimonials_cards">

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

					<div class="form-row">
						<div class="school-form"  >

						<?php
							global $wpdb;
							$school_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wlsm_schools WHERE ID=%d", $school_id));

							// print_r($school_info);
							
						
						?>
									<div class="container" style="border-image-source: url('<?php  echo WLSM_PLUGIN_URL . "assets/images/out.png" ?>');">
									<img style="position:absolute;" src="<?php echo esc_url( wp_get_attachment_url( $school_logo ) ); ?>" class="wlsm-print-school-logo">
													<div class="school-info">
														<input type="hidden" name="school_id" value ="<?php echo $school_id  ?>">
														<h1 id="school-name"><input style="font-size:24px;" type="text" name="school_name" id="" value="<?php echo (!empty($school_info->school_name_bangla) ? $school_info->school_name_bangla : "মোহনগঞ্জ স্কুল এন্ড কলেজ") ;?>"></h1>
														<p><input style="font-size:14px;" type="text" name="school_location" id="" value="<?php echo (!empty($school_info->school_name_bangla) ? $school_info->address_bangla : "মোহনগঞ্জ,নেত্রকোণা") ;?>"></p>
														<h5 ><input style="font-size:20px;"  type="text" name="school_established" id="" value="প্রতিষ্ঠিত: <?php echo (!empty($school_info->school_name_bangla) ? $school_info->established_date_bangla : "১৯১৯") ;?>"></h5>
													</div>
													<div class="center-container">
														<span class="appre"> <input style="font-size:25px;color:#fff;width:150px;" type="text" name="testimonials_name" id="" value="প্রশংসা পত্র"></span>
													</div>
													<div class="date-time">
														<div class="kromik-num">ক্রমিক নং:<span class="kromik_number" ><input type="text" name="kromik_number" id="" value=""></span> </div>
														<div class="tarik-no">তারিখ: <span class="date"><input type="text" name="today_date" id=""></span></div>
													</div>
												<div class="content">
													<div class="description">
														<p>এতদ্বারা অভিজ্ঞান পত্র দেওয়া যাইতেছে যে,<span class="certified"><input type="text" name="student_name" id="name"  value=""></span><br>পিতা নাম :<span class="father"><input type="text" name="father_name" id="father_name"></span> মাতা নাম:<span class="mother"><input type="text" name="mother_name" id="mother_name"></span>রোল নং<span class="villag"><input type="text" name="board_roll" id="board_roll"></span>রেজি নং<span class="post"><input type="text" name="registration_number" id="board_reg"></span> সেশন<span class="upazila"><input type="text" name="session" id="session"></span>বোর্ড<span class="zilla"><input type="text" name="board" id="board"></span> হইতে<span class="pass-date"><input type="text" name="passing_date" id="pass_date"></span>সনে মাধ্যমিক/উচ্চ মাধ্যমিক/স্নাতক থেকে মানবিক/ব্যবসায় শিক্ষা/বিজ্ঞান/কারিগরি শাখায় পরীক্ষা দিয়া<span class="roll"><input type="text" name="cgpa" id="cgpa"></span>বিভাগে পাশ করিয়াছে। <br>তাহার নৈতিক চরিত্র ভালো। এই স্কুল/কলেজ/বিশ্ববিদ্যালয় অধ্যয়ন কালে সে প্রতিষ্ঠানের এবং রাষ্টের আইন শৃঙ্খলার পরিপন্থী কোন রকম কাজে লিপ্ত ছিল বলিয়া আমার জানা নেই।</p>
														<p>আমি তাহার উজ্জ্বল ভবিষ্যৎ কামনা করছি।</p>
													</div>
													</div>
													<div class="date-time">
														<div class="kromik-num"></div>
												
													</div>
													<div class="footer">
													<img  src="<?php echo esc_url( wp_get_attachment_url( $school_signature ) ); ?>" class="wlsm-print-school-logo">
														<p>প্রধান শিক্ষক/অধ্যক্ষ</p>
														
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





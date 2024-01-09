<?php
defined( 'ABSPATH' ) || die();

global $wpdb;

$page_url = WLSM_M_Staff_Class::get_subjects_page_url();

$school_id = $current_school['id'];

$subject = NULL;

$nonce_action = 'add-subject';

$subject_name = '';
$subject_code = '';
$subject_type = '';
$religion_type = '';
$sub_cat = '';
$parent_subject = '';
$subject_group = '';
$has_another_class = '';

$class_id = NULL;

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$id      = absint( $_GET['id'] );
	$subject = WLSM_M_Staff_Class::fetch_subject( $school_id, $id );

	if ( $subject ) {
		$nonce_action = 'edit-subject-' . $subject->ID;

		$subject_name = $subject->subject_name;
		$subject_code = $subject->code;
		$subject_type = $subject->type;
		$religion_type = $subject->religion;
		$sub_cat = $subject->subject_cat;
		$parent_subject = $subject->parent_subject;
		$subject_group = $subject->subject_group;

		$class_id = $subject->class_id;
	}
}

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );

$subject_types = WLSM_Helper::subject_type_list();
$religion_types = WLSM_Helper::religion_type_list();
?>
<div class="row">
	<div class="col-md-12">
		<div class="mt-3 text-center wlsm-section-heading-block">
			<span class="wlsm-section-heading-box">
				<span class="wlsm-section-heading">
					<?php
					if ( $subject ) {
						printf(
							wp_kses(
								/* translators: 1: subject name, 2: subject code */
								__( 'Edit Subject: %1$s (%2$s)', 'school-management' ),
								array(
									'span' => array( 'class' => array() )
								)
							),
							esc_html( WLSM_M_Staff_Class::get_subject_label_text( $subject_name ) ),
							esc_html( $subject_code )
						);
					} else {
						esc_html_e( 'Add New Subject', 'school-management' );
					}
					?>
				</span>
			</span>
			<span class="float-md-right">
				<a href="<?php echo esc_url( $page_url ); ?>" class="btn btn-sm btn-outline-light">
					<i class="fas fa-tags"></i>&nbsp;
					<?php esc_html_e( 'View All', 'school-management' ); ?>
				</a>
			</span>
		</div>
	
		<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="wlsm-save-subject-form">

			<?php $nonce = wp_create_nonce( $nonce_action ); ?>
			<input type="hidden" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( $nonce ); ?>">

			<input type="hidden" name="action" value="wlsm-save-subject">

			<?php if ( $subject ) { ?>
			<input type="hidden" name="subject_id" value="<?php echo esc_attr( $subject->ID ); ?>">
			<?php } ?>

			<div class="wlsm-form-section">
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="wlsm_label" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Subject Name', 'school-management' ); ?>:
						</label>
						<input type="text" name="label" class="form-control" id="wlsm_label" placeholder="<?php esc_attr_e( 'Enter subject name', 'school-management' ); ?>" value="<?php echo esc_attr( stripslashes( $subject_name ) ); ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="wlsm_code" class="wlsm-font-bold">
							<?php esc_html_e( 'Subject Code', 'school-management' ); ?>:
						</label>
						<input type="text" name="code" class="form-control" id="wlsm_code" placeholder="<?php esc_attr_e( 'Enter subject code', 'school-management' ); ?>" value="<?php echo esc_attr( $subject_code ); ?>">
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="wlsm_type" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Subject Type', 'school-management' ); ?>:
						</label>
						<select name="type" class="form-control selectpicker" id="wlsm_type">
							<?php foreach ( $subject_types as $key => $value ) { ?>
							<option <?php selected( $subject_type, $key, true ); ?> value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $value ); ?>
							</option>
							<?php } ?>
						</select>
					</div>

					<div class="form-group col-md-6">
						<label for="wlsm_class_id" class="wlsm-font-bold">
							<?php esc_html_e( 'Class', 'school-management' ); ?>:
						</label>
						<select name="class_id[]" class="form-control selectpicker" id="wlsm_class_id" multiple  data-live-search="true" data-actions-box="true" required>
							<?php foreach ( $classes as $class ) { ?>
							<option <?php selected( $class_id, $class->ID, true ); ?> value="<?php echo esc_attr( $class->ID ); ?>">
								<?php echo esc_html( WLSM_M_Class::get_label_text( $class->label ) ); ?>
							</option>
							<?php } ?>
						</select>
					</div>

					<!-- subject religion -->
					<div class="form-group col-md-6">
						<label for="wlsm_class_id" class="wlsm-font-bold">
							<?php esc_html_e( 'Subject Religion', 'school-management' ); ?>:
						</label>
						<select name="sub_religion" class="form-control selectpicker" id="wlsm_religion_id"  >
						<?php foreach ( $religion_types as $key => $value ) { ?>
							<option <?php selected( $religion_type, $key, true ); ?>   value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $value ); ?>
							</option>
							<?php } ?>
						</select>
					</div>

					<div class="form-group col-md-6" id="wlsm_calss_div" >
								<label for="wlsm_calss_section" class="wlsm-font-bold">
									<?php esc_html_e( 'Class Group ', 'school-management' ); ?>:
								</label>
								<select name="calss_group[]" class="form-control selectpicker" id="wlsm_calss_group" multiple data-live-search="true" data-actions-box="true" required  >
									<?php
									 $options = array(
										'common' => 'Common',
										'science' => 'Science',
										'commerce' => 'Commerce',
										'humanity' => 'Humanity',
										'vocational' => 'Vocational'
									);
									if(empty($subject_group)){
                                       foreach ($options as $value => $label) {
										echo "<option value='$value'>$label</option>";
									   }
									}else{
										$selected_groups = unserialize($subject_group)['subject_group'];
										foreach ($options as $value => $label) {
											$selected = in_array($value, $selected_groups) ? 'selected' : '';
											echo "<option value='$value' $selected>$label</option>";
										}
									}
									
									?>
									
								</select>
					</div>

					<!-- Is Main Subject -->
					<?php 
						$subject_cat = array(
							'parent' => "Parent Subject",
							'sub' => "Sub Subject"
						);
					?>
					<div class="form-group col-md-6">
						<label for="wlsm_class_id" class="wlsm-font-bold">
							<?php esc_html_e( 'Main / Sub Subject', 'school-management' ); ?>:
						</label>
						<select name="subject_cat" class="form-control selectpicker" id="wlsm_subject_cat"  required="required">
						<?php foreach ( $subject_cat as $key => $value ) { ?>
							<option <?php if($key == $sub_cat){ echo "selected" ;} ?> value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $value ); ?>
							</option>
							<?php } ?>
						</select>
					</div>

					<!-- Select Parent Subject -->
					<?php 
						global $wpdb;
						$all_subjects = $wpdb->prepare("SELECT * from {$wpdb->prefix}wlsm_subjects");
						
						$subjects = $wpdb->get_results($all_subjects);

						$id_based_subject = array();
						foreach($subjects as $key=>$subject){
							$id  = $subject->class_school_id ;
							$class_id_query = $wpdb->prepare("SELECT class_id from {$wpdb->prefix}wlsm_class_school WHERE ID =%d",$id );
							$class_id_query_res = $wpdb->get_results($class_id_query);
							$id_based_subject[$key]['class'] = $class_id_query_res;
							$id_based_subject[$key]['subject'] = $subject;
						}
						$subject_data = [];

						foreach ($id_based_subject as $item) {
							$class_id = $item['class'][0]->class_id;
							$subject_info = [
								'subject_id' => $item['subject']->ID,
								'subject_label' => $item['subject']->label,
								'class_id' => $class_id,
							];
							$subject_data[] = $subject_info;
						}

						// echo "<pre>";
						// print_r($subject);
						// echo "</pre>";
					?>
						<?php
						if($sub_cat == 'sub'){
							$style = 'display:block;';
						}else{
							$style = 'display:none;';
						}

						?>
					<div class="form-group col-md-6" id="sub_subject_div" style="<?php echo $style ; ?>">
						<label for="wlsm_class_id" class="wlsm-font-bold">
							<?php esc_html_e( 'Select Parent Subject', 'school-management' ); ?>:
						</label>
						<select name="parent_subject" class="form-control selectpicker" id="parent_subject">
							<option value=""></option>
						<?php foreach ( $subject_data as $key => $subject ) { ?>
							
							<option <?php echo ($parent_subject == $subject['subject_id'])? 'selected': '' ;?> value="<?php echo esc_attr( $subject['subject_id'] ); ?>">
								<?php echo esc_html( $subject['subject_label'] .' ( Class : ' . $subject['class_id'] . ')' ); ?>
							</option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>

			<div class="row mt-2">
				<div class="col-md-12 text-center">
					<button type="submit" class="btn btn-primary" id="wlsm-save-subject-btn">
						<?php
						if ( $subject ) {
							?>
							<i class="fas fa-save"></i>&nbsp;
							<?php
							esc_html_e( 'Update Subject', 'school-management' );
						} else {
							?>
							<i class="fas fa-plus-square"></i>&nbsp;
							<?php
							esc_html_e( 'Add New Subject', 'school-management' );
						}
						?>
					</button>
				</div>
			</div>

		</form>
	</div>
</div>


<script>
	(function($) {
		$(document).ready(function() {

		//  for subject parent or not 
		$("#wlsm_subject_cat").on("change" , function(){

			alert("gfhgf");
			var has_submenu = $(this).val();
			$('#sub_subject_div').hide();
			if(has_submenu == 'sub'){
			$("#sub_subject_div").show();
			}else if(has_submenu == 'parent'){
			$('#sub_subject_div').hide();
			}else{
			$('#sub_subject_div').hide();
			}

			});


		});
	});

</script>
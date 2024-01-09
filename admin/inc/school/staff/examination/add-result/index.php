<?php
defined( 'ABSPATH' ) || die();

$school_id = $current_school['id'];

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
global $wpdb;
// $exams_all = $wpdb->get_results( WLSM_M_Staff_Examination::fetch_exams_class_based( $school_id , 8 ));

?>
<div class="row">

	<div class="col-md-12">
		<div class="mt-2 text-center wlsm-section-heading-block">
			<span class="wlsm-section-heading">
				<i class="fas fa-id-card"></i>
				<?php esc_html_e( 'Add result for an examination', 'school-management' ); ?>
			</span>
		</div>
		<div class="wlsm-students-block wlsm-form-section">
			<div class="row">
				<div class="col-md-12">
					<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="wlsm_add_result_bulk_form" class="mb-3">
						<?php
						$nonce_action = 'add-result';
						?>
						<?php $nonce = wp_create_nonce( $nonce_action ); ?>
						<input type="hidden" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( $nonce ); ?>">

						<input type="hidden" name="action" value="wlsm_add_bulk_result">
						<input type="hidden" name="school_id" value="<?php echo $school_id ?>" id ="wlsm_school_id">

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
								<select name="class_id" class="form-control selectpicker" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" class="wlsm_class_for_exam" id="wlsm_class_for_exam" data-live-search="true">
									<option value=""><?php esc_html_e( 'Select Class', 'school-management' ); ?></option>
									<?php foreach ( $classes as $class ) { ?>
									<option value="<?php echo esc_attr( $class->ID ); ?>">
										<?php echo esc_html( WLSM_M_Class::get_label_text( $class->label ) ); ?>
									</option>
									<?php } ?>
								</select>
							</div>
						
							<div class="form-group col-md-4" id="wlsm_calss_div" >
								<label for="wlsm_calss_section" class="wlsm-font-bold">
									<?php esc_html_e( 'Class Group ', 'school-management' ); ?>:
								</label>
								<select name="calss_Group" class="form-control selectpicker" class="wlsm_calss_group" id="wlsm_calss_group"  >
									<option value=""><?php esc_html_e("Select Group", "school-management");?></option>
									<option value="common" selected><?php esc_html_e("Common", "school-management");?></option>
									<option value="science"><?php esc_html_e("Science", "school-management");?></option>
									<option value="commerce"><?php esc_html_e("Commerce", "school-management");?></option>
									<option value="humanity"><?php esc_html_e("Humanity", "school-management");?></option>
									<option value="vocational"><?php esc_html_e("Vocational", "school-management");?></option>
								</select>
							</div>

							<div class="form-group col-md-4">
								<label for="wlsm_calss_section" class="wlsm-font-bold">
									<?php esc_html_e( 'Class Section ', 'school-management' ); ?>:
								</label>
								<select name="calss_section" class="form-control selectpicker" id="wlsm_calss_section"  >
								</select>
							</div>
							<div class="form-group col-md-4">
								<label for="wlsm_calss_exam" class="wlsm-font-bold">
									<?php esc_html_e( 'Class Exam ', 'school-management' ); ?>:
								</label>
								<select name="calss_exam" class="form-control selectpicker" id="wlsm_calss_exam"  >
								</select>
							</div>
							<div class="form-group col-md-4">
								<label for="wlsm_exam_papers" class="wlsm-font-bold">
									<?php esc_html_e( 'Exams Papers', 'school-management' ); ?>:
								</label>
								<select name="exam_papers" class="form-control selectpicker" id="wlsm_exam_papers">
								
								
								</select>
							</div>
						</div>
					</div>

						<div class="form-row">
							<div class="col-md-12" id="excel_table_show">
							<div class="table-responsive w-100">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th><?php esc_html_e('Roll Number', 'school-management'); ?></th>
										<th><?php esc_html_e('Student Name', 'school-management'); ?></th>
										<th><?php esc_html_e('Maximum Marks', 'school-management'); ?></th>
										<th><?php esc_html_e('Obtained Marks', 'school-management'); ?></th>
										<th><?php esc_html_e('Remark', 'school-management'); ?></th>
									</tr>
								</thead>
								<tbody id="student_result_add_table">
								
										<tr>
											<td><?php esc_html_e("101", "school-management");?></td>
											<td><?php esc_html_e("Jahidul Islam", "school-management");?></td>
											<td><?php esc_html_e("100", "school-management");?></td>
											<td>
												<input type="number" id="" step="any" min="0" name="obtained_marks[]" class="form-control obtained_mark_input" value="">
											</td>
											<td>
												<input type="text"  name="remark[]" class="form-control" value="">
											</td>
										</tr>
									
									
								</tbody>
							</table>
						</div>
							</div>
						</div>


						<tbody>
						<div class="row mt-2" bis_skin_checked="1">
							<div class="col-md-12 text-center" bis_skin_checked="1">
							  <button type="submit" class="btn btn-primary" id="wlsm-save-exam-results-btn"><i class="fas fa-plus"></i>&nbsp;Add  Results For This Subject</button>
							</div>
						</div>
						</tbody>
				
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

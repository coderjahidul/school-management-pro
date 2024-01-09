<?php
defined( 'ABSPATH' ) || die();

$school_id = $current_school['id'];

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
global $wpdb;
$exams = $wpdb->get_results(WLSM_M_Staff_Examination::fetch_exams_admit( $school_id ));

?>
<div class="row">
	<div class="col-md-12">
		<div class="mt-2 text-center wlsm-section-heading-block">
			<span class="wlsm-section-heading">
				<i class="fas fa-id-card"></i>
				<?php esc_html_e( 'Print Results in Excel', 'school-management' ); ?>
			</span>
		</div>
		<div class="wlsm-students-block wlsm-form-section">
			<div class="row">
				<div class="col-md-12">
					<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="wlsm-print-bulk-result-form" class="mb-3">
						<?php
						$nonce_action = 'print-result';
						?>
						<?php $nonce = wp_create_nonce( $nonce_action ); ?>
						<input type="hidden" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( $nonce ); ?>">

						<input type="hidden" name="action" value="wlsm-print-bulk-result">
						<input type="hidden" name="school_id" id="excel_school_id" value="<?php echo $school_id;?>">

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
									<select name="class_id" class="form-control selectpicker class_id_print_excel" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" id="class_id_print_excel" data-live-search="true">
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
									<select name="class_group" class="form-control selectpicker class_group_excel_bulk" id="class_group_excel_bulk"   >
									<option value="">Select Group</option>
									<option value="common" selected>Common</option>
									<option value="science">Science</option>
									<option value="commerce">Commerce</option>
									<option value="humanity">Humanity</option>
									<option value="vocational">Vocational</option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label for="wlsm_section" class="wlsm-font-bold">
										<?php esc_html_e( 'Section', 'school-management' ); ?>:
									</label>
									<select name="section_id" class="form-control selectpicker " id="wlsm_section" data-live-search="true" title="<?php esc_attr_e( 'All Sections', 'school-management' ); ?>" data-all-sections="1">
									</select>
								</div>
								<div class="form-group col-md-4">
									<label for="wlsm_exam" class="wlsm-font-bold">
										<?php esc_html_e( 'Exams', 'school-management' ); ?>:
									</label>
									<select name="exam" class="form-control selectpicker wlsm_exam" id="wlsm_exam">
									
										<!-- <?php foreach ( $exams as $exam ) { ?>
										<option value="<?php echo esc_attr( $exam->ID ); ?>">
											<?php echo esc_html( WLSM_M_Class::get_label_text( $exam->exam_title ) ); ?>
										</option>
										<?php } ?> -->
									</select>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12" id="excel_table_show">
							<center class="excel_result_box" id="wlsm-print-result-excel">
								<table>
								
									<tr>
										<th rowspan="2"><?php esc_html_e("R.NO", 'school-management');?></th>
										<th rowspan="2"><?php esc_html_e("Student Name", 'school-management');?></th>
										<th colspan="7"><?php esc_html_e( "Bangla", 'school-management');?></th>
										<th colspan="5"><?php esc_html_e( "English", 'school-management');?></th>
										<th colspan="6"><?php esc_html_e( "Other Subject", 'school-management');?></th>
										<th rowspan="2"><?php esc_html_e( "Total Marks", 'school-management');?></th>
										<th rowspan="2"><?php esc_html_e( "LG", 'school-management');?></th>
										<th rowspan="2"><?php esc_html_e( "CGPA", 'school-management');?></th>
										
									</tr>
									<tr>
										<th><?php esc_html_e( "1st Paper(CQ)", 'school-management');?></th>
										<th><?php esc_html_e( "1st Paper(MCQ)", 'school-management');?></th>
										<th><?php esc_html_e( "2nd Paper(CQ)", 'school-management');?></th>
										<th><?php esc_html_e( "2nd Paper(MCQ)", 'school-management');?></th>
										<th><?php esc_html_e( "Total", 'school-management');?></th>
										<th><?php esc_html_e( "LG", 'school-management');?></th>
										<th><?php esc_html_e( "GP", 'school-management');?></th>

										<th><?php esc_html_e( "1st Paper(CQ)", 'school-management');?></th>
										<th><?php esc_html_e( "2nd Paper(CQ)", 'school-management');?></th>
										<th><?php esc_html_e( "Total", 'school-management');?></th>
										<th><?php esc_html_e( "LG", 'school-management');?></th>
										<th><?php esc_html_e( "GP", 'school-management');?></th>

										<th><?php esc_html_e( "CQ", 'school-management');?></th>
										<th><?php esc_html_e( "MCQ", 'school-management');?></th>
										<th><?php esc_html_e( "Practical", 'school-management');?></th>
										<th><?php esc_html_e( "Total", 'school-management');?></th>
										<th><?php esc_html_e( "LG", 'school-management');?></th>
										<th><?php esc_html_e( "GP", 'school-management');?></th>
									</tr>

									<tr>
										<td><?php esc_html_e( "101", 'school-management');?></td>
										<td><?php esc_html_e( "Jahidul Islam", 'school-management');?></td>
										<!-- Bangla subject -->
										<td><?php esc_html_e( "50", 'school-management');?></td>
										<td><?php esc_html_e( "28", 'school-management');?></td>
										<td><?php esc_html_e( "55", 'school-management');?></td>
										<td><?php esc_html_e( "21", 'school-management');?></td>
										<td><?php esc_html_e( "154", 'school-management');?></td>
										<td><?php esc_html_e( "A", 'school-management');?></td>
										<td><?php esc_html_e( "4.00", 'school-management');?></td>
										<!-- English subject -->
										<td><?php esc_html_e( "70", 'school-management');?></td>
										<td><?php esc_html_e( "85", 'school-management');?></td>
										<td><?php esc_html_e( "155", 'school-management');?></td>
										<td><?php esc_html_e( "A", 'school-management');?></td>
										<td><?php esc_html_e( "4.00", 'school-management');?></td>
										<!-- Other subject -->
										<td><?php esc_html_e( "49", 'school-management');?></td>
										<td><?php esc_html_e( "26", 'school-management');?></td>
										<td><?php esc_html_e( "20", 'school-management');?></td>
										<td><?php esc_html_e( "90", 'school-management');?></td>
										<td><?php esc_html_e( "A+", 'school-management');?></td>
										<td><?php esc_html_e( "5.00", 'school-management');?></td>
										<!-- Total Mark -->
										<td><?php esc_html_e( "399", 'school-management');?></td>
										<td><?php esc_html_e( "A", 'school-management');?></td>
										<td><?php esc_html_e( "4.00", 'school-management');?></td>

									</tr>
									<tr>
										<td><?php esc_html_e( "101", 'school-management');?></td>
										<td><?php esc_html_e( "Jahidul Islam", 'school-management');?></td>
										<!-- Bangla subject -->
										<td><?php esc_html_e( "50", 'school-management');?></td>
										<td><?php esc_html_e( "28", 'school-management');?></td>
										<td><?php esc_html_e( "55", 'school-management');?></td>
										<td><?php esc_html_e( "21", 'school-management');?></td>
										<td><?php esc_html_e( "154", 'school-management');?></td>
										<td><?php esc_html_e( "A", 'school-management');?></td>
										<td><?php esc_html_e( "4.00", 'school-management');?></td>
										<!-- English subject -->
										<td><?php esc_html_e( "70", 'school-management');?></td>
										<td><?php esc_html_e( "85", 'school-management');?></td>
										<td><?php esc_html_e( "155", 'school-management');?></td>
										<td><?php esc_html_e( "A", 'school-management');?></td>
										<td><?php esc_html_e( "4.00", 'school-management');?></td>
										<!-- Other subject -->
										<td><?php esc_html_e( "49", 'school-management');?></td>
										<td><?php esc_html_e( "26", 'school-management');?></td>
										<td><?php esc_html_e( "20", 'school-management');?></td>
										<td><?php esc_html_e( "90", 'school-management');?></td>
										<td><?php esc_html_e( "A+", 'school-management');?></td>
										<td><?php esc_html_e( "5.00", 'school-management');?></td>
										<!-- Total Mark -->
										<td><?php esc_html_e( "399", 'school-management');?></td>
										<td><?php esc_html_e( "A", 'school-management');?></td>
										<td><?php esc_html_e( "4.00", 'school-management');?></td>

									</tr>


							
								</table>
							</center>
							</div>
						</div>

				
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.display-none {
		display: none;
	}
</style>
<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/staff/WLSM_M_Staff_Transport.php';
require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/staff/WLSM_M_Lecture.php';
require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_Helper.php';

$page_url = WLSM_M_Staff_Transport::get_lecture_page_url();

$school_id = $current_school['id'];

$lecture = null;

$nonce_action = 'add-lecture';

$code     	 = '';
$title       = '';
$description = '';
$class_id    = '';
$title       = '';
$description = '';
$url         = '';
$attachment  = '';
$link_to     = 'url';
$is_active   = 1;
$square_description = '';
$circle_description = '';
$triangle_description = '';

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$id      = absint( $_GET['id'] );
	$lecture = WLSM_M_Staff_Lecture::fetch_lecture( $id );
	if ( $lecture ) {
		$nonce_action = 'edit-lecture-' . $lecture->ID;

		$code	 	 = $lecture->code;
		$title       = $lecture->title;
		$description = $lecture->description;
		$url         = $lecture->url;
		$link_to     = $lecture->link_to;
		$attachment  = $lecture->attachment;
		$class_id    = $lecture->class_id;
		$square_description = $lecture->square_description;
		$circle_description = $lecture->circle_description;
		$triangle_description = $lecture->triangle_description;
	}
}

$classes = WLSM_M_Staff_Class::fetch_classes( $school_id );
?>

<div class="row">
	<div class="col-md-12">
		<div class="mt-3 text-center wlsm-section-heading-block">
			<span class="wlsm-section-heading-box">
				<span class="wlsm-section-heading">
					<?php
					if ( $lecture ) {
						printf(
							wp_kses(
								/* translators: %s: lecture name */
								__( 'Edit Lecture (PI): %1$s %2$s', 'school-management' ),
								array(
									'span' => array( 'class' => array() ),
								)
							),
							esc_html($code),
							esc_html( $title )
						);
					} else {
						esc_html_e( 'Add New Lesson (PI)', 'school-management' );
					}
					?>
				</span>
			</span>
			<span class="float-md-right">
				<a href="<?php echo esc_url( $page_url ); ?>" class="btn btn-sm btn-outline-light">
					<i class="fas fa-home"></i>&nbsp;
					<?php esc_html_e( 'View All', 'school-management' ); ?>
				</a>
			</span>
		</div>
		<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="wlsm-save-lecture-form">

			<?php $nonce = wp_create_nonce( $nonce_action ); ?>
			<input type="hidden" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( $nonce ); ?>">

			<input type="hidden" name="action" value="wlsm-save-lecture">

			<?php if ( $lecture ) { ?>
				<input type="hidden" name="lecture_id" value="<?php echo esc_attr( $lecture->ID ); ?>">
			<?php } ?>

			<div class="wlsm-form-section">
				<div class="form-row">
					<div class="form-group col-md-3">
						<label for="wlsm_lecture_code" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Lesson (PI) Code', 'school-management' ); ?>:
						</label>
						<input type="text" name="code" class="form-control" id="wlsm_lecture_code" placeholder="<?php esc_attr_e( 'Enter Lesson (PI) Code', 'school-management' ); ?>" value="<?php echo esc_attr( stripslashes($code) );?>">
					</div>
					<div class="form-group col-md-3">
						<label for="wlsm_title" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Title', 'school-management' ); ?>:
						</label>
						<input type="text" name="title" class="form-control" id="wlsm_title" placeholder="<?php esc_attr_e( 'Enter title', 'school-management' ); ?>" value="<?php echo esc_attr( stripslashes( $title ) ); ?>">
					</div>

					<div class="form-group col-md-3">
						<label for="wlsm_class" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Class', 'school-management' ); ?>:
						</label>
						<select name="classes" class="form-control selectpicker wlsm_class_subjects" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-sections' ) ); ?>" data-nonce-subjects="<?php echo esc_attr( wp_create_nonce( 'get-class-subjects' ) ); ?>" id="wlsm_class" data-live-search="true">
							<option value=""><?php esc_html_e( 'Select Class', 'school-management' ); ?></option>
							<?php foreach ( $classes as $class ) { ?>
								<option <?php selected( $class->ID, $class_id, true ); ?> value="<?php echo esc_attr( $class->ID ); ?>" <?php selected( $class->ID, $class_id, true ); ?>>
									<?php echo esc_html( WLSM_M_Class::get_label_text( $class->label ) ); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<!-- <div class="form-group col-md-3">
						<label for="wlsm_section" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Section', 'school-management' ); ?>:
						</label>
						<select name="sections[]" class="form-control selectpicker" id="wlsm_section" data-live-search="true" title="<?php esc_attr_e( 'Select Section', 'school-management' ); ?>" data-actions-box="true" multiple>
							<?php foreach ( $sections as $section ) { ?>
								<option value="<?php echo esc_attr( $section->ID ); ?>" <?php selected( in_array( $section->ID, $homework_sections ), true, true ); ?>>
									<?php echo esc_html( WLSM_M_Staff_Class::get_section_label_text( $section->label ) ); ?>
								</option>
							<?php } ?>
						</select>
					</div> -->

					<div class="form-group col-md-3">
						<label for="wlsm_subject" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Subject', 'school-management' ); ?>:
						</label>

						<select name="subject" class="form-control selectpicker wlsm_class_chapter" data-nonce="<?php echo esc_attr( wp_create_nonce( 'get-class-chapter' ) ); ?>" data-nonce-chapter="<?php echo esc_attr( wp_create_nonce( 'get-class-chapter' ) ); ?>" id="wlsm_subject" data-live-search="true" title="<?php esc_attr_e( 'Select subject', 'school-management' ); ?>" data-actions-box="true">
							<?php if ( $subject_id ) : ?>
								<?php foreach ( $subjects as $subject ) { ?>
									<option value="<?php echo esc_attr( $subject->ID ); ?>">
										<?php if ( $subject_id == $subject->ID ) : ?>
											<?php echo esc_html( WLSM_M_Staff_Class::get_subject_label_text( $subject->label ) ); ?>
										<?php endif ?>

									</option>
								<?php } ?>
							<?php elseif ( ! $subject_id ) : ?>
								<?php foreach ( $subjects as $subject ) { ?>
									<option value="<?php echo esc_attr( $subject->ID ); ?>" <?php echo 'selected'; ?>>
										<?php echo esc_html( WLSM_M_Staff_Class::get_subject_label_text( $subject->label ) ); ?>
									</option>
								<?php } ?>
							<?php endif ?>

						</select>
					</div>

					<div class="form-group col-md-3">
						<label for="wlsm_chapter" class="wlsm-font-bold">
							<?php esc_html_e( 'Single Qualification', 'school-management' ); ?>:
						</label>

						<select name="chapter" class="form-control selectpicker" id="wlsm_chapter" data-live-search="true" title="<?php esc_attr_e( 'Select Single Qualification', 'school-management' ); ?>" data-actions-box="true">
						</select>
					</div>
				</div>

				<div class="form-row display-none">
					<div class="form-group col-md-4">
						<label for="wlsm_link_to" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Link to', 'school-management' ); ?>:
						</label>
						<br>
						<div class="form-check form-check-inline">
							<input <?php checked( '', $link_to, true ); ?> class="form-check-input" type="radio" name="link_to" id="wlsm_link_to_none" value="">
							<label class="ml-1 form-check-label text-dark font-weight-bold" for="wlsm_link_to_none">
								<?php echo esc_html( WLSM_M_Staff_Class::get_none_text() ); ?>
							</label>
						</div>
						<div class="form-check form-check-inline">
							<input <?php checked( 'attachment', $link_to, true ); ?> class="form-check-input" type="radio" name="link_to" id="wlsm_link_to_attachment" value="attachment">
							<label class="ml-1 form-check-label text-dark font-weight-bold" for="wlsm_link_to_attachment">
								<?php echo esc_html( WLSM_M_Staff_Class::get_attachment_text() ); ?>
							</label>
						</div>
						<div class="form-check form-check-inline">
							<input <?php checked( 'url', $link_to, true ); ?> class="form-check-input" type="radio" name="link_to" id="wlsm_link_to_url" value="url">
							<label class="ml-1 form-check-label text-dark font-weight-bold" for="wlsm_link_to_url">
								<?php echo esc_html( WLSM_M_Staff_Class::get_url_text() ); ?>
							</label>
						</div>
					</div>
					<div class="form-group col-md-8 wlsm-media-link wlsm-media-url">
						<label for="wlsm_url" class="wlsm-font-bold">
							<?php esc_html_e( 'Media URL', 'school-management' ); ?>:
						</label>
						<input type="text" name="url" class="form-control" id="wlsm_url" placeholder="<?php esc_attr_e( 'Enter media URL', 'school-management' ); ?>" value="<?php echo esc_url( $url ); ?>">
					</div>
					<div class="form-group col-md-8 wlsm-media-link wlsm-media-attachment">
						<div class="wlsm-attachment-box">
							<div class="wlsm-attachment-section">
								<label for="wlsm_attachment" class="wlsm-font-bold">
									<?php esc_html_e( 'Attachment', 'school-management' ); ?>:
								</label>
								<?php if ( ! empty( $attachment ) ) { ?>
									<a target="_blank" href="<?php echo esc_url( wp_get_attachment_url( $attachment ) ); ?>"><i class="fas fa-search"></i></a>
								<?php } ?>
								<div class="custom-file mb-3">
									<input type="file" class="custom-file-input" id="wlsm_attachment" name="attachment">
									<label class="custom-file-label" for="wlsm_attachment">
										<?php esc_html_e( 'Choose Attachment File', 'school-management' ); ?>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-row display-none">
					<div class="col-md-12">
						<label for="wlsm_subject" class="wlsm-font-bold">
							<?php esc_html_e( 'Description', 'school-management' ); ?>:
						</label>
						<div class="form-group">
							<?php
							$settings = array(
								'media_buttons' => false,
								'textarea_name' => 'description_body',
								'textarea_rows' => 10,
								'wpautop'       => false,
							);
							wp_editor( wp_kses_post( stripslashes( $description ) ), 'wlsm_description_body', $settings );
							?>
						</div>
					</div>
				</div>
				<!-- Level of proficiency -->
				<h3 style="margin: 30px 0; text-align: center;"><?php esc_html_e( 'Add Level of Proficiency Description', 'school-management' ); ?></h3>
				<div class="form-row">
					<div class="col-md-4">
						<label for="wlsm_subject_square" class="wlsm-font-bold">
							<?php esc_html_e( 'Square Description', 'school-management' ); ?> (<span>&#9634;</span>) :
						</label>
						<div class="form-group">
							<?php
							$settings = array(
								'media_buttons' => false,
								'textarea_name' => 'square_description_body',
								'textarea_rows' => 10,
								'wpautop'       => false,
							);
							wp_editor( wp_kses_post( stripslashes( $square_description ) ), 'square_description_body', $settings );
							?>
						</div>
					</div>
					<div class="col-md-4">
						<label for="wlsm_subject_circle" class="wlsm-font-bold">
							<?php esc_html_e( 'Circle Description', 'school-management' ); ?> (<span>&#11096;</span>) :
						</label>
						<div class="form-group">
							<?php
							$settings = array(
								'media_buttons' => false,
								'textarea_name' => 'circle_description_body',
								'textarea_rows' => 10,
								'wpautop'       => false,
							);
							wp_editor( wp_kses_post( stripslashes( $circle_description ) ), 'circle_description_body', $settings );
							?>
						</div>
					</div>
					<div class="col-md-4">
						<label for="wlsm_subject_triangle" class="wlsm-font-bold">
							<?php esc_html_e( 'Triangle Description', 'school-management' ); ?> (<span>🛆</span>) :
						</label>
						<div class="form-group">
							<?php
							$settings = array(
								'media_buttons' => false,
								'textarea_name' => 'triangle_description_body',
								'textarea_rows' => 10,
								'wpautop'       => false,
							);
							wp_editor( wp_kses_post( stripslashes( $triangle_description ) ), 'triangle_description_body', $settings );
							?>
						</div>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-primary" id="wlsm-save-lecture-btn">
							<?php
							if ( $lecture ) {
								?>
								<i class="fas fa-save"></i>&nbsp;
								<?php
								esc_html_e( 'Update Lecture (PI)', 'school-management' );
							} else {
								?>
								<i class="fas fa-plus-square"></i>&nbsp;
								<?php
								esc_html_e( 'Add New Lesson (PI)', 'school-management' );
							}
							?>
						</button>
					</div>
				</div>

		</form>
	</div>
</div>

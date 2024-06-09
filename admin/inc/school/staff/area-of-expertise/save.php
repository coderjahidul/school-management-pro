<?php
defined( 'ABSPATH' ) || die();

require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/staff/WLSM_M_Staff_Transport.php';
require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/staff/WLSM_M_Lecture.php';
require_once WLSM_PLUGIN_DIR_PATH . 'includes/helpers/WLSM_Helper.php';

$page_url = WLSM_M_Staff_Transport::get_area_of_expertise_page_url();

$school_id = $current_school['id'];

$area_of_expertise = null;

$nonce_action = 'add-area-of-expertise';

$title       = '';
$description = '';
$class_id    = '';
$title       = '';
$description = '';

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$id      = absint( $_GET['id'] );
	$area_of_expertise = WLSM_M_Staff_Lecture::fetch_area_of_expertise( $id );
	
	if ( $area_of_expertise ) {
		$nonce_action = 'edit-area-of-expertise-' . $area_of_expertise->ID;

		$title       = $area_of_expertise->title;
		$class_id    = $area_of_expertise->class_id;
		$subject_id  = $area_of_expertise->subject_id;
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
					if ( $area_of_expertise ) {
						printf(
							wp_kses(
								/* translators: %s: chapter name */
								__( 'Edit Area of Expertise: %s', 'school-management' ),
								array(
									'span' => array( 'class' => array() ),
								)
							),
							esc_html( $title )
						);
					} else {
						esc_html_e( 'Add New Area of Expertise', 'school-management' );
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
		<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="wlsm-save-area-of-expertise-form">

			<?php $nonce = wp_create_nonce( $nonce_action ); ?>
			<input type="hidden" name="<?php echo esc_attr( $nonce_action ); ?>" value="<?php echo esc_attr( $nonce ); ?>">

			<input type="hidden" name="action" value="wlsm-save-area-of-expertise">

			<?php if ( $area_of_expertise ) { ?>
				<input type="hidden" name="area_of_expertise_id" value="<?php echo esc_attr( $area_of_expertise->ID ); ?>">
			<?php } ?>

			<div class="wlsm-form-section">
				<div class="form-row">
					<div class="form-group col-md-3">
						<label for="wlsm_label" class="wlsm-font-bold">
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

					<div class="form-group col-md-3">
						<label for="wlsm_subject" class="wlsm-font-bold">
							<span class="wlsm-important">*</span> <?php esc_html_e( 'Subject', 'school-management' ); ?>:
						</label>

						<select name="subject" class="form-control selectpicker" id="wlsm_subject" data-live-search="true" title="<?php esc_attr_e( 'Select subject', 'school-management' ); ?>" data-actions-box="true">
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
				</div>

				<div class="row mt-2">
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-primary" id="wlsm-save-area-of-expertise-btn">
							<?php
							if ( $area_of_expertise ) {
								?>
								<i class="fas fa-save"></i>&nbsp;
								<?php
								esc_html_e( 'Update Area of Expertise', 'school-management' );
							} else {
								?>
								<i class="fas fa-plus-square"></i>&nbsp;
								<?php
								esc_html_e( 'Add New Area of Expertise', 'school-management' );
							}
							?>
						</button>
					</div>
				</div>

		</form>
	</div>
</div>

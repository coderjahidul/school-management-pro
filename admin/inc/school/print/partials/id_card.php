<?php
defined( 'ABSPATH' ) || die();

$settings_general = WLSM_M_Setting::get_settings_general( $school_id );
$school_logo      = $settings_general['school_logo'];
$school_signature = $settings_general['school_signature'];
$school           = WLSM_M_School::fetch_school( $school_id );
?>

<style>
/* --- Import Google Font --- */
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

/* --- General Display --- */
.wlsm-id-card-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    font-family: 'Montserrat', sans-serif;
    justify-content: center;
    padding: 20px;
    background: #f0f2f5;
}

.id-card-group {
    display: flex;
    gap: 15px;
    margin-bottom: 40px;
    page-break-inside: avoid;
}

.id-card-container {
    width: 340px;
    height: 520px;
    background: green;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border: none;
}

/* --- Colors & Gradients --- */
:root {
    --primary-gradient: linear-gradient(135deg, #ff0000ff 0%, #ff0000ff 100%);
    --accent-color: #fff;
    --text-dark: #fff;
    --text-muted: #fff;
    --white: #ffffff;
}

/* --- Print Settings --- */
@media print {
    body { background: none; margin: 0; padding: 0; }
    .wlsm-id-card-wrapper { background: none; display: block; padding: 0; }
    .id-card-group { margin-bottom: 25px; page-break-inside: avoid; }
    .id-card-group:nth-child(2n) { page-break-after: always; }
    .id-card-container { box-shadow: none; border: 1px solid #eee; }
}

/* --- Front Side Styling --- */
.card-header-modern {
    background: var(--primary-gradient);
    height: 120px;
    width: 100%;
    color: var(--white);
    text-align: center;
    padding: 20px 15px;
    position: relative;
    clip-path: ellipse(110% 100% at 50% 0%);
}

.school-name-premium {
    font-size: 18px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.2;
    margin-bottom: 5px;
}

.id-badge {
    background: rgba(0, 0, 0, 0.9);
    color: var(--text-dark);
    font-weight: 700;
    padding: 6px 20px;
    border-radius: 50px;
    display: inline-block;
    font-size: 13px;
    margin-top: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.photo-wrapper {
    margin: -25px auto 20px;
    position: relative;
    width: 130px;
    height: 145px;
    z-index: 2;
}

.photo-wrapper img {
    width: 100%;
    height: 100%;
    border-radius: 12px;
    border: 4px solid var(--white);
    object-fit: cover;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.student-data-premium {
    padding: 0 25px 20px;
    position: relative;
    z-index: 1;
}

.student-data-premium::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -40%);
    width: 280px;
    height: 280px;
    background: url('<?php echo esc_url( WLSM_PLUGIN_URL . 'assets/images/lotus-watermark.png' ); ?>') no-repeat center center;
    background-size: contain;
    opacity: 0.15;
    z-index: -1;
    pointer-events: none;
}

.student-name-headline {
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 15px;
    border-bottom: 2px solid #f1f1f1;
    padding-bottom: 8px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.info-item {
    display: flex;
    font-size: 13px;
    align-items: center;
}

.info-item .item-label {
    width: 90px;
    color: var(--text-muted);
    font-weight: 700;
}

.info-item .item-value {
    flex: 1;
    color: var(--text-dark);
    font-weight: 700;
}

/* --- Back Side Styling --- */
.back-header-premium {
    background: var(--primary-gradient);
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
}

.back-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: calc(100% - 60px);
}

.back-logo-box {
    margin-bottom: 15px;
}

.back-logo-box img {
    height: 80px !important;
    width: auto;
}

.back-school-info {
    text-align: center;
    margin-bottom: 15px;
}

.back-school-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--accent-color);
    margin-bottom: 5px;
}

.contact-details {
    width: 100%;
    margin-bottom: 20px;
}

.contact-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 12px;
    margin-bottom: 8px;
    color: var(--text-dark);
    font-weight: 700;
}

.contact-icon {
    width: 18px;
    color: var(--accent-color);
    flex-shrink: 0;
    margin-top: 2px;
}

.signature-section {
    margin-top: auto;
    width: 80%;
    text-align: center;
    padding-bottom: 15px;
}

.signature-img {
    height: 50px !important;
    width: auto;
    margin-bottom: 5px;
}

.signature-line {
    border-top: 1.5px solid #ccc;
    padding-top: 5px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
}

/* --- SVG Icons Helper --- */
.svg-icon {
    width: 100%;
    height: auto;
    fill: currentColor;
}
</style>

<div class="wlsm-id-card-wrapper">
    <div class="id-card-group">
        <!-- Front Side -->
        <div class="id-card-container front-side">
            <div class="card-header-modern">
                <div class="school-name-premium"><?php echo esc_html( $school->label ); ?></div>
                <div class="id-badge">STUDENT ID CARD</div>
            </div>
            
            <div class="photo-wrapper">
                <?php if ( ! empty( $photo_id ) ) : ?>
                    <img src="<?php echo esc_url( wp_get_attachment_url( $photo_id ) ); ?>">
                <?php else: ?>
                    <div style="width: 122px; height: 137px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                        NO PHOTO
                    </div>
                <?php endif; ?>
            </div>

            <div class="student-data-premium">
                <div class="student-name-headline"><?php echo esc_html( strtoupper($student->student_name) ); ?></div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <span class="item-label">Class</span>
                        <span class="item-value"><?php echo esc_html( strtoupper($student->class_label) ); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="item-label">Ses / Sec</span>
                        <span class="item-value">
                            <?php echo esc_html( WLSM_M_Session::get_label_text( $session_label ) ); ?> (<?php echo esc_html( strtoupper($student->section_label) ); ?>)
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="item-label">Roll / ID</span>
                        <span class="item-value"><?php echo esc_html( $student->roll_number ); ?> (<?php echo esc_html( $student->admission_number ); ?>)</span>
                    </div>
                    <div class="info-item">
                        <span class="item-label">Father's</span>
                        <span class="item-value"><?php echo esc_html( $student->father_name); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="item-label">Mother's</span>
                        <span class="item-value"><?php echo esc_html( $student->mother_name); ?></span>
                    </div>
                    <div class="info-item" style="margin-top: 5px;">
                        <span class="item-label">Address</span>
                        <span class="item-value" style="font-size: 11px; line-height: 1.2;"><?php echo esc_html( $student->address ); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Side -->
        <div class="id-card-container back-side">
            <div class="back-header-premium">Verification Info</div>
            
            <div class="back-content">
                <div class="back-logo-box">
                    <?php if ( ! empty ( $school_logo ) ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_url( $school_logo ) ); ?>">
                    <?php endif; ?>
                </div>

                <div class="back-school-info">
                    <div class="back-school-title"><?php echo esc_html( $school->label ); ?></div>
                </div>

                <div class="contact-details">
                    <div class="contact-row">
                        <div class="contact-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        </div>
                        <div><?php echo esc_html( $school->address ); ?></div>
                    </div>
                    <?php if ( $school->phone ) : ?>
                    <div class="contact-row">
                        <div class="contact-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                        </div>
                        <div><?php echo esc_html( $school->phone ); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( $school->email ) : ?>
                    <div class="contact-row">
                        <div class="contact-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        </div>
                        <div><?php echo esc_html( $school->email ); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="contact-row">
                        <div class="contact-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M21 11.5c0-1.04-.6-1.94-1.47-2.38.1-.42.16-.86.16-1.31 0-3.04-2.46-5.5-5.5-5.5-.45 0-.89.06-1.31.16C12.44 1.6 11.54 1 10.5 1c-1.1 0-2 .9-2 2 0 .09.01.18.02.27C7.45 3.1 6.3 3 5 3 2.24 3 0 5.24 0 8c0 .35.04.69.11 1.02C.04 9.17 0 9.33 0 9.5c0 1.1.9 2 2 2h.02c-.01.16-.02.33-.02.5 0 2.76 2.24 5 5 5 .1 0 .21 0 .31-.01.46.31.96.53 1.5.65.1.84.81 1.5 1.69 1.5.97 0 1.75-.78 1.75-1.75V15h2.5c2.76 0 5-2.24 5-5 0-.17-.01-.34-.02-.5h.02c1.1 0 2-.9 2-2 0-.17-.01-.33-.04-.5z"/></svg>
                        </div>
                        <div style="color: #fcfcfcff; text-decoration: underline;"><?php echo str_replace(['http://', 'https://'], '', home_url()); ?></div>
                    </div>
                </div>

                <div class="signature-section">
                    <?php if ( ! empty( $school_signature ) ) : ?>
                        <img src="<?php echo esc_url(wp_get_attachment_url($school_signature))?>" class="signature-img">
                    <?php endif; ?>
                    <div class="signature-line">Authorized Signature</div>
                </div>
            </div>
        </div>
    </div>
</div>
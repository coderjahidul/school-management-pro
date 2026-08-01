<?php
defined( 'ABSPATH' ) || die();

class WLSM_Export {
	public static function export_and_close_csv_file( $f, $filename ) {
		fseek( $f, 0 );

		header( 'Content-Type: text/html' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '";' );

		fpassthru( $f );

		fclose( $f );

		exit;
	}

	public static function export_students_xlsx_file( $filename, $school_name, $headers, $rows ) {
		require_once WLSM_PLUGIN_DIR_PATH . 'includes/vendor/autoload.php';

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();

		$column_count = count( $headers );
		$last_column  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex( $column_count );

		$sheet->setCellValue( 'A1', $school_name );
		$sheet->mergeCells( 'A1:' . $last_column . '1' );
		$sheet->getStyle( 'A1' )->getFont()->setBold( true );
		$sheet->getStyle( 'A1' )->getAlignment()->setHorizontal( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );
		$sheet->getStyle( 'A1' )->getAlignment()->setVertical( \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER );

		$sheet->fromArray( $headers, null, 'A2' );

		$row_index = 3;
		foreach ( $rows as $row ) {
			$sheet->fromArray( $row, null, 'A' . $row_index );
			$row_index++;
		}

		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
		header( 'Cache-Control: max-age=0' );

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $spreadsheet, 'Xlsx' );
		$writer->save( 'php://output' );

		exit;
	}
}

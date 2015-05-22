<?php
/**
 * CPdf is a helper class file that provides creating and basic work with PDF documents
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/ 
 *
 * USAGE:
 * ----------
 * 1. Standard call CPdf::config() + CPdf::createDocument()
 * 2. Direct call with default parameters CPdf::createDocument()
 * 
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * config
 * createDocument
 * 
 */	  

// Include the main TCPDF library (search for installation path).
include(dirname(__FILE__).'/../vendors/tcpdf/config/tcpdf_config.php');
include(dirname(__FILE__).'/../vendors/tcpdf/tcpdf.php');
		
class CPdf
{
	/** @var */
	private static $_page_orientation = PDF_PAGE_ORIENTATION;
	private static $_unit = PDF_UNIT;
	private static $_page_format = PDF_PAGE_FORMAT;
	private static $_unicode = true;
	private static $_encoding = 'UTF-8';
	private static $_creator = PDF_CREATOR;
	private static $_author = PDF_AUTHOR;
	

	/**
	 * Sets a basic configuration
	 * Usage:
	 * CPdf::config(array(
	 *		'page_orientation' 	=> 'P', 		// [P=portrait, L=landscape]
	 *		'unit' 				=> 'mm',		// [pt=point, mm=millimeter, cm=centimeter, in=inch]
	 *		'page_format'		=> 'A4',
	 *		'unicode'			=> true,
	 *		'encoding'			=> 'UTF-8',
	 *		'creator'			=> 'TCPDF',
	 *		'author'			=> 'TCPDF',
	 * ))
	 * 
	 * @param array $params
	 */
    public static function config($params)
    {
		// Page orientation [P=portrait, L=landscape]
		if(isset($params['page_orientation'])) self::$_page_orientation = $params['page_orientation'];
		// Document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch].
		if(isset($params['unit'])) self::$_unit = $params['unit'];
		// Page format: A4
		if(isset($params['page_format'])) self::$_page_format = $params['page_format'];
		// Unicode
		if(isset($params['unicode'])) self::$_unicode = (bool)$params['unicode'];
		// Encoding
		if(isset($params['encoding'])) self::$_encoding = $params['encoding'];
		// Creator
		if(isset($params['creator'])) self::$_creator = $params['creator'];
		// Author
		if(isset($params['author'])) self::$_author = $params['author'];
	}

	/**
	 * Creates PDF document and sends it to defined output
	 * @param string $content
	 * @param string $outputName
	 * @param string $outputDestination		I - send the file inline to the browser (default)
	 * 										D - send to the browser and force a file download with the name given by name.
	 * 										F - save to a local server file with the name given by name.
	 * 										S - return the document as a string (name is ignored).
	 * 										FI - equivalent to F + I option
	 * 										FD - equivalent to F + D option
	 * 										E - return the document as base64 mime multi-part email attachment (RFC 2045)
	 * @return void
	 */
    public static function createDocument($content = '', $outputName = 'example_001.pdf', $outputDestination = 'I')
    {
		// validate output file name		
		if(!preg_match('/\.pdf/i', $outputName)){
			$outputName .= '.pdf';
		}

		// validate output desctination 
		$outputDestination = in_array($outputDestination, array('I', 'D', 'F', 'S', 'FI', 'FD', 'E')) ? $outputDestination : 'I';
		
		// create new PDF document
		$pdf = new TCPDF(self::$_page_orientation, self::$_unit, self::$_page_format, self::$_unicode, self::$_encoding);
		
		// set document information
		$pdf->SetCreator(self::$_creator);
		$pdf->SetAuthor(self::$_author);
		$pdf->SetTitle('TCPDF Example 001');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$pdf->setFooterData(array(0,64,0), array(0,64,128));
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// ---------------------------------------------------------
		
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$pdf->SetFont('dejavusans', '', 14, '', true);
		$pdf->SetFont('helvetica', '', 14, '', true);
		
		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
		
		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
		
		// Set some content to print
		$html = $content;
		
		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		
		// ---------------------------------------------------------
		
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output($outputName, $outputDestination);
	}
	
}
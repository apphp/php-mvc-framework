<?php
/**
 * CPdf is a helper class file that provides creating and basic work with PDF documents
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
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
	private static $_pageFormat = PDF_PAGE_FORMAT;
	private static $_unicode = true;
	private static $_encoding = 'UTF-8';
	private static $_creator = PDF_CREATOR;
	private static $_author = PDF_AUTHOR;
	private static $_title = '';
	private static $_subject = ''; 
	private static $_keywords = '';
	private static $_direction = '';
	private static $_pathImages = '';
	private static $_imageScaleRatio = PDF_IMAGE_SCALE_RATIO;
	private static $_headerEnable = true;
	private static $_headerLogo = '';
	private static $_headerLogoWidth = ''; 
	private static $_headerTitle = '';
	private static $_headerString = '';
	private static $_headerMargin = PDF_MARGIN_HEADER;
	private static $_footerEnable = true;
	private static $_footerFontName = PDF_FONT_NAME_DATA;
	private static $_footerFontSize = PDF_FONT_SIZE_DATA;
	private static $_footerMargin = PDF_MARGIN_FOOTER;
	private static $_autoPageBreak = true;
	private static $_fontNameMain = PDF_FONT_NAME_MAIN;
	private static $_fontSizeMain = PDF_FONT_SIZE_MAIN;
	private static $_textShadow = true;
	private static $_fontMonospaced = PDF_FONT_MONOSPACED;	
	private static $_marginLeft = PDF_MARGIN_LEFT;
	private static $_marginTop = PDF_MARGIN_TOP;
	private static $_marginRight = PDF_MARGIN_RIGHT;
	private static $_marginBottom = PDF_MARGIN_BOTTOM;
	
	
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
	 *		'title'				=> 'TCPDF Example 001',
	 *		'subject'			=> 'TCPDF Tutorial',
	 *		'keywords'			=> 'TCPDF, PDF, example, test, guide',
	 *		'direction'			=> 'ltr'			// 'ltr' (default) or 'rtl'
	 *		'path_images'		=> '',				// relative path
	 *		'image_scale_ratio' => '1.25',			// ratio used to adjust the conversion of pixels to user units
	 *		'header_enable'		=> true,			// true (default), false - disables header
	 *		'header_logo'		=> '',
	 *		'header_logo_width'	=> '',
	 *		'header_title'		=> 'TCPDF Example',
	 *		'header_string'		=> 'by Nicola Asuni - Tecnick.com\nwww.tcpdf.org',
	 *		'header_margin'		=> 5,				// 5 (5px)
	 *		'footer_enable'		=> true,			// true (default), false - disables footer
	 *		'footer_font_name'	=> 'helvetica'		// 'dejavusans', 'helvetica' (default), 'times', 'courier',
	 *		'footer_font_size'	=> 8,
	 *		'footer_margin'		=> 10,				// 10 (10px)
	 *		'auto_page_break'	=> true,
	 *		'font_name_main'	=> 'dejavusans',	// 'dejavusans' (default), 'helvetica', 'times', 'courier'
	 *		'font_size_main'	=> 10,
	 *		'text_shadow'		=> true,
	 *		'font_monospaced'	=> 'courier',		// 'dejavusans', 'helvetica', 'times', 'courier'  (default)
	 *		'margin_left'		=> '15',			// 15 (15px)
	 *		'margin_top'		=> '27',			// 27 (27px)
	 *		'margin_right'		=> '15',			// 15 (15px)
	 *		'margin_bottom'		=> '25',			// 25 (25px)
	 * ))
	 * 
	 * @param array $params
	 * @return void
	 */
    public static function config($params)
    {
		// Page orientation [P=portrait, L=landscape]
		if(isset($params['page_orientation'])) self::$_page_orientation = $params['page_orientation'];
		// Document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch].
		if(isset($params['unit'])) self::$_unit = $params['unit'];
		// Page format: A4
		if(isset($params['page_format'])) self::$_pageFormat = $params['page_format'];
		// Unicode
		if(isset($params['unicode'])) self::$_unicode = (bool)$params['unicode'];
		// Encoding
		if(isset($params['encoding'])) self::$_encoding = $params['encoding'];
		// Creator
		if(isset($params['creator'])) self::$_creator = $params['creator'];
		// Author
		if(isset($params['author'])) self::$_author = $params['author'];
		// Title
		if(isset($params['title'])) self::$_title = $params['title'];
		// Subject
		if(isset($params['subject'])) self::$_subject = $params['subject'];
		// Keywords
		if(isset($params['keywords'])) self::$_keywords = $params['keywords'];
		// Page direction
		if(isset($params['direction'])) self::$_direction = $params['direction'];

		// Path images - relative path
		if(isset($params['path_images'])){
			self::$_pathImages = $params['path_images'];
			if(!defined('K_PATH_IMAGES')){
				define('K_PATH_IMAGES', self::$_pathImages);
			}
		}
		// Image Scale Ration - used to adjust the conversion of pixels to user units.
		if(isset($params['image_scale_ratio'])) self::$_imageScaleRatio = $params['image_scale_ratio']; 

		// Header 
		if(isset($params['header_enable'])) self::$_headerEnable = (bool)$params['header_enable'];
		// Header logo - path to logo image (default PDF_HEADER_LOGO, empty string disables it)
		if(isset($params['header_logo'])) self::$_headerLogo = $params['header_logo'];
		// Header logo image width in user units - default PDF_HEADER_LOGO_WIDTH
		if(isset($params['header_logo_width'])) self::$_headerLogoWidth = $params['header_logo_width'];
		// Header title - default PDF_HEADER_TITLE
		if(isset($params['header_title'])) self::$_headerTitle = $params['header_title'];
		// Header string - default PDF_HEADER_STRING
		if(isset($params['header_string'])) self::$_headerString = $params['header_string'];
		// Header margin - default PDF_MARGIN_HEADER
		if(isset($params['header_margin'])) self::$_headerMargin = $params['header_margin'];		

		// Footer
		if(isset($params['footer_enable'])) self::$_footerEnable = (bool)$params['footer_enable'];
		// Footer font name - default PDF_FONT_SIZE_MAIN
		if(isset($params['footer_font_name'])) self::$_footerFontName = $params['footer_font_name'];		
		// Footer font size - default PDF_FONT_SIZE_DATA
		if(isset($params['footer_font_size'])) self::$_footerFontSize = $params['footer_font_size'];
		// Footer margin - default PDF_MARGIN_FOOTER
		if(isset($params['footer_margin'])) self::$_footerMargin = $params['footer_margin'];
		// Text shadow
		if(isset($params['text_shadow'])) self::$_textShadow = $params['text_shadow'];
		
		// Auto page break
		if(isset($params['auto_page_break'])) self::$_autoPageBreak = (bool)$params['auto_page_break'];
		// Font name main - default 'helvetica'
		if(isset($params['font_name_main'])) self::$_fontNameMain = $params['font_name_main'];
		// Font size main - default 10
		if(isset($params['font_size_main'])) self::$_fontSizeMain = $params['font_size_main'];
		// Font monospaced - default 'courier'
		if(isset($params['font_monospaced'])) self::$_fontMonospaced = $params['font_monospaced'];		

		// Page margins
		if(isset($params['margin_left'])) self::$_marginLeft = $params['margin_left'];
		if(isset($params['margin_top'])) self::$_marginTop = $params['margin_top'];
		if(isset($params['margin_right'])) self::$_marginRight = $params['margin_right'];
		if(isset($params['margin_bottom'])) self::$_marginBottom = $params['margin_bottom'];
		
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
		// Validate output file name		
		if(!preg_match('/\.pdf/i', $outputName)){
			$outputName .= '.pdf';
		}

		// Validate output desctination 
		$outputDestination = in_array($outputDestination, array('I', 'D', 'F', 'S', 'FI', 'FD', 'E')) ? $outputDestination : 'I';
		
		// Create new PDF document
		$pdf = new TCPDF(self::$_page_orientation, self::$_unit, self::$_pageFormat, self::$_unicode, self::$_encoding);
		
		// set document information
		if(!empty(self::$_creator)) $pdf->SetCreator(self::$_creator);
		if(!empty(self::$_author)) $pdf->SetAuthor(self::$_author);
		if(!empty(self::$_title)) $pdf->SetTitle(self::$_title);
		if(!empty(self::$_subject)) $pdf->SetSubject(self::$_subject);
		if(!empty(self::$_keywords)) $pdf->SetKeywords(self::$_keywords);
		
		// Set header data
		if(self::$_headerEnable){
			$pdf->setHeaderData(self::$_headerLogo, self::$_headerLogoWidth, self::$_headerTitle, self::$_headerString, array(0,64,255), array(0,64,128));
			$pdf->setHeaderFont(array(self::$_fontNameMain, '', self::$_fontSizeMain));
			$pdf->setHeaderMargin(self::$_headerMargin);			
		}else{
			$pdf->setPrintHeader(false);
		}
		
		// Set footer data
		if(self::$_footerEnable){
			$pdf->setFooterData(array(0,64,0), array(0,64,128));			
			$pdf->setFooterFont(array(self::$_footerFontName, '', self::$_footerFontSize));
			$pdf->setFooterMargin(self::$_footerMargin);			
		}else{
			$pdf->setPrintFooter(false);
		}

		// Set default monospaced font
		$pdf->setDefaultMonospacedFont(self::$_fontMonospaced);
		
		// Set margins
		$pdf->setMargins(self::$_marginLeft, self::$_marginTop, self::$_marginRight);
		
		// Set auto page breaks
		$pdf->setAutoPageBreak(self::$_autoPageBreak, self::$_marginBottom);
		
		// Set image scale factor
		$pdf->setImageScale(self::$_imageScaleRatio);
		
		// Set default font subsetting mode
		$pdf->setFontSubsetting(true);
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to print standard ASCII chars,
		// you can use core fonts like helvetica or times to reduce file size.
		$pdf->setFont('dejavusans', '', 12, '', true);
		
		// Restore RTL direction
		if(self::$_direction == 'rtl'){
			$pdf->setRTL(true);	
		}		
		
		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->addPage();
		
		// Set text shadow effect
		if(self::$_textShadow){
			$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
		}
		
		// Set some content to print
		$html = $content;

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->output($outputName, $outputDestination);
	}
	
}
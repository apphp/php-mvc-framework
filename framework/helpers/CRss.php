<?php
/**
 * CRss is a helper class that provides basic RSS functionality
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * 
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * setType
 * setChannel
 * setImage
 * setItem
 * output
 * saveFeed
 * cleanTextRss
 *
 *
 * Example of using Rss class
 * -------------------------------------
 * $rss_last_ids = '1-2-3-4-99';
 * $rss_ids = '';
 * self::SetType('rss1');		
 * self::SetChannel('feeds/rss.xml', 'header_text', 'tag_description', 'en-us', '(c) copyright', 'admin_email', 'tag_description');
 * self::SetImage('images/icons/logo.png');
 * 
 * $all_news = Object::GetAllNews('previous');		
 * for($i=0; $i < $all_news[1] && $i < 10; $i++){					
 * 	$rss_ids .= (($i > 0) ? '-' : '').$all_news[0][$i]['id'];
 * }
 * 
 * // check if there difference between RSS IDs, so we have to update RSS file		
 * if($rss_last_ids != $rss_ids){
 *    for($i=0; $i < $all_news[1] && $i < 10; $i++){					
 *       $rss_text = RSSFeed::CleanTextRss(strip_tags($all_news[0][$i]['body_text']));
 * 	     if(strlen($rss_text) > 512) $rss_text = substr_by_word($rss_text, 512).'...';
 * 	     #$rss_text = htmlentities($post_text, ENT_COMPAT, 'UTF-8');
 * 	     self::SetItem(APPHP_BASE.'index.php?page=news&nid='.$all_news[0][$i]['id'], $all_news[0][$i]['header_text'], $rss_text, $all_news[0][$i]['date_created']);
 *    }		
 *    Object::UpdateFields(array('rss_last_ids'=>$rss_ids));				
 * }		
 * 
 * self::SaveFeed();
 * 	
 */	  

class CRss
{

    private static $channelUrl = '';
    private static $channelTitle = '';
    private static $channelDescription = '';
    private static $channelLang = '';
    private static $channelCopyright = '';
    private static $channelDate = '';
    private static $channelCreator = '';
	private static $channelAuthor = '';
    private static $channelSubject = '';
	
	private static $rssType = 'rss1';
	private static $rssTypes = array('rss1', 'rss2', 'atom');
    
    private static $imageUrl = '';

    private static $arrItems = array();
    private static $countItems = 0;
	
    private static $filePath = '';
	private static $fileName = 'rss.xml';
    
	/**
	 * Sets RssFeed type
	 * @param string $type
	 */ 
	public static function setType($type = '')
	{
		if(in_array($type, $rssTypes)) self::$rssType = $type;
	}

	/**
	 * Sets Channel
	 * @param array $params
	 */
    public static function setChannel($params = array())
	{
		// $creator, $subject
        self::$channelUrl	= isset($params['url']) ? $params['url'] : '';
        self::$channelTitle = isset($params['title']) ? $params['title'] : '';
		self::$channelDescription = isset($params['description']) ? $params['description'] : '';
		self::$channelLang  = isset($params['lang']) ? $params['lang'] : '';
		self::$channelCopyright = isset($params['copyright']) ? $params['copyright'] : '';
		self::$channelCreator = isset($params['creator']) ? $params['creator'] : '';
		self::$channelAuthor = isset($params['author']) ? $params['author'] : '';
		self::$channelSubject = isset($params['subject']) ? $params['subject'] : '';

		if(self::$rssType === 'rss1'){
			self::$channelDate = date('Y-m-d').'T'.date('H:i:s').'+02:00';
		}else if(self::$rssType === 'rss2'){
			self::$channelDate = date('D, d M Y H:i:s T');
		}else if(self::$rssType === 'atom'){
			self::$channelDate = date('Y-m-d').'T'.date('H:i:sP');
		}else{
			self::$channelDate=date('Y-m-d').'T'.date('H:i:sT');
		}
    }

	/**
	 * Sets Image
	 * @param string $url
	 */
    public static function setImage($url)
	{
        self::$imageUrl = $url;
    }
    
	/**
	 * Sets Item
	 * @param string $url
	 * @param string $title
	 * @param string $description
	 * @param string $publishDate
	 */
    public static function setItem($url, $title, $description, $publishDate)
	{
        self::$arrItems[self::$countItems]['url'] = $url;
        self::$arrItems[self::$countItems]['title'] = $title;
        self::$arrItems[self::$countItems]['description'] = $description;
		self::$arrItems[self::$countItems]['pub_date'] = $publishDate;
        self::$countItems++;    
    }
    
	/**
	 * Returns Feed
	 */
    public static function output()
	{
		$nl = "\n";
		
		// RSS Atom	
		if(self::$rssType == 'atom'){
			$output =  '<?xml version="1.0" encoding="utf-8"?>'.$nl;
			$output .= '<feed xmlns="http://www.w3.org/2005/Atom">'.$nl;			
			$output .= '<title>'.self::$channelTitle.'</title>'.$nl;
			///$output .= '<subtitle>A SubTitle</subtitle>'.$nl;
			$output .= '<link href="'.self::$channelUrl.'" rel="self" />'.$nl;
			$output .= '<link href="'.str_replace('feeds/rss.xml', '', self::$channelUrl).'" />'.$nl;
			$output .= '<id>'.self::$channelUrl.'</id>'.$nl;
			$output .= '<updated>'.self::$channelDate.'</updated>'.$nl;
			$output .= '<author>'.$nl;
			$output .= '<name>'.self::$channelAuthor.'</name>'.$nl;
			$output .= '</author>'.$nl;
			for($i=0; $i < self::$countItems; $i++) {
				$output .= '<entry>'.$nl;
				$output .= '<title>'.str_replace('&', '&amp;', self::$arrItems[$i]['title']).'</title>'.$nl;
				$output .= '<link href="'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'" />'.$nl;
				$output .= '<id>'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'</id>'.$nl;
				$output .= '<summary>'.self::$arrItems[$i]['description'].'</summary>'.$nl;
				#<id>tag:google.com,2005-10-15:/support/jobs/hr-analyst</id>
				#<issued>2005-10-13T18:30:02Z</issued>
				$output .= '<updated>'.date('Y-m-d', strtotime(self::$arrItems[$i]['pub_date'])).'T'.date('H:i:sP', strtotime(self::$arrItems[$i]['pub_date'])).'</updated>'.$nl;
				$output .= '</entry>'.$nl;			
			}
			$output .= '</feed>'.$nl;			
		
		// RSS 2.0
		}else if(self::$rssType == 'rss2'){
			$output =  '<?xml version="1.0" encoding="utf-8"?>'.$nl;
			$output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'.$nl;
			$output .= '<channel>'.$nl;
			$output .= '<atom:link href="'.self::$channelUrl.'" rel="self" type="application/rss+xml" />'.$nl;
			$output .= '<title>'.self::$channelTitle.'</title>'.$nl;
			$output .= '<link>'.self::$channelUrl.'</link>'.$nl;
			$output .= '<description>'.self::$channelDescription.'</description>'.$nl;
			$output .= '<language>'.self::$channelLang.'</language>'.$nl;
			$output .= '<copyright>'.self::$channelCopyright.'</copyright>'.$nl;
			$output .= '<pubDate>'.self::$channelDate.'</pubDate>'.$nl;
			///$output .= '<lastBuildDate>'.self::$channelDate.'</lastBuildDate>'.$nl;
			$output .= '<image>'.$nl;
			$output .= '<url>'.self::$imageUrl.'</url>'.$nl;
			$output .= '<title>'.self::$channelTitle.'</title>'.$nl;
			$output .= '<link>'.self::$channelUrl.'</link>'.$nl;
			$output .= '</image>'.$nl;
			for($i=0; $i < self::$countItems; $i++) {
				$output .= '<item>'.$nl;
				$output .= '<title>'.str_replace('&', '&amp;', self::$arrItems[$i]['title']).'</title>'.$nl;
				$output .= '<link>'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'</link>'.$nl;
				$output .= '<description>'.self::$arrItems[$i]['description'].'</description>'.$nl;
				$output .= '<author>'.self::$channelCreator.'</author>'.$nl;
				$output .= '<guid>'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'</guid>'.$nl;
				$output .= '<pubDate>'.date('D, d M Y H:i:s T', strtotime(self::$arrItems[$i]['pub_date'])).'</pubDate>'.$nl;
				$output .= '</item>'.$nl;
			};
			$output .= '</channel>'.$nl;
			$output .= '</rss>'.$nl;			

		// RSS 1.0
		}else{
			// encoding='iso-8859-1'
			$output =  '<?xml version="1.0" encoding="utf-8"?>'.$nl;
			$output .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">'.$nl;
			$output .= '<channel rdf:about="'.self::$channelUrl.'">'.$nl;
			$output .= '<title>'.str_replace('&', '&amp;', self::$channelTitle).'</title>'.$nl;
			$output .= '<link>'.self::$channelUrl.'</link>'.$nl;
			$output .= '<description>'.self::$channelDescription.'</description>'.$nl;
			$output .= '<dc:language>'.self::$channelLang.'</dc:language>'.$nl;
			$output .= '<dc:rights>'.self::$channelCopyright.'</dc:rights>'.$nl;
			$output .= '<dc:date>'.self::$channelDate.'</dc:date>'.$nl;
			$output .= '<dc:creator>'.self::$channelCreator.'</dc:creator>'.$nl;
			$output .= '<dc:subject>'.self::$channelSubject.'</dc:subject>'.$nl;
			$output .= '<items>'.$nl;
			$output .= '<rdf:Seq>';
			for($i=0; $i<self::$countItems; $i++) {
				$output .= '<rdf:li rdf:resource="'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'"/>'.$nl;
			};
			$output .= '</rdf:Seq>'.$nl;
			$output .= '</items>'.$nl;
			$output .= '<image rdf:resource="'.self::$imageUrl.'"/>'.$nl;
			$output .= '</channel>'.$nl;
			for($i=0; $i < self::$countItems; $i++) {
				$output .= '<item rdf:about="'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'">'.$nl;
				$output .= '<title>'.str_replace('&', '&amp;', self::$arrItems[$i]['title']).'</title>'.$nl;
				$output .= '<link>'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'</link>'.$nl;
				$output .= '<description>'.self::$arrItems[$i]['description'].'</description>'.$nl;
				$output .= '<feedburner:origLink>'.str_replace('&', '&amp;', self::$arrItems[$i]['url']).'</feedburner:origLink>'.$nl;
				$output .= '</item>'.$nl;
			};
			$output .= '</rdf:RDF>'.$nl;			
		}
        
        return $output;
    }

  	/**
	 * Saves Feed
	 */
    public static function saveFeed()
	{
		$handle = @fopen(self::$fileName,'w+');
		if($handle){
			@fwrite($handle, self::outputFeed());
			@fclose($handle);
			$result = '';
		}else{
			$result = A::t('core', 'Cannot open RSS file to add a new item! Please check your access rights to {file} or try again later.', array('{file}'=>self::$fileName));		
		}
		return $result;
    }

	/**
	 *  Cleans text from all formating
	 *  @param string $text
	 */
	public static function cleanTextRss($text)
	{
		// $text = preg_replace( "']*>.*?'si", '', $text );
		/* Remove this line to leave URL's intact */
		/* $text = preg_replace( '/]*>([^<]+)<\/a>/is', '\2 (\1)', $text ); */
		$text = preg_replace('//', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace('/ /', ' ', $text);
		//$text = preg_replace('/&/', ' ', $text);
		$text = preg_replace('/"/', ' ', $text);
		/* add the second parameter to strip_tags to ignore the tag for URLs */
		$text = strip_tags($text, '');
		$text = stripcslashes($text);
		$text = htmlspecialchars($text);
		//$text = htmlentities( $text );
		
		return $text;
	}
	
}
<?php
/**
 * CPagination widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * init
 *
 */

class CPagination extends CWidgs
{
	
	/**
	 * @const string new line
	 */
    const NL = "\n";

	
    /**
     * Draws pagination
     * @param array $params
     *
     * Usage: (in View file)
     *  echo CWidget::create('CPagination', array(
     *      'actionPath' 		=> $actionPath,
     *      'currentPage' 		=> $currentPage,
     *      'pageSize' 			=> $pageSize,
     *      'totalRecords' 		=> $totalRecords,
     *      'linkType' 			=> 0,
     *      'paginationType' 	=> 'prevNext|olderNewer|fullNumbers|justNumbers'
     *      'linkNames' 		=> array('previous'=>'', 'next'=>''),
     *      'showEmptyLinks' 	=> true,
     *      'showResultsOfTotal' => true,
     *      'htmlOptions' 		=> array('linksWrapperTag' => 'div', 'linksWrapperClass' => 'links-part')
     *      'return' => true
     *  ));
     */
	public static function init($params = array())
	{
		parent::init($params);
		
		// How many adjacent pages should be shown on each side?
		$adjacents = 4;
		$actionPath = self::params('actionPath', '');
		$paramsSign = preg_match('/\?/', $actionPath) ? '&' : '?';
		$page = (int)self::params('currentPage', 1);
		$pageSize = (int)self::params('pageSize', 1);
		$totalRecords = (int)self::params('totalRecords', 0);
		// Link type: 0 - standard, 1 - SEO
		$linkType = (int)self::params('linkType', 1);
		// justNumbers - 1 2 3
		// fullNumbers - previous 1 2 3 next
		// olderNewer - newer older
		// prevNext - previous next
		$paginationType = self::params('paginationType');
		$paginationType = (in_array(strtolower($paginationType), array('prevnext', 'oldernewer', 'fullnumbers', 'justnumbers'))) ? strtolower($paginationType) : 'fullnumbers';
		$showEmptyLinks = (bool)self::params('showEmptyLinks', true);
		$showResultsOfTotal = (bool)self::params('showResultsOfTotal', true);
		$linkNames = self::params('linkNames', array());
		
		$linksWrapperTag = self::params('htmlOptions.linksWrapperTag', 'div');
		$linksWrapperClass = self::params('htmlOptions.linksWrapperClass', 'links-part');
		
		$return = (bool)self::params('return', true);
		
		if ($page) {
			$start = ($page - 1) * $pageSize;            /* first item to display on this page */
		} else {
			$start = 0;                                    /* if no page var is given, set start to 0 */
		}
		
		// Setup page vars for display
		if ($page == 0) $page = 1;                        /* if no page var is given, default to 1. */
		$prev = $page - 1;                                /* previous page is page - 1 */
		$next = $page + 1;                                /* next page is page + 1 */
		$lastpage = !empty($pageSize) ? ceil($totalRecords / $pageSize) : 1; /* lastpage is = total pages / items per page, rounded up.  */
		$lpm1 = $lastpage - 1;                            /* last page minus 1 */
		$output = '';
		$middlePart = '';
		$counter = 0;
		
		$wPrevious = ($paginationType == 'oldernewer') ? A::t('core', 'newer') : A::t('core', 'previous');
		$wNext = ($paginationType == 'oldernewer') ? A::t('core', 'older') : A::t('core', 'next');
		if (isset($linkNames['previous'])) {
			$wPrevious = $linkNames['previous'];
		}
		if (isset($linkNames['next'])) {
			$wNext = $linkNames['next'];
		}
		
		if ($lastpage > 0) {
			$output .= CHtml::openTag('div', array('class' => 'pagination-wrapper')) . self::NL;
			
			if ($showResultsOfTotal) {
				$output .= CHtml::openTag('div', array('class' => 'results-part'));
				$numFrom = ($start + 1);
				$numTo = (($totalRecords > ($start + $pageSize)) ? ($start + $pageSize) : $totalRecords);
				$output .= CHtml::tag('span', array(), A::t('core', 'Results: {from} - {to} of {total}', array('{from}' => $numFrom, '{to}' => $numTo, '{total}' => $totalRecords)));
				$output .= CHtml::closeTag('div') . self::NL;
			}
			
			if ($lastpage > 1) {
				$output .= CHtml::openTag($linksWrapperTag, array('class' => $linksWrapperClass));
				// Draw previous button
				if (in_array($paginationType, array('fullnumbers', 'prevnext', 'oldernewer'))) {
					if ($page > 1) {
						$output .= CHtml::link('&laquo; ' . $wPrevious, $actionPath . (($linkType) ? '/page/' . $prev : $paramsSign . 'page=' . $prev), array('class' => 'first-link'));
					} else {
						if ($showEmptyLinks) $output .= CHtml::tag('span', array('class' => 'disabled'), '&laquo; ' . $wPrevious);
					}
				}
				
				// Pages
				if ($lastpage < 7 + ($adjacents * 2)) {
					// Not enough pages to bother breaking it up
					for ($counter = 1; $counter <= $lastpage; $counter++) {
						if ($counter == $page) {
							$middlePart .= CHtml::tag('span', array('class' => 'current'), $counter);
						} else {
							$middlePart .= CHtml::link($counter, $actionPath . (($linkType) ? '/page/' . $counter : $paramsSign . 'page=' . $counter));
						}
					}
					// Enough pages to hide some
				} elseif ($lastpage > 5 + ($adjacents * 2)) {
					// Close to beginning, only hide later pages
					if ($page < 1 + ($adjacents * 2)) {
						for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
							if ($counter == $page) {
								$middlePart .= CHtml::tag('span', array('class' => 'current'), $counter);
							} else {
								$middlePart .= CHtml::link($counter, $actionPath . (($linkType) ? '/page/' . $counter : $paramsSign . 'page=' . $counter));
							}
						}
						$middlePart .= '...';
						$middlePart .= CHtml::link($lpm1, $actionPath . (($linkType) ? '/page/' . $lpm1 : $paramsSign . 'page=' . $lpm1));
						$middlePart .= CHtml::link($lastpage, $actionPath . (($linkType) ? '/page/' . $lastpage : $paramsSign . 'page=' . $lastpage));
					} // In middle, hide some front and some back
					elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
						$middlePart .= CHtml::link('1', $actionPath . (($linkType) ? '/page/1' : $paramsSign . 'page=1'));
						$middlePart .= CHtml::link('2', $actionPath . (($linkType) ? '/page/2' : $paramsSign . 'page=2'));
						$middlePart .= '...';
						for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
							if ($counter == $page) {
								$middlePart .= CHtml::tag('span', array('class' => 'current'), $counter);
							} else {
								$middlePart .= CHtml::link($counter, $actionPath . (($linkType) ? '/page/' . $counter : $paramsSign . 'page=' . $counter));
							}
						}
						$middlePart .= '...';
						$middlePart .= CHtml::link($lpm1, $actionPath . (($linkType) ? '/page/' . $lpm1 : $paramsSign . 'page=' . $lpm1));
						$middlePart .= CHtml::link($lastpage, $actionPath . (($linkType) ? '/page/' . $lastpage : $paramsSign . 'page=' . $lastpage));
					} // Close to end, just hide early pages
					else {
						$middlePart .= CHtml::link('1', $actionPath . (($linkType) ? '/page/1' : $paramsSign . 'page=1'));
						$middlePart .= CHtml::link('2', $actionPath . (($linkType) ? '/page/2' : $paramsSign . 'page=2'));
						$middlePart .= '...';
						for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
							if ($counter == $page) {
								$middlePart .= CHtml::tag('span', array('class' => 'current'), $counter);
							} else {
								$middlePart .= CHtml::link($counter, $actionPath . (($linkType) ? '/page/' . $counter : $paramsSign . 'page=' . $counter));
							}
						}
					}
				}
				
				// Draw middle part
				if ($paginationType == 'fullnumbers' || $paginationType == 'justnumbers') {
					$output .= $middlePart;
				}
				
				// Draw next button
				if (in_array($paginationType, array('fullnumbers', 'prevnext', 'oldernewer'))) {
					if ($page < $counter - 1) {
						$output .= CHtml::link($wNext . ' &raquo;', $actionPath . (($linkType) ? '/page/' . $next : $paramsSign . 'page=' . $next), array('class' => 'last-link'));
					} else {
						if ($showEmptyLinks) $output .= CHtml::tag('span', array('class' => 'disabled'), $wNext . ' &raquo;');
					}
				}
				
				$output .= CHtml::closeTag($linksWrapperTag) . self::NL;
			}
			
			$output .= CHtml::closeTag('div') . self::NL;
		}
		
		if ($return) return $output;
		else echo $output;
	}
	
}

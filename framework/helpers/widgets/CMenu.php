<?php
/**
 * CMenu widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):         PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * init
 *
 */

class CMenu extends CWidgs
{
	
	/**
	 * @const string new line
	 */
	const NL = "\n";
	
	
	/**
	 * Draws menu
	 * @param array $param
	 *
	 * Usage:
	 *  CWidget::create('CMenu', array(
	 *      'items'=>array(
	 *          array('label'=>'Home', 'url'=>'index/index', 'target'=>'', 'id'=>''),
	 *          (CAuth::isLoggedIn() == true) ? array('label'=>'Dashboard', 'url'=>'page/dashboard', 'target'=>'', 'id'=>'') : '',
	 *          array('label'=>'Public Page #1', 'url'=>'page/public/id/1', 'target'=>'', 'id'=>''),
	 *          array('label'=>'Public Page #2', 'url'=>'page/public/id/2', 'target'=>'', 'id'=>''),
	 *          array(
	 *            'label'=>'Public Page Level #1',
	 *            'url'=>'page/public/pid/1',
	 *            'id'=>''
	 *            'class'=>'',
	 *                'items'=>array(
	 *                array('label'=>'Level #2-1', 'url'=>'page/public/pid/1', 'target'=>'', 'id'=>''),
	 *                array('label'=>'Level #2-2', 'url'=>'page/public/pid/2', 'target'=>'', 'id'=>''),
	 *                array(
	 *                    'label'=>'Public Page Level #2',
	 *                    'url'=>'page/public/pid/1',
	 *                    'target'=>'',
	 *                    'id'=>''
	 *                        'items'=>array(
	 *                        array('label'=>'Level #3-1', 'url'=>'admins/level3-1', 'target'=>''),
	 *                        array('label'=>'Level #3-2', 'url'=>'admins/level3-2', 'target'=>''),
	 *                        ),
	 *                ),
	 *                ),
	 *          ),
	 *      ),
	 *      'type'=>'horizontal',
	 *      'class'=>'',
	 *      'subMenuClass'=>'',
	 *      'dropdownItemClass'=>'',
	 *      'dropdownItemLinkClass'=>'',
	 *      'dropdownItemLinkDataToggle'=>'',
	 *      'activeItemClass'=>'',
	 *      'separator'=>'',
	 *      'itemInnerTag'=>'span'
	 *      'id'=>'',
	 *      'selected'=>$this->_activeMenu,
	 *      'return'=>true
	 *  ));
	 *
	 *  Example:
	 *  <ul class="class" id="top-menu">
	 *      <li class="active"><a href="#"><span>Item #1</span></a></li>
	 *      <li><a href="#"><span>Item #2</span></a></li>
	 *      <li class="dropdownItemClass">
	 *          <a href="#" class="dropdownItemLinkClass" data-toggle="dropdownItemLinkDataToggle"><span>Item #3</span></a>
	 *          <ul class="subMenuClass">
	 *              <li><a href="#"><span>SubItem #1</span></a></li>
	 *              <li><a href="#"><span>SubItem #2</span></a></li>
	 *          </ul>
	 *      </li>
	 *  </ul>
	 *
	 */
	public static function init($params = array())
	{
		parent::init($params);
		
		$return = self::params('return', true);
		$items = self::params('items', '');
		$class = self::params('class', 'menu');
		$subMenuClass = self::params('subMenuClass', '');
		$dropdownItemClass = self::params('dropdownItemClass', '');
		$dropdownItemLinkClass = self::params('dropdownItemLinkClass', 'dropdown-toggle');
		$dropdownItemLinkDataToggle = self::params('dropdownItemLinkDataToggle', 'dropdown');
		$activeItemClass = self::params('activeItemClass', 'active');
		$type = self::params('type', 'horizontal');
		$separator = self::params('separator', '');
		$itemInnerTag = self::params('itemInnerTag', '');
		$id = self::params('id', '');
		$selected = self::params('selected', '');
		$itemsCount = 0;
		
		$output = '';
		$tagName = 'ul';
		$htmlOptions = array();
		
		if (is_array($items) && count($items) > 0) {
			$htmlOptions['class'] = $class;
			if ($id != '') $htmlOptions['id'] = $id;
			$output .= CHtml::openTag($tagName, $htmlOptions) . self::NL;
			foreach ($items as $item => $val) {
				if (empty($val)) continue;
				
				$url = isset($val['url']) ? $val['url'] : '';
				$label = isset($val['label']) ? $val['label'] : '';
				$id = isset($val['id']) ? $val['id'] : '';
				$readonly = isset($val['readonly']) ? $val['readonly'] : false;
				$itemClass = isset($val['class']) ? $val['class'] : '';
				$itemClass .= (!strcasecmp($selected, $url)) ? ($itemClass ? ' ' : '') . $activeItemClass : '';
				$innerItems = isset($val['items']) ? $val['items'] : '';
				$linkHtmlOptions = (isset($val['target']) && $val['target'] != '') ? array('target' => $val['target']) : array();
				if (is_array($innerItems) && count($innerItems) > 0) {
					$linkHtmlOptions['class'] = $dropdownItemLinkClass;
					$linkHtmlOptions['data-toggle'] = $dropdownItemLinkDataToggle;
					$itemClass .= is_array($innerItems) ? ($itemClass ? ' ' : '') . $dropdownItemClass : '';
					$label .= ' ' . CHtml::tag('b', array('class' => 'caret'), '', true);
				}
				if (!empty($itemInnerTag)) {
					$label = CHtml::tag($itemInnerTag, array(), $label, true);
				}
				$output .= CHtml::openTag('li', array('class' => ($itemClass ? $itemClass : false), 'id' => ($id ? $id : false))) . self::NL;
				$output .= ($separator && $itemsCount > 0) ? $separator : '';
				$output .= ((!$readonly) ? CHtml::link($label, $url, $linkHtmlOptions) : CHtml::label($label)) . self::NL;
				
				// Draw inner items for 2nd level (if exist)
				if (!empty($innerItems) && is_array($innerItems)) {
					$output .= CHtml::openTag('ul', array('class' => $subMenuClass)) . self::NL;
					foreach ($innerItems as $iItem => $iVal) {
						if (empty($iVal)) continue;
						$iUrl = isset($iVal['url']) ? $iVal['url'] : '';
						$iLabel = isset($iVal['label']) ? $iVal['label'] : '';
						$iId = isset($iVal['id']) ? $iVal['id'] : '';
						$iActive = (!strcasecmp($selected, $iUrl)) ? true : false;
						$iInnerItems = isset($iVal['items']) ? $iVal['items'] : '';
						$iLinkHtmlOptions = (isset($iVal['target']) && $iVal['target'] != '') ? array('target' => $iVal['target']) : array();
						if (!empty($itemInnerTag)) {
							$iLabel = CHtml::tag($itemInnerTag, array(), $iLabel, true);
						}
						
						$output .= CHtml::openTag('li', array('class' => ($iActive ? $activeItemClass : false), 'id' => ($iId ? $iId : false)));
						$output .= CHtml::link($iLabel, $iUrl, $iLinkHtmlOptions);
						
						// Draw inner items for 3nd level (if exist)
						if (is_array($iInnerItems)) {
							$output .= CHtml::openTag('ul', array()) . self::NL;
							foreach ($iInnerItems as $iiItem => $iiVal) {
								if (empty($iiVal)) continue;
								$iiUrl = isset($iiVal['url']) ? $iiVal['url'] : '';
								$iiLabel = isset($iiVal['label']) ? $iiVal['label'] : '';
								$iiActive = (!strcasecmp($selected, $iiUrl)) ? true : false;
								if (!empty($itemInnerTag)) {
									$iiLabel = CHtml::tag($itemInnerTag, array(), $iiLabel, true);
								}
								$output .= CHtml::openTag('li', array('class' => ($iiActive ? $activeItemClass : false)));
								$output .= CHtml::link($iiLabel, $iiUrl);
								$output .= CHtml::closeTag('li') . self::NL;
								$itemsCount++;
							} // Foreach
							$output .= CHtml::closeTag('ul') . self::NL;
						}
						
						$output .= CHtml::closeTag('li') . self::NL;
					}
					$output .= CHtml::closeTag('ul') . self::NL;
				}
				
				$output .= CHtml::closeTag('li') . self::NL;
				$itemsCount++;
			} // Foreach
			$output .= CHtml::closeTag($tagName) . self::NL;
		}
		
		if ($return) return $output;
		else echo $output;
	}
	
}

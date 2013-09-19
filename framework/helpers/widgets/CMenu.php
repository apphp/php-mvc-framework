<?php
/**
 * CMenu widget helper class file
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
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * 
 */	  

class CMenu
{
    const NL = "\n";

    /**
     * Draws menu
     * @param array $param
     * 
     * Usage:
     *  CWidget::create('CMenu' array(
     *      'items'=>array(
     *          array('label'=>'Home', 'url'=>'index/index', 'target'=>'', 'id'=>''),
     *          (CAuth::isLoggedIn() == true) ? array('label'=>'Dashboard', 'url'=>'page/dashboard', 'target'=>'', 'id'=>'') : '',
     *          array('label'=>'Public Page #1', 'url'=>'page/public/id/1', 'target'=>'', 'id'=>''),
     *          array('label'=>'Public Page #2', 'url'=>'page/public/id/2', 'target'=>'', 'id'=>''),
     *          array(
     *          	'label'=>'Public Page Level #1',
     *          	'url'=>'page/public/pid/1',
     *          	'id'=>''
     *				'items'=>array(
     *              	array('label'=>'Level #2-1', 'url'=>'page/public/pid/1', 'target'=>'', 'id'=>''),
     *              	array('label'=>'Level #2-2', 'url'=>'page/public/pid/2', 'target'=>'', 'id'=>''),
     *              	array(
     *          			'label'=>'Public Page Level #2',
     *          			'url'=>'page/public/pid/1',
     *          			'target'=>'', 
     *          			'id'=>''
     *						'items'=>array(
     *                      	array('label'=>'Level #3-1', 'url'=>'admins/level3-1', 'target'=>''),
     *                      	array('label'=>'Level #3-2', 'url'=>'admins/level3-2', 'target'=>''),
     *						),
     *              	),
     *				),
     *          ),
     *      ),
     *      'type'=>'horizontal',					
     *      'class'=>'',
     *      'separator'=>'',
     *      'id'=>'',
     *      'selected'=>$this->_activeMenu,
	 *      'return'=>true
     *  ));
     */
    public static function init($params = array())
    {
        $output = '';
        $tagName = 'ul';
        $htmlOptions = array();
        
        $return = isset($params['return']) ? $params['return'] : true;
        $items = isset($params['items']) ? $params['items'] : '';        
        $class = isset($params['class']) ? $params['class'] : 'menu';
		$type = isset($params['type']) ? $params['type'] : 'horizontal';
		$separator = isset($params['separator']) ? $params['separator'] : '';
		$itemsCount = 0;
		$id = isset($params['id']) ? $params['id'] : '';
        $selected = isset($params['selected']) ? $params['selected'] : '';        
        
        if(is_array($items) && count($items) > 0){
            $htmlOptions['class'] = $class;
			if($id != '') $htmlOptions['id'] = $id;
            $output .= CHtml::openTag($tagName, $htmlOptions).self::NL;
            foreach($items as $item => $val){
                if(empty($val)) continue;

                $url = isset($val['url']) ? $val['url'] : '';
                $label = isset($val['label']) ? $val['label'] : '';
                $id = isset($val['id']) ? $val['id'] : '';
                $readonly = isset($val['readonly']) ? $val['readonly'] : false;            
				$active = (!strcasecmp($selected, $url)) ? 'active' : '';
                $innerItems = isset($val['items']) ? $val['items'] : '';
				$linkHtmlOptions = (isset($val['target']) && $val['target'] != '') ? array('target'=>$val['target']) : array();

                $output .= CHtml::openTag('li', array('class'=>($active ? $active : false), 'id'=>($id ? $id : false))).self::NL;
				$output .= ($separator && $itemsCount > 0) ? $separator : '';
                $output .= ((!$readonly) ? CHtml::link($label, $url, $linkHtmlOptions) : CHtml::label($label)).self::NL;

                // draw inner items for 2nd level (if exist)
                if(is_array($innerItems)){
                    $output .= CHtml::openTag('ul', array()).self::NL;
                    foreach($innerItems as $iItem => $iVal){
                        if(empty($iVal)) continue;
                        $iUrl = isset($iVal['url']) ? $iVal['url'] : '';
                        $iLabel = isset($iVal['label']) ? $iVal['label'] : '';
						$iId = isset($iVal['id']) ? $iVal['id'] : '';
                        $iActive = (!strcasecmp($selected, $iUrl)) ? 'active' : '';
						$iInnerItems = isset($iVal['items']) ? $iVal['items'] : '';
						$iLinkHtmlOptions = (isset($iVal['target']) && $iVal['target'] != '') ? array('target'=>$iVal['target']) : array();

                        $output .= CHtml::openTag('li', array('class'=>($iActive ? $iActive : false), 'id'=>($iId ? $iId : false)));
                        $output .= CHtml::link($iLabel, $iUrl, $iLinkHtmlOptions);
						
						// draw inner items for 3nd level (if exist)
						if(is_array($iInnerItems)){
							$output .= CHtml::openTag('ul', array()).self::NL;
							foreach($iInnerItems as $iiItem => $iiVal){
								if(empty($iiVal)) continue;
								$iiUrl = isset($iiVal['url']) ? $iiVal['url'] : '';
								$iiLabel = isset($iiVal['label']) ? $iiVal['label'] : '';
								$iiActive = (!strcasecmp($selected, $iiUrl)) ? 'active' : '';
								$output .= CHtml::openTag('li', array('class'=>($iiActive ? $iiActive : false)));
								$output .= CHtml::link($iiLabel, $iiUrl);
								$output .= CHtml::closeTag('li').self::NL;
								$itemsCount++;
							} // foreach
							$output .= CHtml::closeTag('ul').self::NL;
						}						
						
                        $output .= CHtml::closeTag('li').self::NL;        
                    }
                    $output .= CHtml::closeTag('ul').self::NL;
                }                    

                $output .= CHtml::closeTag('li').self::NL;
				$itemsCount++;
            } // foreach
            $output .= CHtml::closeTag($tagName).self::NL;
        }
        
        if($return) return $output;
        else echo $output;
    }
    
}
<?php
/**
 * CDataGrid widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE (static):
 * ----------               ----------                  ----------
 * init
 *
 *
 */

class CDataGrid extends CWidgs
{
	
	const NL = "\n";
	/** @var array */
	private static $_allowedActs = array('send', 'change');
	/** @var bool */
	private static $_clientValidation = true;
	/** @var bool */
	private static $_clientValidationOnlyRequired = true;
	
	/**
	 * Draws datagrid
	 * @param array $params
	 *
	 * Notes:
	 *
	 *   *** SORTING:
	 *   - 'sortType'=>'string|numeric' - defines soritng type ('string' is default)
	 *   - 'sortBy'=>'' - defines field to perform sorting by
	 *   - 'changeOrder'=>true - allowd to change rows order from main view by clicking of arrows
	 *
	 *
	 *
	 * Usage: (in view file)
	 *  echo CWidget::create('CDataGrid', array(
	 *       'model'			=> 'tableName',
	 *       'actionPath'		=> 'controller/action | locations/add | locations/edit/id/1',
	 *    	 'pagination'		=> array('enable'=>true, 'pageSize'=>20),
	 *    	 'sorting'			=> true,
	 *       'fields'=>array(
	 *           'field_1' => array('title'=>'Field 1', 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'changeOrder'=>false, 'definedValues'=>array(), 'stripTags'=>false, 'case'=>'', 'maxLength'=>'', 'showTooltip'=>true, 'callback'=>array('function'=>$functionName, 'params'=>$functionParams), 'trigger'=>array('trigger_key'=>'', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'', 'wrong_value'=>'')),
	 *       ),
	 *       'buttons'=>array(
	 *          'submit'=>array('type'=>'submit', 'value'=>'Send', 'htmlOptions'=>array('name'=>'')),
	 *          'submitUpdate'=>array('type'=>'submit', 'value'=>'Update', 'htmlOptions'=>array('name'=>'btnUpdate')),
	 *          'submitUpdateClose'=>array('type'=>'submit', 'value'=>'Update & Close', 'htmlOptions'=>array('name'=>'btnUpdateClose')),
	 *          'reset'=>array('type'=>'reset', 'value'=>'Reset', 'htmlOptions'=>array()),
	 *          'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
	 *          'custom'=>array('type'=>'button', 'value'=>'Custom', 'htmlOptions'=>array('onclick'=>"jQuery(location).attr('href','categories/index');")),
	 *       ),
	 *       'buttonsPosition'=>'bottom',
	 *       'messagesSource'=>'core',
	 * 		 'customMessages'=>array('insert'=>array('success'=>'', 'error'=>''), 'update'=>array('success'=>'', 'error'=>'')),
	 *       'showAllErrors'=>false,
	 *		 'alerts'=>array('type'=>standard|flash, 'itemName'=>A::t('app', 'Field Name').' #'.$id),
	 *		 'clientValidation'=>array('enabled'=>true, 'onlyRequired'=>false),
	 *       'return'=>true,
	 *  ));
	 */
	public static function init($params = array())
	{
		parent::init($params);
		
		$baseUrl = A::app()->getRequest()->getBaseUrl();
		$cRequest = A::app()->getRequest();
		$output = '';
		
		$model = self::params('model', '');
		
		$actionPath = self::params('actionPath', '');
		
		$sorting = (bool)self::params('sorting', false);
		$pagination = (bool)self::params('pagination.enable', '');
		$pageSize = abs((int)self::params('pagination.pageSize', '10'));
		
//		$resetBeforeStart = (bool)self::params('resetBeforeStart', false);
//		$primaryKey = (int)self::params('primaryKey', '');
//		$operationType = self::params('operationType', 'add', 'in_array', array('edit', 'add'));
//		$action = self::params('action', '');
//		$successUrl = self::params('successUrl', '');
//		$successCallbackAdd = self::params('successCallback.add', '');
//		$successCallbackEdit = self::params('successCallback.edit', '');
//		$cancelUrl = self::params('cancelUrl', '');
//		$method = self::params('method', 'post');
//		$htmlOptions = (array)self::params('htmlOptions', array(), 'is_array');
//		$requiredFieldsAlert = self::params('requiredFieldsAlert', false);
//		$fieldSets = self::params('fieldSets', array(), 'is_array');
//		$fieldWrapperTag = self::params('fieldWrapper.tag', 'div');
//		$fieldWrapperClass = self::params('fieldWrapper.class', 'row');
//		$linkType = (int)self::params('linkType', 0); /* Link type: 0 - standard, 1 - SEO */
		$return = (bool)self::params('return', true);
		
		$filters = self::params('filters', array());
		$fields = self::params('fields', array(), 'is_array');

//		$translationInfo = self::params('translationInfo', array());
//		$languages = self::keyAt('languages', $translationInfo, array());
//
//		$relation = self::keyAt('relation', $translationInfo, array());
//		$keyFrom = isset($relation[0]) ? $relation[0] : '';
//		$keyTo = isset($relation[1]) ? $relation[1] : '';
//
//		$translationFields = self::params('translationFields', array());
//		$msgSource = self::params('messagesSource', 'core');
//		$customMessages = self::params('customMessages', array());
//		$showAllErrors = (bool)self::params('showAllErrors', false);
//		$alertType = self::params('alerts.type', 'standard');
//		$alertItemName = self::params('alerts.itemName', '');
//		$buttonsPosition = self::params('buttonsPosition', 'bottom');
//		$buttons = self::params('buttons', array());
//		if (self::issetKey('cancel', $buttons) && !empty($cancelUrl)) {
//			$buttons['cancel']['htmlOptions']['onclick'] = 'jQuery(location).attr(\'href\',\'' . $baseUrl . $cancelUrl . '\');';
//		}
		
		$output .= CWidget::create('CGridView', array(
			'model'				=> $model,
        	'relationType'		=> '',
			'actionPath'		=> $actionPath,
//			'condition'			=> CConfig::get('db.prefix').'countries.id <= 30',
//			'groupBy'				=> '',
//			'limit'				=> '',
//			'defaultOrder'		=> array('field_1'=>'DESC', 'field_2'=>'ASC' [,...]),
//			'passParameters'		=> false,
			'pagination'			=> array('enable'=>$pagination, 'pageSize'=>$pageSize),
			'sorting'				=> $sorting,
//			'linkType' 			=> 0,
//			'options'	=> array(
//				 'filterDiv' 	=> array('class'=>''),
//				 'filterForm' 	=> array('class'=>''),
//				 'filterType' 	=> 'default|megamenu',
//				 'gridWrapper'	=> array('tag'=>'div', 'class'=>''),
//				 'gridTable' 	=> array('class'=>''),
//			),
			'filters'	=> $filters,
			'fields'	=> $fields,
//			'actions'	=> array(
//				 'edit'    => array('link'=>'locations/edit/id/{id}/page/{page}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>'Edit this record'),
//				 'delete'  => array('link'=>'locations/delete/id/{id}/page/{page}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>'Delete this record', 'onDeleteAlert'=>true),
//			),
	 		//...
			'return'			=> true,
		));
		

		if ($return) return $output;
		else echo $output;
	}
	
}

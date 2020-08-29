<?php
/**
 * CGridView widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE (static):		
 * ----------               ----------                  ----------
 * init													_additionalParams
 * 														_customParams
 * 														_getFieldParam
 * 														_getAllRecords
 * 														_countAllRecords
 */	  

class CGridView extends CWidgs
{

	/** @const string */
    const NL = "\n";
    /** @var string */
    private static $_rowCount = 0;
    /** @var string */
    private static $_pickerCount = 0;
    /** @var string */
    private static $_autocompleteCount = 0;

    /**
     * Draws grid view
     * @param array $params
     *
     * Notes:
     *   *** FIELD ATTRIBUTES:
     *   - to disable any field, including filtering or button use: 'disabled'=>true
     *   - 'prependCode=>'', 'appendCode'=>'' - insert code before or after field (for all fields): 
     *   - 'data'=>'' - attribute for type 'label', allows to show data from PHP variables
     *   - 'case'=>'normal' - attribute for type 'label', allows to convert value to 'upper', 'lower', 'camel' or 'humanize' cases
     *   - 'maxLength'=>'X' - attribute for type 'label', specifies to show maximum X characters of the string
     *     'showTooltip'=>true - specifies whether to show tooltip on field if it's length was reduced
     *   - 'aggregate'=>['function'=>'sum|avg'] - allow to run aggregate function on specific column
     *   - 'sourceField'=>'' - used to show data from another field
     *   - 'callback'=>array('class'=>'className', 'function'=>'functionName', 'params'=>$functionParams)
     *      callback of closure function that is called when item created (available for labels only), $record - all current record
     *      PHP_VERSION >= 5.3.0 $functionName = function($record, $params){ return record['field_name']; }
     *      Ex.: function callbackFunction($record, $params){...} - passed a whole record
     *   - 'trigger'=>array('trigger_key'=>'field name', 'trigger_operation'=>'!=', 'trigger_value'=>'0', 'success_value'=>'0', 'wrong_value'=>'0'),
     *   	trigger is used for changing field valut depending on simple condition
     *   	Ex.: 'trigger'=>array('trigger_key'=>'is_active', 'trigger_operation'=>'==', 'trigger_value'=>'1', 'success_value'=>A::t('app', 'Active), 'wrong_value'=>''),
     *   - select classes: 'class'=>'chosen-select-filter' or 'class'=>'chosen-select'
     *   - Encryption for standard label or html fields: 'encryption'=>array('enabled'=>CConfig::get('text.encryption'), 'encryptAlgorithm'=>CConfig::get('text.encryptAlgorithm'), 'encryptKey'=>CConfig::get('text.encryptKey'))
     *   
     *   *** SORTING:
     *   - 'sortType'=>'string|numeric' - defines soritng type ('string' is default)
     *   - 'sortBy'=>'' - defines field to perform sorting by
     *   - 'changeOrder'=>true - allowd to change rows order from main view by clicking of arrows
     *   
     *   *** FILTERING:
     *   - to perform search by few fields define them comma separated: 'field1,field2' => array(...)
     *   - for filters attribute 'table' is empty by default. Remember: to add CConfig::get('db.prefix') in 'table'=>CConfig::get('db.prefix').'table'
     *   - 'compareType'=>'string|numeric|binary' - attribute for filtering fields
     *   - 'visible'=>true|false - attribute for visibility of filtering fileds
     *   - 'sourceFilter'=>'' - used to filter from another field
     *
     *   *** MODEL:
     *   - 'relationType' (optional) - defines a type of relation for specific model, if not defined - used default relation type
     *   - 'getAllRecords' - specify custom methods for select of all records of model (optional). Ex.: 'getAllRecords'=>'findAll'
     *   - 'countAllRecords' - specify custom methods for select of all records of model (optional). Ex.: 'countAllRecords'=>'count'
     *   
     * Usage:
     *  echo CWidget::create('CGridView', array(
     *    'model'				=> 'ModelName',
     *    'relationType'		=> '',
     *    'actionPath'			=> 'controller/action',
     *    'condition'			=> CConfig::get('db.prefix').'countries.id <= 30',
     *    'groupBy'				=> '',
     *    'limit'				=> '',
     *    'defaultOrder'		=> array('field_1'=>'DESC', 'field_2'=>'ASC' [,...]),
     *    'passParameters'		=> false,
     *    [DEPRECATED from v0.7.1] 'customParameters'	=> array('param_1'=>'integer', 'param_1'=>'string' [,...]), 
	 *    'pagination'			=> array('enable'=>true, 'pageSize'=>20),
	 *    'sorting'				=> true,
	 *    'linkType' 			=> 0,
	 *    'options'	=> array(
	 *    	 'filterDiv' 	=> array('class'=>''),
	 *    	 'filterForm' 	=> array('class'=>''),
	 *    	 'filterType' 	=> 'default|megamenu',
	 *    	 'gridWrapper'	=> array('tag'=>'div', 'class'=>''),
	 *    	 'gridTable' 	=> array('class'=>''),
	 *    ),
     *    'filters'	=> array(
     *    	 'field_1' 	=> ['title'=>'Field 1', 'type'=>'textbox', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'htmlOptions'=>[]],
     *    	 'field_2' 	=> ['title'=>'Field 2', 'type'=>'textbox', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'maxLength'=>'', 'autocomplete'=>array('enable'=>true, 'ajaxHandler'=>'path/to/handler/file', 'minLength'=>3, 'returnId'=>true), 'htmlOptions'=>[]],
     *    	 'field_3' 	=> ['title'=>'Field 3', 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>array('0'=>'No', '1'=>'Yes'), 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter')],
     *    	 'field_4' 	=> ['title'=>'Field 5', 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'80px', 'maxLength'=>'', 'format'=>'', 'htmlOptions'=>[], 'viewType'=>'datetime|date|time'],
     *    ),
	 *    'fields'	=> array(
	 *       'field_1' => array('title'=>'Field 1', 'type'=>'index', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>false),
	 *       'field_2' => array('title'=>'Field 2', 'type'=>'concat', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'concatFields'=>array('first_name', 'last_name'), 'concatSeparator'=>', ',),
	 *       'field_3' => array('title'=>'Field 3', 'type'=>'decimal', 'align'=>'', 'width'=>'', 'class'=>'right', 'headerTooltip'=>'', 'headerClass'=>'right', 'isSortable'=>true, 'format'=>'american|european', 'decimalPoints'=>''),
	 *       'field_4' => array('title'=>'Field 4', 'type'=>'datetime', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>[], 'format'=>''),
	 *       'field_5' => array('title'=>'Field 5', 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'center', 'headerTooltip'=>'', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>array('0'=>'No', '1'=>'Yes')),
	 *       'field_6' => array('title'=>'Field 6', 'type'=>'image', 'align'=>'', 'width'=>'', 'class'=>'center', 'headerTooltip'=>'', 'headerClass'=>'center', 'isSortable'=>false, 'imagePath'=>'images/flags/', 'defaultImage'=>'', 'imageWidth'=>'16px', 'imageHeight'=>'16px', 'alt'=>'', 'showImageInfo'=>true),
	 *       'field_7' => array('title'=>'Field 7', 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'changeOrder'=>false, 'definedValues'=>[], 'stripTags'=>false, 'case'=>'', 'maxLength'=>'', 'showTooltip'=>true, 'callback'=>array('function'=>$functionName, 'params'=>$functionParams), 'trigger'=>array('trigger_key'=>'', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'', 'wrong_value'=>'')),
	 *       'field_8' => array('title'=>'Field 8', 'type'=>'html', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>[], 'stripTags'=>false, 'case'=>'', 'maxLength'=>'', 'showTooltip'=>true, 'callback'=>array('function'=>$functionName, 'params'=>$functionParams), 'trigger'=>array('trigger_key'=>'', 'trigger_operation'=>'!=', 'trigger_value'=>'', 'success_value'=>'', 'wrong_value'=>'')),
	 *       'field_9' => array('title'=>'Field 9', 'type'=>'link', 'align'=>'', 'width'=>'', 'class'=>'center', 'headerTooltip'=>'', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'path/to/param/{field_name}/page/{page}', 'linkText'=>'{field_name}|free text', 'definedValues'=>[], 'htmlOptions'=>[]),
	 *       'field_10' => array('title'=>'Field 10', 'type'=>'evaluation', 'align'=>'', 'width'=>'', 'class'=>'center', 'headerTooltip'=>'', 'headerClass'=>'center', 'isSortable'=>false, 'minValue'=>1, 'maxValue'=>5, 'tooltip'=>A::t('app', 'Value'), 'counts'=>array('fieldName'=>'', 'title'=>A::t('app', 'Evaluations')), 'definedValues'=>[], 'htmlOptions'=>[]),
	 *       'field_11' => array('title'=>'Field 11', 'type'=>'template', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>false, 'sortBy'=>'', 'html'=>'{category_id}', 'fields'=>array('category_id'=>array('default'=>'', 'prefix'=>'', 'postfix'=>'', 'source'=>[]))),
	 *    ),
	 *    'actions'	=> array(
     *    	 'edit'    => array('link'=>'locations/edit/id/{id}/page/{page}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>'Edit this record'),
     *    	 'delete'  => array('link'=>'locations/delete/id/{id}/page/{page}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>'Delete this record', 'onDeleteAlert'=>true),
     *    ),
	 *    'return'=>true,
     *  ));
    */
	public static function init($params = [])
	{
		parent::init($params);
		
		$output = '';
		$model = self::params('model', '');
		$modelName = $model;
		$relationType = self::params('relationType', '');
		$actionPath = self::params('actionPath', '');
		$condition = self::params('condition', '');
		$groupBy = self::params('groupBy', '');
		$limit = self::params('limit', '');
		$defaultOrder = self::params('defaultOrder', '');
		$passParameters = (bool)self::params('passParameters', false);
		$customParameters = self::params('customParameters', false);
		$return = (bool)self::params('return', true);
		$fields = self::params('fields', []);
		$filters = self::params('filters', []);
		$actions = self::params('actions', []);
		$sortingEnabled = (bool)self::params('sorting', false);
		$filterDiv = self::params('options.filterDiv', []);
		$filterForm = self::params('options.filterForm', []);
		$filterItemDiv = self::params('options.filterItemDiv', []);
		$filterType = self::params('options.filterType', 'default');
		$gridWrapper = self::params('options.gridWrapper', []);
		$gridTable = self::params('options.gridTable', array());
		$linkType = (int)self::params('linkType', 0);    /* Link type: 0 - standard, 1 - SEO */
		$pagination = (bool)self::params('pagination.enable', '');
		$pageSize = abs((int)self::params('pagination.pageSize', '10'));
		$activeActions = is_array($actions) ? count($actions) : 0;
		$onDeleteRecord = false;
		
		$baseUrl = A::app()->getRequest()->getBaseUrl();
		$page = A::app()->getRequest()->getQuery('page', 'integer', 1);
		
		// Get pure model name
		$namespace = explode('\\', $model);
		if (count($namespace) > 1) {
			$modelName = $namespace[count($namespace) - 1];
		}
		
		// Remove disabled actions
		if (is_array($actions)) {
			foreach ($actions as $key => $val) {
				if ((bool)self::keyAt('disabled', $val) === true) {
					unset($actions[$key]);
				}
				if ((bool)self::keyAt('onDeleteAlert', $val) === true) {
					$onDeleteRecord = true;
				}
			}
			$activeActions = count($actions);
		}
		
		// Prepare sorting variables
		// ---------------------------------------		
		$sortBy = '';
		$sortDir = '';
		$sortUrl = '';
		$orderClause = '';
		if ($sortingEnabled) {
			$sortBy = A::app()->getRequest()->getQuery('sort_by', 'dbfield', '');
			$sortDir = (strtolower(A::app()->getRequest()->getQuery('sort_dir', 'alpha', '')) == 'desc') ? 'desc' : 'asc';
		}
		
		if ($sortingEnabled && !empty($sortBy)) {
			$sortType = self::_getFieldParam($fields, $sortBy, 'sortType', array('string', 'numeric'));
			$orderClause = $sortBy . ($sortType == 'numeric' ? ' + 0 ' : '') . ' ' . $sortDir;
		} elseif (is_array($defaultOrder)) {
			foreach ($defaultOrder as $oField => $oFieldType) {
				$sortType = self::_getFieldParam($fields, $oField, 'sortType', array('string', 'numeric'));
				$orderClause .= (!empty($orderClause) ? ', ' : '') . $oField . ($sortType == 'numeric' ? ' + 0 ' : '') . ' ' . $oFieldType;
			}
		}
		
		// Prepare filter variables
		// ---------------------------------------
		$whereClause = (!empty($condition)) ? $condition : '';
		$filterUrl = '';
		if (is_array($filters) && !empty($filters)) {
			// Boodstrap menu
			$filterMegaMenu = strtolower($filterType) == 'megamenu' ? true : false;
			if ($filterMegaMenu) {
				$filterCount = 0;
			}
			
			// Remove disabled fields
			foreach ($filters as $key => $val) {
				if ((bool)self::keyAt('disabled', $val) === true) unset($filters[$key]);
				if ($filterMegaMenu) {
					if (self::keyAt('visible', $val, true) == true) {
						$filterCount++;
					}
				}
			}
			
			if ($filterMegaMenu) {
				$filterIterator = 0;
				$filterOddHalf = false;
				
				$filterHalf = ceil($filterCount / 4) * 2;
				// If the left half of the filter 3 element larger than the right half of the filter, move 1 element in the right half of filter
				if ($filterHalf * 2 - $filterCount == 3) {
					$filterHalf--;
					$filterOddHalf = true;
				}
				$output .= CHtml::openTag('a', array('href' => 'javascript:void(0);', 'class' => 'search-trigger filtering-button'));
				$output .= CHtml::tag('i', array('class' => 'fa fa-search'), '');
				$output .= CHtml::closeTag('a');
				$output .= CHtml::openTag('div', array('class' => (!empty($filterDiv['class']) ? $filterDiv['class'] : 'filtering-wrapper'), 'style' => 'display:none;')) . self::NL;
				$output .= CHtml::openTag('div', array('class' => 'filtering-wrapper-inner')) . self::NL;
				$output .= CHtml::openForm($actionPath, 'get', array('id' => 'frmFilter' . $modelName, 'class' => (!empty($filterForm['class']) ? $filterForm['class'] : null))) . self::NL;
				$output .= CHtml::openTag('div', array('class' => 'row')) . self::NL;
			} else {
				$output .= CHtml::openTag('div', array('class' => (!empty($filterDiv['class']) ? $filterDiv['class'] : 'filtering-wrapper'))) . self::NL;
				$output .= CHtml::openForm($actionPath, 'get', array('id' => 'frmFilter' . $modelName)) . self::NL;
			}
			
			// An array of iterations for the source filter
			$fArrSourceIter = array();
			foreach ($filters as $fKey => $fValue) {
				$visible = isset($fValue['visible']) ? $fValue['visible'] : true;
				$title = isset($fValue['title']) ? $fValue['title'] : '';
				$type = isset($fValue['type']) ? $fValue['type'] : '';
				$table = isset($fValue['table']) ? $fValue['table'] : '';
				$width = (isset($fValue['width']) && CValidator::isHtmlSize($fValue['width'])) ? 'width:' . $fValue['width'] . ';' : '';
				$maxLength = isset($fValue['maxLength']) && !empty($fValue['maxLength']) ? (int)$fValue['maxLength'] : '255';
				$fieldOperator = isset($fValue['operator']) ? $fValue['operator'] : '';
				$fieldDefaultValue = isset($fValue['default']) ? $fValue['default'] : '';
				$compareType = isset($fValue['compareType']) ? $fValue['compareType'] : '';
				$autocomplete = isset($fValue['autocomplete']) ? $fValue['autocomplete'] : array();
				$htmlOptions = isset($fValue['htmlOptions']) ? $fValue['htmlOptions'] : array();
				$fId = !empty($htmlOptions['id']) ? $htmlOptions['id'] : $fKey;
				$htmlOptions['id'] = $fId;
				
				// Check if we want to use another filter as a "source filter"
				$sourceFilter = self::keyAt('sourceFilter', $fValue, '');
				if (!empty($sourceFilter)) {
					$fIsArray = true;
					$fName = $sourceFilter . '[]';
					$fNameAbbr = $sourceFilter;
				} else {
					$fIsArray = false;
					$fName = $fKey;
					$fNameAbbr = $fKey;
				}
				
				if (A::app()->getRequest()->getQuery('but_filter')) {
					if ($fIsArray) {
						$fieldValue = '';
						$arrFieldValue = A::app()->getRequest()->getQuery($fNameAbbr);
						$fIter = isset($fArrSourceIter[$sourceFilter]) ? $fArrSourceIter[$sourceFilter] : 0;
						if (!empty($arrFieldValue) && is_array($arrFieldValue)) {
							$fieldValue = isset($arrFieldValue[$fIter]) ? CHtml::decode($arrFieldValue[$fIter]) : '';
						}
						$fArrSourceIter[$sourceFilter] = ++$fIter;
					} else {
						$fieldValue = CHtml::decode(A::app()->getRequest()->getQuery($fNameAbbr));
					}
				} else {
					$fieldValue = $fieldDefaultValue;
				}
				
				if ($visible) {
					if ($filterMegaMenu) {
						$filterNewRow = $filterIterator > $filterHalf && $filterOddHalf
							? $filterIterator % 2 == 1
							: $filterIterator % 2 == 0;
						if ($filterIterator == 0 || $filterIterator == $filterHalf) {
							$output .= CHtml::openTag('div', array('class' => 'col-md-6 col-sm-6'));
							$output .= CHtml::openTag('div', array('class' => 'row'));
						} elseif ($filterNewRow) {
							$output .= CHtml::openTag('div', array('class' => 'row'));
						}
						
						$output .= CHtml::openTag('div', array('class' => 'col-md-6'));
					}
					
					if (!empty($filterItemDiv)) {
						$output .= CHtml::openTag('div', array('class' => (!empty($filterItemDiv['class']) ? $filterItemDiv['class'] : ''))) . self::NL;
					}
					if ($filterMegaMenu && !empty($title)) {
						$output .= CHtml::tag('label', array(), $title);
					}
					
					$output .= !empty($title) ? $title . ': ' : '';
					
					switch ($type) {
						case 'enum':
							$source = isset($fValue['source']) ? $fValue['source'] : array();
							$emptyOption = isset($fValue['emptyOption']) ? (bool)$fValue['emptyOption'] : false;
							$emptyValue = !empty($fValue['emptyValue']) ? $fValue['emptyValue'] : '--';
							$sourceCount = count($source);
							if ($sourceCount >= 1 || !empty($emptyOption)) {
								if ($emptyOption) {
									$source = array('' => $emptyValue) + $source;
								}
								$htmlOptions['style'] = $width;
								if ($filterMegaMenu && !empty($title)) {
									$htmlOptions['class'] = !empty($htmlOptions['class']) ? $htmlOptions['class'] : 'form-control selectpicker';
								}
								$output .= (count($source)) ? CHtml::dropDownList($fName, $fieldValue, $source, $htmlOptions) : 'no';
							} else {
								$output .= A::t('core', 'none');
							}
							$output .= '&nbsp;' . self::NL;
							break;
						
						case 'datetime':
						case 'datetimepicker':
							$format = !empty($fValue['format']) ? $fValue['format'] : 'yy-mm-dd';
							$viewType = !empty($fValue['viewType']) ? $fValue['viewType'] : 'date';
							
							$htmlOptions['maxlength'] = $maxLength;
							$htmlOptions['style'] = $width;
							if ($filterMegaMenu && !empty($title)) {
								$htmlOptions['class'] = !empty($htmlOptions['class']) ? $htmlOptions['class'] : 'form-control';
							}
							$output .= CHtml::textField($fName, $fieldValue, $htmlOptions);
							A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
							A::app()->getClientScript()->registerCssFile('assets/vendors/datetimepicker/jquery-ui-timepicker-addon.css');
							
							// It's better if it was alredy added in template file
							// A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 2);
							A::app()->getClientScript()->registerScriptFile('assets/vendors/datetimepicker/jquery-ui-timepicker-addon.js', 2);
							A::app()->getClientScript()->registerScriptFile('assets/vendors/datetimepicker/i18n/jquery-ui-timepicker-addon-i18n.min.js', 2);
							A::app()->getClientScript()->registerScriptFile('assets/vendors/datetimepicker/jquery-ui-slideraccess.js', 2);
							
							// UI:
							//		dateFormat: dd/mm/yy | d M, y | mm/dd/yy  | yy-mm-dd
							// Bootstrap:
							// 		dateFormat: dd/mm/yyyy | d M, y | mm/dd/yyyy  | yyyy-mm-dd
							//		autoclose: true,
							if ($viewType == 'datetime' || $viewType == 'date') {
								A::app()->getClientScript()->registerScript(
									'datetimepicker_' . self::$_pickerCount++,
									'jQuery("#' . $fId . '").datetimepicker({
                                        showOn: "button",
                                        buttonImage: "assets/vendors/jquery/images/calendar.png",
                                        buttonImageOnly: true,
                                        showWeek: false,
                                        firstDay: 1,
                                        autoclose: true,						
                                        format: "' . ($format == 'yy-mm-dd' ? 'yyyy-mm-dd' : $format) . '",
                                        dateFormat: "' . $format . '",
                                        changeMonth: true,
                                        changeYear: true,							
                                        hourMin: 0,
                                        hourMax: 23,
                                        isRTL: ' . (A::app()->getLanguage('direction') == 'rtl' ? 'true' : 'false') . ',
                                        showTimepicker: ' . ($viewType == 'datetime' ? 'true' : 'false') . ',
                                        ' . ($viewType == 'datetime' ? 'timeInput: true,' : '') . '
                                        ' . ($viewType == 'datetime' ? 'timeFormat: "hh:mm",' : '') . '
                                        appendText : ""
                                    });'
								);
							} elseif ($viewType = 'time') {
								A::app()->getClientScript()->registerScript(
									'timepicker' . self::$_pickerCount++,
									'jQuery("#' . $fId . '").timepicker({
                                        showOn: "button",
                                        buttonImage: "assets/vendors/jquery/images/calendar.png",
                                        buttonImageOnly: true,
                                        autoclose: true,						
                                        hourMin: 0,
                                        hourMax: 23,
                                        isRTL: ' . (A::app()->getLanguage('direction') == 'rtl' ? 'true' : 'false') . ',
                                        showTimepicker: ' . ($viewType = 'datetime' ? 'true' : '') . ',
                                        timeInput: true,
                                        timeFormat: "hh:mm",
                                        appendText : ""
                                    });'
								);
							}
							break;
						
						case 'textbox':
						default:
							$autocompleteEnabled = self::keyAt('enable', $autocomplete);
							$autocompleteAjaxHandler = self::keyAt('ajaxHandler', $autocomplete, '');
							$autocompleteMinLength = self::keyAt('minLength', $autocomplete, 1);
							$autocompleteReturnId = self::keyAt('returnId', $autocomplete, true);
							
							if ($autocompleteEnabled) {
								$cRequest = A::app()->getRequest();
								
								A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
								// Already included in backend default.php
								if (A::app()->view->getTemplate() != 'backend') {
									A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 2);
								}
								
								$fKeySearch = $autocompleteReturnId ? $fId . '_result' : $fId;
								A::app()->getClientScript()->registerScript(
									'autocomplete_' . self::$_autocompleteCount++,
									'jQuery("#' . $fKeySearch . '").autocomplete({
										source: function(request, response){
											$.ajax({
												url: "' . CHtml::encode($autocompleteAjaxHandler) . '",
												global: false,
												type: "POST",
												data: ({
													' . $cRequest->getCsrfTokenKey() . ': "' . $cRequest->getCsrfTokenValue() . '",
													act: "send",
													search : jQuery("#' . $fKeySearch . '").val()
												}),
												dataType: "json",
												async: true,
												error: function(html){
													' . ((APPHP_MODE == 'debug') ? 'alert("AJAX: cannot connect to the server or server response error! Please try again later.");' : '') . '
												},
												success: function(data){
													if(data.length == 0){
														response({label: "' . A::te('core', 'No matches found') . '"});
													}else{
														response($.map(data, function(item){
															if(item.label !== undefined){
																return {id: ' . ($autocompleteReturnId ? 'item.id' : 'item.label') . ', label: item.label}
															}else{
																// Empty search value if nothing found
																jQuery("#' . $fId . '").val("");
															}
														}));
													}
												}
											});
										},
										minLength: ' . (int)$autocompleteMinLength . ',
										select: function(event, ui) {
											jQuery("#' . $fId . '").val(ui.item.id);
											if(typeof(ui.item.id) == "undefined"){
												jQuery("#' . $fKeySearch . '").val("");
												return false;
											}
										}
									});',
									4
								);
								
								if ($autocompleteReturnId) {
									// Draw hidden field for real field
									$output .= CHtml::hiddenField($fName, CHtml::encode($fieldValue), $htmlOptions) . self::NL;
								}
								// Draw textbox
								$htmlOptions['style'] = $width;
								$htmlOptions['maxlength'] = $maxLength;
								if ($filterMegaMenu && !empty($title)) {
									$htmlOptions['class'] = !empty($htmlOptions['class']) ? $htmlOptions['class'] : 'form-control';
								}
								$fieldValueSearch = $cRequest->getQuery($fKeySearch);
								$output .= CHtml::textField($fKeySearch, CHtml::encode($fieldValueSearch), $htmlOptions) . self::NL;
							} else {
								// Draw textbox
								$htmlOptions['style'] = $width;
								$htmlOptions['maxlength'] = $maxLength;
								if ($filterMegaMenu && !empty($title)) {
									$htmlOptions['class'] = !empty($htmlOptions['class']) ? $htmlOptions['class'] : 'form-control';
								}
								$output .= CHtml::textField($fName, CHtml::encode($fieldValue), $htmlOptions) . self::NL;
							}
							break;
					}
					if (!empty($filterItemDiv)) {
						$output .= CHtml::closeTag('div') . self::NL;
					}
					if ($filterMegaMenu) {
						$filterEndRow = $filterIterator >= $filterHalf && $filterOddHalf
							? $filterIterator % 2 == 0
							: $filterIterator % 2 == 1;
						$output .= CHtml::closeTag('div');
						if ($filterIterator == ($filterHalf - 1) || $filterIterator == ($filterCount - 1)) {
							$output .= CHtml::closeTag('div');
							$output .= CHtml::closeTag('div');
						} elseif ($filterEndRow) {
							$output .= CHtml::closeTag('div');
						}
						$filterIterator++;
					}
				}
				
				if ($fieldValue !== '') {
					$filterUrl .= (!empty($filterUrl) ? '&' : '') . $fName . '=' . $fieldValue;
					
					// Check if there is an autocomplete key that must be added to filter string
					$autocompleteValue = A::app()->getRequest()->getQuery($fId . '_result');
					if ($autocompleteValue != '') {
						$filterUrl .= (!empty($filterUrl) ? '&' : '') . $fId . '_result=' . $autocompleteValue;
					}
					
					$escapedFieldValue = strip_tags(CString::quote($fieldValue));
					$quote = ($compareType == 'numeric' || $compareType == 'binary') ? '' : "'";
					$binary = ($compareType == 'binary') ? 'BINARY ' : '';
					$whereClauseMiddle = '';
					
					$fKeyParts = explode(',', $fNameAbbr);
					$fKeyPartsCount = count($fKeyParts);
					foreach ($fKeyParts as $key => $val) {
						if (!empty($table)) $val = $table . '.' . $val;
						if ($fKeyPartsCount == 1) {
							$whereClauseMiddle .= !empty($whereClause) ? ' AND ' : '';
						} else {
							$whereClauseMiddle .= !empty($whereClauseMiddle) ? ' OR ' : '';
						}
						$whereClauseMiddle .= $binary . $val . ' ';
						switch ($fieldOperator) {
							case 'like':
								$whereClauseMiddle .= "like " . $quote . $escapedFieldValue . $quote;
								break;
							case 'not like':
								$whereClauseMiddle .= "not like " . $quote . $escapedFieldValue . $quote;
								break;
							case '%like':
								$whereClauseMiddle .= "like '%" . $escapedFieldValue . "'";
								break;
							case 'like%':
								$whereClauseMiddle .= "like '" . $escapedFieldValue . "%'";
								break;
							case '%like%':
								$whereClauseMiddle .= "like '%" . $escapedFieldValue . "%'";
								break;
							case '!=':
							case '<>':
								$whereClauseMiddle .= "!= " . $quote . $escapedFieldValue . $quote;
								break;
							case '>':
								$whereClauseMiddle .= "> " . $quote . $escapedFieldValue . $quote;
								break;
							case '>=':
								$whereClauseMiddle .= ">= " . $quote . $escapedFieldValue . $quote;
								break;
							case '<':
								$whereClauseMiddle .= "< " . $quote . $escapedFieldValue . $quote;
								break;
							case '<=':
								$whereClauseMiddle .= "<= " . $quote . $escapedFieldValue . $quote;
								break;
							case '=':
							default:
								$whereClauseMiddle .= "= " . $quote . $escapedFieldValue . $quote;
								break;
						}
					}
					if ($fKeyPartsCount > 1) {
						$whereClause .= (!empty($whereClause) ? ' AND' : '') . ' (' . $whereClauseMiddle . ')';
					} else {
						$whereClause .= $whereClauseMiddle;
					}
				}
			}
			
			if ($filterMegaMenu) {
				$output .= CHtml::tag('div', array('class' => 'spacer-20'), '');
				$output .= CHtml::openTag('div', array('class' => 'col-md-12')) . self::NL;
				if (A::app()->getRequest()->getQuery('but_filter')) {
					$filterUrl .= (!empty($filterUrl) ? '&' : '') . 'but_filter=true';
					$output .= CHtml::button(A::t('core', 'Cancel'), array('name' => '', 'class' => 'btn btn-default btn-lg', 'onclick' => 'jQuery(location).attr(\'href\',\'' . $baseUrl . $actionPath . '\');')) . self::NL;
				}
				$output .= CHtml::submitButton(A::t('core', 'Filter'), array('name' => 'but_filter', 'class' => 'btn btn-info btn-lg pull-right')) . self::NL;
				$output .= CHtml::closeTag('div');
			} else {
				$output .= CHtml::openTag('div', array('class' => 'buttons-wrapper')) . self::NL;
				if (A::app()->getRequest()->getQuery('but_filter')) {
					$filterUrl .= (!empty($filterUrl) ? '&' : '') . 'but_filter=true';
					$output .= CHtml::button(A::t('core', 'Cancel'), array('name' => '', 'class' => 'button white', 'onclick' => 'jQuery(location).attr(\'href\',\'' . $baseUrl . $actionPath . '\');')) . self::NL;
				}
				$output .= CHtml::submitButton(A::t('core', 'Filter'), array('name' => 'but_filter')) . self::NL;
			}
			
			if ($filterMegaMenu) $output .= CHtml::closeTag('div') . self::NL;
			$output .= CHtml::closeTag('div') . self::NL;
			$output .= CHtml::closeForm() . self::NL;
			if ($filterMegaMenu) $output .= CHtml::closeTag('div') . self::NL;
			$output .= CHtml::closeTag('div') . self::NL;
			$filterUrl = CHtml::encode($filterUrl);
		}
		
		// Prepare pagination variables
		// ---------------------------------------
		$totalRecords = $totalPageRecords = 0;
		$currentPage = '';
        $modelParams = ! empty($relationType) ? [$relationType] : [];
        $objModel = call_user_func_array($model . '::model', $modelParams);
		if (!$objModel) {
            CDebug::addMessage('errors', 'missing-model', A::t('core', 'Unable to find class "{class}".', ['{class}' => $model]), 'session');
            return '';
		}
		
		// Replacing rows
		// ---------------------------------------
		$act = A::app()->getRequest()->getQuery('act', 'alpha', '');
		if ($act == 'replace') {
			$replaceField = A::app()->getRequest()->getQuery('replace_field', 'dbfield', '');
			if (isset($fields[$replaceField])) {
				$replaceIds = A::app()->getRequest()->getQuery('replace_ids', 'dbfield', '');
				$replaceIdsParts = explode('-', $replaceIds);
				$id1 = !empty($replaceIdsParts[0]) ? (int)$replaceIdsParts[0] : 0;
				$id2 = !empty($replaceIdsParts[1]) ? (int)$replaceIdsParts[1] : 0;
				$record1 = $objModel->findByPk($id1);
				if ($record1) {
					$val1 = $record1->$replaceField;
					$val2 = '';
					$record2 = $objModel->findByPk($id2);
					if ($record2) {
						$val2 = $record2->$replaceField;
					}
					if ($val1 != '' && $val2 != '') {
                        $objModel->updateByPk($id1, [$replaceField => $val2]);
                        $objModel->updateByPk($id2, [$replaceField => $val1]);
                        if (APPHP_MODE == 'demo') {
							A::app()->getSession()->setFlash('alert', A::t('core', 'This operation is blocked in Demo Mode!'));
							A::app()->getSession()->setFlash('alertType', 'warning');
						} else {
							A::app()->getSession()->setFlash('alert', A::t('core', 'The changing order operation has been successfully completed!'));
							A::app()->getSession()->setFlash('alertType', 'success');
						}
						
						// Add sorting and custom parameters to URL
						$searchActionPath = $actionPath;
						$separateSymbol = preg_match('/\?/', $searchActionPath) ? '&' : '?';
						$searchActionPath .= self::_additionalParams($passParameters, $linkType, $separateSymbol);
						$separateSymbol = preg_match('/\?/', $searchActionPath) ? '&' : '?';
						$searchActionPath .= self::_customParams($customParameters, $linkType, $separateSymbol);
						
						header('location: ' . $baseUrl . $searchActionPath);
						exit;
					}
				}
			}
		}
		
		if ($pagination) {
			$currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
			$totalRecords = self::_countAllRecords($objModel, ['condition' => $whereClause, 'group' => $groupBy]);
			if ($currentPage) {
				$records = self::_getAllRecords($objModel, array('condition' => $whereClause, 'order' => $orderClause, 'group' => $groupBy, 'limit' => (($currentPage - 1) * $pageSize) . ', ' . $pageSize));
				$totalPageRecords = is_array($records) ? count($records) : 0;
			}
			if (!$totalPageRecords || !$currentPage) {
				if (A::app()->getRequest()->getQuery('but_filter')) {
                    $output .= CWidget::create('CMessage', ['warning', A::t('core', 'No records found or incorrect parameters passed')]);
                } else {
                    $output .= CWidget::create('CMessage', ['warning', A::t('core', 'No records found')]);
                }
            }
		} else {
			$records = self::_getAllRecords($objModel, ['condition' => $whereClause, 'order' => $orderClause, 'group' => $groupBy, 'limit' => $limit]);
			$totalPageRecords = is_array($records) ? count($records) : 0;
			if (!$totalPageRecords) {
                $output .= CWidget::create('CMessage', ['warning', A::t('core', 'No records found')]);
            }
		}
		
		// Draw table
		// ---------------------------------------
		if ($totalPageRecords > 0) {
			// Remove disabled fields
			foreach ($fields as $key => $val) {
				if ((bool)self::keyAt('disabled', $val) === true) unset($fields[$key]);
			}
			
			// ----------------------------------------------
			// Draw TABLE wrapper
			// ----------------------------------------------
			if (!empty($gridWrapper['tag'])) {
                $output .= CHtml::openTag($gridWrapper['tag'], ['class' => (! empty($gridWrapper['class']) ? $gridWrapper['class'] : null)]).self::NL;
            }
			// ----------------------------------------------
			// Draw <THEAD> rows 
			// ----------------------------------------------
            $output .= CHtml::openTag('table', ['class' => 'grid-view-table'.(! empty($gridTable['class']) ? ' '.$gridTable['class'] : '')]).self::NL;
            $output .= CHtml::openTag('thead') . self::NL;
            $output .= CHtml::openTag('tr', ['id' => 'tr'.$modelName.'_'.self::$_rowCount++]).self::NL;
            foreach ($fields as $key => $val) {
				
				// Check if we want to use another field as a "source field"
				$sourceField = self::keyAt('sourceField', $val, '');
				if (!empty($sourceField)) {
					$key = $sourceField;
				}
				
				$type = self::keyAt('type', $val, '');
				$title = self::keyAt('title', $val, '');
				$headerClass = self::keyAt('headerClass', $val, '');
				$isSortable = (bool)self::keyAt('isSortable', $val, true);
				$sortField = self::keyAt('sortBy', $val, '');
				$widthAttr = self::keyAt('width', $val);
				$alignAttr = self::keyAt('align', $val);
				$headerTooltip = self::keyAt('headerTooltip', $val);
				
				// Prepare style attributes
				$width = (!empty($widthAttr) && CValidator::isHtmlSize($widthAttr)) ? 'width:' . $widthAttr . ';' : '';
				$align = (!empty($alignAttr) && CValidator::isAlignment($alignAttr)) ? 'text-align:' . $alignAttr . ';' : '';
				$style = $width . $align;
				
				if ($sortingEnabled && $isSortable && ($type != 'template' || ($type == 'template' && !empty($sortField)))) {
					$sortByField = !empty($sortField) ? $sortField : $key;
					$colSortDir = (($sortBy != $sortByField) ? 'asc' : (($sortDir == 'asc') ? 'desc' : 'asc'));
					$sortImg = ($sortBy == $sortByField) ? ' ' . CHtml::tag('span', ['class' => 'sort-arrow'], (($colSortDir == 'asc') ? '&#9660;' : '&#9650;')) : '';
					if ($sortBy == $sortByField) $sortUrl = 'sort_by=' . $sortByField . '&sort_dir=' . $sortDir;
					
					$separateSymbol = preg_match('/\?/', $actionPath) ? '&' : '?';
					$linkUrl = $actionPath . $separateSymbol . 'sort_by=' . $sortByField . '&sort_dir=' . $colSortDir;
					$linkUrl .= !empty($currentPage) ? '&page=' . $currentPage : '';
					$linkUrl .= !empty($filterUrl) ? '&' . $filterUrl : '';
					$linkUrl .= self::_customParams($customParameters, $linkType, '&');
					$title = CHtml::link($title . $sortImg, $linkUrl);
				}

                $title .= ! empty($headerTooltip) ? ' '.CHtml::link('', false, ['class' => 'tooltip-icon', 'title' => $headerTooltip]) : '';

                $output .= CHtml::tag('th', ['class' => $headerClass, 'style' => $style], $title).self::NL;
            }
			if ($activeActions > 0) {
                $output .= CHtml::tag('th', ['class' => 'actions'], A::t('core', 'Actions')).self::NL;
            }
			$output .= CHtml::closeTag('tr') . self::NL;;
			$output .= CHtml::closeTag('thead') . self::NL;
			
			// ----------------------------------------------
			// Draw <TBODY> rows 
			// ----------------------------------------------
            $aggregateRow = [];
            $output .= CHtml::openTag('tbody') . self::NL;
			for ($i = 0; $i < $totalPageRecords; $i++) {
                $output .= CHtml::openTag('tr', ['id' => 'tr'.$modelName.'_'.self::$_rowCount++]).self::NL;
                $id = (isset($records[$i]['id'])) ? $records[$i]['id'] : '';
				$prevId = (isset($records[$i - 1]['id'])) ? $records[$i - 1]['id'] : '';
				$nextId = (isset($records[$i + 1]['id'])) ? $records[$i + 1]['id'] : '';
				
				// ----------------------------------------------
				// Display columns in each row
				// ----------------------------------------------
				foreach ($fields as $key => $val) {
					
					// Check if we want to use another field as a "source field"
					$sourceField = self::keyAt('sourceField', $val, '');
					if (!empty($sourceField)) {
						$key = $sourceField;
					}
					
					$align = self::keyAt('align', $val, '');
					$style = (!empty($align) && CValidator::isAlignment($align)) ? 'text-align:' . $align . ';' : '';
					$class = self::keyAt('class', $val, '');
					$type = self::keyAt('type', $val, '');
					$title = self::keyAt('title', $val, '');
					$format = self::keyAt('format', $val, '');
					$definedValues = self::keyAt('definedValues', $val, '');
                    $htmlOptions = (array)self::keyAt('htmlOptions', $val, []);
                    $prependCode = self::keyAt('prependCode', $val, '');
					$appendCode = self::keyAt('appendCode', $val, '');
					$fieldValue = (isset($records[$i][$key])) ? $records[$i][$key] : ''; /* $key */
					$fieldValueOriginal = $fieldValue;
					$aggregateFunction = self::keyAt('aggregate.function', $val, '');
                    $aggregateFunction = in_array(strtolower($aggregateFunction), ['sum', 'avg']) ? strtolower($aggregateFunction) : '';
                    $changeOrder = (bool)self::keyAt('changeOrder', $val, false);
					
					$fieldOutput = '';
					switch ($type) {
						case 'concat':
							$concatFields = self::keyAt('concatFields', $val, '');
							$concatSeparator = self::keyAt('concatSeparator', $val, '');
							$concatResult = '';
							if (is_array($concatFields)) {
								foreach ($concatFields as $cfKey) {
									if (!empty($concatResult)) $concatResult .= $concatSeparator;
									$concatResult .= $records[$i][$cfKey];
								}
							}
							$fieldOutput .= $concatResult;
							break;
						
						case 'decimal':
							$decimalPoints = (int)self::keyAt('decimalPoints', $val, 0);
							if ($format === 'european') {
								// 1,222.33 => '1.222,33'
								$fieldValue = str_replace(',', '', $fieldValue);
								$fieldValue = number_format((float)$fieldValue, $decimalPoints, ',', '.');
							} else {
								$fieldValue = number_format((float)$fieldValue, $decimalPoints);
							}
							$fieldOutput .= $fieldValue;
							break;
						
						case 'datetime':
							if (is_array($definedValues) && isset($definedValues[$fieldValue])) {
								$fieldValue = $definedValues[$fieldValue];
							} elseif ($format != '') {
								$fieldValue = date($format, strtotime($fieldValue));
							}
							$fieldOutput .= $fieldValue;
							break;
						
						case 'enum':
							$source = self::keyAt('source', $val, '');
							$outputValue = isset($source[$fieldValue]) ? $source[$fieldValue] : '';
							if (is_array($definedValues) && isset($definedValues[$outputValue])) {
								$fieldOutput .= $definedValues[$outputValue];
							} else {
								$fieldOutput .= $outputValue;
							}
							break;
						
						case 'index':
							$fieldOutput .= ($i + 1) . '.';
							break;
						
						case 'image':
							$imagePath = self::keyAt('imagePath', $val, '');
							$defaultImage = self::keyAt('defaultImage', $val, '');
							$alt = self::keyAt('alt', $val, '');
							$imageWidth = self::keyAt('imageWidth', $val, '');
							$imageHeight = self::keyAt('imageHeight', $val, '');
							$showImageInfo = (bool)self::keyAt('showImageInfo', $val, true);
							
							$htmlOptions = array();
							if (!empty($imageWidth) && CValidator::isHtmlSize($imageWidth)) $htmlOptions['width'] = $imageWidth;
							if (!empty($imageHeight) && CValidator::isHtmlSize($imageHeight)) $htmlOptions['height'] = $imageHeight;
							if ((!$fieldValue || !file_exists($imagePath . $fieldValue)) && !empty($defaultImage)) $fieldValue = $defaultImage;
							if ($showImageInfo) {
								$htmlOptions['title'] = $fieldValue;
								//$htmlOptions['title'] .= '(';
								//$htmlOptions['title'] .= CFile::getFileSize($imagePath.$fieldValue, 'kb').' Kb';
								//$imageDimensions = CFile::getImageDimensions($imagePath.$fieldValue);
								//$htmlOptions['title'] .= ', '.$imageDimensions['width'].'x'.$imageDimensions['height'];
								//$htmlOptions['title'] .= ')';
							}
							
							$fieldOutput .= CHtml::image($imagePath . $fieldValue, $alt, $htmlOptions) . self::NL;
							break;
						
						case 'link':
							$linkUrl = self::keyAt('linkUrl', $val, '#');
							$linkText = self::keyAt('linkText', $val, '');
							
							// Replace for linkURL
							if (preg_match_all('/{(.*?)}/i', $linkUrl, $matches)) {
								if (isset($matches[1]) && is_array($matches[1])) {
									foreach ($matches[1] as $kKey => $kVal) {
										if ($kVal == 'page' && $pagination) {
											$currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
											$linkUrl = str_ireplace('{' . $kVal . '}', $currentPage, $linkUrl);
										} else {
											$kValValue = (isset($records[$i][$kVal])) ? $records[$i][$kVal] : '';
											$linkUrl = str_ireplace('{' . $kVal . '}', $kValValue, $linkUrl);
										}
									}
								}
							}
							
							// Replace for linkText
							$fieldValue = (isset($records[$i][$key])) ? $records[$i][$key] : ''; /* $key */
							if (is_array($definedValues) && isset($definedValues[$fieldValue])) {
								$linkText = $definedValues[$fieldValue];
							} else {
								if (preg_match_all('/{(.*?)}/i', $linkText, $matches)) {
									if (isset($matches[1]) && is_array($matches[1])) {
										foreach ($matches[1] as $kKey => $kVal) {
											$kValValue = (isset($records[$i][$kVal])) ? $records[$i][$kVal] : '';
											$linkText = str_ireplace('{' . $kVal . '}', $kValValue, $linkText);
										}
									}
								}
							}
							
							$fieldOutput .= CHtml::link($linkText, $linkUrl, $htmlOptions);
							break;
						
						case 'evaluation':
							$minValue = self::keyAt('minValue', $val, 1);
							$maxValue = self::keyAt('maxValue', $val, 5);
							$size = max(0, (min($maxValue, $fieldValue))) * 16;
							$tooltip = self::keyAt('tooltip', $val, '');
							$titleText = CHtml::encode($tooltip) . ': ' . $fieldValue;
							
							$countsField = self::keyAt('counts.fieldName', $val, '');
							$countsTitle = self::keyAt('counts.title', $val, '');
							if (!empty($countsField)) {
								$countsValue = !empty($countsField) && isset($records[$i][$countsField]) ? $records[$i][$countsField] : '';
								$titleText .= ' / ' . $countsTitle . ': ' . $countsValue;
							}
							
							$fieldOutput .= CHtml::tag('label', array('class' => 'stars tooltip-link', 'title' => $titleText), '<label style="width:' . $size . 'px;"></label>');
							break;
						
						case 'template':
							$htmlCode = self::keyAt('html', $val, '');
							$templateFields = self::keyAt('fields', $val, array());
							$templateSources = self::keyAt('sources', $val, array());
							
							if (is_array($templateFields)) {
								foreach ($templateFields as $tfKey => $tfValue) {
									if (is_array($tfValue)) {
										$default = isset($tfValue['default']) ? $tfValue['default'] : '';
										$prefix = isset($tfValue['prefix']) ? $tfValue['prefix'] : '';
										$postfix = isset($tfValue['postfix']) ? $tfValue['postfix'] : '';
										$source = isset($tfValue['source']) ? $tfValue['source'] : array();
										
										$templateFieldValue = isset($records[$i][$tfKey]) ? $records[$i][$tfKey] : '';
										// Check if there is an array with predefined values
										if (!empty($source) && is_array($source)) {
											$templateFieldValue = isset($source[$templateFieldValue]) ? $source[$templateFieldValue] : '';
										}
										if (empty($templateFieldValue)) $templateFieldValue = $default;
										if (!empty($templateFieldValue)) $templateFieldValue = $prefix . $templateFieldValue . $postfix;
										
										$htmlCode = str_ireplace('{' . $tfKey . '}', $templateFieldValue, $htmlCode);
									} else {
										$templateFieldValue = isset($records[$i][$tfValue]) ? $records[$i][$tfValue] : '';
										// Check if there is an array with predefined values
										if (isset($templateSources[$tfValue])) {
											$templateFieldValue = !empty($templateSources[$tfValue][$templateFieldValue]) ? $templateSources[$tfValue][$templateFieldValue] : '';
										}
										$htmlCode = str_ireplace('{' . $tfValue . '}', $templateFieldValue, $htmlCode);
									}
								}
							}
							
							$fieldOutput .= $htmlCode;
							break;
						
						case 'html':
						case 'label':
						default:
							$fieldEncryption = (bool)self::keyAt('encryption.enabled', $val, false);
							if ($fieldEncryption) {
								$encryptAlgorithm = self::keyAt('encryption.encryptAlgorithm', $val, '');
								$encryptKey = self::keyAt('encryption.encryptKey', $val, '');
								if (empty($fieldValue)) {
									$fieldValue = '';
								} else {
									$fieldValue = CHash::decrypt($fieldValue, $encryptKey, $encryptAlgorithm);
								}
							}
							
							// Trigger
							$triggerKey = self::keyAt('trigger.trigger_key', $val);
							$triggerOperation = self::keyAt('trigger.trigger_operation', $val);
							$triggerValue = self::keyAt('trigger.trigger_value', $val);
							$successValue = self::keyAt('trigger.success_value', $val);
							$wrongValue = self::keyAt('trigger.wrong_value', $val);
							if (!empty($triggerKey)) {
								if (isset($records[$i][$triggerKey])) {
									switch ($triggerOperation) {
										case '>':
											$fieldValue = ($records[$i][$triggerKey] > $triggerValue) ? $successValue : $wrongValue;
											break;
										case '>=':
											$fieldValue = ($records[$i][$triggerKey] >= $triggerValue) ? $successValue : $wrongValue;
											break;
										case '<':
											$fieldValue = ($records[$i][$triggerKey] < $triggerValue) ? $successValue : $wrongValue;
											break;
										case '<=':
											$fieldValue = ($records[$i][$triggerKey] <= $triggerValue) ? $successValue : $wrongValue;
											break;
										case '!=':
											$fieldValue = ($records[$i][$triggerKey] != $triggerValue) ? $successValue : $wrongValue;
											break;
										default:
											$fieldValue = ($records[$i][$triggerKey] == $triggerValue) ? $successValue : $wrongValue;
											break;
									}
								}
							}
							
							// Call of closure function on item creating event
							$callbackClass = self::keyAt('callback.class', $val);
							$callbackFunction = self::keyAt('callback.function', $val);
							$callbackParams = self::keyAt('callback.params', $val, array());
							if (!empty($callbackFunction)) {
								if (CClass::isExists($callbackClass)) {
									// Call a class method
									$callbackObject = new $callbackClass();
									if (CClass::isMethodExists($callbackObject, $callbackFunction) && CClass::isMethodCallable($callbackObject, $callbackFunction)) {
										$fieldValue = $callbackObject::$callbackFunction($records[$i], $callbackParams);
										/// DEPRECATED 01.12.2018 - for PHP_VERSION | phpversion() < 5.3.0
										/// $fieldValue = call_user_func(array($callbackObject, $callbackFunction), $records[$i], $callbackParams);
									}
								} elseif (is_callable($callbackFunction)) {
									// Call a function
									$fieldValue = $callbackFunction($records[$i], $callbackParams);
									/// DEPRECATED 01.12.2018 - for PHP_VERSION | phpversion() < 5.3.0
									/// $fieldValue = call_user_func($callbackFunction, $records[$i], $callbackParams);
								}
							}
							
							$dataValue = self::keyAt('data', $val, '');
							if (!empty($dataValue)) $fieldValue = $dataValue;
							if (is_array($definedValues) && isset($definedValues[$fieldValue])) {
								$fieldValue = $definedValues[$fieldValue];
							} elseif ($format != '' && $format != 'american' && $format != 'european') {
								$fieldValue = date($format, strtotime($fieldValue));
							} else {
								$stripTags = (bool)self::keyAt('stripTags', $val, false);
								if ($stripTags) {
									$fieldValue = strip_tags($fieldValue);
								}
								$case = self::keyAt('case', $val, '');
								if ($case == 'upper') {
									$fieldValue = strtoupper($fieldValue);
								} elseif ($case == 'lower') {
									$fieldValue = strtolower($fieldValue);
								} elseif ($case == 'camel') {
									$fieldValue = ucwords($fieldValue);
								} elseif ($case == 'humanize') {
									$fieldValue = CString::humanize($fieldValue);
								}
								
								$maxLength = (int)self::keyAt('maxLength', $val, '');
								$showTooltip = (int)self::keyAt('showTooltip', $val, true);
								if (!empty($maxLength)) {
									$fieldValue = str_replace(array("\r\n", "\r", "\n", '&nbsp;', '<br>', '<br />'), array(' '), $fieldValue);
									$fieldValue = $type == 'html' ? $fieldValue : CHtml::encode($fieldValue);
									$fieldValue = CHtml::tag('span', array('title' => ($showTooltip ? $fieldValue : '')), CString::substr($fieldValue, $maxLength, '', true));
								} else {
									$fieldValue = $type == 'html' ? $fieldValue : CHtml::encode($fieldValue);
								}
							}
							
							$changeOrderLink = '';
							if ($changeOrder) {
								$changeOrderUrl = $baseUrl . $actionPath;
								$changeOrderUrl .= !empty($sortUrl) ? '?' . $sortUrl : '';
								$changeOrderUrl .= !empty($filterUrl) ? (empty($sortUrl) ? '?' : '&') . $filterUrl : '';
								$changeOrderUrl .= (empty($sortUrl) && empty($filterUrl) ? '?' : '&');
								
								if ($i > 0) {
									$changeOrderLink = ' <a style="text-decoration:none;" href="' . $changeOrderUrl . 'act=replace&replace_field=' . $key . '&replace_ids=' . $id . '-' . $prevId . (!empty($page) ? '&page=' . $page : '') . '" title="' . A::te('core', 'Move Up') . '" onclick="javascript:void()">&#9650;</a>';
								}
								if ($i < $totalPageRecords - 1) {
									$changeOrderLink .= '<a style="text-decoration:none;" href="' . $changeOrderUrl . 'act=replace&replace_field=' . $key . '&replace_ids=' . $id . '-' . $nextId . (!empty($page) ? '&page=' . $page : '') . '" title="' . A::te('core', 'Move Down') . '" onclick="javascript:void()">&#9660;</a>';
								}
							}
							
							$fieldOutput .= $fieldValue . $changeOrderLink;
							break;
					}
					
					// Calculate aggregate data
					if (!empty($aggregateFunction)) {
						switch ($aggregateFunction) {
							case 'avg':
								if (!isset($aggregateRow[$key])) {
									$aggregateRow[$key] = array('function' => A::t('core', 'AVG'), 'result' => $fieldValueOriginal, 'sum' => $fieldValueOriginal, 'count' => 1);
								} else {
									$aggregateRow[$key]['sum'] += $fieldValueOriginal;
									$aggregateRow[$key]['count']++;
									$aggregateRow[$key]['result'] = $aggregateRow[$key]['sum'] / $aggregateRow[$key]['count'];
								}
								break;
							case 'sum':
							default:
								if (!isset($aggregateRow[$key])) {
									$aggregateRow[$key] = array('function' => A::t('core', 'SUM'), 'result' => $fieldValueOriginal);
								} else {
									$aggregateRow[$key]['result'] += $fieldValueOriginal;
								}
								break;
						}
					}
					
					$output .= CHtml::openTag('td', array('class' => $class, 'style' => $style));
					$output .= $prependCode;
					$output .= $fieldOutput;
					$output .= $appendCode;
					$output .= CHtml::closeTag('td') . self::NL;
				}
				
				if ($activeActions > 0) {
					$output .= CHtml::openTag('td', array('class' => 'actions')) . self::NL;
					foreach ($actions as $aKey => $aVal) {
						$htmlOptions = (array)self::keyAt('htmlOptions', $aVal);
						$htmlOptions['class'] = (isset($htmlOptions['class']) ? $htmlOptions['class'] . ' ' : '') . 'tooltip-link gridview-delete-link';
						$htmlOptions['data-id'] = $id;
						if (isset($aVal['title'])) {
							$htmlOptions['title'] = $aVal['title'];
						}
						if ((bool)self::keyAt('onDeleteAlert', $aVal) === true) {
							$htmlOptions['onclick'] = 'return onDeleteRecord(this);';
						}
						$imagePath = self::keyAt('imagePath', $aVal);
						$linkUrl = str_ireplace('{id}', $id, self::keyAt('link', $aVal, '#'));
						// Add additional parameters if allowed
						if ($linkUrl != '#') {
							$separateSymbol = preg_match('/\?/', $linkUrl) ? '&' : '?';
							$linkUrl .= self::_additionalParams($passParameters, $linkType, $separateSymbol);
							$separateSymbol = preg_match('/\?/', $linkUrl) ? '&' : '?';
							$linkUrl .= self::_customParams($customParameters, $linkType, $separateSymbol);
						}
						
						$linkLabel = (!empty($imagePath) ? '<img src="' . $imagePath . '" alt="' . $aKey . '" />' : $aKey);
						
						$output .= CHtml::link($linkLabel, $linkUrl, $htmlOptions) . self::NL;
					}
					$output .= CHtml::closeTag('td') . self::NL;
				}
				$output .= CHtml::closeTag('tr') . self::NL;
			}
			$output .= CHtml::closeTag('tbody') . self::NL;
			
			// ----------------------------------------------
			// Draw <TFOOT> aggregate row
			// ----------------------------------------------
			if (!empty($aggregateRow) && is_array($aggregateRow)) {
				$output .= CHtml::openTag('tfoot') . self::NL;
				$output .= CHtml::openTag('tr', array('id' => 'tr' . $modelName . '_' . self::$_rowCount++)) . self::NL;
				foreach ($fields as $key => $val) {
					$title = '';
					$headerClass = self::keyAt('headerClass', $val, '');
					$prependCode = self::keyAt('prependCode', $val, '');
					$appendCode = self::keyAt('appendCode', $val, '');
					
					$format = self::keyAt('format', $val, '');
					$decimalPoints = (int)self::keyAt('decimalPoints', $val, 0);
					$widthAttr = self::keyAt('width', $val);
					$alignAttr = self::keyAt('align', $val);
					
					// Prepare style attributes
					$width = (!empty($widthAttr) && CValidator::isHtmlSize($widthAttr)) ? 'width:' . $widthAttr . ';' : '';
					$align = (!empty($alignAttr) && CValidator::isAlignment($alignAttr)) ? 'text-align:' . $alignAttr . ';' : '';
					$style = $width . $align;
					
					if (isset($aggregateRow[$key])) {
						$aggregateValue = $aggregateRow[$key]['result'];
						$aggregateValue = number_format((float)$aggregateValue, $decimalPoints);
						if ($format === 'european') {
							$aggregateValue = str_replace('.', '#', $aggregateValue);
							$aggregateValue = str_replace(',', '.', $aggregateValue);
							$aggregateValue = str_replace('#', ',', $aggregateValue);
						}
						$title .= $aggregateRow[$key]['function'] . ': ' . $prependCode . $aggregateValue . $appendCode;
					}
					
					$output .= CHtml::tag('td', array('class' => $headerClass, 'style' => $style), $title) . self::NL;
				}
				if ($activeActions > 0) {
					$output .= CHtml::tag('td', array(), '') . self::NL;
				}
				$output .= CHtml::closeTag('tr') . self::NL;;
				$output .= CHtml::closeTag('tfoot') . self::NL;
			}
			
			$output .= CHtml::closeTag('table') . self::NL;
			// ----------------------------------------------
			// Draw TABLE wrapper
			// ----------------------------------------------
			if (!empty($gridWrapper['tag'])) {
				$output .= CHtml::closeTag($gridWrapper['tag']) . self::NL;
			}
			
			// Register script if onDeleteAlert is true
			if ($onDeleteRecord) {
				A::app()->getClientScript()->registerScript(
					'delete-record',
					'function onDeleteRecord(el){return confirm("ID: " + jQuery(el).data("id") + "\n' . A::te('core', 'Are you sure you want to delete this record?') . '");}',
					2
				);
			}
			
			// Draw pagination
			if ($pagination) {
				$paginationUrl = $actionPath;
				$paginationUrl .= !empty($sortUrl) ? '?' . $sortUrl : '';
				$paginationUrl .= !empty($filterUrl) ? (empty($sortUrl) ? '?' : '&') . $filterUrl : '';
				$output .= CWidget::create('CPagination', array(
					'actionPath' => $paginationUrl,
					'currentPage' => $currentPage,
					'pageSize' => $pageSize,
					'totalRecords' => $totalRecords,
					'linkType' => 0,
					'paginationType' => 'fullNumbers',
				));
			}
		}
		
		if ($return) return $output;
		else echo $output;
	}
	
	/**
	 * Prepare additional parameters that will be passed
	 * @param bool $allow
	 * @param int $linkType
	 * @param char $separateSymbol
	 * @return string
	 */
	private static function _additionalParams($allow = false, $linkType = 0, $separateSymbol = '&')
	{
		$output = '';
		
		if ($allow) {
			$page = A::app()->getRequest()->getQuery('page', 'integer', 1);
			$sortBy = A::app()->getRequest()->getQuery('sort_by', 'string');
			$sortDir = A::app()->getRequest()->getQuery('sort_dir', 'string');
			
			if ($sortBy) {
				$output .= ($linkType == 1) ? '/sort_by/' . $sortBy : (!empty($output) ? '&' : $separateSymbol) . 'sort_by=' . $sortBy;
			}
			if ($sortDir) {
				$output .= ($linkType == 1) ? '/sort_dir/' . $sortDir : (!empty($output) ? '&' : $separateSymbol) . 'sort_dir=' . $sortDir;
			}
			if ($page) {
				$output .= ($linkType == 1) ? '/page/' . $page : (!empty($output) ? '&' : $separateSymbol) . 'page=' . $page;
			}
		}
		
		return $output;
	}
	
	/**
	 * Prepare custom parameters that will be passed
	 * @param array $params
	 * @param int $linkType
	 * @param string $separateSymbol
	 * @return string
	 */
	private static function _customParams($params, $linkType = 0, $separateSymbol = '&')
	{
		$output = '';
		
		if (is_array($params)) {
			foreach ($params as $key => $val) {
				$output .= ($linkType == 1) ? '/' . $key . '/' . $val : (!empty($output) ? '&' : $separateSymbol) . $key . '=' . $val;
			}
		}
		
		return $output;
	}
	
	/**
	 * Returns field attribute
	 * @param array $fields
	 * @param string $field
	 * @param string $attr
	 * @param array $allowedValues
	 * @return mixed
	 */
	private static function _getFieldParam($fields = array(), $field = '', $attr = '', $allowedValues = array())
	{
		$paramValue = '';
		
		if (!empty($fields) && is_array($fields)) {
			if (!empty($allowedValues) && is_array($allowedValues)) {
				$paramValue = isset($fields[$field][$attr]) && in_array($fields[$field][$attr], $allowedValues) ? $fields[$field][$attr] : '';
			} else {
				$paramValue = isset($fields[$field][$attr]) ? $fields[$field][$attr] : '';
			}
		}
		
		return $paramValue;
	}
	
	/**
	 * Returns all records from defined SELECT query
	 * @param object $objModel
	 * @param array $params
	 * @return array
	 */
	private static function _getAllRecords($objModel, $params = array())
	{
		$model = self::params('model', '');
		$getAllRecords = self::params('getAllRecords', '');
		$records = array();
		
		if (!empty($getAllRecords)) {
			if (CClass::isMethodExists($objModel, $getAllRecords)) {
				$records = $objModel->$getAllRecords($params);
			} else {
				CDebug::addMessage('errors', 'wrong-method', A::t('core', 'Check if this function exists and usable: {function}', array('{function}' => $model . '->' . $getAllRecords)));
			}
		} else {
			$records = $objModel->findAll($params);
		}
		
		return $records;
	}
	
	/**
	 * Counts numbner of all records from defined SELECT query
	 * @param object $objModel
	 * @param array $params
	 * @return int
	 */
	private static function _countAllRecords($objModel, $params = array())
	{
		$model = self::params('model', '');
		$getAllRecords = self::params('getAllRecords', '');
		$countAllRecords = self::params('countAllRecords', '');
		$totalRecords = 0;
		
		if (!empty($countAllRecords)) {
			if (CClass::isMethodExists($objModel, $countAllRecords)) {
				$totalRecords = $objModel->$countAllRecords($params);
			} else {
				CDebug::addMessage('errors', 'wrong-method', A::t('core', 'Check if this function exists and usable: {function}', array('{function}' => $model . '->' . $countAllRecords)));
			}
		} elseif (!empty($getAllRecords)) {
			CDebug::addMessage('errors', 'wrong-method', A::t('core', 'The {method} method expects to be passed an array containing data.', array('{method}' => $model . '->???')));
		} else {
			$totalRecords = $objModel->count($params);
		}
		
		return $totalRecords;
	}
}

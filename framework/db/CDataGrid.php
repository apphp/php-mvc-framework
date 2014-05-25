<?php
/**
 * CDataGrid base class for classes that represent relational data.
 * It implements the datagrid design pattern.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/ 
 *
 * Usage:
 * public function __construct()
 * {
 *    parent::__construct();
 *  
 *    $this->_actionUrl = 'controller/action';
 *    $this->_defaultOrder = 'field_name ASC';
 *  			
 *    $this->_viewModeColumns = array(
 *       'field_1' => array('title'=>'Field 1', 'type'=>'label', 'align'=>'left', 'width'=>'', 'maxlength'=>''),						
 *    );
 * }
 *
 *
 * PUBLIC:					PROTECTED:				    PRIVATE:				STATIC:
 * -------		            ----------          	    --------                -------
 * __construct				_getFieldByType              
 * drawViewMode             _getAll                      
 *                                                      
 *
 *	
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * dgrid
 * 
 **/

abstract class CDataGrid extends CModel
{	
	/** @var object */    
    private static $_instance;
	/** @var Database */
	protected $_db;	
	/**	@var boolean */
	protected $_error;
	/**	@var string */
	protected $_errorMessage;

    /* class name => dgrids */
    private static $_dgrids = array();			

    /**	@var string */
    protected $_table = '';
    /**	@var string */
    protected $_tableTranslation = '';
	/**	@var */ 
	protected $_viewModeColumns = array();

	/**	@var string */
	protected $_actionUrl = '';
	
    /**	@var boolean */
    protected $_isSortingAllowed = true;
	/**	@var string */
	protected $_defaultOrder = '';
	/**	@var string */
	private $_orderClause = '';
	

	/**
	 * Class constructor
	 * @param array $params
	 */
	public function __construct() 
	{
		$this->_db = CDatabase::init();
        $this->_error = CDatabase::getError();
        $this->_errorMessage = CDatabase::getErrorMessage();
	}
	
	/**
	 * Initializes the database class
	 * @param array $params
	 */
	public static function init($params = array())
	{
		if(self::$_instance == null) self::$_instance = new self($params);
        return self::$_instance;    		
	}
    

	/**
	 * Returns the static model of the specified DG class
	 * @param string $className
	 * 
	 * EVERY derived DG class must override this method in following way,
	 * <pre>
	 * public static function dgrid($className = __CLASS__)
	 * {
	 *     return parent::dgrid($className);
	 * }
	 * </pre>
	 */
	public static function dgrid($className = __CLASS__)
	{        
		if(isset(self::$_dgrids[$className])){
			return self::$_dgrids[$className];
		}else{
			return self::$_dgrids[$className] = new $className(null);
		}        
    }


	
	/***********************************************************************
	 *
	 *	Draw View Mode
	 *	
	 ***********************************************************************/
	public function drawViewMode()
	{        
		$output = '';
		$cRequest = A::app()->getRequest();
		
//		$this->IncludeJSFunctions();
//      $this->BeforeViewRecords();
//		
		$sortingFields  = $cRequest->getPost('sorting_fields');
		$sortingTypes   = $cRequest->getPost('sorting_types');
//		$page 			 = self::GetParameter('page');
//		$total_pages	 = $page;
//
//		$rid 		     = self::GetParameter('rid');
//      $action          = self::GetParameter('action');
//		$operation 		 = self::GetParameter('operation');
//		$operation_type  = self::GetParameter('operation_type');
//		$operation_field = self::GetParameter('operation_field');
//		
//		$search_status   = self::GetParameter('search_status');
//		
//		$concat_sign 	 = (preg_match('/\?/', $this->_actionUrl) ? '&' : '?');		
		$colspan 		 = 1;
//		$start_row 		 = 0;
		$totalRecords 	 = 0;
//		$sort_by         = '';
//		$export_content  = array();
//      $calendar_fields = array();
		$nl = "\n";


		// prepare sorting data
		//----------------------------------------------------------------------
		if($this->_isSortingAllowed){
//			if($operation == 'sorting'){
//				if($sortingFields != ''){
//                    if($action == 'delete'){
//                        // $sortingTypes
//                    }else{
//                        if(strtolower($sortingTypes) == 'asc') $sortingTypes = 'DESC';
//                        else $sortingTypes = 'ASC';
//                    }
//					$sort_type = isset($this->arrViewModeFields[$sortingFields]['sort_type']) ? $this->arrViewModeFields[$sortingFields]['sort_type'] : 'string';
//					$sort_by = isset($this->arrViewModeFields[$sortingFields]['sort_by']) ? $this->arrViewModeFields[$sortingFields]['sort_by'] : $sortingFields;
//					if($sort_type == 'numeric'){
//						$this->ORDER_CLAUSE = ' ORDER BY ABS('.$sort_by.') '.$sortingTypes.' ';	
//					}else{
//						$this->ORDER_CLAUSE = ' ORDER BY '.$sort_by.' '.$sortingTypes.' ';	
//					}					
//				}else{
//					$sortingTypes = 'ASC';
//				}
//			}else{
//				if($sortingFields != '' && $sortingTypes != ''){
//					$this->ORDER_CLAUSE = ' ORDER BY '.$sortingFields.' '.$sortingTypes.' ';	
//				}
//			}
			$this->_orderClause = $this->_defaultOrder;
		}
//		
//		// prepare filtering data
//		//----------------------------------------------------------------------
//		if($this->isFilteringAllowed){
//			if($search_status == 'active'){
//				if($this->WHERE_CLAUSE == '') $this->WHERE_CLAUSE .= ' WHERE 1=1 ';
//				$count = 0;
//				foreach($this->arrFilteringFields as $key => $val){
//                    $custom_handler = isset($val['custom_handler']) ? $val['custom_handler'] : false;
//					if(!$custom_handler && self::GetParameter('filter_by_'.$val['table'].$val['field'], false) !== ''){
//						$sign = '='; $sign_start = ''; $sign_end = '';
//						if($val['sign'] == '='){
//							$sign = '=';
//						}else if($val['sign'] == '>='){
//							$sign = '>=';
//						}else if($val['sign'] == '<='){
//							$sign = '<=';
//						}else if($val['sign'] == 'like%'){
//							$sign = 'LIKE';
//							$sign_end = '%';
//						}else if($val['sign'] == '%like'){
//							$sign = 'LIKE';
//							$sign_start = '%';
//						}else if($val['sign'] == '%like%'){
//							$sign = 'LIKE';
//							$sign_start = '%';
//							$sign_end = '%';
//						}
//						$key_value = self::GetParameter('filter_by_'.$val['table'].$val['field'], false);
//						if(isset($val['table']) && $val['table'] != '') $field_name = $val['table'].'.'.$val['field'];
//						else $field_name = $val['field'];
//                        
//                        $date_format = isset($val['date_format']) ? $val['date_format'] : '';
//                        $type = isset($val['type']) ? $val['type'] : '';
//                        if($type == 'calendar') $key_value = $this->PrepareDateTime($key_value, $date_format);
//                        if($this->IsSecureField($key, $val)) $field_name = $this->UncryptValue($field_name, $val, false);
//                        
//						$this->WHERE_CLAUSE .= ' AND '.$field_name.' '.$sign.' \''.$sign_start.CString::quote($key_value).$sign_end.'\' ';                        
//					}
//				}
//			}			
//		}		
//
//		// prepare paging data
//		//----------------------------------------------------------------------
//		if($this->isPagingAllowed){
//			if(!is_numeric($page) || (int)$page <= 0) $page = 1;
//            if($this->debug) $start_time = $this->_getFormattedMicrotime();
//
//            // Way #1
//            // set sql_mode to empty if you have Mixing of GROUP columns SQL issue - in connection.php file
//            /// database_void_query('SET sql_mode = ""');            
//			$sql = preg_replace('/SELECT\b/i', 'SELECT COUNT(*) as dg_total_records, ', $this->VIEW_MODE_SQL, 1).' '.$this->WHERE_CLAUSE.' LIMIT 0, 1';
//            $result = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
//			$totalRecords = isset($result[0]['dg_total_records']) ? (int)$result[0]['dg_total_records'] : '1';
//            // Way #2
//            // $sql = $this->VIEW_MODE_SQL.' '.$this->WHERE_CLAUSE;
//            // $result = database_query($sql, DATA_AND_ROWS, ALL_ROWS);
//            // $totalRecords = isset($result[1]) ? (int)$result[1] : '1';
//
//            if($this->debug) $finish_time = $this->_getFormattedMicrotime();
//			if($this->debug){
//				if(!mysql_error()){ 
//                    $this->arrSQLs['total_records_sql'] = '<i>Total Records</i> | T: '.round((float)$finish_time - (float)$start_time, 4).' sec. <br>'.$sql;
//				}else{
//					$this->arrErrors['total_records_sql'] = $sql.'<br>'.mysql_error();		
//				}
//			}
//			if($this->pageSize == 0) $this->pageSize = '10';
//			$total_pages = (int)($totalRecords / $this->pageSize);
//			// when you back from other languages where more pages than on current
//			if($page > ($total_pages+1)) $page = 1; 
//			if(($totalRecords % $this->pageSize) != 0) $total_pages++;
//			$start_row = ($page - 1) * $this->pageSize;				
//		}
//		
//		// check if there is move operation and perform it
//		//----------------------------------------------------------------------
//		if($operation == 'move'){			
//			// block if this is a demo mode
//			if(strtolower(SITE_MODE) == 'demo'){
//				$this->error = _OPERATION_BLOCKED;
//			}else{
//				$operation_field_p = explode('#', $operation_field);
//				$operation_field_p0 = explode('-', $operation_field_p[0]);
//				$operation_field_p1 = explode('-', $operation_field_p[2]);
//				$of_first 	= isset($operation_field_p0[0]) ? $operation_field_p0[0] : '';
//				$of_second 	= isset($operation_field_p0[1]) ? $operation_field_p0[1] : '';
//				$of_name 	= $operation_field_p[1];
//				$of_first_value  = isset($operation_field_p1[0]) ? $operation_field_p1[0] : '';
//				$of_second_value = isset($operation_field_p1[1]) ? $operation_field_p1[1] : '';
//				
//				if(($of_first_value != '') && ($of_second_value != '')){
//					$sql = 'UPDATE '.$this->_table.' SET '.$of_name.' = \''.$of_second_value.'\' WHERE '.$this->primaryKey.' = \''.$of_first.'\'';
//					database_void_query($sql);
//					if($this->debug) $this->arrSQLs['select_move_1'] = $sql;
//					$sql = 'UPDATE '.$this->_table.' SET '.$of_name.' = \''.$of_first_value.'\' WHERE '.$this->primaryKey.' = \''.$of_second.'\'';
//					database_void_query($sql);
//					if($this->debug) $this->arrSQLs['select_move_2'] = $sql;					
//				}				
//			}
//		}		
//		
		$arrRecords = $this->_getAll(
			array('order' => $this->_orderClause)
		);//$this->GetAll($this->ORDER_CLAUSE, 'LIMIT '.$start_row.', '.(int)$this->pageSize);
		$totalRecords = (is_array($arrRecords)) ? count($arrRecords) : 0;
//		if(!$this->isPagingAllowed){
//			$totalRecords = $arrRecords[1];
//		}		
//	
		$output .= CHtml::openForm($this->_actionUrl, 'post', array('name'=>'frmDataGrid_'.$this->_table));
//		draw_hidden_field('dg_prefix', $this->uPrefix); echo $nl;
//		draw_hidden_field('dg_action', 'view'); echo $nl;
//		draw_hidden_field('dg_rid', ''); echo $nl;
		$output .= CHtml::hiddenField('dg_sorting_fields', $sortingFields).$nl;
		$output .= CHtml::hiddenField('dg_sorting_types', $sortingTypes).$nl;
//		draw_hidden_field('dg_page', $page); echo $nl;
//		draw_hidden_field('dg_operation', $operation); echo $nl;
//		draw_hidden_field('dg_operation_type', $operation_type); echo $nl;
//		draw_hidden_field('dg_operation_field', $operation_field); echo $nl;
//		draw_hidden_field('dg_search_status', $search_status); echo $nl;
//		draw_hidden_field('dg_language_id', $this->languageId); echo $nl;
//		draw_hidden_field('dg_operation_code', self::GetRandomString(20)); echo $nl;
//		draw_token_field(); echo $nl;
//
//		if($this->actions['add'] || $this->allowLanguages || $this->allowRefresh || $this->isExportingAllowed){
//			echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="mgrid_table">
//				<tr>';
//					echo '<td align="'.Application::Get('defined_left').'" valign="middle">';
//					if($this->actions['add']) echo '<input class="mgrid_button" type="button" name="btnAddNew" value="'._ADD_NEW.'" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'add\');">&nbsp;&nbsp;&nbsp;';
//					if($this->operationLinks != '')  echo $this->operationLinks;
//					echo '</td>';
//					
//					echo '<td align="'.Application::Get('defined_right').'" valign="middle">';
//					if($this->isExportingAllowed){
//                        if(strtolower(SITE_MODE) == 'demo' || !$arrRecords[1]){
//                            echo '<span class="gray">[ '._EXPORT.' ]</span> &nbsp;';
//                        }else{
//                            if($operation == 'switch_to_export'){
//                                echo '[ <a href="javascript:void(\'export|cancel\');" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', null, null, null, null, \'switch_to_normal\');" title="'._SWITCH_TO_NORMAL.'">'._BUTTON_CANCEL.'</a> | '._DOWNLOAD.' - <a href="javascript:void(\'csv\');" onclick="javascript:appGoToPage(\'index.php?admin=export&file=export.csv\')"><img src="images/microgrid_icons/csv.gif" alt="'._DOWNLOAD.' CSV"></a> ] &nbsp;';
//                            }else{
//                                echo '<a href="javascript:void(\'export\');" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', null, null, null, null, \'switch_to_export\');" title="'._SWITCH_TO_EXPORT.'">[ '._EXPORT.' ]</a> &nbsp;';
//                            }                            
//                        }
//					}
//					if($this->allowRefresh)	echo '<a href="javascript:void(\'refresh\');" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\');" title="'._REFRESH.'"><img src="images/microgrid_icons/refresh.gif" alt="'._REFRESH.'"></a>';
//					echo '</td>';						
//					
//					if($this->allowLanguages){
//						echo '<td align="'.Application::Get('defined_right').'" width="80px">';
//						(($this->allowLanguages) ? draw_languages_box('dg_language_id', $arrLanguages[0], 'abbreviation', 'lang_name', $this->languageId, '', 'onchange="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', null, null, null, null, \'change_language\', this.value, \'language_id\');"') : '');
//						echo '</td>';
//					}
//					echo '
//				</tr>
//				<tr><td nowrap height="10px"></td></tr>
//			</table>';
//		}
//
//		if($this->isFilteringAllowed){
//			echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="mgrid_table">
//				<tr>
//					<td align="'.Application::Get('defined_left').'">';
//						echo '<b>'._FILTER_BY.'</b>: &nbsp;&nbsp;&nbsp;';
//						foreach($this->arrFilteringFields as $key => $val){
//							if(!$this->IsVisible($val)) continue;
//							$filter_field_value = ($search_status == 'active') ? self::GetParameter('filter_by_'.$val['table'].$val['field'], false) : '';
//							if($val['type'] == 'text'){
//								echo $key.':&nbsp;<input type="text" class="mgrid_text" name="filter_by_'.$val['table'].$val['field'].'" value="'.$this->GetDataDecoded($filter_field_value).'" style="width:'.$val['width'].'" maxlength="125">&nbsp;&nbsp;&nbsp;';
//							}else if($val['type'] == 'dropdownlist'){
//								if(is_array($val['source'])){
//									echo $key.':&nbsp;<select class="mgrid_text" name="filter_by_'.$val['table'].$val['field'].'" style="width:'.$val['width'].'">';
//									echo '<option value="">-- '._SELECT.' --</option>';	
//									foreach($val['source'] as $key => $val){
//										echo '<option '.(($filter_field_value !== '' && $filter_field_value == $key) ? ' selected="selected"' : '').' value="'.$this->GetDataDecoded($key).'">'.$val.'</option>';	
//									}
//									echo '</select>&nbsp;&nbsp;&nbsp;';
//								}								
//                            }else if($val['type'] == 'calendar'){
//                                $date_format = isset($val['date_format']) ? $val['date_format'] : '';
//                                if($date_format == 'mm/dd/yyyy'){
//                                    $calendar_date_format = '%m-%d-%Y';
//									$placeholder_date_format = 'mm-dd-yyyy';
//                                }else if($date_format == 'dd/mm/yyyy'){                                   
//                                    $calendar_date_format = '%d-%m-%Y';
//									$placeholder_date_format = 'dd-mm-yyyy';
//                                }else{
//                                    $calendar_date_format = '%Y-%m-%d';
//									$placeholder_date_format = 'yyyy-dd-mm';
//                                }
//
//								echo $key.':&nbsp;<input type="text" id="filter_cal'.$val['field'].'" class="mgrid_text" name="filter_by_'.$val['table'].$val['field'].'" value="'.$this->GetDataDecoded($filter_field_value).'" style="width:'.$val['width'].'" maxlength="19" placeholder="'.$placeholder_date_format.'">&nbsp;';
//                                echo '<img id="filter_cal'.$val['field'].'_img" src="images/microgrid_icons/cal.gif" alt="" title="'._SET_TIME.'" style="cursor:pointer;">';
//                                echo '&nbsp;&nbsp;';
//                                $calendar_fields[] = array('field'=>'filter_cal'.$val['field'], 'format'=>$calendar_date_format);
//							}
//						}
//						if(count($this->arrFilteringFields) > 0){
//							echo '&nbsp;';
//							if($search_status == 'active') echo ' <input type="button" class="mgrid_button" name="btnReset" value="'._BUTTON_RESET.'" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \'\', \'\', \'\', \'\', \'reset_filtering\');">';
//							echo ' <input type="button" class="mgrid_button" name="btnSearch" value="'._SEARCH.'" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \'\', \'\', \'\', \'\', \'filtering\')">';
//						}
//			echo '	</td>
//				</tr>
//				<tr><td nowrap height="10px"></td></tr>
//			</table>';
//		}
//

		// draw rows
		if($totalRecords > 0){            
			$output .= '<table width="100%" class="dgrid-table">'.$nl;
			$output .= '<thead>'.$nl;
			$output .= '<tr>'.$nl;
				// draw column headers
				foreach($this->_viewModeColumns as $key => $val){
//					$width = isset($val['width']) ? ' width="'.$val['width'].'"': '';
//					if(isset($val['align']) && $val['align'] == 'left' && Application::Get('defined_left') == 'right'){
//						$align = ' align="right"';
//					}else if(isset($val['align']) && $val['align'] == 'right' && Application::Get('defined_right') == 'left'){
//						$align = ' align="left"';
//					}else if(isset($val['align'])){
//						$align = ' align="'.$val['align'].'"';
//					}else{
//						$align = '';	
//					}					
//					$visible = (isset($val['visible']) && $val['visible']!=='') ? $val['visible'] : true;
//					$sortable = (isset($val['sortable']) && $val['sortable']!=='') ? $val['sortable'] : true;
//					$th_class = ($key == $sort_by) ? ' class="th_sorted"' : '';
                    $title = isset($val['title']) ? $val['title'] : '';
//					if($visible){
						$output .= '<th>'; //'.$width.$align.$th_class.'
//							if($this->isSortingAllowed && $sortable){
//								$field_sorting = 'DESC';
//								$sort_icon = '';
//								if($key == $sortingFields){
//									if(strtolower($sortingTypes) == 'asc'){
//										$sort_icon = ' <img src="images/microgrid_icons/up.png" alt="" title="asc">';
//									}else if(strtolower($sortingTypes) == 'desc'){
//										$sort_icon = ' <img src="images/microgrid_icons/down.png" alt="" title="desc">';
//									}
//									$field_sorting = $sortingTypes;
//								}
//								echo '<a href="javascript:void(\'sort\');" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \'\', \''.$key.'\', \''.$field_sorting.'\', \''.$page.'\', \'sorting\')"><b>'.$title.'</b></a>'.$sort_icon;
//                                $this->DrawHeaderTooltip($val);
//							}else{
								$output .= '<label>'.$title.'</label>';
//							}
						$output .= '</th>'.$nl;
//						if($operation == 'switch_to_export' && strtolower(SITE_MODE) != 'demo') $export_content[0][] = $val['title'];
//					}					
				} // for
//			if($this->actions['details'] || $this->actions['edit'] || $this->actions['delete']){
//				echo '<th width="8%">'._ACTIONS.'</th>';
//			}
			$output .= '</tr>'.$nl;
			$output .= '</thead>'.$nl;
			//$output .= '<tr><td colspan="'.$colspan.'" height="3px" nowrap="nowrap">---------------</td></tr>';
			$output .= '<tbody>'.$nl;
			for($i=0; $i<$totalRecords; $i++){
				
                $output .= '<tr>'.$nl;
					foreach($this->_viewModeColumns as $key => $val){
//						if(isset($val['align']) && $val['align'] == 'left' && Application::Get('defined_left') == 'right'){
//							$align = ' align="right"';
//						}else if(isset($val['align']) && $val['align'] == 'right' && Application::Get('defined_right') == 'left'){
//							$align = ' align="left"';
//						}else if(isset($val['align'])){
//							$align = ' align="'.$val['align'].'"';
//						}else{
							$align = '';	
//						}					
						$wrap    = (isset($val['nowrap']) && $val['nowrap'] == 'nowrap') ? ' nowrap="'.$val['nowrap'].'"': ' wrap';
//						$visible = (isset($val['visible']) && $val['visible'] !== '') ? $val['visible'] : true;
//						$movable = (isset($val['movable']) && $val['movable'] !== '') ? $val['movable'] : false;
						if(isset($arrRecords[$i][$key])){
							$field_value = $this->_getFieldByType('view', $key, $val, $arrRecords[$i], false);
//                            if($this->isAggregateAllowed && isset($this->arrAggregateFields[$key])){
//                                $key_agreg = (isset($this->arrAggregateFields[$key]['aggregate_by']) && $this->arrAggregateFields[$key]['aggregate_by'] !== '') ? $this->arrAggregateFields[$key]['aggregate_by'] : $key;
//                                if(!isset($this->arrAggregateFieldsTemp[$key])){
//                                    $this->arrAggregateFieldsTemp[$key] = array('sum'=>$arrRecords[0][$i][$key_agreg], 'count'=>1);
//                                }else{
//                                    $this->arrAggregateFieldsTemp[$key]['sum'] += $arrRecords[0][$i][$key_agreg];
//                                    $this->arrAggregateFieldsTemp[$key]['count']++;
//                                }
//                            }
						}else{
//							if($this->debug) $this->arrWarnings['wrong_'.$key] = 'Field <b>'.$key.'</b>: wrong definition in View mode or at least one field has no value in SQL! Please check currefully your code.';
							$field_value = '';
						}
//						if($visible){
							$move_link = '';
//							if($movable){
//								$move_prev_id  = $arrRecords[0][$i]['id'].'-'.(isset($arrRecords[0][$i-1]['id']) ? $arrRecords[0][$i-1]['id'] : '').'#';
//								$move_prev_id .= $key.'#';
//								$move_prev_id .= $arrRecords[0][$i][$key].'-'.(isset($arrRecords[0][$i-1][$key]) ? $arrRecords[0][$i-1][$key] : '');							
//								$move_next_id  = $arrRecords[0][$i]['id'].'-'.(isset($arrRecords[0][$i+1]['id']) ? $arrRecords[0][$i+1]['id'] : '').'#';
//								$move_next_id .= $key.'#';
//								$move_next_id .= $arrRecords[0][$i][$key].'-'.(isset($arrRecords[0][$i+1][$key]) ? $arrRecords[0][$i+1][$key] : '');
//								if(isset($arrRecords[0][$i-1]['id'])){
//									$move_link .= ' <a href="javascript:void(\'move|up\');" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \''.$arrRecords[0][$i]['id'].'\', \'\', \'\', \'\', \'move\', \'up\', \''.$move_prev_id.'\')">';
//									$move_link .= ($this->actionIcons) ? '<img src="images/microgrid_icons/up.png" style="margin-bottom:2px" alt="" title="'._UP.'">' : _UP;
//									$move_link .= '</a>';										
//								}else{
//									$move_link .= ' <span style="width:11px;height:11px;"></span>';
//								}
//								if(isset($arrRecords[0][$i+1]['id'])){									
//									$move_link .= '<a href="javascript:void(\'move|down\');" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \''.$arrRecords[0][$i]['id'].'\', \'\', \'\', \'\', \'move\', \'down\', \''.$move_next_id.'\')">';
//									$move_link .= ($this->actionIcons) ? '<img src="images/microgrid_icons/down.png" style="margin-top:2px" alt="" title="'._DOWN.'">' : ((isset($arrRecords[0][$i-1]['id'])) ? '/' : '')._DOWN;
//									$move_link .= '</a>';
//								}else{
//									$move_link .= '<span style="width:11px;height:11px;"></span>';
//								}
//							}
							$output .= '<td'.$align.$wrap.'>'.$field_value.$move_link.'</td>'.$nl;
//							if($operation == 'switch_to_export' && strtolower(SITE_MODE) != 'demo') $export_content[$i+1][] = str_replace(',', '', strip_tags($field_value));
//						}
					} // for
//					if($this->actions['details'] || $this->actions['edit'] || $this->actions['delete']){
//						echo '<td align="center" nowrap="nowrap">';
//						if($this->actions['details']){
//							echo '<a href="javascript:void(\'details|'.$arrRecords[0][$i][$this->primaryKey].'\');" title="'._VIEW_WORD.'" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'details\', \''.$arrRecords[0][$i]['id'].'\')">'.(($this->actionIcons) ? '<img src="images/microgrid_icons/details.gif" title="'._VIEW_WORD.'" alt="" border="0" style="margin:0px; padding:0px;" height="16px">' : _VIEW_WORD).'</a>';
//						}				
//						if($this->actions['edit']){
//							if($this->actions['details']) echo '&nbsp;'.(($this->actionIcons) ? '&nbsp;' : '').draw_divider(false).'&nbsp'; 
//							echo '<a href="javascript:void(\'edit|'.$arrRecords[0][$i][$this->primaryKey].'\')" title="'._EDIT_WORD.'" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'edit\', \''.$arrRecords[0][$i]['id'].'\')">'.(($this->actionIcons) ? '<img src="images/microgrid_icons/edit.gif" title="'._EDIT_WORD.'" alt="" border="0" style="margin:0px;padding:0px;" height="16px">' : _EDIT_WORD).'</a>';
//						}
//						if($this->actions['delete']){
//							if($this->actions['edit'] || $this->actions['details']) echo '&nbsp;'.(($this->actionIcons) ? '&nbsp;' : '').draw_divider(false).'&nbsp'; 
//							echo '<a href="javascript:void(\'delete|'.$arrRecords[0][$i][$this->primaryKey].'\')" title="'._DELETE_WORD.'" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'delete\', \''.$arrRecords[0][$i]['id'].'\')">'.(($this->actionIcons) ? '<img src="images/microgrid_icons/delete.gif" title="'._DELETE_WORD.'" alt="" border="0" style="margin:0px;padding:0px;" height="16px">' : _DELETE_WORD).'</a>';
//						}
//						echo '&nbsp;</td>';
//					}
				$output .= '</tr>'.$nl;
			} // for
//            
//            // draw aggregate fields row
//            if($this->isAggregateAllowed){
//                echo '<tr><td colspan="'.$colspan.'" height="5px" nowrap="nowrap">'.draw_line('no_margin_line', IMAGE_DIRECTORY, false).'</td></tr>';
//                echo '<tr>';
//                foreach($this->arrViewModeFields as $key => $val){
//					$visible = (isset($val['visible']) && $val['visible'] !== '') ? $val['visible'] : true;
//                    if($visible){
//                        $ag_field_total = isset($this->arrAggregateFieldsTemp[$key]) ? $this->arrAggregateFieldsTemp[$key]['sum'] : 0;
//                        $ag_field_count = isset($this->arrAggregateFieldsTemp[$key]) ? $this->arrAggregateFieldsTemp[$key]['count'] : 0;
//                        $ag_field_function = strtoupper(isset($this->arrAggregateFields[$key]['function']) ? $this->arrAggregateFields[$key]['function'] : '');
//						$ag_field_align = strtoupper(isset($this->arrAggregateFields[$key]['align']) ? $this->arrAggregateFields[$key]['align'] : 'center');
//                        $ag_decimal_place = isset($this->arrAggregateFields[$key]['decimal_place']) ? (int)$this->arrAggregateFields[$key]['decimal_place'] : 2;
//                        $ag_field_value = '';
//                        if($ag_field_function == 'SUM'){
//                            $ag_field_value = ($ag_field_count != 0) ? number_format($ag_field_total, $ag_decimal_place) : '';    
//                        }else if($ag_field_function == 'AVG'){
//                            $ag_field_value = ($ag_field_count != 0) ? number_format($ag_field_total / $ag_field_count, $ag_decimal_place) : '';    
//                        }                        
//                        echo '<td align="'.$ag_field_align.'">'.(($ag_field_function != '') ? $ag_field_function.'=' : '').$ag_field_value.'</td>';
//                    }                    
//                }
//                echo '</tr>';
//                echo '<tr><td colspan="'.$colspan.'" height="5px" nowrap="nowrap">'.draw_line('no_margin_line', IMAGE_DIRECTORY, false).'</td></tr>';
//            }else{
//                echo '<tr><td colspan="'.$colspan.'" height="15px" nowrap="nowrap">'.draw_line('no_margin_line', IMAGE_DIRECTORY, false).'</td></tr>';                
//            }
			$output .= '</tbody>'.$nl;
			$output .= '</table>'.$nl;
//			
//			echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="mgrid_table">';
//			echo '<tr valign="top">';
//			echo '<td>';
//				if($this->isPagingAllowed){
//					echo '<b>'._PAGES.':</b> ';
//					$prev_dots = $post_dots = false;
//					for($i = 1; $i <= $total_pages; $i++){
//						if($i <= 15){
//							echo '<a class="paging_link" href="javascript:void(\'paging\')" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \'\', \'\', \'\', \''.$i.'\', \'\')">'.(($i == $page) ? '<b>['.$i.']</b>' : $i).'</a> ';
//						}else if($i > $total_pages - 15){
//							echo '<a class="paging_link" href="javascript:void(\'paging\')" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \'\', \'\', \'\', \''.$i.'\', \'\')">'.(($i == $page) ? '<b>['.$i.']</b>' : $i).'</a> ';
//						}else{
//							if($i < $page - 5){
//								if(!$prev_dots) { echo '... '; $prev_dots = true; }
//							}else if($i > $page + 5){
//								if(!$post_dots) { echo '... '; $post_dots = true; }
//							}else{
//								echo '<a class="paging_link" href="javascript:void(\'paging\')" onclick="javascript:__mgDoPostBack(\''.$this->_table.'\', \'view\', \'\', \'\', \'\', \''.$i.'\', \'\')">'.(($i == $page) ? '<b>['.$i.']</b>' : $i).'</a> ';								
//							}							
//						}
//					}				
//				}			
//			echo '</td>';
//			echo '<td align="'.Application::Get('defined_right').'">';
//					$row_from = ($start_row + 1);
//					$row_to   = ((($start_row + $this->pageSize) < $totalRecords) ? ($start_row + $this->pageSize) : $totalRecords);						
//					echo '<b>'._TOTAL.'</b>: '.(($row_from < $row_to) ? $row_from.' - '.$row_to : $row_from).' / '.$totalRecords;
//			echo '</td>';
//			echo '</tr>';
//			$output .= '</table>';
//			
//			// prepare export file
//			//----------------------------------------------------------------------
//			if($operation == 'switch_to_export'){
//                if(strtolower(SITE_MODE) == 'demo'){
//                    $this->error = _OPERATION_BLOCKED;
//                }else{
//                    $export_content_count = count($export_content);
//                    $fe = @fopen('tmp/export/export.csv', 'w+');
//                    @fwrite($fe, "\xEF\xBB\xBF");
//                    for($i=0; $i<$export_content_count; $i++){
//                        @fputcsv($fe, $export_content[$i]);
//                    }
//                    @fclose($fe);                                        
//                }
//			}			
		}else{
			// for filtering 
			//$output .= CWidget::create('CMessage', array('warning', A::t('core', 'No records found or incorrect parameters passed')));
			$output .= CWidget::create('CMessage', array('warning', A::t('core', 'No records found!'), array('return'=>true)));
		}
		
		$output .= CHtml::closeForm().$nl;
        
//        $this->CalendarSetupFields($calendar_fields);
//		
//		$this->AfterViewRecords();
//		
//        $this->DrawVersionInfo();
//		$this->DrawRunningTime();
//		$this->DrawErrors();
//		$this->DrawWarnings();
//		$this->DrawSQLs();	
//		$this->DrawPostInfo();

		//$output = '<table>
		//	<thead>
		//	<tr>
		//	<th style="width:40px;"></th>
		//	<th class="left" style="width:110px;"><a ref="admins/view?sort_by=fullname&sort_dir=asc&page=1">Full Name</a></th>
		//	<th class="left" style="width:110px;"><a ref="admins/view?sort_by=username&sort_dir=asc&page=1">Username</a></th>
		//	<th class="left"><a ref="admins/view?sort_by=email&sort_dir=asc&page=1">Email</a></th>
		//	<th class="center" style="width:110px;"><a ref="admins/view?sort_by=role&sort_dir=asc&page=1">Account Type</a></th>
		//	<th class="center" style="width:110px;"><a ref="admins/view?sort_by=is_active&sort_dir=asc&page=1">Active</a></th>
		//	<th class="center" style="width:100px;"><a ref="admins/view?sort_by=last_visited_at&sort_dir=asc&page=1">Last Visit</a></th>
		//	<th class="actions">Actions</th>
		//	</tr>
		//	</thead>
		//	<tbody><tr>
		//	<td class="left"><img width="26px" src="templates/backend/images/accounts/adm_wpb1oswtvn.jpg" alt="" />
		//	</td>
		//	<td class="left">aaa1 bbb1</td>
		//	<td class="left">aaaaaaa11</td>
		//	<td class="left">aaa11@aaa.com</td>
		//	<td class="center">Main Admin</td>
		//	<td class="center"><span class="badge-green">Yes</span></td>
		//	<td class="center">Never</td>
		//	<td class="actions"><a class="tooltip-link" title="Edit this record" href="admins/edit/id/54"><img src="templates/backend/images/edit.png" alt="edit"></a> <a class="tooltip-link" title="Delete this record" onclick="return onDeleteRecord();" href="admins/delete/id/54"><img src="templates/backend/images/delete.png" alt="delete"></a> </td>
		//	</tr>
		//	</tbody>
		//	</table>';

		return $output;

	}


	/**
	 * Draw field by type
	 *		@param $field_name
	 *		@param $field_array - ['field'] => array(''.....)
	 *		@param $params
	 */	
	protected function _getFieldByType($mode, $field_name, $field_array = array(), $params = array())
	{
		if($field_name == '') return false;

		$output = '';
        $nl = "\n";
		
//		$direction    = ($language_dir == 'rtl' || $language_dir == 'ltr') ? ' dir="'.$language_dir.'"' : '';
//		$rid 		  = isset($params[$this->primaryKey]) ? $params[$this->primaryKey] : '';
		$field_type   = isset($field_array['type']) ? $field_array['type'] : '';
//		$source       = isset($field_array['source']) ? $field_array['source'] : '';
//		$default_option = (isset($field_array['default_option']) && $field_array['default_option'] !== '') ? $field_array['default_option'] : '-- '._SELECT.' --';
//		$readonly     = (isset($field_array['readonly']) && $field_array['readonly'] === true) ? true : false;
//		$default 	  = isset($field_array['default']) ? $field_array['default'] : '';
//		$true_value   = isset($field_array['true_value']) ? $field_array['true_value'] : '1';
//		$width        = isset($field_array['width']) ? $field_array['width'] : '';
//		$height       = isset($field_array['height']) ? $field_array['height'] : '';
//		$image_width  = isset($field_array['image_width']) ? $field_array['image_width'] : '120px';
//		$image_height = isset($field_array['image_height']) ? $field_array['image_height'] : '90px';
//		$show_seconds = isset($field_array['show_seconds']) ? $field_array['show_seconds'] : true;
//        $minutes_step = isset($field_array['minutes_step']) ? (int)$field_array['minutes_step'] : 1;
//		$maxlength    = isset($field_array['maxlength']) ? $field_array['maxlength'] : '';
//		$editor_type  = isset($field_array['editor_type']) ? $field_array['editor_type'] : '';							
//		$no_image     = isset($field_array['no_image']) ? $field_array['no_image'] : '';
//		$required 	  = isset($field_array['required']) ? $field_array['required'] : false;

		$prependCode  = isset($field_array['prependCode']) ? $field_array['prependCode'] : '';
		$appendCode   = isset($field_array['appendCode']) ? $field_array['appendCode'] : '';
//		$format 	  = isset($field_array['format']) ? $field_array['format'] : '';
//		$format_parameter = isset($field_array['format_parameter']) ? $field_array['format_parameter'] : '';
//		$tooltip 	  = isset($field_array['tooltip']) ? $field_array['tooltip'] : '';
//		$min_year     = isset($field_array['min_year']) ? $field_array['min_year'] : '90';
//		$max_year     = isset($field_array['max_year']) ? $field_array['max_year'] : '10';
//		$href         = isset($field_array['href']) ? $field_array['href'] : '#';
//		$target       = isset($field_array['target']) ? $field_array['target'] : '';
//		$javascript_event = isset($field_array['javascript_event']) ? $field_array['javascript_event'] : '';
//		$visible      = (isset($field_array['visible']) && $field_array['visible']!=='') ? $field_array['visible'] : true;
//        $autocomplete = isset($field_array['autocomplete']) ? $field_array['autocomplete'] : '';
//        $cryptography = isset($field_array['cryptography']) ? $field_array['cryptography'] : false;
//        $cryptography_type = isset($field_array['cryptography_type']) ? $field_array['cryptography_type'] : '';
//        $username_generator = isset($field_array['username_generator']) ? $field_array['username_generator'] : false;
//        $password_generator = isset($field_array['password_generator']) ? $field_array['password_generator'] : false;
//		$view_type    = isset($field_array['view_type']) ? $field_array['view_type'] : '';
//		$multi_select = isset($field_array['multi_select']) ? $field_array['multi_select'] : '';
//		
//		$atr_readonly = ($readonly) ? ' readonly="readonly"' : '';
//		$atr_disabled = ($readonly) ? ' disabled="disabled"' : '';
//		$css_disabled = ($readonly) ? ' mgrid_disabled' : '';
//		$attr_maxlength = ($maxlength != '') ? ' maxlength="'.intval($maxlength).'"' : '';
//        $autocomplete = ($autocomplete == 'off') ? ' autocomplete="off"' : '';
//
		$field_value  = isset($params[$field_name]) ? $params[$field_name] : '';
//		if($mode == 'add' && $field_value == '') $field_value = $default;
//		if($this->isHtmlEncoding) $field_value = $this->GetDataDecoded($field_value);
//
		if($mode == 'view'){            
//            $this->OnItemCreated_ViewMode($field_name, $field_value);
			// View Mode
			switch($field_type){
//				case 'link':
//					$target_str = ($target != '') ? ' target="'.$target.'"' : '';
//					$href_str = $href;
//					$title = '';
//					if($maxlength != '' && $this->IsInteger($maxlength)){
//						$this->PrepareSubString($field_value, $title, $maxlength);
//					}else if($tooltip != ''){
//						$title = $tooltip;
//					}
//					if(preg_match_all('/{.*?}/i', $href, $matches)){
//						foreach($matches[0] as $key => $val){
//							$val = trim($val, '{}');
//							if(isset($params[$val])) $href_str = str_replace('{'.$val.'}', $params[$val], $href_str);
//						}
//					}
//					$output = '<a href="'.$href_str.'"'.$target_str.' title="'.strip_tags($title).'">'.$field_value.'</a>';
//					break;
//
//				case 'enum':
//					if(is_array($source)){
//                        if(isset($source[$field_value])){
//                            $output = $source[$field_value];
//                            break;
//                        }
//					}
//					break;			
//
//				case 'image':
//				    if($field_value == '' && $no_image != '') $field_value = $no_image;
//					$output = '<img src="'.$target.$field_value.'" title="'.$field_value.'" alt="" width="'.$image_width.'" height="'.$image_height.'">';
//					break;
//				
				default:
				case 'label':
					$title = '';
					//$field_value  = $this->FormatFieldValue($field_value, $format, $format_parameter);
					//if($maxlength != '' && $this->IsInteger($maxlength)){
					//	$this->PrepareSubString($field_value, $title, $maxlength);
					//}else if($tooltip != ''){
					//	$title = $tooltip;
					//}
					//$output = $pre_html.'<label class="mgrid_label" title="'.$this->GetDataDecoded(strip_tags($title)).'">'.$this->GetDataDecodedText($field_value).'</label>'.$post_html;
					$output .= $prependCode.'<label class="dgrid-label">'.$field_value.'</label>'.$appendCode;
					break;			
			}			
//		}else{
//            
//            if($mode == 'details') $this->OnItemCreated_DetailsMode($field_name, $field_value);
//            
//			// Add/Edit/Detail Modes 
//			switch($field_type){
//				case 'checkbox':
//					$checked = '';
//					$rid = self::GetParameter('rid');
//					if($mode == 'add'){
//					    if(empty($rid) && $default == $true_value){ // opens page first time 
//							$checked = ' checked="checked"';
//						}else{
//							if($field_value == '1') $checked = ' checked="checked"';
//						}
//					}else{
//						if($field_value == '1') $checked = ' checked="checked"';
//					}
//					if($readonly){
//						$output  = '<input type="checkbox" name="'.$field_name.'" id="'.$field_name.'" class="mgrid_checkbox" value="1"'.$checked.$atr_disabled.'>';
//						$output .= draw_hidden_field($field_name, '1', false, $field_name);						
//					}else{
//						$output = '<input type="checkbox" name="'.$field_name.'" id="'.$field_name.'" class="mgrid_checkbox" value="1"'.$checked.'>';						
//					}
//					$output .= $post_html;
//					break;
//				case 'date':
//				case 'datetime':
//                case 'time':
//					if($mode != 'details'){
//						$lang = array();
//						$lang['months'][1] = (defined('_JANUARY')) ? _JANUARY : 'January';
//						$lang['months'][2] = (defined('_FEBRUARY')) ? _FEBRUARY : 'February';
//						$lang['months'][3] = (defined('_MARCH')) ? _MARCH : 'March';
//						$lang['months'][4] = (defined('_APRIL')) ? _APRIL : 'April';
//						$lang['months'][5] = (defined('_MAY')) ? _MAY : 'May';
//						$lang['months'][6] = (defined('_JUNE')) ? _JUNE : 'June';
//						$lang['months'][7] = (defined('_JULY')) ? _JULY : 'July';
//						$lang['months'][8] = (defined('_AUGUST')) ? _AUGUST : 'August';
//						$lang['months'][9] = (defined('_SEPTEMBER')) ? _SEPTEMBER : 'September';
//						$lang['months'][10] = (defined('_OCTOBER')) ? _OCTOBER : 'October';
//						$lang['months'][11] = (defined('_NOVEMBER')) ? _NOVEMBER : 'November';
//						$lang['months'][12] = (defined('_DECEMBER')) ? _DECEMBER : 'December';
//						$show_link = true;
//                        $meridiem = '';
//                        
//						if($field_type == 'datetime'){
//							$datetime_format = 'Y-m-d H:i:s';
//							$datetime_empty_value = '0000-00-00 00:00:00';
//                            if($minutes_step != '1') $show_link = false;
//						}else if($field_type == 'time'){
//                            if($format_parameter == 'am/pm'){
//                                $datetime_format = ($show_seconds) ? 'g:i:s A' : 'g:i A';
//                            }else{
//                                $datetime_format = ($show_seconds) ? 'H:i:s' : 'H:i';
//                            }
//							$datetime_empty_value = '00:00:00';
//                            if($minutes_step != '1') $show_link = false;
//                        }else{
//							$datetime_format = 'Y-m-d';	
//							if(!empty($format_parameter)){
//								if(strtolower($format_parameter) == 'm-d-y') $datetime_format = 'm-d-Y';
//								else if(strtolower($format_parameter) == 'd-m-y') $datetime_format = 'd-m-Y';
//							}
//							$datetime_empty_value = '0000-00-00';
//						}
//						$date_datetime_format = @date($datetime_format);
//						
//						$year = substr($field_value, 0, 4);
//						$month = substr($field_value, 5, 2);
//						$day = substr($field_value, 8, 2);
//						if($field_type == 'datetime'){
//							$hour = substr($field_value, 11, 2);
//							$minute = substr($field_value, 14, 2);
//							$second = substr($field_value, 17, 2);							
//						}else if($field_type == 'time'){
//                            $hour = substr($field_value, 0, 2);
//                            $minute = substr($field_value, 3, 2);
//                            $second = ($show_seconds) ? substr($field_value, 6, 2) : '00';                            
//                            if($format_parameter == 'am/pm'){
//                                $meridiem = '';
//                                if($hour == '0'){
//                                    $hour = 12;
//                                    $meridiem = 'am';
//                                }else if($hour < '12'){
//                                    $meridiem = 'am';
//                                }else if($hour == '12'){
//                                    $meridiem = 'pm';     
//                                }else{
//                                    $hour -= 12;
//                                    if($hour > 10) $hour = '0'.(int)$hour;
//                                    $meridiem = 'pm';     
//                                }
//                            }                            
//                        }
//						
//						$arr_ret_date = array();
//                        if($field_type == 'datetime' || $field_type == 'date'){
//                            $arr_ret_date['y'] = '<select'.$atr_disabled.' name="'.$field_name.'__nc_year" id="'.$field_name.'__nc_year" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="">'._YEAR.'</option>'; for($i=@date('Y')-$min_year; $i<=@date('Y')+$max_year; $i++) { $arr_ret_date['y'] .= '<option value="'.$i.'"'.(($year == $i) ? ' selected="selected"' : '').'>'.$i.'</option>'; }; $arr_ret_date['y'] .= '</select>';                            
//                            $arr_ret_date['m'] = '<select'.$atr_disabled.' name="'.$field_name.'__nc_month" id="'.$field_name.'__nc_month" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="">'._MONTH.'</option>'; for($i=1; $i<=12; $i++) { $arr_ret_date['m'] .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($month == $i) ? ' selected="selected"' : '').'>'.$lang['months'][$i].'</option>'; }; $arr_ret_date['m'] .= '</select>';
//                            $arr_ret_date['d'] = '<select'.$atr_disabled.' name="'.$field_name.'__nc_day" id="'.$field_name.'__nc_day" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="">'._DAY.'</option>'; for($i=1; $i<=31; $i++) { $arr_ret_date['d'] .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($day == $i) ? ' selected="selected"' : '').'>'.(($i < 10) ? '0'.$i : $i).'</option>'; }; $arr_ret_date['d'] .= '</select>';
//    
//                            $output  = $arr_ret_date[strtolower(substr($datetime_format, 0, 1))];
//                            $output .= $arr_ret_date[strtolower(substr($datetime_format, 2, 1))];
//                            $output .= $arr_ret_date[strtolower(substr($datetime_format, 4, 1))];
//                        }
//
//						if($field_type == 'datetime' || $field_type == 'time'){
//							if($field_type == 'datetime') $output .= ' : ';
//                            if($format_parameter == 'am/pm'){
//                                $output .= '<select'.$atr_disabled.' name="'.$field_name.'__nc_hour" id="'.$field_name.'__nc_hour" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')">'; for($i=1; $i<=12; $i++) { $output .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($hour == $i) ? ' selected="selected"' : '').'>'.(($i < 10) ? '0'.$i : $i).'</option>'; }; $output .= '</select>';
//                                $output .= '<select'.$atr_disabled.' name="'.$field_name.'__nc_minute" id="'.$field_name.'__nc_minute" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')">'; for($i=0; $i<=59; $i=$i+$minutes_step) { $output .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($minute == $i) ? ' selected="selected"' : '').'>'.(($i < 10) ? '0'.$i : $i).'</option>'; }; $output .= '</select>';
//                                $output .= '<select'.$atr_disabled.' name="'.$field_name.'__nc_meridiem" id="'.$field_name.'__nc_meridiem" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="am" '.(($meridiem == 'am') ? 'selected="selected"' : '').'>AM</option><option value="pm" '.(($meridiem == 'pm') ? 'selected="selected"' : '').'>PM</option></select>';                    
//                            }else{
//                                $output .= '<select'.$atr_disabled.' name="'.$field_name.'__nc_hour" id="'.$field_name.'__nc_hour" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="00">'._HOUR.'</option>'; for($i=0; $i<=23; $i++) { $output .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($hour == $i) ? ' selected="selected"' : '').'>'.(($i < 10) ? '0'.$i : $i).'</option>'; }; $output .= '</select>';
//                                $output .= '<select'.$atr_disabled.' name="'.$field_name.'__nc_minute" id="'.$field_name.'__nc_minute" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="00">'._MIN.'</option>'; for($i=0; $i<=59; $i=$i+$minutes_step) { $output .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($minute == $i) ? ' selected="selected"' : '').'>'.(($i < 10) ? '0'.$i : $i).'</option>'; }; $output .= '</select>';                    
//                            }
//							if($show_seconds){ $output .= '<select'.$atr_disabled.' name="'.$field_name.'__nc_second" id="'.$field_name.'__nc_second" onChange="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\')"><option value="">'._SEC.'</option>'; for($i=0; $i<=59; $i++) { $output .= '<option value="'.(($i < 10) ? '0'.$i : $i).'"'.(($second == $i) ? ' selected="selected"' : '').'>'.(($i < 10) ? '0'.$i : $i).'</option>'; }; $output .= '</select>'; }
//						}
//						if(!$readonly){
//							if($show_link) $output .= ' <a href="javascript:void(\'date|set\');" onclick="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\', \''.@date($datetime_format).'\', \''.(@date('Y')-$min_year).'\', false)">[ '.$date_datetime_format.' ]</a>';
//							if(!$required) $output .= ' <a href="javascript:void(\'date|reset\');" onclick="setCalendarDate(\'frmDataGrid_'.$this->_table.'\', \''.$field_name.'\', \''.$datetime_format.'\', \''.$datetime_empty_value.'\', \'1\', false)">[ '._RESET.' ]</a>';
//						}
//						$output .= '<input style="width:0px;border:0px;margin:0px;padding:0px;" type="text" name="'.$field_name.'" id="'.$field_name.'" value="'.$field_value.'">';					
//					}else{
//						if($field_value != '' && !preg_match('/0000-00-00/', $field_value)){
//							if(empty($format_parameter)) $format_parameter = ($format == 'datetime') ? 'Y-m-d H:i:s' : 'Y-m-d';
//							$field_value = date($format_parameter, strtotime($field_value));
//						}else{
//							$field_value = '';
//						}
//						$output = '<label class="mgrid_label">'.$this->GetDataDecoded($field_value).'</label>';				
//					}
//					break;
//				case 'file':
//				case 'image':
//					if(strtolower(SITE_MODE) == 'demo') $atr_readonly = ' disabled="disabled"';
//				
//					if(($mode == 'edit' || $mode == 'details')){
//						if($mode == 'edit'){
//							if($field_value != ''){
//                                $filesize = number_format((@filesize($target.$field_value) / 1024),  1).' Kb';                                
//								$output = ($field_type == 'file') ? $field_value : '<img src="'.$target.$field_value.'" title="'.$field_value.' ('.$filesize.')" alt="" width="'.$image_width.'" height="'.$image_height.'">';								
//								if($required) $output .= draw_hidden_field($field_name, $field_value, false, $field_name);
//								if(strtolower(SITE_MODE) != 'demo' && !$readonly) $output .= '<br><a href="'.$this->_actionUrl.'&dg_prefix='.$this->uPrefix.'&dg_action=edit&dg_rid='.$rid.'&dg_operation=remove&dg_operation_field='.$field_name.'">['._DELETE_WORD.']</a>';
//							}else{
//								$output = '<input type="file" name="'.$field_name.'" id="'.$field_name.'" class="mgrid_file" '.$atr_readonly.'>';		
//							}
//						}else if($mode == 'details'){
//							if($field_value == '' && $no_image != '') $field_value = $no_image;
//							$output = ($field_type == 'file') ? $field_value : '<img src="'.$target.$field_value.'" title="'.$field_value.'" alt="" width="'.$image_width.'" height="'.$image_height.'">';								
//						}
//					}else{
//						$output = '<input type="file" name="'.$field_name.'" id="'.$field_name.'" class="mgrid_file" '.$atr_readonly.'>';
//					}
//					break;				
//				case 'enum':
//					if(is_array($source)){
//						if($mode == 'add' || $mode == 'edit'){
//                            if($view_type == 'checkboxes'){
//                                $output = '';
//                                $params_edit = ($mode == 'edit') ? @unserialize($field_value) : array();
//                                $checkboxes_count = 1;
//                                foreach($source as $key => $val){
//                                    if($mode == 'edit'){
//                                        $checked = (is_array($params_edit) && in_array($key, $params_edit)) ? 'checked="checked"' : '';
//                                    }else{
//                                        $checked = (isset($params[$field_name]) && is_array($params[$field_name]) && in_array($key, $params[$field_name])) ? 'checked="checked"' : '';
//                                    }
//                                    $output .= '<div style="float:'.Application::Get('defined_left').';width:220px;"><input type="checkbox" name="'.$field_name.'[]" id="'.$field_name.$checkboxes_count.'" value="'.$key.'" '.$checked.'/> <label for="'.$field_name.$checkboxes_count.'">'.$val.'</label></div>';
//                                    $checkboxes_count++;
//                                }
//								$output .= '<input type="hidden" name="'.$field_name.'[]" value="-placeholder-" />'; /* add placeholder for checkboxes */
//                            }else if($view_type == 'label'){
//                                if(isset($source[$field_value])){
//                                    $output = $source[$field_value];
//                                }                                
//                            }else{
//                                $output_start = '<select class="mgrid_select" name="'.$field_name.'" id="'.$field_name.'" '.(($javascript_event!='') ? ' '.$javascript_event : '').' style="'.(($width!='')?'width:'.$width.';':'').'" '.(($readonly) ? 'disabled="disabled"' : '').'>';
//                                $output_options = '';
//                                if($default_option) $output_options .= '<option value="">'.$default_option.'</option>';
//                                foreach($source as $key => $val){
//                                    $output_options .= '<option value="'.$key.'" ';
//                                    $output_options .= ($field_value == $key) ? 'selected="selected" ' : '';
//                                    $output_options .= '>'.$val.'</option>';
//                                }
//                                $output = $output_start.$output_options.'</select>';												
//                            }
//						}else{
//                            if($view_type == 'checkboxes'){
//                                $params_details = @unserialize($field_value);
//                                foreach($source as $key => $val){
//                                    $checked = (is_array($params_details) && in_array($key, $params_details)) ? '<span class="green">+</span> ' : '';
//                                    $output .= '<div style="float:'.Application::Get('defined_left').';width:190px;"><label>'.$checked.(($checked) ? $val : '<span class="lightgray">&#8226; '.$val.'</span>').'</label></div>';
//                                }                                
//                            }else{
//                                if(isset($source[$field_value])){
//                                    $output = $source[$field_value];
//                                }                                
//                            }
//						}
//					}
//					$output .= $post_html;
//					break;				
//				case 'label':
//					$title = '';
//					$field_value  = $this->FormatFieldValue($field_value, $format, $format_parameter);
//					if($maxlength != '' && $this->IsInteger($maxlength)){
//						$this->PrepareSubString($field_value, $title, $maxlength);
//					}					
//					$output = $pre_html.'<label class="mgrid_label mgrid_wrapword" title="'.strip_tags($title).'">'.$this->GetDataDecoded($field_value).'</label>'.$post_html;			
//					break;
//                case 'html':
//                    if($mode == 'details'){
//                        $output = $pre_html.$this->GetDataDecodedText($field_value).$post_html;
//                    }
//                    break;
//				case 'object':
//					if(!preg_match('/youtube/i', $field_value)){
//						$output = '<object width="'.$width.'" height="'.$height.'">
//								   <param name="movie" value="'.$field_value.'">
//								   <embed src="'.$field_value.'" width="'.$width.'" height="'.$height.'"></embed>
//								   </object>';														
//					}else{
//						$output = $field_value;
//					}
//					break;
//				case 'password':
//					if($mode == 'add' || $mode == 'edit'){
//                        if($cryptography && strtolower($cryptography_type) == 'md5') $field_value = '';
//
//                        if($password_generator && $mode == 'add'){
//                            $post_html_temp  = ' &nbsp;<a href="javascript:__mgGenerateRandom(\'password\', \'random-password\')" id="link-password">[ '._GENERATE.' ]</a>';
//                            $post_html_temp .= ' &nbsp;<span id="random-password-div" style="display:none;"><a href="javascript:void(0);" onclick="__mgUseThisPassword(\''.$field_name.'\')" id="link-confirm-password">[ '._USE_THIS_PASSWORD.' ]</a> <label id="random-password" style="background-color:#f4f4f4;font-size:14px;margin:0 5px;"></label></span>';
//                            $post_html .= $post_html_temp.$post_html; 
//                        }
//						$output = '<input type="password" class="mgrid_text" name="'.$field_name.'" id="'.$field_name.'" style="'.(($width!='')?'width:'.$width.';':'').'" value="'.$this->GetDataDecoded($field_value).'" '.$atr_readonly.$attr_maxlength.'>'.$post_html;
//					}else{
//						$output = '<label class="mgrid_label">*****</label>';				
//					}				
//					break;	
//				case 'textarea':
//					$output = '';
//					if($editor_type == 'wysiwyg'){
//						$wysiwyg_state = (isset($_COOKIE['wysiwyg_'.$field_name.'_mode'])) ? $_COOKIE['wysiwyg_'.$field_name.'_mode'] : '0';
//						$output .= '<script type="text/javascript">';
//						$output .= '__mgAddListener(document, \'load\', function() { toggleEditor(\''.$wysiwyg_state.'\',\''.$field_name.'\',\''.$height.'\'); }, false);'.$nl;
//						$output .= '__mgAddListener(this, \'load\', function() { toggleEditor(\''.$wysiwyg_state.'\',\''.$field_name.'\',\''.$height.'\'); }, false);'.$nl;					
//						$output .= '</script>';						
//						$output .= '[ <a id="lnk_0_'.$field_name.'" style="display:none;" href="javascript:toggleEditor(\'0\',\''.$field_name.'\');" title="Switch to Simple Mode">'._SIMPLE.'</a><a id="lnk_1_'.$field_name.'" href="javascript:toggleEditor(\'1\',\''.$field_name.'\');" title="Switch to Advanced Mode">'._ADVANCED.'</a> ]<br>';
//					}
//					$output .= $pre_html.'<textarea class="mgrid_textarea" name="'.$field_name.'" id="'.$field_name.'" style="'.(($width != '') ? 'width:'.$width.';' : ' rows="7"').(($height != '') ? 'height:'.$height.';' :' cols="60"').'" '.$atr_disabled.$direction.$attr_maxlength.'>'.$this->GetDataDecodedText($field_value).'</textarea>'.$post_html;				
//					break;
//				default:
//				case 'textbox':
//                    if($username_generator && $mode == 'add'){
//                        $post_html .= ' &nbsp;<a href="javascript:__mgGenerateRandom(\'username\', \''.$field_name.'\')" id="link-username">[ '._GENERATE.' ]</a>'.$post_html;                       
//                    }
//					$output = $pre_html.'<input class="mgrid_text'.$css_disabled.'" name="'.$field_name.'" id="'.$field_name.'"  style="'.(($width != '') ? 'width:'.$width.';' : '').(($visible == false) ? 'display:none;' : '').'" value="'.$this->GetDataDecoded($field_value).'" '.$atr_readonly.$attr_maxlength.$direction.$autocomplete.' />'.$post_html;
//					break;				
//			}			
		}		
		
		return $output;		
	}
	
    /** 
    * This method queries your database to find all related objects
    * Ex.: findAll('postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
    * Ex.: findAll(array('condition'=>'postID = :postID AND isActive = :isActive', 'order'=>'id DESC', 'limit'=>'0, 10'), 'params'=>array(':postID'=>10, 'isActive'=>1)));
    * @param mixed $conditions
    * @param array $params 
    */
	protected function _getAll($conditions = '', $params = '', $fetchMode = PDO::FETCH_ASSOC)
    {
        if(is_array($conditions)){
        //    $where = isset($conditions['condition']) ? $conditions['condition'] : '';
			$order = isset($conditions['order']) ? $conditions['order'] : '';
        //    $limit = isset($conditions['limit']) ? $conditions['limit'] : '';
        }else{
            $where = $conditions;
            $order = '';
            $limit = '';
        }
        $whereClause = !empty($where) ? ' WHERE '.$where : '';
        $orderBy = !empty($order) ? ' ORDER BY '.$order : '';
        $limit = !empty($limit) ? ' LIMIT '.$limit : '';
        //
        //$relations = $this->getRelations();
		$relations['fields'] = '';
        $customFields = '';//$this->getCustomFields();
        
		//SELECT './*$customFields*/.' './*$relations['fields']*/.'
		//FROM './*$relations['tables']*/.'
        $sql = 'SELECT
                    `'.CConfig::get('db.prefix').$this->_table.'`.*
                FROM `'.CConfig::get('db.prefix').$this->_table.'`
                    
                '.$whereClause.'
                '.$orderBy.'
                '.$limit;
		
        return $this->_db->select($sql, $params);
    }

  
}
?>
<?php

/**
 * Search if a given key presents in array
 * @param string $needle,
 * @param array $arrSearch
 * @return bool
 */
function my_array_search($needle, $arrSearch){
	
	$needle = strtolower($needle);
	
	foreach($arrSearch as $key => $val){
		if(is_array($val)){
			if(isset($val['links'][$needle])){
				return true;
			}
		}else{
			if($needle == $key){
				return true;
			}
		}
	}
	
	return false;
}

$menus = array(
	'introduction' 						=> 'Introduction',
	'installation' 						=> 'Installation',
	'updating' 							=> 'Updating',
	'running-examples' 					=> 'Running Examples',
	'coding-standards' 					=> 'Coding Standards',
	'knowledge-base' 					=> 'Knowledge Base',

	'group-utils' => array(
		'name' 				=> 'Utils',
		'links'				=> array(
			'requirements-checker'		=> 'Requirements Checker',
			'tests'						=> 'Tests',
			'code-generators'			=> 'Code Generators',
		),
	),
			   
	'group-framework-structure'	=> array(
		'name' 				=> 'Framework Structure',
		'links'				=> array(
			'framework-structure'		=> 'General Review',
			'collections'				=> 'Collections',
			'components'				=> 'Components',
			'core'						=> 'Core',
			'database'					=> 'Database',
			'helpers'					=> 'Helpers',
			'i18n'						=> 'Internationalization (i18n)',
			'messages'					=> 'Messages',
			'vendors'					=> 'Vendors',
		),
	),

	'group-application-development'	=> array(
		'name' 				=> 'Application Development',
		'links'				=> array(
			'dummy-application'			=> 'Dummy Application',
			'directy-cmf'				=> 'Directy CMF',
			'setup-application'			=> 'Setup',
			'file-structure'			=> 'File Structure',
			'application-modes'			=> 'Application Modes',
			'templates'					=> 'Templates',
			'layouts'					=> 'Layouts',
			'configuration-files'		=> 'Configuration Files',
			'routing'					=> 'Routing',
			'controllers-and-actions'	=> 'Controllers & Actions',
			'models'					=> 'Models',
			'views'						=> 'Views',
			'authorization'				=> 'Authorization',
			'l10n'						=> 'Localization (l10n)',
			'application-components'	=> 'Components',
			'widgets'					=> 'Widgets',
			'application-vendors'		=> 'Application Vendors',
			'errors-handling'			=> 'Errors Handling',
			'sessions'					=> 'Sessions',
			'development-workflow'		=> 'Development Workflow',
		),
	),

	'group-special-topics'	=> array(
		'name' 				=> 'Special Topics',
		'links'				=> array(
			'security'					=> 'Security',
			'cron-jobs'					=> 'Cron Jobs',
			'database-request-caching'	=> 'Data Caching',
			'session-custom-storage'	=> 'Session Custom Storage',
			'shopping-cart'				=> 'Shopping Cart',
		),
	),

	'group-working-with-forms'	=> array(
		'name' 				=> 'Working with Forms',
		'links'				=> array(
			'overview'					=> ''
		),
	),


	'group-application-modules'	=> array(
		'name' 				=> 'Application Modules',
		'links'				=> array(
			'modules-overview'			=> 'Overview',
			'modules-structure'			=> 'Module Structure',
			'modules-creating'			=> 'Creating a Module',
		),
	),
);



<?php
/**
 * CHtml is a helper class that provides a collection of helper methods for creating HTML elements
 *
 * @project   ApPHP Framework
 * @author    ApPHP <info@apphp.com>
 * @link      http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license   http://www.apphpframework.com/license/
 *
 * PUBLIC (static):            PROTECTED (static):            PRIVATE (static):
 * ----------               ----------                  ----------
 * tag                      _inputField                 _renderAttributes
 * openTag                  _clientChange
 * closeTag
 * link
 * label
 * encode
 * decode
 * css
 * cssFile
 * cssFiles
 * script
 * scriptFile
 * scriptFiles
 * form
 * openForm
 * closeForm
 * hiddenField
 * textField
 * passwordField
 * fileField
 * colorField
 * emailField
 * searchField
 * getIdByName
 * dropDownList
 * listBox
 * listOptions
 * textArea
 * checkBox
 * checkBoxList
 * radioButton
 * radioButtonList
 * submitButton
 * resetButton
 * image
 * video
 * audio
 * button
 * convertFileSize
 * convertImageDimensions
 * escapeHex
 * escapeHexEntity
 *
 */

class CHtml
{

	/** @const string */
	const ID_PREFIX = 'ap';
	/** @var string */
	public static $afterRequiredLabel = ' <span class="required">*</span>';
	/** @var string */
	private static $_count = 0;

	/**
	 * Generates an HTML tag
	 * @param string $tag
	 * @param array $htmlOptions
	 * @param mixed $content
	 * @param boolean $closeTag
	 * @return string - HTML tag
	 */
    public static function tag($tag, $htmlOptions = [], $content = false, $closeTag = true)
    {
        $html = '<' . $tag . self::_renderAttributes($htmlOptions);
		if ($content === false) {
			return $closeTag ? $html . ' />' : $html . '>';
		} else {
			return $closeTag ? $html . '>' . $content . '</' . $tag . '>' : $html . '>' . $content;
		}
	}

	/**
	 * Generates an open HTML tag
	 * @param string $tag
	 * @param array $htmlOptions
	 * @return string - HTML tag
	 */
    public static function openTag($tag, $htmlOptions = [])
    {
        return '<' . $tag . self::_renderAttributes($htmlOptions) . '>';
	}

	/**
	 * Generates a close HTML tag
	 * @param string $tag
	 * @return string - HTML tag
	 */
	public static function closeTag($tag)
	{
		return '</' . $tag . '>';
	}

	/**
	 * Generates a hyperlink tag
	 * @param string $text
	 * @param string $url
	 * @param array $htmlOptions
	 * @return string - HTML tag
	 */
    public static function link($text, $url = '#', $htmlOptions = [])
    {
        if ($url !== '') $htmlOptions['href'] = $url;
		if (isset($htmlOptions['escape']) && $htmlOptions['escape'] === true) {
			$text = self::escapeHexEntity($text);
			$htmlOptions['href'] = self::escapeHex($htmlOptions['href']);
			if (isset($htmlOptions['escape'])) unset($htmlOptions['escape']);
		}
		// Prevent target="_blank" vulnerability
		if (isset($htmlOptions['target']) && strtolower($htmlOptions['target']) === '_blank' && empty($htmlOptions['rel'])) {
			$htmlOptions['rel'] = 'noopener noreferrer';
		}
		return self::tag('a', $htmlOptions, $text);
	}

	/**
	 * Generates a label tag
	 * @param string $label
	 * @param string $for
	 * @param array $htmlOptions
	 * @return string - HTML tag
	 */
    public static function label($label, $for = false, $htmlOptions = [])
    {
        if ($for === false) {
			if (isset($htmlOptions['for'])) unset($htmlOptions['for']);
		} else {
			$htmlOptions['for'] = $for;
		}
		return self::tag('label', $htmlOptions, $label);
	}

	/**
	 * Encodes special characters into HTML entities
	 * @param string $text
	 * @param int $flag
	 * @return string
	 */
	public static function encode($text, $flag = ENT_QUOTES)
	{
		if (version_compare(phpversion(), '5.5', '<')) {
			// Generates error if text is  ASCII and A::app()->charset is UTF-8
			return @htmlspecialchars($text, $flag, A::app()->charset);
		} else {
			return htmlspecialchars($text, $flag, A::app()->charset);
		}
	}

	/**
	 * Decodes special HTML entities back to the corresponding characters
	 * @param string $text
	 * @param int $flag
	 * @return string
	 */
	public static function decode($text, $flag = ENT_QUOTES)
	{
		return htmlspecialchars_decode($text, $flag);
	}

	/**
	 * Encloses the passed CSS content with a CSS tag
	 * @param string $text
	 * @param string $media
	 * @param bool $newLine
	 * @return string the CSS tag
	 */
	public static function css($text, $media = '', $newLine = true)
	{
		if ($media !== '') $media = ' media="' . $media . '"';
		$newLine = (($newLine) ? "\n" : '');
		return "<style type=\"text/css\"{$media}>" . $newLine . "/*<![CDATA[*/\n{$text}\n/*]]>*/" . $newLine . "</style>";
	}

	/**
	 * Links to required CSS file
	 * @param string $url
	 * @param string $media
	 * @param bool $newLine
	 * @return string - HTML tag
	 */
	public static function cssFile($url, $media = '', $newLine = true)
	{
		if ($media !== '') $media = ' media="' . $media . '"';
		return '<link rel="stylesheet" type="text/css" href="' . self::encode($url) . '"' . $media . ' />' . (($newLine) ? "\n" : '');
	}

	/**
	 * Links to required CSS files
	 * @param array $urls Usage: ['url1', 'url2' => ['media'=>'print']]
	 * @param string $path
	 * @param bool $newLine
	 * @return string - HTML tag
	 */
    public static function cssFiles($urls = [], $path = '', $newLine = true)
    {
        $output = '';

		if (!is_array($urls)) {
			return $output;
		}

		foreach ($urls as $key => $val) {
			if (empty($val)) continue;
			$path = !empty($path) ? trim($path, '/') . '/' : '';
			$href = is_array($val) ? $key : $val;
			$media = (is_array($val) && !empty($val['media'])) ? ' media="' . $val['media'] . '"' : '';
			$output .= '<link rel="stylesheet" type="text/css" href="' . $path . self::encode($href) . '"' . $media . ' />' . (($newLine) ? "\n" : '');
		}

		return $output;
	}

	/**
	 * Encloses the passed JavaScript within a Script tag
	 * @param string $text
	 * @return string the Script tag
	 */
	public static function script($text)
	{
		return "<script type=\"text/javascript\">\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</script>";
	}

	/**
	 * Includes a JavaScript file
	 * @param string $url
	 * @param bool $newLine
	 * @param bool $preventDouble
	 * @param array $htmlOptions
	 * @return string - HTML tag
	 */
    public static function scriptFile($url, $newLine = true, $preventDouble = false, $htmlOptions = [])
    {
        $include = false;
		if ($preventDouble) {
			$hash_name = md5($url);
			if (!defined($hash_name)) {
				define(md5($url), true);
				$include = true;
			}
		} else {
			$include = true;
		}

		if ($include) {
			return '<script type="text/javascript" src="' .
				self::encode($url) . '"' .
				self::_renderAttributes($htmlOptions) .
				'></script>' . (($newLine) ? "\n" : '');
		}
	}

	/**
	 * Links to required JavaScript files
	 * @param array $urls Usage: ['url1', 'url2' => array['integrity'=>'...', 'crossorigin'=>'...']]
	 * @param string $path
	 * @param bool $newLine
	 * @return string - HTML tag
	 */
    public static function scriptFiles($urls = [], $path = '', $newLine = true)
    {
        $output = '';

		if (!is_array($urls)) {
			return $output;
		}

		foreach ($urls as $key => $val) {
			$path = !empty($path) ? trim($path, '/') . '/' : '';
			$href = is_array($val) ? $key : $val;
            $htmlOptions = is_array($val) ? $val : [];
            $output .= '<script type="text/javascript" src="' .
				$path . self::encode($href) . '"' .
				self::_renderAttributes($htmlOptions) .
				'></script>' . (($newLine) ? "\n" : '');
		}

		return $output;
	}

	/**
	 * Generates an open form tag
	 * This is a shortcut to {@link openForm}
	 * @param mixed $action
	 * @param string $method
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function form($action = '', $method = 'post', $htmlOptions = [])
    {
        return self::openForm($action, $method, $htmlOptions);
	}

	/**
	 * Generates an opening form tag
	 * Only the open tag is generated, a close tag should be placed manually at the end of the form
	 * @param mixed $action
	 * @param string $method
	 * @param array $htmlOptions
	 * @return string
	 * @see endForm
	 */
    public static function openForm($action = '', $method = 'post', $htmlOptions = [])
    {
        $htmlOptions['action'] = $url = $action;
		$htmlOptions['method'] = $method;
		$form = self::tag('form', $htmlOptions, false, false);
        $hiddens = [];
        if (!strcasecmp($method, 'get') && ($pos = strpos($url, '?')) !== false) {
			foreach (explode('&', substr($url, $pos + 1)) as $pair) {
				if (($pos = strpos($pair, '=')) !== false) {
                    $hiddens[] = self::hiddenField(urldecode(substr($pair, 0, $pos)), urldecode(substr($pair, $pos + 1)), ['id' => false]);
                }
			}
		}

		$request = A::app()->getRequest();
		if ($request->getCsrfValidation() && !strcasecmp($method, 'post')) {
            $hiddens[] = self::hiddenField($request->getCsrfTokenKey(), $request->getCsrfTokenValue(), ['id' => false]);
        }
        if ($hiddens !== []) {
            $form .= "\n".implode("\n", $hiddens)."\n";
        }

        return $form;
	}

	/**
	 * Generates a closing form tag
	 * @return string
	 * @see openForm
	 */
	public static function closeForm()
	{
		return '</form>';
	}

	/**
	 * Generates a hidden input
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function hiddenField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('hidden', $name, $value, $htmlOptions) . "\n";
	}

	/**
	 * Generates a textbox input
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function textField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('text', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a password field
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function passwordField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('password', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a file field
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function fileField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('file', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a color input
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function colorField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('color', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a email input
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function emailField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('email', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a search input
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 * @see inputField
	 */
    public static function searchField($name, $value = '', $htmlOptions = [])
    {
        return self::_inputField('search', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a valid HTML ID based on name
	 * @param string $name
	 * @return string
	 */
	public static function getIdByName($name)
	{
        return str_replace(['#', '[]', '][', '[', ']'], ['-', '', '_', '_', ''], $name);
    }

    /**
	 * Generates an input HTML tag
	 * This method generates an input HTML tag based on the given name of input tag and value
	 * @param string $type
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 */
	protected static function _inputField($type, $name, $value, $htmlOptions)
	{
		$htmlOptions['type'] = $type;
		$htmlOptions['value'] = $value;
		$htmlOptions['name'] = $name;
		if (!isset($htmlOptions['id'])) $htmlOptions['id'] = self::getIdByName($name);
		elseif ($htmlOptions['id'] === false) unset($htmlOptions['id']);
		return self::tag('input', $htmlOptions, false);
	}

	/**
	 * Draws textarea
	 * @param string $name
	 * @param string $value
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function textArea($name, $value = '', $htmlOptions = [])
    {
        $htmlOptions['name'] = $name;
		if (!isset($htmlOptions['id'])) $htmlOptions['id'] = self::getIdByName($name);
		elseif ($htmlOptions['id'] === false) unset($htmlOptions['id']);
		return self::tag('textarea', $htmlOptions, isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value));
	}

	/**
	 * Generates a check box
	 * @param string $name
	 * @param boolean $checked
	 * @param array $htmlOptions
	 * @see inputField
	 */
    public static function checkBox($name, $checked = false, $htmlOptions = [])
    {
        if ($checked) {
			$htmlOptions['checked'] = 'checked';
		} elseif (isset($htmlOptions['checked'])) {
			unset($htmlOptions['checked']);
		}

		$value = (isset($htmlOptions['value']) && $htmlOptions['value'] !== '') ? $htmlOptions['value'] : 1;
		/// TODO self::_clientChange('click', $htmlOptions);

		if (array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		} else {
			$uncheck = null;
		}

		if ($uncheck !== null) {
			// Add a hidden field so that if the checkbox is not selected, it still submits a value
			if (isset($htmlOptions['id']) && $htmlOptions['id'] !== false) {
                $uncheckOptions = ['id' => self::ID_PREFIX.$htmlOptions['id']];
            } else {
                $uncheckOptions = ['id' => false];
            }
			$hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
		} else {
			$hidden = '';
		}

		// Add a hidden field so that if the checkbox  is not selected, it still submits a value
		return $hidden . self::_inputField('checkbox', $name, $value, $htmlOptions);
	}

	/**
	 * Generates a check box list
	 * @param string $name
	 * @param mixed $select
	 * @param array $data
	 * @param array $htmlOptions
	 * @see tag
	 */
    public static function checkBoxList($name, $select, $data, $htmlOptions = [])
    {
        $listWrapperTag = isset($htmlOptions['listWrapperTag']) ? $htmlOptions['listWrapperTag'] : 'span';
		$listWrapperClass = isset($htmlOptions['listWrapperClass']) ? $htmlOptions['listWrapperClass'] : '';
		$template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
		$separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "<br/>\n";
		$multiple = isset($htmlOptions['multiple']) ? (bool)$htmlOptions['multiple'] : true;

		unset($htmlOptions['template'],
			$htmlOptions['separator'],
			$htmlOptions['listWrapperTag'],
			$htmlOptions['listWrapperClass'],
			$htmlOptions['multiple']);

		if ($multiple && substr($name, -2) !== '[]') {
			$name .= '[]';
		}

		// Get Check All option
		if (isset($htmlOptions['checkAll'])) {
			$checkAllLabel = $htmlOptions['checkAll'];
			$checkAllLast = isset($htmlOptions['checkAllLast']) && $htmlOptions['checkAllLast'];
		}
		unset($htmlOptions['checkAll'], $htmlOptions['checkAllLast']);

        $labelOptions = [];
        if (isset($htmlOptions['labelOptions'])) {
			$labelOptions = $htmlOptions['labelOptions'];
			unset($htmlOptions['labelOptions']);
		}

        $items  = [];
        $baseID = self::getIdByName($name);
		$id = 0;
		$checkAll = true;

		foreach ($data as $value => $label) {
			$checked = !is_array($select) && !strcmp($value, $select) || is_array($select) && in_array($value, $select);
			$checkAll = $checkAll && $checked;
			$htmlOptions['value'] = $value;
			$htmlOptions['id'] = $baseID . '_' . $id++;
			$option = self::checkBox($name, $checked, $htmlOptions);
			$label = self::label($label, $htmlOptions['id'], $labelOptions);
            $items[] = strtr($template, ['{input}' => $option, '{label}' => $label]);
        }

        if (isset($checkAllLabel)) {
			$htmlOptions['value'] = 1;
			$htmlOptions['id'] = $id = $baseID . '_all';
			$option = self::checkBox($id, $checkAll, $htmlOptions);
			$label = self::label($checkAllLabel, $id, $labelOptions);
            $item = strtr($template, ['{input}' => $option, '{label}' => $label]);
            if ($checkAllLast) {
				$items[] = $item;
			} else {
				array_unshift($items, $item);
			}
            $name = strtr($name, ['[' => '\\[', ']' => '\\]']);
            $js = '$(\'#' . $id . '\').click(function() {$("input[name=\'' . $name . '\']").prop(\'checked\', this.checked);});';
			$js .= '$("input[name=\'' . $name . '\']").click(function() {$(\'#' . $id . '\').prop(\'checked\', !$("input[name=\'' . $name . '\']:not(:checked)").length);});';
			$js .= '$(\'#' . $id . '\').prop(\'checked\', !$("input[name=\'' . $name . '\']:not(:checked)").length);';

			$clientScript = A::app()->getClientScript();
			$clientScript->registerScript('Apphp.CHtml.#' . $id, $js);
		}

        return self::tag($listWrapperTag, ['id' => $baseID, 'class' => $listWrapperClass], implode($separator, $items));
    }

    /**
	 * Generates a radio button
	 * @param string $name
	 * @param boolean $checked
	 * @param array $htmlOptions
	 * @see inputField
	 */
    public static function radioButton($name, $checked = false, $htmlOptions = [])
    {
        if ($checked) $htmlOptions['checked'] = 'checked';
		elseif (isset($htmlOptions['checked'])) unset($htmlOptions['checked']);

		$value = isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;

		/// TODO self::_clientChange('click', $htmlOptions);
		if (array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		} else {
			$uncheck = null;
		}

		if ($uncheck !== null) {
			// Add a hidden field (if radio button is not selected, it still will submit a value)
			if (isset($htmlOptions['id']) && $htmlOptions['id'] !== false) {
                $uncheckOptions = ['id' => self::ID_PREFIX.$htmlOptions['id']];
            } else {
                $uncheckOptions = ['id' => false];
            }
            $hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
		} else {
			$hidden = '';
		}
		return $hidden . self::_inputField('radio', $name, $value, $htmlOptions);
	}

	/**
	 * Generates radio buttons list
	 * @param string $name
	 * @param string $select
	 * @param array $data
	 * @param array $htmlOptions
	 * @see tag
	 */
    public static function radioButtonList($name, $select, $data, $htmlOptions = [])
    {
        $template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
		$separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "\n";
		unset($htmlOptions['template'], $htmlOptions['separator']);
        $labelOptions = [];
        if (isset($htmlOptions['labelOptions'])) {
			$labelOptions = $htmlOptions['labelOptions'];
			unset($htmlOptions['labelOptions']);
		}
        $items  = [];
        $baseID = self::getIdByName($name);
		$id = 0;
		foreach ($data as $value => $label) {
			$checked = !strcmp($value, $select);
			$htmlOptions['value'] = $value;
			$htmlOptions['id'] = $baseID . '_' . $id++;
			$option = self::radioButton($name, $checked, $htmlOptions);
			$label = self::label($label, $htmlOptions['id'], $labelOptions);
            $items[] = strtr($template, ['{input}' => $option, '{label}' => $label]);
        }

        return self::tag('span', ['id' => $baseID], implode($separator, $items));
    }

    /**
	 * Draws dropdown list
	 * @param string $name
	 * @param mixed $select
	 * @param array $data
	 * @param array $htmlOptions
	 * @param array $specialOptions
	 * @return string
	 */
    public static function dropDownList($name, $select = '', $data = [], $htmlOptions = [], $specialOptions = [])
    {
        $multiple = isset($htmlOptions['multiple']) ? (bool)$htmlOptions['multiple'] : false;
		if ($multiple && substr($name, -2) !== '[]') {
			$name .= '[]';
		}

		$htmlOptions['name'] = $name;
		if (!isset($htmlOptions['id'])) $htmlOptions['id'] = self::getIdByName($name);
		elseif ($htmlOptions['id'] === false) unset($htmlOptions['id']);
		self::_clientChange('change', $htmlOptions);

		$specialType = isset($specialOptions['type']) ? $specialOptions['type'] : '';
		$specialStep = isset($specialOptions['step']) ? (int)$specialOptions['step'] : 1;
		if ($specialType == 'hours') {
			if ($specialStep < 1 || $specialStep > 24) $specialStep = 1;
			for ($i = 0; $i < 24; $i += $specialStep) {
				$ind = (($i < 10) ? '0' : '') . $i;
				$data[$ind] = $ind;
			}
		} elseif ($specialType == 'minutes') {
			if ($specialStep < 1 || $specialStep > 60) $specialStep = 1;
			for ($i = 0; $i < 60; $i += $specialStep) {
				$ind = (($i < 10) ? '0' : '') . $i;
				$data[$ind] = $ind;
			}
		}
		$options = "\n" . self::listOptions($select, $data, $htmlOptions);
		return self::tag('select', $htmlOptions, $options);
	}

	/**
	 * Draws dropdown list
	 * @param string $name
	 * @param mixed $select
	 * @param array $data
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function listBox($name, $select = '', $data = [], $htmlOptions = [])
    {
        if (!isset($htmlOptions['size'])) $htmlOptions['size'] = 4;
		if (isset($htmlOptions['multiple'])) {
			if (substr($name, -2) !== '[]') $name .= '[]';
		}
		return self::dropDownList($name, $select, $data, $htmlOptions);
	}

	/**
	 * Generates the list of options
	 * @param mixed $selection
	 * @param array $listData
	 * @param array $htmlOptions
	 * @return string
	 *
	 * Usage:
	 * $array=>['0'=>'Option A', '1'=>'Option B', '2'=>'Option C'];
	 * $array=>['0'=>'Option A', '1'=>'Option B', '2'=>['optionValue'=>'Option C', 'optionDisabled'=>true]];
	 */
	public static function listOptions($selection, $listData, &$htmlOptions)
	{
		$raw = isset($htmlOptions['encode']) && !$htmlOptions['encode'];
		$content = '';
		if (isset($htmlOptions['prompt'])) {
            $content .= '<option value="">'.strtr($htmlOptions['prompt'], ['<' => '&lt;', '>' => '&gt;'])."</option>\n";
            unset($htmlOptions['prompt']);
        }
        if (isset($htmlOptions['empty'])) {
            if ( ! is_array($htmlOptions['empty'])) {
                $htmlOptions['empty'] = ['' => $htmlOptions['empty']];
            }
            foreach ($htmlOptions['empty'] as $value => $label) {
                $content .= '<option value="'.self::encode($value).'">'.strtr($label, ['<' => '&lt;', '>' => '&gt;'])."</option>\n";
            }
			unset($htmlOptions['empty']);
		}
		if (isset($htmlOptions['options'])) {
			$options = $htmlOptions['options'];
			unset($htmlOptions['options']);
		} else {
            $options = [];
        }
        $key = isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
		if (is_array($selection)) {
			foreach ($selection as $i => $item) {
				if (is_object($item)) $selection[$i] = $item->$key;
			}
		} elseif (is_object($selection)) {
			$selection = $selection->$key;
		}
		if (!is_array($listData)) return $content;
		foreach ($listData as $key => $value) {
			if (is_array($value)) {
				if (isset($value['optionValue'])) {
					// For single-level arrays where additional options available
                    $attributes = ['value' => (string)$key, 'encode' => ! $raw];
                    if (!empty($value['optionDisabled'])) $attributes['disabled'] = true;
					if (!is_array($selection) && !strcmp($key, $selection) || is_array($selection) && in_array($key, $selection)) {
						$attributes['selected'] = 'selected';
					}
					if (isset($options[$key])) $attributes = array_merge($attributes, $options[$key]);
					$content .= self::tag('option', $attributes, $raw ? (string)$value['optionValue'] : self::encode((string)$value['optionValue'])) . "\n";
				} else {
					// For multi-level arrays
					$content .= '<optgroup label="' . ($raw ? $key : self::encode($key)) . "\">\n";
                    $dummy = ['options' => $options];
                    if (isset($htmlOptions['encode'])) $dummy['encode'] = $htmlOptions['encode'];
					$content .= self::listOptions($selection, $value, $dummy);
					$content .= '</optgroup>' . "\n";
				}
			} else {
                $attributes = ['value' => (string)$key, 'encode' => ! $raw];
                if (!is_array($selection) && !strcmp($key, $selection) || is_array($selection) && in_array($key, $selection)) {
					$attributes['selected'] = 'selected';
				}
				if (isset($options[$key])) $attributes = array_merge($attributes, $options[$key]);
				$content .= self::tag('option', $attributes, $raw ? (string)$value : self::encode((string)$value)) . "\n";
			}
		}
		if (isset($htmlOptions['key'])) unset($htmlOptions['key']);
		return $content;
	}

	/**
	 * Draws submit button
	 * @param string $label
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function submitButton($label = 'submit', $htmlOptions = [])
    {
        $htmlOptions['type'] = 'submit';
		return self::button($label, $htmlOptions);
	}

	/**
	 * Generates reset button
	 * @param string $label
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function resetButton($label = 'reset', $htmlOptions = [])
    {
        $htmlOptions['type'] = 'reset';
		return self::button($label, $htmlOptions);
	}

	/**
	 * Draws button
	 * @param string $label
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function button($label = 'button', $htmlOptions = [])
    {
        if (!isset($htmlOptions['name'])) {
			if (!array_key_exists('name', $htmlOptions)) $htmlOptions['name'] = self::ID_PREFIX . self::$_count++;
		}

		if (!isset($htmlOptions['type'])) $htmlOptions['type'] = 'button';
		$buttonTag = 'input';
		if (isset($htmlOptions['buttonTag'])) {
			$buttonTag = $htmlOptions['buttonTag'];
			unset($htmlOptions['buttonTag']);
		}

		if ($buttonTag == 'button') {
			if (isset($htmlOptions['value'])) {
				$buttonValue = $htmlOptions['value'];
				unset($htmlOptions['value']);
				unset($htmlOptions['buttonTag']);
			} else {
				$buttonValue = $label;
			}
			return self::tag('button', $htmlOptions, $buttonValue);
		} else {
			if (!isset($htmlOptions['value'])) $htmlOptions['value'] = $label;
			return self::tag('input', $htmlOptions);
		}
	}

	/**
	 * Generates an image tag
	 * @param string $src
	 * @param string $alt
	 * @param array $htmlOptions
	 * @return string
	 */
    public static function image($src, $alt = '', $htmlOptions = [])
    {
        $htmlOptions['src'] = $src;
		$htmlOptions['alt'] = $alt;
		return self::tag('img', $htmlOptions);
	}

	/**
	 * Generates an video tag
	 * @param string $src
	 * @param array $options
	 * Ex.: ['width'=>'560', 'height'=>'350', 'autoplay'=>true, 'allowfullscreen'=>true, 'controls'=>true]
	 * @return string
	 */
    public static function video($src, $options = [])
    {
        $videoHtml = '';

		$srcParts = explode('/', $src);
		$videoId = array_pop($srcParts);

		$htmlOptions = array();
		$width = !empty($options['width']) ? $options['width'] : '560';
		$htmlOptions['width'] = $width;
		$height = !empty($options['height']) ? $options['height'] : '315';
		$htmlOptions['height'] = $height;

		if (preg_match('/(youtube\.|youtu\.)/i', $src)) {
			$autoplayParam = !empty($options['autoplay']) ? '?autoplay=1' : '';
			$htmlOptions['frameborder'] = '0';
			$htmlOptions['allow'] = 'encrypted-media' . (!empty($autoplayParam) ? ' autoplay' : '');
			$htmlOptions['allowfullscreen'] = !empty($options['allowfullscreen']) ? 'allowfullscreen' : null;
			$htmlOptions['src'] = 'https://www.youtube.com/embed/' . $videoId . $autoplayParam;
			$videoHtml = self::openTag('iframe', $htmlOptions) . self::closeTag('iframe');
		} elseif (preg_match('/vimeo\./i', $src)) {
			$autoplayParam = !empty($options['autoplay']) ? '?autoplay=1' : '';
			$htmlOptions['frameborder'] = '0';
			$htmlOptions['webkitallowfullscreen'] = !empty($options['webkitallowfullscreen']) ? 'webkitallowfullscreen' : null;
			$htmlOptions['mozallowfullscreen'] = !empty($options['mozallowfullscreen']) ? 'mozallowfullscreen' : null;
			$htmlOptions['src'] = 'https://player.vimeo.com/video/' . $videoId . $autoplayParam;
			$videoHtml = self::openTag('iframe', $htmlOptions) . self::closeTag('iframe');
		} else {
			$htmlOptions['autoplay'] = !empty($options['autoplay']) ? ' autoplay' : null;
			$htmlOptions['controls'] = !empty($options['controls']) ? ' controls' : null;
			$videoHtml = self::openTag('video', $htmlOptions);
			$videoHtml .= self::tag('source', array('src' => $src, 'type' => 'video/mp4'));
			$videoHtml .= self::closeTag('video');
		}

		if (!empty($htmlOptions)) {
			$videoHtml = self::openTag('iframe', $htmlOptions, false, false) . self::closeTag('iframe');
		}

		return $videoHtml;
	}

	/**
	 * Generates an audio tag
	 * @param string $src
	 * @param array $options
	 * Ex.: ['autoplay'=>true, 'controls'=>true]
	 * @return string
	 */
    public static function audio($src, $options = [])
    {
        $htmlOptions             = [];
        $htmlOptions['autoplay'] = ! empty($options['autoplay']) ? ' autoplay' : null;
        $htmlOptions['controls'] = ! empty($options['controls']) ? ' controls' : null;

        $audioHtml = self::openTag('audio', $htmlOptions);
        $audioHtml .= self::tag('source', ['src' => $src, 'type' => 'audio/mpeg']);
        $audioHtml .= self::closeTag('audio');

        return $audioHtml;
	}

	/**
	 * Returns a file size in bytes from the given string
	 * @param mixed $fileSize
	 */
	public static function convertFileSize($fileSize)
	{
		$return = $fileSize;
		if (!is_numeric($fileSize)) {
			if (stripos($fileSize, 'm') !== false) {
				$return = intval($fileSize) * 1024 * 1024;
			} elseif (stripos($fileSize, 'k') !== false) {
				$return = intval($fileSize) * 1024;
			} elseif (stripos($fileSize, 'g') !== false) {
				$return = intval($fileSize) * 1024 * 1024 * 1024;
			}
		}
		return $return;
	}

	/**
	 * Returns an image width or height in pixels from the given string
	 * @param mixed $fileDimension
	 */
	public static function convertImageDimensions($fileDimension)
	{
		$return = $fileDimension;
		if (!is_numeric($fileDimension)) {
			if (stripos($fileDimension, 'px') !== false) {
				$return = intval($fileDimension);
			}
		}
		return $return;
	}

	/**
	 * Renders escaped hex string
	 * Ex. for link: '<a href="'.CHtml::escapeHex($string).'">...</a>'
	 * @param string $string
	 */
	public static function escapeHex($string)
	{
		$return = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$return .= ($string[$i] == '/') ? $string[$i] : '%' . bin2hex($string[$i]);
		}
		return $return;
	}

	/**
	 * Renders escaped hex entity string
	 * Ex. for text: '<a href="...">'.CHtml::escapeHexEntity($string).'</a>'
	 * @param string $string
	 */
	public static function escapeHexEntity($string)
	{
		$return = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$return .= '&#x' . bin2hex($string[$i]) . ';';
		}
		return $return;
	}

	/**
	 * Generates JavaScript code with specified client changes
	 * @param string $event
	 * @param array $htmlOptions
	 */
	protected static function _clientChange($event, &$htmlOptions)
	{
		if (!isset($htmlOptions['submit'])) {
			return;
		}

		$clientScript = A::app()->getClientScript();
		$request = A::app()->getRequest();
		$handler = '';

		if (isset($htmlOptions['id'])) {
			$id = $htmlOptions['id'];
		} else {
			$id = $htmlOptions['id'] = isset($htmlOptions['name']) ? $htmlOptions['name'] : self::ID_PREFIX . self::$_count++;
		}

		$csrf = isset($htmlOptions['csrf']) ? (bool)$htmlOptions['csrf'] : false;

		// Add csrf token key if needed
		if ($request->getCsrfValidation() && $csrf) {
			$handler .= '$(this).closest("form").append(\'<input type="hidden" name="' . $request->getCsrfTokenKey() . '" value="' . $request->getCsrfTokenValue() . '">\');';
		}

		if (!empty($htmlOptions['submit']) && !is_bool($htmlOptions['submit'])) {
			$handler .= $htmlOptions['submit'];
		}

		/// Check? document.forms["'.$formName.'"].submit();';
		$handler .= '$(this).closest("form").submit();';

		$clientScript->registerScript('Apphp.CHtml.#' . $id, "$('body').on('$event','#$id',function(){{$handler}});");
		/// Check? $clientScript->registerScript('Apphp.CHtml.#'.$id, "$('#$id').on('$event', function(){{$handler}});");

		unset($htmlOptions['submit']);
	}

	/**
	 * Renders the HTML tag attributes
	 * @param string $htmlOptions
	 */
	private static function _renderAttributes($htmlOptions)
	{
		// Attributes that looks like attribute = "attribute"
		static $specialAttributes = [
			'checked' => 1,
			'declare' => 1,
			'defer' => 1,
			'disabled' => 1,
			'ismap' => 1,
			'multiple' => 1,
			'nohref' => 1,
			'noresize' => 1,
			'readonly' => 1,
			'selected' => 1,
			'autofocus' => 1,
		];

        if ($htmlOptions === []) {
            return '';
        }

        $output = '';
		$encode = false;

		if (isset($htmlOptions['encode'])) {
			$encode = (bool)$htmlOptions['encode'];
			unset($htmlOptions['encode']);
		}

		if (isset($htmlOptions['id']) && $htmlOptions['id'] === false) unset($htmlOptions['id']);
		if (isset($htmlOptions['href']) && $htmlOptions['href'] === false) unset($htmlOptions['href']);
		if (isset($htmlOptions['class']) && $htmlOptions['class'] == '') unset($htmlOptions['class']);
		if (isset($htmlOptions['style']) && $htmlOptions['style'] == '') unset($htmlOptions['style']);
		if (isset($htmlOptions['showAlways'])) unset($htmlOptions['showAlways']);

		if (is_array($htmlOptions)) {
			foreach ($htmlOptions as $name => $value) {
				if (isset($specialAttributes[$name])) {
					if ($value) $output .= ' ' . $name . '="' . $name . '"';
				} elseif ($value !== null) {
					$output .= ' ' . $name . '="' . (($encode) ? self::encode($value) : $value) . '"';
				}
			}
		}

		return $output;
	}

}

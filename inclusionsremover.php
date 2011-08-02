<?php
/*
* Author: Hut32
* WebSite: http://hut32.blogspot.com/
* Plugin is released under GNU/GPL license (http://www.gnu.org/copyleft/gpl.html)
*  
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemInclusionsremover extends JPlugin
{ 
	function plgSystemInclusionsremover(&$subject, $config)
	{
		parent::__construct($subject, $config); 

		$this->loadLanguage();
	}
	
	function onAfterDispatch()
	{
		$mainframe =& JFactory::getApplication();
		if ($mainframe->isAdmin()) return ;
		
		$document =& JFactory::getDocument();
		$doctype = $document->getType();
		
		if ($doctype !== 'html')
			return;
		
		$headData = $document->getHeadData();

		$headData['scripts'] = $this->getUpdatedInclusions($headData['scripts'], 'jsInclusions');
		$headData['styleSheets'] = $this->getUpdatedInclusions($headData['styleSheets'], 'cssInclusions');

		$document->setHeadData($headData);
	}
	
	function getUpdatedInclusions($inclusions, $paramName)
	{
		if (!is_array($inclusions)) return $inclusions;
		
		$files = $this->getFilesForRemoving($paramName);
		if (count($files) == 0) return $inclusions;

		
		foreach ($inclusions as $incFile => $value)
		{
			$uIncFile = $this->_updatePath($incFile);
			foreach ($files as $file)
			{
				if (strpos($uIncFile, $file) !== false) unset($inclusions[$incFile]);
			}
		}

		return $inclusions;
	}
	
	function getFilesForRemoving($paramName)
	{
		$files = array();
		
		$filesStr = $this->params->get($paramName, '');
		$tempFiles = split("[\n|\r]", $filesStr);//explode("\n", $filesStr);
		
		foreach ($tempFiles as $file)
		{
			$file = trim($file);
			if (!empty($file)) $files[] = $this->_updatePath($file);
		}

		return $files;
	}
	
	function _updatePath($file)
	{
		return str_replace('\\', '/', $file);
	}
}
?>
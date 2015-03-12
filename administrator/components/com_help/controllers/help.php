<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Help\Controllers;

use Hubzero\Component\AdminController;

/**
 * Help controller class
 */
class Help extends AdminController
{
	/**
	 * Display Help Article Pages
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get the page we are trying to access
		$page      = \JRequest::getWord('page', '');
		$component = \JRequest::getWord('component', 'com_help');
		$extension = \JRequest::getWord('extension', '');

		if ($component == $this->_option && !\JRequest::getString('tmpl', '') && !$page)
		{
			$this->view
				->set('components', self::getComponents())
				->setLayout('overview')
				->display();
			return;
		}

		$page = $page ?: 'index';

		// Force help template
		\JRequest::setVar('tmpl', 'help');

		$tpl = \JFactory::getApplication()->getTemplate();
		$lng = \JFactory::getLanguage()->getTag();

		$paths = array();
		// Template override help page
		$paths[] = JPATH_ADMINISTRATOR . DS . 'templates' . DS . $tpl . DS .  'html' . DS . $component  . DS . 'help' . DS . $lng . DS . $page . '.phtml';
		$paths[] = JPATH_ADMINISTRATOR . DS . 'templates' . DS . $tpl . DS .  'html' . DS . 'plg_' . str_replace('com_', '', $component) . '_' . $page . DS . 'help' . DS . $lng . DS . 'index.phtml';

		// Path to help page
		if (file_exists(JPATH_SITE . DS . 'components' . DS . $component . DS . 'admin' . DS . 'help'))
		{
			$paths[] = JPATH_SITE . DS . 'components' . DS . $component . DS . 'admin' . DS . 'help' . DS . $lng . DS . $page . '.phtml';
		}
		else
		{
			$paths[] = JPATH_ADMINISTRATOR . DS . 'components' . DS . $component . DS . 'help' . DS . $lng . DS . $page . '.phtml';
		}
		$paths[] = JPATH_ADMINISTRATOR . DS . 'plugins' . DS . str_replace('com_', '', $component) . DS . $page . DS . 'help' . DS . $lng . DS . 'index.phtml';

		// If we have an extension
		if (isset($extension) && $extension != '')
		{
			$paths[] = JPATH_ADMINISTRATOR . DS . 'plugins' . DS . str_replace('com_', '', $component) . DS . $extension . DS . 'help' . DS . $lng . DS . $page . '.phtml';
			$paths[] = JPATH_ADMINISTRATOR . DS . 'templates' . DS . $tpl . DS .  'html' . DS . 'plg_' . str_replace('com_', '', $component) . '_' . $extension . DS . 'help' . DS . $lng . DS . $page . '.phtml';
		}

		// Store final page
		$finalHelpPage = '';

		// Determine path for help page, check template first
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$finalHelpPage = $path;
				break;
			}
		}

		// Var to hold content
		$this->view->content = '';

		// If we have an existing pge
		if ($finalHelpPage != '')
		{
			ob_start();
			require_once($finalHelpPage);
			$this->view->content = ob_get_contents();
			ob_end_clean();
		}
		else if (isset($component) && $component != '' && $page == 'index')
		{
			// Get list of component pages
			$pages[] = $this->helpPagesForComponent($component);

			// Display page
			$this->view->content = $this->displayHelpPageIndexForPages($pages, 'h2');
		}
		else
		{
			// Raise error to avoid security bug
			\JError::raiseError(404, \JText::_('COM_HELP_PAGE_NOT_FOUND'));
		}

		// Set vars for views
		$this->view->modified  = filemtime($finalHelpPage);
		$this->view->component = $component;
		$this->view->extension = $extension;
		$this->view->page      = $page;

		// Display
		$this->view->display();
	}

	/**
	 * Get array of components
	 *
	 * @param   boolean  $authCheck
	 * @return  array
	 * @since   1.3.2
	 */
	public static function getComponents($authCheck = true)
	{
		// Initialise variables.
		$lang   = \JFactory::getLanguage();
		$user   = \JFactory::getUser();
		$db     = \JFactory::getDbo();
		$query  = $db->getQuery(true);
		$result = array();
		$langs  = array();

		// Prepare the query.
		$query->select('m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element');
		$query->from('#__menu AS m');

		// Filter on the enabled states.
		$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
		$query->where('m.client_id = 1');
		$query->where('e.enabled = 1');
		$query->where('m.id > 1');

		// Order by lft.
		$query->order('m.lft');

		$db->setQuery($query);
		// component list
		$components	= $db->loadObjectList();

		// Parse the list of extensions.
		foreach ($components as &$component)
		{
			// Trim the menu link.
			$component->link = trim($component->link);

			if ($component->parent_id == 1)
			{
				// Only add this top level if it is authorised and enabled.
				if ($authCheck == false || ($authCheck && $user->authorise('core.manage', $component->element)))
				{
					// Root level.
					$result[$component->id] = $component;
					if (!isset($result[$component->id]->submenu))
					{
						$result[$component->id]->submenu = array();
					}

					// If the root menu link is empty, add it in.
					if (empty($component->link))
					{
						$component->link = 'index.php?option=' . $component->element;
					}

					if (!empty($component->element))
					{
						// Load the core file then
						// Load extension-local file.
						$lang->load($component->element.'.sys', JPATH_BASE, null, false, false)
						|| $lang->load($component->element.'.sys', JPATH_ADMINISTRATOR . '/components/' . $component->element, null, false, false)
						|| $lang->load($component->element.'.sys', JPATH_BASE, $lang->getDefault(), false, false)
						|| $lang->load($component->element.'.sys', JPATH_ADMINISTRATOR . '/components/' . $component->element, $lang->getDefault(), false, false);
					}
					$component->text = $lang->hasKey($component->title) ? \JText::_($component->title) : $component->alias;
				}
			}
			else
			{
				// Sub-menu level.
				if (isset($result[$component->parent_id]))
				{
					// Add the submenu link if it is defined.
					if (isset($result[$component->parent_id]->submenu) && !empty($component->link))
					{
						$component->text = $lang->hasKey($component->title) ? \JText::_($component->title) : $component->alias;
						$result[$component->parent_id]->submenu[] = &$component;
					}
				}
			}
		}

		return \JArrayHelper::sortObjects($result, 'text', 1, true, $lang->getLocale());
	}

	/**
	 * Get array of help pages for component
	 *
	 * @param   string  $component  Component to get pages for
	 * @return  array
	 */
	private function helpPagesForComponent($component)
	{
		// Get component name from database
		$sql = "SELECT `name` FROM `#__extensions` WHERE `type`=" . $this->database->quote('component') . " AND `element`=" . $this->database->quote($component) . " AND `enabled`=1";
		$this->database->setQuery($sql);
		$name = $this->database->loadResult();

		// Make sure we have a component
		if ($name == '')
		{
			return array();
		}

		// Path to help pages
		$helpPagesPath = JPATH_ADMINISTRATOR . DS . 'components' . DS . $component . DS . 'help' . DS . \JFactory::getLanguage()->getTag();

		// Make sure directory exists
		$pages = array();
		if (is_dir($helpPagesPath))
		{
			jimport('joomla.filesystem.folder');

			// Get help pages for this component
			$pages = \JFolder::files($helpPagesPath , '.phtml');
		}

		// Return pages
		return array(
			'name'   => $name,
			'option' => $component,
			'pages'  => $pages
		);
	}

	/**
	 * Get array of help pages for component
	 *
	 * @param   array   $componentAndPages  Component info and corresponding help pages
	 * @param   string  $headingLevel       Leading level for component separation
	 * @return  array
	 */
	private function displayHelpPageIndexForPages($componentAndPages, $headingLevel = 'h1')
	{
		// Var to hold content
		$content = '';

		// Loop through each component and pages group passed in
		foreach ($componentAndPages as $component)
		{
			$name = ucfirst(str_replace('com_', '',$component['name']));

			// Build content to return
			$content .= '<' . $headingLevel . '>' . \JText::sprintf('COM_HELP_COMPONENT_HELP', $name) . '</' . $headingLevel . '>';

			// Make sure we have pages
			if (count($component['pages']) > 0)
			{
				$content .= '<p>' . \JText::sprintf('COM_HELP_PAGE_INDEX_EXPLANATION', $name) . '</p>';
				$content .= '<ul>';
				foreach ($component['pages'] as $page)
				{
					$name = str_replace('.phtml', '', $page);
					$url  = \JRoute::_('index.php?option=com_help&component=' . $component['option'] . '&page=' . $name);

					$content .= '<li><a href="' . $url . '">' . ucwords(str_replace('_', ' ', $name)) . '</a></li>';
				}
				$content .= '</ul>';
			}
			else
			{
				$content .= '<p>' . \JText::_('COM_HELP_NO_PAGES_FOUND') . '</p>';
			}
		}

		return $content;
	}
}
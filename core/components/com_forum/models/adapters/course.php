<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models\Adapters;

use Hubzero\Utility\Str;
use Request;

require_once __DIR__ . DS . 'base.php';

/**
 * Adapter class for a forum post link for course forum
 */
class Course extends Base
{
	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array(
		'option' => 'com_courses',
	);

	/**
	 * Constructor
	 *
	 * @param   integer  $scope_id  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($scope_id=0)
	{
		$this->set('scope_id', $scope_id);

		include_once \Component::path('com_courses') . DS . 'models' . DS . 'courses.php';

		$offering = \Components\Courses\Models\Offering::getInstance($this->get('scope_id'));
		$course   = \Components\Courses\Models\Course::getInstance($offering->get('course_id'));

		$this->_segments['gid']      = $course->get('alias');
		$this->_segments['offering'] = $offering->alias();
		$this->_segments['active']   = 'discussions';
		if (Request::getWord('active') == 'outline')
		{
			$this->_segments['active']   = 'outline';
		}

		$this->_name = Str::truncate($course->get('alias'), 50) . ': ' . Str::truncate($offering->get('alias'), 50);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function build($type='', $params=null)
	{
		$segments = $this->_segments;

		if ($this->_segments['active'] == 'outline')
		{
			if ($this->get('section'))
			{
				$segments['unit'] = $this->get('section');
			}
			if ($this->get('category'))
			{
				$segments['b'] = $this->get('category');
			}
		}

		$anchor = '';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				return $this->_base . '?' . (string) $this->_build($segments);
			break;

			case 'edit':
				if ($this->_segments['active'] == 'outline')
				{
					$segments['active']   = 'discussions';
					unset($segments['unit']);
					unset($segments['b'] );
				}
				if ($this->get('thread'))
				{
					$segments['action'] = 'edit';
					$segments['post'] = $this->get('post');
					$segments['thread'] = $this->get('thread');
				}
				else
				{
					$segments['action'] = 'edit';
				}
			break;

			case 'delete':
				if ($this->get('thread'))
				{
					$segments['action'] = 'delete';
					$segments['post'] = $this->get('post');
					$segments['thread'] = $this->get('thread');
				}
				else
				{
					$segments['task'] = 'delete';
				}
			break;

			case 'new':
			case 'newthread':
				if ($this->get('thread'))
				{
					unset($segments['c']);
				}
				$segments['task'] = 'new';
			break;

			case 'download':
				$segments['active']   = 'discussions';
				$segments['unit'] = 'download';
				$segments['b'] = $this->get('thread');
				$segments['file'] = '';
			break;

			case 'reply':
				$segments['thread'] = $this->get('thread');
				$segments['reply'] = $this->get('post');
			break;

			case 'anchor':
				if ($this->get('post'))
				{
					$anchor = '#c' . $this->get('post');
				}
			break;

			case 'abuse':
				return 'index.php?option=com_support&task=reportabuse&category=forum&id=' . $this->get('post') . '&parent=' . $this->get('parent');
			break;

			case 'permalink':
			default:

			break;
		}

		if (is_string($params))
		{
			$params = str_replace('&amp;', '&', $params);

			if (substr($params, 0, 1) == '#')
			{
				$anchor = $params;
			}
			else
			{
				if (substr($params, 0, 1) == '?')
				{
					$params = substr($params, 1);
				}
				parse_str($params, $parsed);
				$params = $parsed;
			}
		}

		$segments = array_merge($segments, (array) $params);

		return $this->_base . '?' . (string) $this->_build($segments) . (string) $anchor;
	}
}

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Components\Courses\Tables;

/**
 * Content based asset handler (i.e. things like notes, wiki, html, etc...)
 */
class Content extends Handler
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
		'action_message' => 'As textual content',
		'responds_to'    => array('text')
	);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		// Include needed files
		require_once dirname(__DIR__) . DS . 'asset.php';
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.association.php';

		if (!empty($this->asset['tool-alias']))
		{
			$this->asset['url'] = '/tools/' . $this->asset['tool-alias'] . '/invoke';
		}

		// Create our asset table object
		$asset = new \Components\Courses\Models\Asset();

		// Grab the incoming content
		$content = Request::getString('content', '', 'default');

		// Get everything ready to store
		// Check if vars are already set (i.e. by a sub class), before setting them here
		$asset->set('title', ((!empty($this->asset['title']))        ? $this->asset['title']        : strip_tags(substr($content, 0, 25))));
		$asset->set('type', ((!empty($this->asset['type']))         ? $this->asset['type']         : 'text'));
		$asset->set('subtype', ((!empty($this->asset['subtype']))      ? $this->asset['subtype']      : 'content'));
		$asset->set('content', ((!empty($this->asset['content']))      ? $this->asset['content']      : $content));
		$asset->set('url', ((!empty($this->asset['url']))          ? $this->asset['url']          : ''));
		$asset->set('graded', ((!empty($this->asset['graded']))       ? $this->asset['graded']       : 0));
		$asset->set('grade_weight', ((!empty($this->asset['grade_weight'])) ? $this->asset['grade_weight'] : ''));
		$asset->set('created', \Date::toSql());
		$asset->set('created_by', App::get('authn')['user_id']);
		$asset->set('course_id', Request::getInt('course_id', 0));
		$asset->set('state', 0);

		// Check whether asset should be graded
		if ($graded = Request::getInt('graded', false))
		{
			$asset->set('graded', $graded);
			$asset->set('grade_weight', 'homework');
		}

		// Save the asset
		if (!$asset->store())
		{
			return array('error' => 'Asset save failed');
		}

		// If we're saving progress calculation var
		if ($progress = Request::getInt('progress_factors', false))
		{
			$asset->set('progress_factors', array('asset_id' => $asset->get('id'), 'section_id' => Request::getInt('section_id', 0)));
			$asset->store();
		}

		// Create asset assoc object
		$assocObj = new Tables\AssetAssociation($this->db);

		$this->assoc['asset_id'] = $asset->get('id');
		$this->assoc['scope']    = Request::getCmd('scope', 'asset_group');
		$this->assoc['scope_id'] = Request::getInt('scope_id', 0);

		// Save the asset association
		if (!$assocObj->save($this->assoc))
		{
			return array('error' => 'Asset association save failed');
		}

		// Get the url to return to the page
		$course_id      = Request::getInt('course_id', 0);
		$offering_alias = Request::getCmd('offering', '');
		$course         = new \Components\Courses\Models\Course($course_id);
		$course->offering($offering_alias);

		$url = Route::url($course->offering()->link() . '&asset=' . $asset->get('id'));
		$url = str_replace('/api', '', $url);
		$url = rtrim(Request::root(), '/') . '/' . ltrim($url, '/');

		$files = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'asset_state'    => $asset->get('state'),
			'scope_id'       => $this->assoc['scope_id']
		);

		$return_info = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'course_id'      => $asset->get('course_id'),
			'offering_alias' => $offering_alias,
			'scope_id'       => $this->assoc['scope_id'],
			'files'          => array($files)
		);

		// Return info
		return array('assets' => $return_info);
	}

	/**
	 * Save method for this handler
	 * // @FIXME: reduce code duplication here
	 *
	 * @return array of assets created
	 **/
	public function save()
	{
		// Include needed files
		require_once dirname(__DIR__) . DS . 'asset.php';

		// Create our asset object
		$id    = Request::getInt('id', null);
		$asset = new \Components\Courses\Models\Asset($id);

		// Grab the incoming content
		$content = Request::getString('content', '');
		if (isset($this->asset['tool-alias']))
		{
			$toolAlias = $this->asset['tool-alias'];
			if (!empty($toolAlias))
			{
				$asset->set('url', '/tools/' . $toolAlias . '/invoke');
			}
			else
			{
				$asset->set('url', '');
			}
		}

		// Get everything ready to store
		// Check if vars are already set (i.e. by a sub class), before setting them here
		$asset->set('title', ((!empty($this->asset['title']))   ? $this->asset['title']   : strip_tags(substr($content, 0, 25))));
		$asset->set('type', ((!empty($this->asset['type']))    ? $this->asset['type']    : 'text'));
		$asset->set('subtype', ((!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'content'));
		$asset->set('content', ((!empty($this->asset['content'])) ? $this->asset['content'] : $content));

		// If we have a state coming in as an int
		if ($graded = Request::getInt('graded', false))
		{
			$asset->set('graded', $graded);
			// By default, weight asset as a 'homework' type
			$grade_weight = $asset->get('grade_weight');
			if (empty($grade_weight))
			{
				$asset->set('grade_weight', 'homework');
			}
			else
			{
				$asset->set('grade_weight', $grade_weight);
			}
		}
		elseif ($graded = Request::getInt('edit_graded', false))
		{
			$asset->set('graded', 0);
		}

		// If we're saving progress calculation var
		if ($progress = Request::getInt('progress_factors', false))
		{
			$asset->set('progress_factors', array('asset_id'=>$asset->get('id'), 'section_id'=>Request::getInt('section_id', 0)));
		}
		elseif (Request::getInt('edit_progress_factors', false))
		{
			$asset->set('section_id', Request::getInt('section_id', 0));
			$asset->set('progress_factors', 'delete');
		}

		// Save the asset
		if (!$asset->store())
		{
			return array('error' => 'Asset save failed');
		}

		$scope_id          = Request::getInt('scope_id', null);
		$original_scope_id = Request::getInt('original_scope_id', null);
		$scope             = Request::getCmd('scope', 'asset_group');

		// Only worry about this if scope id is changing
		if (!is_null($scope_id) && !is_null($original_scope_id) && $scope_id != $original_scope_id)
		{
			// Create asset assoc object
			require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.association.php';
			$assoc = new Tables\AssetAssociation($this->db);

			if (!$assoc->loadByAssetScope($asset->get('id'), $original_scope_id, $scope))
			{
				return array('error' => 'Failed to load asset association');
			}

			// Save the asset association
			if (!$assoc->save(array('scope_id'=>$scope_id)))
			{
				return array('error' => 'Asset association save failed');
			}
		}

		// Get the url to return to the page
		$course_id      = Request::getInt('course_id', 0);
		$offering_alias = Request::getCmd('offering', '');
		$course         = new \Components\Courses\Models\Course($course_id);
		$course->offering($offering_alias);

		$url = Route::url($course->offering()->link() . '&asset=' . $asset->get('id'));
		$url = rtrim(str_replace('/api', '', Request::root()), '/') . '/' . ltrim($url, '/');

		$files = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'asset_state'    => $asset->get('state'),
			'scope_id'       => $scope_id,
		);

		$return_info = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'course_id'      => $asset->get('course_id'),
			'offering_alias' => $offering_alias,
			'scope_id'       => $scope_id,
			'files'          => array($files)
		);

		// Return info
		return array('assets' => $return_info);
	}
}

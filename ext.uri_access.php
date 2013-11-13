<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * URI access Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Darren Miller
 * @link
 */

class Uri_access_ext {

	public $settings 		= array();
	public $description		= 'Establishes if we\'re allowed to be at the current URI';
	public $docs_url		= '';
	public $name			= 'URI access';
	public $settings_exist	= 'n';
	public $version			= '1.0';

	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->settings = $settings;
	}

    // ----------------------------------------------------------------------

	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();

		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'sessions_end',
			'hook'		=> 'sessions_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		ee()->db->insert('extensions', $data);

	}

	// ----------------------------------------------------------------------

	/**
	 * sessions_end
	 *
	 * @param
	 * @return
	 */
	public function sessions_end($session)
	{
        $compareUri = (ee()->config->item('uri_access_compare_uri')) ? ee()->config->item('uri_access_compare_uri') : ee()->uri->uri_string();

        $checker = new UriAccess\Checker( ee()->config->item('uri_access_map') );

        if(!$checker->checkAllowed($compareUri,
                                   $session->userdata('group_id'),
                                   $session->userdata('group_id'),$session->userdata('member_id'))) {

            ee()->functions->redirect(ee()->config->item('uri_access_denied_url'), FALSE, 403);
        }
	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}

	// ----------------------------------------------------------------------
}

/* End of file ext.Uri_access.php */
/* Location: /system/expressionengine/third_party/Uri_access/ext.Uri_access.php */
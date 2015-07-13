<?php

/**
 * Beams controller.
 *
 * @category   apps
 * @package    beams
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2015 Marine VSAT
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/beams/
 */

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Beams controller.
 *
 * @category   apps
 * @package    beams
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2015 Marine VSAT
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/beams/
 */

class Beams extends ClearOS_Controller
{
	/**
	 * Beams server overview.
	 */

	function index()
	{
		// Load libraries
		//---------------

		$this->lang->load('beams');

		// Load views
		//-----------

        $controllers = array('beams/settings', 'beams/satellites');

        $options = array();
        $this->page->view_controllers($controllers, lang('beams_app_name'), $options);
	}
}

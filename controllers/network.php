<?php

/**
 * Network controller.
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
 * Network controller.
 *
 * @category   apps
 * @package    beams
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2015 Marine VSAT
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/beams/
 */

class Network extends ClearOS_Controller
{
    /**
     * Default controller.
     *
     * @return view
     */
    function index()
    {
        // Load libraries
        //---------------

        $this->load->library('beams/Beams');
		$this->lang->load('beams');

        $data['interfaces'] = $this->beams->get_interface_status();
        $this->page->view_form('beams/network', $data, lang('beams_network_status'));
    }

    /**
     * Edit nickname of NIC controller.
     *
     * @return view
     */
    function edit($nic)
    {
        // Load libraries
        //---------------

        $this->load->library('beams/Beams');
		$this->lang->load('beams');

        if ($this->session->userdata('username') != 'root') {
            $this->page->set_message(lang('beams_access_denied'));
            redirect('/beams');
            return;
        }

        $this->form_validation->set_policy('nickname', 'beams/Beams', 'validate_nickname', TRUE);

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && ($form_ok === TRUE)) {

            try {
                $this->beams->set_nickname($this->input->post('name'), $this->input->post('nickname'));
                redirect('/beams/network');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }
        $interfaces = $this->beams->get_interface_status();
        $data['name'] = $nic;
        $data['nic'] = $interfaces[$nic];
        $this->page->view_form('beams/nickname', $data, lang('beams_nickname'));
    }

    /**
     * Network option summary controller.
     *
     * @return view
     */
    function summary()
    {
        // Load libraries
        //---------------

        $this->load->library('beams/Beams');
		$this->lang->load('beams');

        if ($this->session->userdata('username') != 'root') {
            $this->page->set_message(lang('beams_access_denied'));
            redirect('/beams');
            return;
        }

        $data['configs'] = $this->beams->get_interface_configs();
        $this->page->view_form('beams/network_configs', $data, lang('beams_network_configs'));
    }

    /**
     * Toggle network controller.
     *
     * @param string $name name of NIC
     *
     * @return void 
     */
    function toggle($name)
    {
        // Load libraries
        //---------------

        $this->load->library('beams/Beams');
		$this->lang->load('beams');

        /*
        if ($this->session->userdata('username') != 'root') {
            $this->page->set_message(lang('beams_access_denied'));
            redirect('/beams');
            return;
        }
        */

        $this->beams->toggle_nic($name);
        $this->page->set_message(lang('beams_nic_toggle_complete'), 'info');
        redirect('/beams/network');
        return;
    }
}

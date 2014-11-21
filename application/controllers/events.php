<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @property Event $event
 */

class Events extends EV_Controller {
    function __construct() {
        parent::__construct();
        //$this->load->model('Event');
    }

    public function index() {
        //$r = $this->event-search();
        //$this->load->view('events');
	}
}
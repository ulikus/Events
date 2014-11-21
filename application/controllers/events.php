<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @property Event $Event
 */

class Events extends EV_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('Event');
        $this->load->helper('form');
        $this->load->library('session');
    }

    public function index() {
        $data = array();
        $this->load->view('events', $data);
	}

    public function results($page = 0) {

        if (count($_POST) > 3) {
            $this->session->set_userdata('search_results', $_POST);
        } else {
            $_POST = $this->session->userdata('search_results');
        }

        if ($order = $this->input->get('order', TRUE)) {
            $this->session->set_userdata('search_order', $order);
        } else {
            $order = $this->session->userdata('search_order');
        }

        $price_from = $this->input->post('price_from', TRUE);
        $price_to = $this->input->post('price_to', TRUE);
        $date = $this->input->post('date', TRUE);
        $text = $this->input->post('text', TRUE);

        $this->load->library('pagination');
        $config_pagination['base_url'] = '/events/results/';
        $config_pagination['per_page'] = 20;
        $config_pagination['total_rows'] = $this->Event->search_count($price_from, $price_to, $date, $text);
        $this->pagination->initialize($config_pagination);

        $data = array(
            'pagination' => $this->pagination->create_links(),
            'events' => $this->Event->search($price_from, $price_to, $date, $text, $page, $config_pagination['per_page'], $order),
            'order' => $order
        );
        $this->load->view('results', $data);
    }
}
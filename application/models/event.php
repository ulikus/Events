<?php


class Event extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function  _search_conditions($price_from, $price_to, $date, $text) {
        if ($price_from > 0) {
            $this->db->where('ep.usd >', $price_from);
        }
        if ($price_to > 0 && $price_to > $price_from) {
            $this->db->where('ep.usd <', $price_to);
        }
        if ($text !='') {
            $this->db->where('MATCH(ep.package_name,ep.package_description) AGAINST("'.$text.'")');
        }
        if ($date != '') {
            $this->db->where('e.event_begin_date <', $date);
            $this->db->where('e.event_end_date >', $date);
        }
        $this->db->select('e.*, ep.*');
        $this->db->select('(SELECT min(usd) FROM event_packages WHERE event_id = e.id) as min_price');
        $this->db->select('(SELECT count(usd) FROM event_packages WHERE event_id = e.id) as total');
        $this->db->join('event_packages ep', 'ep.event_id=e.id');

        // dont work for some reason?
        //$this->db->join('(SELECT event_id, min(usd) as min_price, count(id) as total FROM event_packages) AS epl', 'e.id = epl.event_id', 'LEFT OUTER');
    }

    function search($price_from, $price_to, $date, $text, $offset = 0, $limit = 20, $order = 'date_desc') {
        if ($order) {
            list($order_by, $order_type) = explode('_', $order);
            if (in_array($order_by, array('price','date')) && in_array($order_type, array('desc','asc')) ) {
                $order_assoc = array('date' => 'e.event_begin_date', 'price' =>'ep.usd');
                $this->db->order_by($order_assoc[$order_by], $order_type);
            }
        }

        $this->_search_conditions($price_from, $price_to, $date, $text);

        $events = $this->db->get('events e',$limit, $offset);
        return $events->result_array();
    }

    function search_count($price_from, $price_to, $date, $text) {
        $this->_search_conditions($price_from, $price_to, $date, $text);

        return $this->db->count_all_results('events e');
    }

} 
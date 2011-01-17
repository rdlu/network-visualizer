<?php

class Controller_Tools extends Controller {

    public function action_check() {
        if(!isset($_POST['ip'])) throw new Kohana_Exception('Compulsory data not set, must be called with post',$_POST);

        $ip = $_POST['ip'];
        if(Validate::ipOrHostname($ip)) {
	         $realip = Network::getAddress($ip);
            $data = Snmp::instance($realip)->group('linuxManager');
            $json['address'] = $realip;
            $json['data'] = $data;
            $this->request->headers['Content-Type'] = 'application/json';
            if(Request::$is_ajax) $this->request->response = json_encode($json);
            else throw new Kohana_Exception('This controller only accepts AJAX requests',$json);
        } else {
            throw new Kohana_Exception("Invalid format for IP Address $ip");
        }
    }

    public function action_cities() {
        if(!isset($_POST['startsWith'])) throw new Kohana_Exception('Compulsory data not set, must be called with post',$_POST);
        $post = 'Port';
        if(isset($_POST['startsWith'])) $post = (string) $_POST['startsWith'];
        $query['geonames'] = Db::select('city','state')->from('entities')->where('city','like',$post.'%')->group_by('city')->order_by('city','ASC')->execute()->as_array();
        $this->request->headers['Content-Type'] = 'application/json';
        if(Request::$is_ajax) $this->request->response = json_encode($query);
        else throw new Kohana_Exception('This controller only accepts AJAX requests',$query);
    }

    public function action_pie() {
        $this->request->headers['Content-Type'] = 'text/x-component';
        include( DOCROOT.'pie/PIE.htc' );
    }

	public function action_entityStatus($id) {
		$id = (int) $id;
		if(Request::$is_ajax) {
			$this->auto_render = false;
			$dados = Sprig::factory('entity', array("id" => $id))->load();
			$this->request->headers['Content-Type'] = 'text/json';

		}
	}
}

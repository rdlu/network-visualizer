<?php

class Controller_Tools extends Controller {

    public function action_check() {
        if(!isset($_POST['ip'])) throw new Kohana_Exception('Compulsory data not set, must be called with post',$_POST);

        $ip = $_POST['ip'];
        if(Validate::Ip($ip)) {
            $data = Snmp::instance($ip)->group('linuxManager');
            Fire::group('SNMP Fetched Data for '.$ip)->info($data)->groupEnd();
            $json['address'] = $ip;
            $json['data'] = $data;
            $this->request->headers += array('Content-Type'=>'application/json');
            $this->request->response = json_encode($json);
        } else {
            throw new Kohana_Exception('Invalid format for IP Address',array($ip));
        }
    }
}

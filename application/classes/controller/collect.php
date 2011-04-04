<?php

class Controller_Collect extends Controller {
	public function action_id($id=0,$metric=null) {
		$response = 'Received';
	    if($id===0) {
		    $id = $_POST['id'];
	    }

	    if(!$metric) {
		    $metric = $_POST['metric'];
	    }

	    $ip = $_SERVER['REMOTE_ADDR'];

	    if($id!=0) {
		    $process = Sprig::factory('process',array('id'=>$id))->load();

		    if($process->count() == 0) {
			    throw new Kohana_Exception("Process $id does not exist.",$_POST);
		    }
		    $destination = $process->destination->load();
		    $source = $process->source->load();
		    Fire::info($source->as_array());
		    $profile = $process->profile->load();
	       if(true || $source->ipaddress == $ip) {
		       $snmp = Snmp::instance($source->ipaddress);
		       $simple = $snmp->group('agentSimple',array('pid'=>$id));
		       $dip = $simple['ipaddress'];
		       if($destination->ipaddress == $dip) {
			       $data = $snmp->group($metric,array('id'=>$id));
			       $timestamp = Snmp::convertTimestamp($simple['timestamp']);
		          $rrd = Rrd::instance($source->ipaddress,$destination->ipaddress)->update($profile->id,$metric,$data,$timestamp);
		          $destination->updated = $timestamp;
			       $source->updated = $timestamp;
			       $process->updated = $timestamp;
		          $destination->update();
			       $source->update();
			       $process->update();
		          $response = 'Updated';
		       } else {
			       throw new Kohana_Exception("Source IP $ip for id $id on Destination IP $ip does not match the records on DB",$simple);
		       }
	       } else {
		       throw new Kohana_Exception("Requester IP $ip not found in database",$_POST);
	       }

	    } else {
		    throw new Kohana_Exception('Invalid ID in Collect/id',$_POST);
	    }

	    $this->response->body($response);
   }
}

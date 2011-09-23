<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Dynagent extends Controller {

	/*
	 * 
	 * */         

	public function action_collect() {
		echo "OK\n";
		try {
                        $dyndata = ORM::factory('dyndata'); //new Model_DynData();
                        if(isset($_POST['cellid']) && $_POST['cellid'] == '-') $_POST['cellid'] = 0;
			$dyndata->values($_POST)->save();
                        foreach($_POST as $key => $value){
                            Kohana::$log->add(Log::ERROR, time().' : '.$key.' : '.$value);
                        }
		} catch (ORM_Validation_Exception $e) {
			Kohana::$log->add(Log::ERROR,"Failed validation from Windows Agent ".$e->errors());
		}
	}
}

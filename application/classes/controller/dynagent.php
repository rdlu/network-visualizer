<?php

class Controller_Dynagent extends Controller {

	/*
	 * 
	 * */
	public function action_collect() {
		echo "OK\n";
		try {
			$dyndata = new Model_DynData();
			$dyndata->values($_POST)->save();
		} catch (ORM_Validation_Exception $e) {
			Kohana::$log->add(Log::ERROR,"Failed validation from Windows Agent ".$e->errors());
		}
	}
}

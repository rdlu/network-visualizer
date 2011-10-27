<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Dynagent extends Controller {

	/*
	 * 
	 * */         

	public function action_collect() {
		echo "OK\n";
                if($_POST){
                    try {
                            foreach($_POST as $key => $value){
                                Kohana::$log->add(Log::ERROR, time().' : '.$key.' : '.$value."\n");
                            }
                            $dyndata = ORM::factory('dyndata'); //new Model_DynData();
                            if(isset($_POST['cellid']) && $_POST['cellid'] == '-') $_POST['cellid'] = 0;
                            $dyndata->values($_POST)->save();                            
                    } catch (ORM_Validation_Exception $e) {
                            Kohana::$log->add(Log::ERROR,"Failed validation from Windows Agent ".$e);
                    }
                }
                else {
                    Kohana::$log->add(Log::ERROR,"Falha: nenhum dado enviado à Winagent\n");
                }
	}
}

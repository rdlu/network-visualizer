<?php
class Controller_Log extends Controller_Skeleton {

    public $auth_required = 'admin';

    public function before() {
        parent::before();
        $this->template->title .= 'Registros :: ';
    }

	public function action_view()
	{
		$log_dir = APPPATH.'logs';
		$date = $this->request->param('date', date('Y/m/d'));
		if (!file_exists($log_file = $log_dir.DIRECTORY_SEPARATOR.$date.EXT))
		{
			throw new Log_Exception("log file $log_file not exists");
		}
		$logs = Log_Parser::parse_file($log_file);
		$this->template->content = View::factory('log/view')
			->set('logs', $logs);
	}
}

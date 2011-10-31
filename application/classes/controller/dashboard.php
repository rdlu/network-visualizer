<?php defined('SYSPATH') or die('No direct script access.');
/* informações presentes:
 * 
 * entidades:
 *  nome, cidade, ip, status, descrição, versão
 *
 *
 * não é viável consultar cada sonda pela sua versão, portanto irei adicionar isso ao banco de dados
 */


 class Controller_Dashboard extends Controller_Skeleton
{

    public $auth_required = array('login');

	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    public $secure_actions = FALSE;

    public function before() {
        parent::before();
        $this->template->title .= "Sintetização";
    }

    public function after(){
        
            $styles = array(
		'css/dashboard.css' => 'all',
            );            
            $scripts = array(
                    'js/dev/jquery.floatheader.js',
                    'js/dev/dashboard.js'                    
            );
            $this->template->styles = array_merge($this->template->styles, $styles);
            $this->template->scripts = array_merge($scripts, $this->template->scripts);
            parent::after();        	
    }

    public function action_index() {
        
	$processes = Sprig::factory('process')->load(Db::select()->group_by('destination_id'), FALSE);
        $resp = array();
        $sources = null;
        foreach($processes as $process) {
            if($sources === null){
                $sources[] = $process->source->load(Db::select()->limit(1));
            }
            $destinations[] = $process->destination->load(Db::select()->group_by('state'));
        }
        
        foreach($sources as $source){
            foreach($destinations as $destination) {
                //Resultados do MemCached
                $pair = Pair::instance($source->id,$destination->id);
                $system = Sonda::instance($destination->id)->getCachedVersion();
                //var_dump($system); die();
                //$resultss = $pair->lastResults();
                $resFromMemCache[] = array('source' => $source->id, 
                                           'destination' => array(
                                               'id' => $destination->id,
                                               'name' => $destination->name,
                                               'city' => $destination->city,
                                               'ipaddress' => $destination->ipaddress,
                                               'status' => $destination->status,
                                               'address' => $destination->address,
                                               'addressnum' => $destination->addressnum,
                                               'district' => $destination->district,
                                               'state' => $destination->state,
                                               'updated' => $destination->updated,
                                               'system' => $system
                                           ),
                                           'results' => Kohana_Cache::instance('memcache')->get("$source->id-$destination->id")
                );
               
                //$pair = Pair::instance($source->id,$destination->id);
                //$resultss[] = $pair->lastResults();
            }

            

             if(!$this->request->is_ajax()){
                    //var_dump($resFromMemCache);
                    $view = View::factory('dashboard/index')->bind('medicoes', $resFromMemCache);
                    $this->template->content = $view;
                }
                else {
                    $this->response->body(json_encode($resFromMemCache));
                }
        }
    }
}
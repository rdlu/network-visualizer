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
        
            //$styles = array(
		//'css/map.css' => 'all',
            //);            
            $scripts = array(
                    'js/dev/jquery.floatheader.js',
                    'js/dev/dashboard.js'                    
            );
            //$this->template->styles = array_merge($styles, $this->template->styles);
            $this->template->scripts = array_merge($scripts, $this->template->scripts);
            parent::after();
        	
    }

    public function action_index() {      
        
	$processes = Sprig::factory('process')->load(Db::select()->group_by('destination_id'), FALSE);
        $resp = array();
        foreach($processes as $process) {
            $sources[] = $process->source->load();
            $destinations[] = $process->destination->load();
        }
        
        foreach($sources as $source){
            foreach($destinations as $destination) {
                //Resultados do MemCached
                $pair = Pair::instance($source->id,$destination->id);
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
                                               'updated' => $destination->updated
                                           ),
                                           'results' => Kohana_Cache::instance('memcache')->get("$source->id-$destination->id")
                );
                if(!$this->request->is_ajax()){
                    //var_dump($resFromMemCache);
                    $view = View::factory('dashboard/index')->bind('medicoes', $resFromMemCache);
                    $this->template->content = $view;
                }
                else {
                    $this->response->body(json_encode($resFromMemCache));
                }
                //$pair = Pair::instance($source->id,$destination->id);
                //$resultss[] = $pair->lastResults();
            }
        }       
    }
}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Storage extends CI_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        
    }

    public function index() {
       $data = array(
            'pageId'    => 'storage',
            'pageClass' => 'storage',
            'title'     => 'Local Storage'
        );
        $this->template->load('default', 'storage/index', $data);
    }
    
}

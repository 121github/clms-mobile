<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * Make a directory if it doesn't already exist.
     * 
     * @param string $path
     * @return boolean
     */
    public function makeDir($path) {
        return is_dir($path) || mkdir($path);
    }
    
    /**
     *
     * Get all files within the folder relating to the company urn.
     * 
     * @param int $urn
     * @return array
     */
    public function get_lead_docs($urn) {
        return get_dir_file_info($_SERVER['DOCUMENT_ROOT'] . '\docs\\' . $urn);
    }

}
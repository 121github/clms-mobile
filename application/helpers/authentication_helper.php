<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('user_auth_check') )
{
    function user_auth_check()
    {
        //unset($_SESSION['login']);
        if (empty($_SESSION['login'])) 
        { 
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo 'Timeout';
                exit;
            }
            redirect(base_url() . 'index.php/user/login');
        }
        return true;
    }   
}


/* End of file authentication_helper.php */
/* Location: ./application/helpers/authentication_helper.php */
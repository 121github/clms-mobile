<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class handles all file related functionality
 */
class File extends CI_Controller
{

    private $_config = array(
        'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx|txt|csv|xls|xlsx',
        'max_size'      => '1000',
        'max_width'     => '1024',
        'max_height'    => '768'
    );

    public function __construct()
    {
        parent::__construct();

        user_auth_check();
        
        $this->load->model('File_model');
        $this->load->model('History_model');
        
        $this->_config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '\docs\\';
    }

    /**
     *
     * Upload documentation to the company folder identified by URN.
     * If no folder exists, create a new one
     */
    public function upload()
    {
        $urn = $this->input->post('urn');
        if (!$urn) {
            redirect('leads/search');
        }


        
        //Append the urn to the file path to give us this companies docs dir
        $this->_config['upload_path'] .= $urn;
        //Make the dir if it doesn't exist
        $this->File_model->makeDir($this->_config['upload_path']);
        $this->load->library('upload', $this->_config);
        //Attempt to upload
        if (!$this->upload->do_upload()) {
            $_SESSION['file_error'] = $this->upload->display_errors('', '');
        } else {
             //get the filename
             $upload_data = $this->upload->data(); 
             $file_name =   $upload_data['file_name'];
             //log the upload
            $this->History_model->log_file_uploaded($urn,$file_name);
        }
        $_SESSION['open_file_tab'] = true;
        redirect('leads/detail/' . $urn);
    }

    /**
     * Download a single file as is or multiple files as a zip file.
     */
    public function download() {

        $this->load->helper('download');

        $urn = $this->input->post('urn');
        if (!$urn) {
            redirect('leads/search');
        }

        $data = $this->input->post();
        if (empty($data)) {
            $this->_download_error($urn, 'Please select one or more files to download.');
        }
        unset($data['urn']);

        $this->_config['upload_path'] .= $urn;

        //Check the directory exists.
        if (!is_dir($this->_config['upload_path'])) {
            $this->_download_error($urn, 'The file path does not appear to be valid.');
        }

        $filePaths = array();
        foreach ($data as $file) {
            $filePath = $this->_config['upload_path'] . '\\' . $file;
            if (!file_exists($filePath)) {
                $this->_download_error($urn, "The file '" . $file . "' does not appear to be valid.");
            }
            $filePaths[] = array(
                'name' => $file,
                'path' => $filePath
            );
        }
        
        $numFiles = count($filePaths);
        $this->History_model->log_file_downloaded($urn, $numFiles);
        
        //If they only want one file, download it.
        if ($numFiles === 1) {
            force_download($filePaths[0]['name'], file_get_contents($filePaths[0]['path']));
        } else {
            //If they want multple files, zip them.
            $this->load->library('zip');
            foreach ($filePaths as $item) {
                $this->zip->read_file($item['path']);
            }
            $this->zip->download($urn . '.zip');
        }
        
    }

    /**
     *
     * Helper function to set error variables & redirect on file download error.
     *
     * @param int $urn
     * @param string $msg
     */
    private function _download_error($urn, $msg) {
        $_SESSION['file_error'] = $msg;
        $_SESSION['open_file_tab'] = true;
        redirect('leads/detail/' . $urn);
    }

    /**
     * Delete the selected files. Return the files that have been successfully
     * deleted so that they can be removed from the ui.
     */
    public function delete() {

        $urn = $this->input->post('urn');
        $files = $this->input->post('files');

        if (empty($files)) {
            echo json_encode(array('success' => false, 'message' => 'Please select one or more files to delete.'));
            exit;
        }

        //Check the directory exists.
        $this->_config['upload_path'] .= $urn;
        if (!is_dir($this->_config['upload_path'])) {
            echo json_encode(array('success' => false, 'message' => 'The file path does not appear to be valid.'));
            exit;
        }

        $deleted = array();
        foreach ($files as $file) {
            $path = $this->_config['upload_path'] . '\\' . $file;
            if (file_exists($path)) {
                unlink($path);
                $deleted[] = $file;
            }
        }
        
        $this->History_model->log_file_deleted($urn, count($deleted));
        
        echo json_encode(array('success' => true, 'message' => 'OK.', 'deleted' => $deleted));
        exit;
    }

}
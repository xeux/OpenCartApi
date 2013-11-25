<?php

class ControllerApiInformation extends Controller {
    
    private $debug = false;
    
    public function _get() {
        $this->load->model('catalog/information');
        $json = array('success' => true);

        # -- $_GET params ------------------------------
                
        if (isset($this->request->get['id'])) {
            $information_id = $this->request->get['id'];
        } else {
            $information_id = 0;
        }

        # -- End $_GET params --------------------------
		
		$information_info = $this->model_catalog_information->getInformation($information_id);
        
        $json['result'] = $information_info;

        if ($this->debug) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }
    
    function __call( $methodName, $arguments ) {
        //call_user_func(array($this, str_replace('.', '_', $methodName)), $arguments);
        call_user_func(array($this, "_$methodName"), $arguments);
    }
    
}

?>
<?php

class ControllerApiStore extends Controller {
    
    private $debug = false;
    
    public function _get() {
        $this->load->model('setting/setting');
        $json = array('success' => true);

        # -- $_GET params ------------------------------
                
        if (isset($this->request->get['store'])) {
            $store_id = $this->request->get['store'];
        } else {
            $store_id = 0;
        }

        # -- End $_GET params --------------------------
        
        $store = $this->model_setting_setting->getSetting('config');

        $json['store'] = $store;


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
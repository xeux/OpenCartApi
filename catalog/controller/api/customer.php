<?php

class ControllerApiCustomer extends Controller {
    
    private $debug = false;
    
    public function _get() {
        $this->load->model('account/customer');
        $json = array('success' => true);

        # -- $_GET params ------------------------------
                
        if (isset($this->request->get['id'])) {
            $customer = $this->model_account_customer->getCustomer($this->request->get['id']);
        } else if (isset($this->request->get['email'])) {
            $customer = $this->model_account_customer->getCustomerByEmail($this->request->get['email']);
        } else if (isset($this->request->get['token'])) {
            $customer = $this->model_account_customer->getCustomerByToken($this->request->get['token']);
        } else {
            $customer = $this->model_account_customer->getCustomer(0);
        }

        # -- End $_GET params --------------------------

        $json['customer'] = array(
            'customer_id' => $customer['customer_id'],
            'store_id' => $customer['store_id'],
            'firstname' => $customer['firstname'],
            'lastname' => $customer['lastname'],
            'email' => $customer['email'],
            'telephone' => $customer['telephone'],
            'fax' => $customer['fax'],
            'password' => $customer['password'],
            'salt' => $customer['salt'],
//            'cart' => $customer['cart'],
//            'wishlist' => $customer['wishlist'],
//            'newsletter' => $customer['newsletter'],
//            'address_id' => $customer['address_id'],
//            'customer_group_id' => $customer['customer_group_id'],
//            'ip' => $customer['ip'],
//            'status' => $customer['status'],
//            'approved' => $customer['approved'],
//            'token' => $customer['token'],
//            'date_added' => $customer['date_added'],
        );


        if ($this->debug) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }
    
    public function _list() {
        $this->load->model('account/customer');
        $json = array('success' => true, 'customers' => array());
        
        # -- $_GET params ------------------------------
        if (isset($this->request->get['category'])) {
            $category_id = $this->request->get['category'];
        } else {
            $category_id = 0;
        }
        # -- End $_GET params --------------------------

        $customers = $this->model_account_customer->getCustomers(array(
            //'filter_name'        => ''
        ));
        
        foreach ($customers as $customer) {
            $json['customers'][] = array(
                'id' => $customer['customer_id'],
                'firstname' => $customer['firstname'],
                'lastname' => $customer['lastname'],
                'email' => $customer['email'],
            );
        }
                
        $this->response->setOutput(json_encode($json));
    }
    
    function __call( $methodName, $arguments ) {
        //call_user_func(array($this, str_replace('.', '_', $methodName)), $arguments);
        call_user_func(array($this, "_$methodName"), $arguments);
    }
    
}

?>
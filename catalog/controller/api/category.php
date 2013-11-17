<?php

class ControllerApiCategory extends Controller {
    
    private $debug = false;
    
    private function getCategoriesTree($parent = 0, $level = 1) {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');
                
        $result = array();

        $categories = $this->model_catalog_category->getCategories($parent);

        if ($categories && $level > 0) {
            $level--;

            foreach ($categories as $category) {

                if ($category['image']) {
                    $image = $this->model_tool_image->resize($category['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
                } else {
                    $image = false;
                }

                $result[] = array(
                    'category_id' => $category['category_id'],
                    'parent_id' => $category['parent_id'],
                    'name' => $category['name'],
                    'image' => $image,
                    'href' => $this->url->link('product/category', 'category_id=' . $category['category_id']),
                    'categories' => $this->getCategoriesTree($category['category_id'], $level)
                );
            }

            return $result;
        }
    }
        
    public function _get() {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $json = array('success' => true);

        # -- $_GET params ------------------------------
                
        if (isset($this->request->get['id'])) {
            $category_id = $this->request->get['id'];
        } else {
            $category_id = 0;
        }

        # -- End $_GET params --------------------------

        $category = $this->model_catalog_category->getCategory($category_id);
                
        $json['category'] = array(
            'id' => $category['category_id'],
            'name' => $category['name'],
            'description' => $category['description'],
            'href' => $this->url->link('product/category', 'category_id=' . $category['category_id']),
            'image' => $category['image']
        );

        if ($this->debug) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }
    
    public function _list() {
        $this->load->model('catalog/category');
        $json = array('success' => true);

        # -- $_GET params ------------------------------
                
        if (isset($this->request->get['parent'])) {
            $parent = $this->request->get['parent'];
        } else {
            $parent = 0;
        }

        if (isset($this->request->get['level'])) {
            $level = $this->request->get['level'];
        } else {
            $level = 1;
        }

        # -- End $_GET params --------------------------
        
        $json['categories'] = $this->getCategoriesTree($parent, $level);

        if ($this->debug) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }
    
    public function _count() {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');
        $json = array('success' => true, 'result' => '');
        
        # -- $_GET params ------------------------------
        if (isset($this->request->get['parent'])) {
            $parent = $this->request->get['parent'];
        } else {
            $parent = 0;
        }
        # -- End $_GET params --------------------------
        
        $categories = $this->model_catalog_category->getTotalCategoriesByCategoryId($parent);
        
        $json['result'] = $categories;
        
        $this->response->setOutput(json_encode($json));
    }
    
    function __call( $methodName, $arguments ) {
        //call_user_func(array($this, str_replace('.', '_', $methodName)), $arguments);
        call_user_func(array($this, "_$methodName"), $arguments);
    }
    
}

?>
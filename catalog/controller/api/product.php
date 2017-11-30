<?php
class ControllerApiProduct extends Controller {
    private $debug = false;

    // TODO: fix
    public function _add($productData = array()) {
		// Assoc array of data
		// $productData = array(
		//	'name' => 'Product Name Here',
		//	'model' => 'ABC123',
		//	...
		//);
		
		// Load model into memory if it isn't already
		$this->load->model('catalog/product');
		
		// Attempt to pass the assoc array to the add Product method
        $this->model_catalog_product->addProduct($productData);
    }

    public function _get() {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $json = array('success' => true);

        # -- $_GET params ------------------------------
                
        if (isset($this->request->get['id'])) {
            $product_id = $this->request->get['id'];
        } else {
            $product_id = 0;
        }

        # -- End $_GET params --------------------------

        $product = $this->model_catalog_product->getProduct($product_id);

        # product image
        if ($product['image']) {
            $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
        } else {
            $image = '';
        }

        #additional images
        $additional_images = $this->model_catalog_product->getProductImages($product['product_id']);
        $images = array();

        foreach ($additional_images as $additional_image) {
            $images[] = $this->model_tool_image->resize($additional_image, $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'));
        }

        #specal
        if ((float)$product['special']) {
            $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
        } else {
            $special = false;
        }

        #discounts
        $discounts = array();
        $data_discounts = $this->model_catalog_product->getProductDiscounts($product['product_id']);

        foreach ($data_discounts as $discount) {
            $discounts[] = array(
                'quantity' => $discount['quantity'],
                'price' => $this->currency->format($this->tax->calculate($discount['price'], $product['tax_class_id'], $this->config->get('config_tax')))
            );
        }

        // options
        $options = array();

        foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                $option_value_data = array();
                                
                foreach ($option['option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                            $price = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax')));
                        } else {
                            $price = false;
                        }
                                                
                        $option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id' => $option_value['option_value_id'],
                            'name' => $option_value['name'],
                            'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                            'price' => $price,
                            'price_prefix' => $option_value['price_prefix']
                        );
                    }
                }
                                
                $options[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'option_id' => $option['option_id'],
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'option_value' => $option_value_data,
                    'required' => $option['required']
                );                                        
            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $options[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'option_id' => $option['option_id'],
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'option_value' => $option['option_value'],
                    'required' => $option['required']
                );                                                
            }
        }

        #minimum
        if ($product['minimum']) {
            $minimum = $product['minimum'];
        } else {
            $minimum = 1;
        }

        $json['product'] = array(
            'id' => $product['product_id'],
            'name' => $product['name'],
            'description' => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
            'meta_description' => $product['meta_description'],
            'meta_keyword' => $product['meta_keyword'],
            'tag' => $product['tag'],
            'model' => $product['model'],
            'sku' => $product['sku'],
            'upc' => $product['upc'],
            'ean' => $product['ean'],
            'jan' => $product['jan'],
            'isbn' => $product['isbn'],
            'mpn' => $product['mpn'],
            'location' => $product['location'],
            'quantity' => $product['quantity'],
            'stock_status' => $product['stock_status'],
            'image' => $image,
            'images' => $images,
            'manufacturer_id' => $product['manufacturer_id'],
            'manufacturer' => $product['manufacturer'],
            // $product['price'];
            'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
            // $product['special'];
            'special' => $special,
            'reward' => $product['reward'],
            'points' => $product['points'],
            'tax_class_id' => $product['tax_class_id'],
            'date_available' => $product['date_available'],
            'weight' => $product['weight'],
            'weight_class_id' => $product['weight_class_id'],
            'length' => $product['length'],
            'width' => $product['width'],
            'height' => $product['height'],
            'length_class_id' => $product['length_class_id'],
            'subtract' => $product['subtract'],
            'rating' => (int)$product['rating'],
            'reviews' => (int)$product['reviews'],
            'minimum' => $minimum,
            'sort_order' => $product['sort_order'],
            'status' => $product['status'],
            'date_added' => $product['date_added'],
            'date_modified' => $product['date_modified'],
            'viewed' => $product['viewed'],
            'discounts' => $discounts,
            'options' => $options,
            'attribute_groups' => $this->model_catalog_product->getProductAttributes($product['product_id'])
        );


        if ($this->debug) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }
    
    public function _list() {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $json = array('success' => true, 'products' => array());
        
        // -- $_GET params ------------------------------
        if (isset($this->request->get['category'])) {
            $category_id = $this->request->get['category'];
        } else {
            $category_id = 0;
        }
        # -- End $_GET params --------------------------

        $products = $this->model_catalog_product->getProducts(array(
            'filter_category_id'        => $category_id
        ));
        
        foreach ($products as $product) {

            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
            } else {
                $image = false;
            }

            $json['products'][] = array(
                'id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                'thumb' => $image,
            );
        }
                
        $this->response->setOutput(json_encode($json));
    }
    
    public function _count() {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $json = array('success' => true, 'result' => '');
        
        // -- $_GET params ------------------------------
        if (isset($this->request->get['category'])) {
            $category_id = $this->request->get['category'];
        } else {
            $category_id = 0;
        }
        // -- End $_GET params --------------------------
        
        $products = $this->model_catalog_product->getTotalProducts(array(
            'filter_category_id' => $category_id
        ));
        
        $json['result'] = $products;
        
        $this->response->setOutput(json_encode($json));
    }
    
    function __call( $methodName, $arguments ) {
        //call_user_func(array($this, str_replace('.', '_', $methodName)), $arguments);
        call_user_func(array($this, "_$methodName"), $arguments);
    }
 }

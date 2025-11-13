<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Shop Controller
 *
 * @property Product_model $Product_model
 * @property Ad_model $Ad_model
 * @property Settings_model $Settings_model
 */
class Product_shop extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Ad_model');
        $this->load->model('Settings_model');
        $this->load->helper('url');
    }

    // Display product catalog page
    public function index() {
        // Get active products
        $products = $this->Product_model->get_active_products();

        // Get active ads for modal (same as booking page)
        $active_ads = $this->Ad_model->get_active_ads();

        // Get slider interval from settings
        $slider_interval = $this->Settings_model->get('ad_slider_interval', 2); // Default 2 seconds

        $data = [
            'title' => 'Product Shop',
            'products' => $products,
            'active_ads' => $active_ads,
            'slider_interval' => (int)$slider_interval * 1000, // Convert to milliseconds for JS
        ];

        $this->load->view('product_shop', $data);
    }

    // Display product detail page
    public function detail($id = null) {
        if (!$id || !is_numeric($id)) {
            show_404();
        }

        // Get product by ID
        $product = $this->Product_model->get_product_by_id($id);

        if (!$product) {
            show_404();
        }

        // Get active ads for modal (same as other pages)
        $active_ads = $this->Ad_model->get_active_ads();

        // Get slider interval from settings
        $slider_interval = $this->Settings_model->get('ad_slider_interval', 2); // Default 2 seconds

        $data = [
            'title' => htmlspecialchars($product['name']) . ' - Product Detail',
            'product' => $product,
            'active_ads' => $active_ads,
            'slider_interval' => (int)$slider_interval * 1000, // Convert to milliseconds for JS
        ];

        $this->load->view('product_detail', $data);
    }
}
?>
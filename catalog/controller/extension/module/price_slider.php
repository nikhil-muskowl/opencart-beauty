<?php

/*
  Module Name: Price Slider
  Description:Price Slider plugin is one of the best product filter plugin for opencart. It has feature to filter products by
  price range.
  Author: Softech Planet
  Author Email:info@softechplanet.com
  Author URI: http://softechplanet.com
  Version: 1.0
  Tags: product filter, price filter, price slider
 */

class ControllerExtensionModulePriceSlider extends Controller {

    public function index() {
        $this->load->language('extension/module/price_slider');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_filter'] = $this->language->get('button_filter');
        $this->document->addScript('catalog/view/javascript/bootstrap-slider.js');
        $this->document->addStyle('catalog/view/theme/default/stylesheet/bootstrap-slider.css');

        $min_max = '';

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string) $this->request->get['path']);

            $category_id = (int) array_pop($parts);
        } else {
            $category_id = 0;
        }

        if ($category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);
        } else {
            $category_info = array();
        }


        if ($category_info) {
            $filter_data = array(
                'filter_category_id' => $category_id,
            );
            $results = $this->model_catalog_product->getMinMaxProduct($filter_data);

            foreach ($results as $result) {
                if (!$min_max) {
                    $min_max = $result;
                } else {
                    $min_max .= '-' . $result;
                }
            }
        } else {
            $results = $this->model_catalog_product->getMinMaxProduct();

            foreach ($results as $result) {
                if (!$min_max) {
                    $min_max = $result;
                } else {
                    $min_max .= '-' . $result;
                }
            }
        }


        if (isset($this->request->get['path'])) {
            if (stristr($this->request->get['path'], '_') === FALSE) {
                $parts = $this->request->get['path'];
                $category_id = $this->request->get['path'];
            } else {
                $path = '';
                $parts = explode('_', (string) $this->request->get['path']);
                $category_id = '';
                $category_id = (int) array_pop($parts);
            }

            $data['price_slider_status'] = $this->config->get('module_price_slider_status');
            $data['price_slider_title'] = $this->config->get('module_price_slider_heading');

            if (!isset($price_slider)) {
                $price_slider = array();
            }

            if (isset($this->request->get['pr'])) {
                $data['price_range'] = explode(',', $this->request->get['pr']);
                $price_min = $this->currency->convert($data['price_range'][0], $this->session->data['currency'], $this->config->get('config_currency'));
                $price_max = $this->currency->convert($data['price_range'][1], $this->session->data['currency'], $this->config->get('config_currency'));

                if ($price_max != null) {
                    $price_max = round($price_max);
                    $price_max = $price_max + (10 - (substr($price_max, -1)));
                }
            } else {
                $data['price_range'] = array();
            }

            if (version_compare(VERSION, '2.2.0.0', '<') == true) {
                $pcode = $this->currency->getCode();
            } else {
                $pcode = $this->session->data['currency'];
            }

            if ($this->currency->getSymbolLeft($pcode)) {
                $code = $this->currency->getSymbolLeft($pcode);
                $data['right_code'] = false;
            } else {
                $code = $this->currency->getSymbolRight($pcode);
                $data['right_code'] = true;
            }

            $data['price_code'] = $code;

            $url = '';

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer'])) {
                $url .= '&manufacturer=' . $this->request->get['manufacturer'];
            }

            if (isset($this->request->get['brand_filter'])) {
                $url .= '&brand_filter=' . $this->request->get['brand_filter'];
            }

            if (isset($this->request->get['country_origin_filter'])) {
                $url .= '&country_origin_filter=' . $this->request->get['country_origin_filter'];
            }           

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $action = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (isset($action[0])) {
                $data['action'] = str_replace('&amp;', '&', $this->url->link($action[0], 'path=' . $this->request->get['path'] . $url));
            } else {
                $data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));
            }


            if (!$min_max) {
                $range = explode('-', '0-0');
            } else {
                $range = explode('-', $min_max);
            }
            $data['min_max'] = $min_max;
            $data['price_range_min'] = $this->currency->format($range[0], $pcode, '', false);
            $data['price_range_max'] = $this->currency->format($range[1], $pcode, '', false);

            $data['price_min'] = $this->currency->format($range[0], $pcode);
            $data['price_max'] = $this->currency->format($range[1], $pcode);

            return $this->load->view('extension/module/price_slider', $data);
        }
    }

}

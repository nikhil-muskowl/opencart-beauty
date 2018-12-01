<?php

class ControllerExtensionModuleBrandFilter extends Controller {

    public function index() {
        $this->load->language('extension/module/brand_filter');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_filter'] = $this->language->get('button_filter');


        if (isset($this->request->get['brand_filter'])) {
            $brand_filter = explode(',', (string) $this->request->get['brand_filter']);
        } else {
            $brand_filter = array();
        }

        $this->load->language('product/manufacturer');

        $this->load->model('catalog/manufacturer');

        $this->load->model('tool/image');

        $data['categories'] = array();

        $results = $this->model_catalog_manufacturer->getManufacturers();

        foreach ($results as $result) {
            if (is_numeric(utf8_substr($result['name'], 0, 1))) {
                $key = '0 - 9';
            } else {
                $key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
            }

            if (!isset($data['categories'][$key])) {
                $data['categories'][$key]['name'] = $key;
            }

            $checked = TRUE;

            if (in_array($result['manufacturer_id'], $brand_filter)) {
                $checked = TRUE;
            } else {
                $checked = FALSE;
            }

            $data['categories'][$key]['manufacturer'][] = array(
                'manufacturer_id' => $result['manufacturer_id'],
                'checked' => $checked,
                'name' => $result['name'],
                'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
            );
        }


        if (isset($this->request->get['manufacturer_id'])) {
            $manufacturer_id = (int) $this->request->get['manufacturer_id'];
        } else {
            $manufacturer_id = 0;
        }

        if (isset($this->request->get['filter'])) {
            $filter = $this->request->get['filter'];
        } else {
            $filter = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = (int) $this->request->get['limit'];
        } else {
            $limit = (int) $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
        }

        if (isset($this->request->get['path'])) {
            $url = '';            

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['pr'])) {
                $url .= '&pr=' . $this->request->get['pr'];
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

           $data['action'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] .urldecode($url));

            return $this->load->view('extension/module/brand_filter', $data);
        }
    }

}

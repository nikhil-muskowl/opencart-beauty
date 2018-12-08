<?php

class ControllerExtensionModuleCountryOriginFilter extends Controller {

    public function index() {
        $this->load->language('extension/module/country_origin_filter');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_filter'] = $this->language->get('button_filter');


        if (isset($this->request->get['country_origin_filter'])) {
            $country_origin_filter = explode(',', (string) $this->request->get['country_origin_filter']);
        } else {
            $country_origin_filter = array();
        }

        $this->load->language('product/country_origin');

        $this->load->model('catalog/country_origin');

        $this->load->model('tool/image');

        $data['country_origins'] = array();

        $results = $this->model_catalog_country_origin->getCountryOrigins();

        foreach ($results as $result) {

            $checked = TRUE;

            if (in_array($result['country_origin_id'], $country_origin_filter)) {
                $checked = TRUE;
            } else {
                $checked = FALSE;
            }

            $data['country_origins'][] = array(
                'country_origin_id' => $result['country_origin_id'],
                'checked' => $checked,
                'name' => $result['name'],
            );
        }

        if (isset($this->request->get['path'])) {

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

            $action = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (isset($action[0])) {
                $data['action'] = str_replace('&amp;', '&', $this->url->link($action[0], 'path=' . $this->request->get['path'] . $url));
            } else {
                $data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));
            }

            return $this->load->view('extension/module/country_origin_filter', $data);
        }
    }

}

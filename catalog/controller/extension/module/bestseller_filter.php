<?php

class ControllerExtensionModuleBestsellerFilter extends Controller {

    public function index() {
        $this->load->language('extension/module/bestseller_filter');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_filter'] = $this->language->get('button_filter');



        if (isset($this->request->get['bestseller_filter'])) {
            $bestseller_filter = (int) $this->request->get['bestseller_filter'];
        } else {
            $bestseller_filter = 0;
        }

        $data['bestseller_filter'] = $bestseller_filter;

        if (isset($this->request->get['manufacturer_id'])) {
            $manufacturer_id = (int) $this->request->get['manufacturer_id'];
        } else {
            $manufacturer_id = 0;
        }

        $this->load->model('tool/image');


        if (isset($this->request->get['path'])) {
            $path = $this->request->get['path'];
        } else {
            $path = '';
        }
        if (isset($this->request->get['route'])) {
            $route = $this->request->get['route'];
        } else {
            $route = '';
        }



        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['brand_filter'])) {
            $url .= '&brand_filter=' . $this->request->get['brand_filter'];
        }

        if (isset($this->request->get['manufacturer'])) {
            $url .= '&manufacturer=' . $this->request->get['manufacturer'];
        }

        if (isset($this->request->get['country_origin_filter'])) {
            $url .= '&country_origin_filter=' . $this->request->get['country_origin_filter'];
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

        $action = '';

        if ($path) {
            $action .= 'path=' . $path;
        }

        if ($manufacturer_id) {
            $action .= '&manufacturer_id=' . $manufacturer_id;
        }

        $action .= $url;

        $data['action'] = str_replace('&amp;', '&', $this->url->link($route, $action));

        return $this->load->view('extension/module/bestseller_filter', $data);
    }

}

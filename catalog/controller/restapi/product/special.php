<?php

class ControllerRestApiProductSpecial extends Controller {

    public function index() {
        $this->load->language('product/special');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        if (isset($this->request->post['customer_id'])) {
            $this->customer->setId($this->request->post['customer_id']);
        }

        if (isset($this->request->get['category_filter'])) {
            $category_filter = $this->request->get['category_filter'];
        } else {
            $category_filter = 0;
        }
        
        if (isset($this->request->get['pr'])) {
            $pr = $this->request->get['pr'];
        } else {
            $pr = '';
        }

        if (isset($this->request->get['brand_filter'])) {
            $brand_filter = $this->request->get['brand_filter'];
        } else {
            $brand_filter = '';
        }

        if (isset($this->request->get['country_origin_filter'])) {
            $country_origin_filter = $this->request->get['country_origin_filter'];
        } else {
            $country_origin_filter = '';
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
            $limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
        }


        $data['heading_title'] = $this->language->get('heading_title');

        $url = '';

        if (isset($this->request->get['category_filter'])) {
            $url .= '&category_filter=' . $this->request->get['category_filter'];
        }
        
        if (isset($this->request->get['country_origin_filter'])) {
            $url .= '&country_origin_filter=' . $this->request->get['country_origin_filter'];
        }

        if (isset($this->request->get['brand_filter'])) {
            $url .= '&brand_filter=' . $this->request->get['brand_filter'];
        }

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['manufacturer'])) {
            $url .= '&manufacturer=' . $this->request->get['manufacturer'];
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

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['products'] = array();

        $filter_data = array(
            'filter_filter' => $filter,
            'price_filter' => $pr,
            'brand_filter' => $brand_filter,
            'country_origin_filter' => $country_origin_filter,
            'category_filter' => $category_filter,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $product_total = $this->model_catalog_product->getTotalProductSpecials($filter_data);

        $results = $this->model_catalog_product->getProductSpecials($filter_data);

        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float) $result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_tax')) {
                $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
            } else {
                $tax = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = (int) $result['rating'];
            } else {
                $rating = false;
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'thumb' => $image,
                'name' => $result['name'],
                'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                'price' => $price,
                'special' => $special,
                'tax' => $tax,
                'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
                'rating' => $result['rating'],
                'href' => $this->url->api_link('restapi/product', 'product_id=' . $result['product_id'] . $url)
            );
        }

        $url = '';

        if (isset($this->request->get['category_filter'])) {
            $url .= '&category_filter=' . $this->request->get['category_filter'];
        }
        
        if (isset($this->request->get['country_origin_filter'])) {
            $url .= '&country_origin_filter=' . $this->request->get['country_origin_filter'];
        }

        if (isset($this->request->get['brand_filter'])) {
            $url .= '&brand_filter=' . $this->request->get['brand_filter'];
        }

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['manufacturer'])) {
            $url .= '&manufacturer=' . $this->request->get['manufacturer'];
        }

        if (isset($this->request->get['pr'])) {
            $url .= '&pr=' . $this->request->get['pr'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['sorts'] = array();

        $data['sorts'][] = array(
            'text' => $this->language->get('text_default'),
            'value' => 'p.sort_order-ASC',
            'href' => $this->url->api_link('restapi/special', 'sort=p.sort_order&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_bestseller_asc'),
            'value' => 'order_total-ASC',
            'href' => $this->url->api_link('restapi/special', '&sort=order_total&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_bestseller_desc'),
            'value' => 'order_total-DESC',
            'href' => $this->url->api_link('restapi/special', '&sort=order_total&order=DESC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_latest_asc'),
            'value' => 'p.date_added-ASC',
            'href' => $this->url->api_link('restapi/special', '&sort=p.date_added&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_latest_desc'),
            'value' => 'p.date_added-DESC',
            'href' => $this->url->api_link('restapi/special', '&sort=p.date_added&order=DESC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_popular_asc'),
            'value' => 'p.viewed-ASC',
            'href' => $this->url->api_link('restapi/special', '&sort=p.viewed&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_popular_desc'),
            'value' => 'p.viewed-DESC',
            'href' => $this->url->api_link('restapi/special', '&sort=p.viewed&order=DESC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_name_asc'),
            'value' => 'pd.name-ASC',
            'href' => $this->url->api_link('restapi/special', 'sort=pd.name&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_name_desc'),
            'value' => 'pd.name-DESC',
            'href' => $this->url->api_link('restapi/special', 'sort=pd.name&order=DESC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_price_asc'),
            'value' => 'ps.price-ASC',
            'href' => $this->url->api_link('restapi/special', 'sort=ps.price&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_price_desc'),
            'value' => 'ps.price-DESC',
            'href' => $this->url->api_link('restapi/special', 'sort=ps.price&order=DESC' . $url)
        );

        if ($this->config->get('config_review_status')) {
            $data['sorts'][] = array(
                'text' => $this->language->get('text_rating_desc'),
                'value' => 'rating-DESC',
                'href' => $this->url->api_link('restapi/special', 'sort=rating&order=DESC' . $url)
            );

            $data['sorts'][] = array(
                'text' => $this->language->get('text_rating_asc'),
                'value' => 'rating-ASC',
                'href' => $this->url->api_link('restapi/special', 'sort=rating&order=ASC' . $url)
            );
        }

        $data['sorts'][] = array(
            'text' => $this->language->get('text_model_asc'),
            'value' => 'p.model-ASC',
            'href' => $this->url->api_link('restapi/special', 'sort=p.model&order=ASC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_model_desc'),
            'value' => 'p.model-DESC',
            'href' => $this->url->api_link('restapi/special', 'sort=p.model&order=DESC' . $url)
        );

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['limits'] = array();

        $limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

        sort($limits);

        foreach ($limits as $value) {
            $data['limits'][] = array(
                'text' => $value,
                'value' => $value,
                'href' => $this->url->api_link('restapi/special', $url . '&limit=' . $value)
            );
        }

        $url = '';

        if (isset($this->request->get['category_filter'])) {
            $url .= '&category_filter=' . $this->request->get['category_filter'];
        }
        
        if (isset($this->request->get['country_origin_filter'])) {
            $url .= '&country_origin_filter=' . $this->request->get['country_origin_filter'];
        }
        
        if (isset($this->request->get['brand_filter'])) {
            $url .= '&brand_filter=' . $this->request->get['brand_filter'];
        }

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['manufacturer'])) {
            $url .= '&manufacturer=' . $this->request->get['manufacturer'];
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

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->api_link('restapi/special', $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['limit'] = $limit;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

}

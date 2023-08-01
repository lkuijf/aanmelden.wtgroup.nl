<?php
// Create Custom REST API for Filter (https://stackoverflow.com/questions/59135291/filter-product-list-by-mutiple-attribute-and-its-attribute-terms-in-woocommerce/66421170)
    add_action('rest_api_init', 'wp_rest_filterproducts_endpoints');
    function wp_rest_filterproducts_endpoints($request) {
        // register_rest_route('wp/v3', 'filter/products', array(
        register_rest_route('wtcustom', '/filter-products', array(
            'methods' => 'GET',
            'callback' => 'wp_rest_filterproducts_endpoint_handler',
        ));
    }
    
    function wp_rest_filterproducts_endpoint_handler($request = null) {
        $output = array();
        $params = $request->get_params();
    
        $category = $params['category'];
        $filters  = $params['filter'];
        $crb      = $params['crb']; // Carbon Fields -filter
        $per_page = $params['per_page'];
        $offset   = $params['offset'];
        $order    = $params['order'];
        $orderby  = $params['orderby'];
        $count    = $params['count'];
        
        // Use default arguments.
        $args = [
            'post_type'         => 'product',
            // 'posts_per_page'    => 10,
            'post_status'       => 'publish',
            // 'paged'             => 1,
            // 'no_found_rows'     => true, // can make the query faster ?!?! https://wordpress.stackexchange.com/questions/177908/return-only-count-from-a-wp-query-request
        ];
    
        if ( ! empty( $count ) ) { // when counting totals, select only IDs
            $args['fields'] = 'ids';
        }
    
        // Posts per page.
        if ( ! empty( $per_page ) ) {
          $args['posts_per_page'] = $per_page;
        }
        // Pagination, starts from 1.
        if ( ! empty( $offset ) ) {
          $args['paged'] = $offset;
        }
        // Order condition. ASC/DESC.
        if ( ! empty( $order ) ) {
          $args['order'] = $order;
        }
        // Orderby condition. Name/Price.
        if ( ! empty( $orderby ) ) {
          if ( $orderby === 'price' ) {
            $args['orderby'] = 'meta_value_num';
          } else {
            $args['orderby'] = $orderby;
          }
        }
        // If filter buy category or attributes.
        if ( ! empty( $category ) || ! empty( $filters ) ) {
          $args['tax_query']['relation'] = 'AND';
          // Category filter.
          if ( ! empty( $category ) ) {
            $args['tax_query'][] = [
              'taxonomy' => 'product_cat',
            //   'field'    => 'slug',
              'terms'    => [ $category ],
            ];
          }
          // Attributes filter.
          if ( ! empty( $filters ) ) {
            foreach ( $filters as $filter_key => $filter_value ) {
              if ( $filter_key === 'min_price' || $filter_key === 'max_price' ) {
                continue;
              }
    
              $args['tax_query'][] = [
                'taxonomy' => $filter_key,
                'field'    => 'term_id',
                // 'field'    => 'slug',
                'terms'    => \explode( ',', $filter_value ),
              ];
            }
          }
          // Min / Max price filter.
          if ( isset( $filters['min_price'] ) || isset( $filters['max_price'] ) ) {
            $price_request = [];
            if ( isset( $filters['min_price'] ) ) {
              $price_request['min_price'] = $filters['min_price'];
            }
            if ( isset( $filters['max_price'] ) ) {
              $price_request['max_price'] = $filters['max_price'];
            }
            $args['meta_query'][] = \wc_get_min_max_price_meta_query( $price_request );
            }
        }
        // Carbon Fields filter.
        if ( ! empty( $crb ) ) {
            foreach ( $crb as $crb_key => $crb_value ) {
                $carbon = [
                    'key' => $crb_key,
                    'value' => $crb_value,
                ];
                $args['meta_query'][] = $carbon;
            }
        }
    // print_r($args);
        // $crb = [
        //     'key' => 'is_featured',
        //     'value' => 'yes',
        // ];
        // $args['meta_query'][] = $crb;
        
        $the_query = new \WP_Query( $args );
    
        if ( ! $the_query->have_posts() ) {
          return $output;
        }
    
        if ( ! empty( $count ) ) {
            $output['total'] = $the_query->found_posts;
        } else {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $product = wc_get_product( get_the_ID() );  
        
                // Product Properties
                $wcproduct['id'] = $product->get_id();
                $wcproduct['name'] = $product->get_name();
                $wcproduct['price'] = $product->get_price();
                $wcproduct['regular_price'] = $product->get_regular_price();
                $wcproduct['sale_price'] = $product->get_sale_price();
                $wcproduct['slug'] = $product->get_slug();
                $wcproduct['short_description'] = $product->get_short_description();
                $mainImageId = $product->get_image_id();
                $imageGalleryIds = $product->get_gallery_image_ids();
                $AllImgSrcs = bundleProductImages($mainImageId, $imageGalleryIds);
                $wcproduct['images'] = $AllImgSrcs;
                // $wcproduct['categories'] = wc_get_product_category_list($product->get_id());
    
                $catIds = [];
                $ancestors = [];
                $catTerms = get_the_terms( get_the_ID(), 'product_cat' );
                foreach($catTerms  as $term  ) {
                    $catIds[] = $term->term_id;
                    $ancestors = array_merge($ancestors, get_ancestors($term->term_id, 'product_cat'));
                }
                $wcproduct['categories'] = $catIds;
                $wcproduct['ancestors'] = array_unique($ancestors);
    
                $output[] = $wcproduct;
            }
        }
    
        wp_reset_postdata();
    
        // return new WP_REST_Response($output, 123);
        $response = new WP_REST_Response($output);
        $response->set_status(200);
        return $response;
    }
// Custum REST API for all attributes + terms for current category
add_action('rest_api_init', 'wp_rest_attributes_terms');
function wp_rest_attributes_terms($request) {
    register_rest_route('wtcustom', '/attributes-terms', array(
        'methods' => 'GET',
        'callback' => 'wp_rest_attributes_terms_handler',
    ));
}
function wp_rest_attributes_terms_handler($request = null) {
    $output = array();
    $params = $request->get_params();
    $category = $params['category'];

    // Use default arguments.
    $args = [
        'post_type'         => 'product',
        // 'posts_per_page'    => 10,
        'post_status'       => 'publish',
        'fields'            => 'ids',
        // 'paged'             => 1,
        // 'no_found_rows'     => true, // can make the query faster ?!?! https://wordpress.stackexchange.com/questions/177908/return-only-count-from-a-wp-query-request
    ];
    $args['tax_query']['relation'] = 'AND';
    // Category filter.
    if ( ! empty( $category ) ) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
        //   'field'    => 'slug',
            'terms'    => [ $category ],
        ];
    }

    $the_query = new \WP_Query( $args );

    if ( ! $the_query->have_posts() ) {
        return $output;
    }

    $data = array();
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $product = wc_get_product( get_the_ID() );  
        foreach( $product->get_attributes() as $taxonomy => $attribute ){
            if(substr($taxonomy, 0, 3) != 'pa_') continue;
            $attribute_name = wc_attribute_label( $taxonomy ); // Attribute name
            $data[$taxonomy]['name'] = $attribute_name;
            foreach ( $attribute->get_terms() as $term ){
                $data[$taxonomy]['values'][$term->term_id]['name'] = $term->name;
                // $data[$taxonomy]['values'][$term->name]['name'] = $term->name;
            }
        }

    }
    $output = $data;

    wp_reset_postdata();

    // return new WP_REST_Response($output, 123);
    $response = new WP_REST_Response($output);
    $response->set_status(200);
    return $response;
}

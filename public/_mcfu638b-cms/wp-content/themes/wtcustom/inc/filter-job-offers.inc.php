<?php
    add_action('rest_api_init', 'wp_rest_filter_joboffers_endpoint');
    function wp_rest_filter_joboffers_endpoint($request) {
        // register_rest_route('wp/v3', 'filter/products', array(
        register_rest_route('wtcustom', '/filter-job-offers', array(
            'methods' => 'GET',
            'callback' => 'wp_rest_filter_joboffers_endpoint_handler',
        ));
    }
    
    function wp_rest_filter_joboffers_endpoint_handler($request = null) {
        $output = array();
        $params = $request->get_params();

        $string = $params['string'];
        $location = $params['location'];
        $type = $params['type'];
        
        // Use default arguments.
        $args = [
            'post_type'         => 'job_offer',
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            // 'paged'             => 1,
            // 'no_found_rows'     => true, // can make the query faster ?!?! https://wordpress.stackexchange.com/questions/177908/return-only-count-from-a-wp-query-request
        ];
        
        if ( ! empty( $string ) ) {
          $args['meta_query']['relation'] = 'OR';
          $args['meta_query'][] = [
            'key'     => 'intro',
            'value'   => str_replace('-', ' ', $string),
            'compare' => 'LIKE',
          ];
          $args['meta_query'][] = [
            'key'     => 'text',
            'value'   => str_replace('-', ' ', $string),
            'compare' => 'LIKE',
          ];
        }
        if ( ! empty( $location ) ) {
          $args['tax_query'][] = [
            'taxonomy' => 'locatie',
            'field'    => 'slug',
            'terms'    => [ $location ],
          ];
        }
        if ( ! empty( $type ) ) {
          $type = str_replace('-', ' ', $type);
          $type = explode(':', $type);
          $args['tax_query'][] = [
            'taxonomy' => 'type_job',
            'field'    => 'slug',
            'terms'    => $type,
          ];
        }
// var_dump($args);
        $the_query = new \WP_Query( $args );
    
        if ( ! $the_query->have_posts() ) {
          return $output;
        }
    
        if ( ! empty( $count ) ) {
            $output['total'] = $the_query->found_posts;
        } else {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
    
                $introFieldVals = get_post_custom_values('_intro');
                $imageFieldVals = get_post_custom_values('_image');
                $taxonomyTerms = [];
                $taxonomies = array('job_cat','uren_per_week','type_job', 'locatie');
                foreach($taxonomies as $taxonomy) {
                  $taxonomyTerms[$taxonomy] = [];
                  $terms = get_the_terms( get_the_ID(), $taxonomy );
                  if($terms && count($terms)) {
                    foreach($terms as $term) {
                      $taxonomyTerms[$taxonomy][] = $term->name;
                    }
                  }
                }
                
                $cPost = new \stdClass();
                $cPost->title = get_the_title();
                $cPost->slug = get_post_field( 'post_name' );
                $cPost->intro = $introFieldVals[0];
                $cPost->image = $imageFieldVals[0];
                $cPost->taxonomyTerms = $taxonomyTerms;

                $output[] = $cPost;
            }
        }
    
        wp_reset_postdata();

        // return new WP_REST_Response($output, 123);
        $response = new WP_REST_Response($output);
        $response->set_status(200);
        return $response;
    }

<?php
function getTaxonomiesSimplified(WP_REST_Request $request) {
    $taxonomies = get_taxonomies([
        'public'   => true,
        '_builtin' => false
    ], 'obejcts');
    $taxonomies = (object)$taxonomies;
    $taxAndTerms = new \stdClass();
    foreach($taxonomies as $k => $taxonomy) {
        $taxAndTerms->{$k}['label'] = $taxonomy->label;
        $terms = get_terms(array(
                'taxonomy' => $k,
                'hide_empty' => false,
            ));
        $termList = new \stdClass();
        foreach($terms as $term) $termList->{$term->term_id} = $term->name;
        $taxAndTerms->{$k}['terms'] = $termList;
    }
    $response = new WP_REST_Response($taxAndTerms);
    $response->set_status(200);
    return $response;
}
function getMediaSimplified(WP_REST_Request $request) {
    $media = get_posts([
        'numberposts' => -1,
        'post_type' => 'attachment',
    ]);
    $sizes = get_intermediate_image_sizes();
    $aRes = [];
    foreach ($media as $item) {
        $oP = new stdClass();
        $oP->id = $item->ID;
        $oP->url = $item->guid;
        $topic = '';
        $alt = '';
        if(isset(get_post_meta($item->ID, 'attach_to_topic')[0])) $topic = get_post_meta($item->ID, 'attach_to_topic')[0];
        if(isset(get_post_meta($item->ID, '_wp_attachment_image_alt')[0])) $alt = get_post_meta($item->ID, '_wp_attachment_image_alt')[0];
        $oP->topic = $topic;
        $oP->alt = $alt;

        $thumbnails = new stdClass();
        foreach($sizes as $key => $size) {
            $src = wp_get_attachment_image_src( $item->ID, $size)[0];
            // if($src && $src != $item->guid) $thumbnails->{$size} = $src;
            if($src) $thumbnails->{$size} = $src;
        }
        $oP->sizes = $thumbnails;

        $aRes[] = $oP;
    }
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}
function getPagesSimplified(WP_REST_Request $request) {
    $pages = get_pages();
    foreach($pages as $k => $page) {
        $pages[$k]->hide_from_menu = get_post_meta($page->ID, '_hide_from_menu');
    }
    $aRes = getPagesCollectionAttrs($pages);
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}
function getPostsSimplified(WP_REST_Request $request) {
    $parameters = $request->get_params();
    $orderby = 'date';
    $order = 'DESC';
    if (isset($parameters['orderby'])) {
        $orderby = $parameters['orderby'];
    }
    if (isset($parameters['order'])) {
        $order = $parameters['order'];
    }
    $posts = get_posts([
        'numberposts' => -1,
        'orderby' => $orderby,
        'order' => $order,
        'post_type' => 'job_offer',
    ]);
    $aRes = getPostsCollectionAttrs($posts);
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}
function getCustomPostsSimplified(WP_REST_Request $request) {
    $parameters = $request->get_params();
    $orderby = 'date';
    $order = 'DESC';
    $postType = 'post';
    $category = false;
    $highlighted = false;
    $ids = false;
    if (isset($parameters['orderby'])) {
        $orderby = $parameters['orderby'];
    }
    if (isset($parameters['order'])) {
        $order = $parameters['order'];
    }
    if (isset($parameters['post_type'])) {
        $postType = $parameters['post_type'];
    }
    if (isset($parameters['category'])) {
        $category = $parameters['category'];
    }
    if (isset($parameters['highlighted'])) {
        $highlighted = $parameters['highlighted'];
    }
    if (isset($parameters['ids'])) {
        $ids = explode(',', $parameters['ids']);
    }
    $postParams = [
        'numberposts' => -1,
        'orderby' => $orderby,
        'order' => $order,
        'post_type' => $postType,
    ];
    if($ids) $postParams['post__in'] = $ids;
    if($category) $postParams['tax_query'] = array(
        array(
            'taxonomy' => 'case_category',
            'field'    => 'slug',
            'terms'    => array( $category )
            )
        );
    if($highlighted) $postParams['meta_query'] = array(
        array(
            'key' => '_highlighted',
            'value'    => 'yes'
            )
        );
    // if($id) $postParams['meta_query'] = array(
    //     array(
    //         'key' => 'id',
    //         'value'    => $id
    //         )
    //     );
    $posts = get_posts($postParams);

    $aRes = getCustomPostsCollectionAttrs($posts, $postType);
    
    $response = new WP_REST_Response($aRes);
    $response->set_status(200);
    return $response;
}

function getWebsiteOptions() {
    global $carbonFieldsArgs; // using global. Importing does not work: https://stackoverflow.com/questions/11086773/php-function-use-variable-from-outside
    $aOptions = array();
    foreach($carbonFieldsArgs['websiteOptions'] as $opt) {
        $aOptions[$opt[1]] = carbon_get_theme_option($opt[1]);
    }
    $response = new WP_REST_Response($aOptions);
    $response->set_status(200);
    return $response;
}
// function getHeadContent() {
//     do_action( 'wp_head' );
//     exit;
//     // $res = do_action( 'wp_head' );
//     // $response = new WP_REST_Response($res);
//     // $response->set_status(200);
//     // return $response;
// }
function getWordPressGeneratedPage() {
    $post_id = 1067; // Set the desired post ID (Or Page Id)

    global $post;
    $post = get_post($post_id); // Get the post object
    setup_postdata($post);

    //!!!! get_header(), the_content() and get_footer() HAVE TO BE CALLED ALL 3 ! for right code to be generated. 

    get_header(); // with HTML tags
    // wp_head();
    the_content();
    get_footer(); // with HTML tags
    // wp_footer();

    wp_reset_postdata(); // Restore the global $post data

    exit;
}
function getPagesCollectionAttrs($coll) {
    $aRes = [];
    foreach ($coll as $item) {
        $oP = new stdClass();
        $oP->id = $item->ID;
        $oP->title = $item->post_title;
        $oP->slug = $item->post_name;
        $oP->parent = $item->post_parent;
        $oP->order = $item->menu_order;
        $oP->status = $item->post_status;
        $oP->date = $item->post_date;
        
        $oP->hide_from_menu = false;
        if(count($item->hide_from_menu) && $item->hide_from_menu[0] == 'yes') $oP->hide_from_menu = true;

        $aRes[] = $oP;
    }
    return $aRes;
}
function getPostsCollectionAttrs($coll) {
    $aRes = [];
    foreach ($coll as $item) {
        $oP = new stdClass();

        $tags = get_the_tags($item->ID);
        $aTags = array();
        if($tags) {
            foreach ($tags as $oTag) {
                $aTags[$oTag->slug] = $oTag->name;
            }
        }

        $groups = get_post_meta($item->ID, 'esplendor_group');
        $group = false;
        if($groups) {
            $group = $groups[0];
        }

        $metaTopics = get_post_meta($item->ID, 'topics');
        $topics = array();
        if($metaTopics && count(array_filter($metaTopics))) {
            $topics = $metaTopics[0];
        }

        $oP->id = $item->ID;
        $oP->title = $item->post_title;
        $oP->slug = $item->post_name;
        $oP->parent = $item->post_parent;
        $oP->order = $item->menu_order;
        $oP->status = $item->post_status;
        $oP->date = $item->post_date;
        $oP->category = get_the_category($item->ID)[0]->name;
        $oP->tags = $aTags;
        $oP->esplendor_group = $group;
        $oP->topics = $topics;
        $aRes[] = $oP;
    }
    return $aRes;
}
function getCustomPostsCollectionAttrs($coll, $pType) {
    $aRes = [];

// var_dump($coll);
// die('dead');

    foreach ($coll as $item) {
        $oP = new stdClass();


        // $tags = get_the_tags($item->ID);
        // $aTags = array();
        // if($tags) {
        //     foreach ($tags as $oTag) {
        //         $aTags[$oTag->slug] = $oTag->name;
        //     }
        // }

        // $metaTopics = get_post_meta($item->ID, 'topics');
        // $topics = array();
        // if($metaTopics && count(array_filter($metaTopics))) {
        //     $topics = $metaTopics[0];
        // }

        // $metaCategories = get_post_meta($item->ID, 'categories');
        // $cats = array();
        // if($metaCategories && count(array_filter($metaCategories))) {
        //     $cats = $metaCategories[0];
        // }


        if($pType == 'review') {
            $oP->id = $item->ID;
            $oP->title = $item->post_title;
            $oP->slug = $item->post_name;
            $oP->status = $item->post_status;
            $oP->date = $item->post_date;
            $oP->text = carbon_get_post_meta( $item->ID, 'text' );
            $oP->image = carbon_get_post_meta( $item->ID, 'image' );
            $oP->leading_title = carbon_get_post_meta( $item->ID, 'leading_title' );
            // $oP->by = carbon_get_post_meta( $item->ID, 'by' );
            $aRes[] = $oP;
        } elseif($pType == 'teammember') {
            $oP->id = $item->ID;
            $oP->title = $item->post_title;
            $oP->slug = $item->post_name;
            $oP->function = carbon_get_post_meta( $item->ID, 'function' );
            $oP->status = $item->post_status;
            $oP->date = $item->post_date;
            $oP->text = carbon_get_post_meta( $item->ID, 'text' );
            $oP->image = carbon_get_post_meta( $item->ID, 'image' );
            // $oP->leading_title = carbon_get_post_meta( $item->ID, 'leading_title' );
            $aRes[] = $oP;
        } else {
            $cats = [];
            $catTerms = get_the_terms( $item->ID, 'case_category' );
            if($catTerms && count($catTerms)) {
                foreach($catTerms as $term) {
                    $sCat = [];
                    $sCat['slug'] = $term->slug;
                    $sCat['name'] = $term->name;
                    $cats[] = $sCat;
                }
            }
    
            $oP->id = $item->ID;
            $oP->title = $item->post_title;
            $oP->slug = $item->post_name;
            // $oP->parent = $item->post_parent;
            // $oP->order = $item->menu_order;
            $oP->status = $item->post_status;
            $oP->date = $item->post_date;
            $oP->card_text = carbon_get_post_meta( $item->ID, 'card_text' );
            $oP->page_title = carbon_get_post_meta( $item->ID, 'page_title' );
            $oP->page_meta_description = carbon_get_post_meta( $item->ID, 'page_meta_description' );
            $oP->gallery = carbon_get_post_meta( $item->ID, 'gallery' );
            // $oP->category = get_the_category($item->ID)[0]->name;
            // $oP->tags = $aTags;
            // $oP->topics = $topics;
            $oP->categories = $cats;
            $aRes[] = $oP;
        }
       
        
    }
    return $aRes;
}

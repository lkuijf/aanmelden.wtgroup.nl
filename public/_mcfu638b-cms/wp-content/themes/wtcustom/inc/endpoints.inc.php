<?php
/** Register endpoints so they will be cached. */

// 28-8-2023. Leon Kuijf. Removed api-endpoint caching. Using Laravel Response Cache instead.
/*
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_pages_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_posts_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_custom_posts_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_website_options_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_media_endpoint', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_simple_taxonomies_endpoint', 10, 1);

add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_custom_filter_products', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_custom_attributes_terms', 10, 1);
add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_v3_endpoints', 10, 1);
*/

// add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_woo_v3_endpoints_term_test', 10, 1);
// add_filter('wp_rest_cache/allowed_endpoints', 'wprc_add_head_content_endpoint', 10, 1); /** Somehow head-content is not cached. Could be due to no json-response(?). Caching is not important, it is just for the developers **/

/*
function wprc_add_simple_pages_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-pages', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-pages';
    return $allowed_endpoints;
}
function wprc_add_simple_posts_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-posts', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-posts';
    return $allowed_endpoints;
}
function wprc_add_simple_custom_posts_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-custom-posts', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-custom-posts';
    return $allowed_endpoints;
}
function wprc_add_website_options_endpoint($allowed_endpoints) {
  if(!isset($allowed_endpoints['wtcustom']) || !in_array('website-options', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'website-options';
  return $allowed_endpoints;
}
function wprc_add_simple_media_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-media', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-media';
    return $allowed_endpoints;
  }
function wprc_add_simple_taxonomies_endpoint($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('simple-taxonomies', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'simple-taxonomies';
    return $allowed_endpoints;
}
function wprc_add_woo_custom_filter_products($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('filter-products', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'filter-products';
    return $allowed_endpoints;
}
function wprc_add_woo_custom_attributes_terms($allowed_endpoints) {
    if(!isset($allowed_endpoints['wtcustom']) || !in_array('attributes-terms', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'attributes-terms';
    return $allowed_endpoints;
}
function wprc_add_woo_v3_endpoints($allowed_endpoints) {
    if(!isset($allowed_endpoints['wc']) || !in_array('v3', $allowed_endpoints['wc'])) $allowed_endpoints['wc'][] = 'v3';
    return $allowed_endpoints;
}
*/

/** Somehow head-content is not cached. Could be due to no json-response(?). Caching is not important, it is just for the developers **/
// function wprc_add_head_content_endpoint($allowed_endpoints) {
  // if(!isset($allowed_endpoints['wtcustom']) || !in_array('head-content', $allowed_endpoints['wtcustom'])) $allowed_endpoints['wtcustom'][] = 'head-content';
  // return $allowed_endpoints;
// }

add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-pages', array(
        'methods' => 'GET',
        'callback' => 'getPagesSimplified',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-posts', array(
        'methods' => 'GET',
        'callback' => 'getPostsSimplified',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-custom-posts', array(
        'methods' => 'GET',
        'callback' => 'getCustomPostsSimplified',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/website-options', array(
        'methods' => 'GET',
        'callback' => 'getWebsiteOptions',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-media', array(
        'methods' => 'GET',
        'callback' => 'getMediaSimplified',
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('wtcustom', '/simple-taxonomies', array(
        'methods' => 'GET',
        'callback' => 'getTaxonomiesSimplified',
    ));
});
/** display <head> section, (for copy-pasting plugin css and js includes) **/
// add_action('rest_api_init', function () {
//     register_rest_route( 'wtcustom', '/head-content',array(
//       'methods'  => 'GET',
//       'callback' => 'getHeadContent'
//     ));
//   });
/** display <head><content><footer> sections, (for copy-pasting plugin css and js includes) **/
add_action('rest_api_init', function () {
    register_rest_route( 'wtcustom', '/wp-generated-content',array(
      'methods'  => 'GET',
      'callback' => 'getWordPressGeneratedPage'
    ));
  });
  
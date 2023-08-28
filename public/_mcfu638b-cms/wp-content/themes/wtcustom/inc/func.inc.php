<?php
function remove_editor_init() {
    remove_post_type_support('page', 'editor');
}
function wprc_change_settings_capability( $capability ) {
    return 'edit_posts'; // Change the capability to users who can edit posts.
}
function set_default_page_template() {
    global $post;
    $currentScreen = get_current_screen();
    if($post->post_type == 'page' && $currentScreen->action == 'add') {
        $post->page_template = "template-section-based.php";
    }
}
// 28-8-2023. Leon Kuijf. Removed api-endpoint caching. Using Laravel Response Cache instead.
/*
function deleteWebsiteOptionsRestCache() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/website-options' );
}
function deleteSimpleMediaRestCache() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-media' );
}
function deleteSimpleTaxonomiesRestCache() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-taxonomies' );
}
function deleteSimplePagesRestCache() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-pages' );
}
function deleteSimpleCustomPostsRestCacheBlog() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=blog' );
}
function deleteSimpleCustomPostsRestCacheCase() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=case' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=case&highlighted=1' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?highlighted=1&post_type=case' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=case&category=online-marketing' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=case&category=web-development' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=case&category=events' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?category=online-marketing&post_type=case' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?category=web-development&post_type=case' );
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?category=events&post_type=case' );
}
function deleteSimpleCustomPostsRestCacheReview() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=review' );
}
function deleteSimpleCustomPostsRestCacheTeammember() {
    \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=teammember' );
}
*/

// function deleteSimplePostsRestCache() {
//     \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-posts' );
// }



/* Remove bulk actions for type: page */
function remove_from_bulk_actions($actions) {
    return array();
}
/* Remove row actions for type: page */
function remove_page_row_actions($actions, $post) {
    if ($post->post_type == 'page') {
        $actions = array();
    }
    return $actions;
}
function removePageActionsEditorRole() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            if(jQuery('body.post-type-page .page-title-action').text() == 'Add New')
                jQuery('body.post-type-page .page-title-action').remove();
            jQuery('body.toplevel_page_nestedpages .page-title-action').remove();
        });
    </script>
    <?php
}
function removePostActionsEditorRole() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            // console.log('[' + jQuery('.page-title-action').text().trim() + ']');
            jQuery('.page-title-action').each(function(){
                if(jQuery(this).text().trim() != 'Add New') jQuery(this).remove();
            });
        });
    </script>
    <?php
}
function customBackendScriptsEditorRol() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            // jQuery('.page-title-action').remove();
            jQuery('.misc-pub-visibility').remove();
            // jQuery('.page-template-label-wrapper').remove();
            // jQuery('#page_template').remove();
            jQuery('#post-preview').remove();

            let pagesToProtect = ['[HOMEPAGE]', 'Blog', 'Online Marketing', 'Webdevelopment', 'Events', 'Instagram feed'];
            pagesToProtect.forEach(page => {
                jQuery('input[value="' + page + '"]').attr('disabled', 'disabled').parent().next().find('button').remove();
                jQuery('input[value="' + page + '"]').closest('#post-body').find('#major-publishing-actions #delete-action').remove();
                jQuery('a.row-title:contains("' + page + '")').parent().siblings('.row-actions').find('.trash').remove();
            });


            /*
            jQuery('.term-display-type-wrap').remove(); // wooCommerce category display type
            jQuery('.term-thumbnail-wrap').remove(); // wooCommerce category thumbnail
            jQuery('h2.nav-tab-wrapper a#settings').remove();
            jQuery('h2.nav-tab-wrapper a#endpoint-api').remove();
            jQuery('input[value="Clear REST Cache"]').parent().remove();
            jQuery('select#dropdown_product_type').remove();
            jQuery('ul.subsubsub li.byorder').remove();
            jQuery('div.row-actions span.inline').remove();
            jQuery('div.row-actions span.view').remove();
            jQuery('a#add-bookly-form').remove();
            jQuery('div#woocommerce-product-data div.postbox-header h2 label[for="_virtual"]').remove();
            jQuery('div#woocommerce-product-data div.postbox-header h2 label[for="_downloadable"]').remove();
            jQuery('div#woocommerce-product-data select#product-type option[value="grouped"]').remove();
            jQuery('div#woocommerce-product-data select#product-type option[value="external"]').remove();
            jQuery('div#woocommerce-product-data select#product-type option[value="variable"]').remove();
            jQuery('ul.product_data_tabs li.shipping_options').remove();
            jQuery('ul.product_data_tabs li.linked_product_options').remove();
            jQuery('ul.product_data_tabs li.advanced_options').remove();
            jQuery('span.description a.sale_schedule').remove();
            jQuery('div#inventory_product_data div.show_if_simple.show_if_variable').remove();
            jQuery('input#attribute_public').parent().parent().remove();
            jQuery('select#attribute_orderby').parent().remove();
            let woomsg = jQuery('.wrap.woocommerce div#message').text();
            if(woomsg.indexOf("With the release of WooCommerce 4.0, these reports are being replaced. There is a new and better Analytics section")) jQuery('.wrap.woocommerce div#message').remove();
            jQuery('aside#woocommerce-activity-panel').remove();
            jQuery('a#post-preview').remove();
            jQuery('a:contains("Preview page")').remove();
            jQuery('a:contains("View page")').remove();
            jQuery('select#post_status option[value=pending]').remove();
            jQuery('div#misc-publishing-actions div#visibility').remove();
            jQuery('div#pageparentdiv p.post-attributes-label-wrapper.menu-order-label-wrapper').remove();
            jQuery('div#pageparentdiv input#menu_order').remove();
            jQuery('div#pageparentdiv p.post-attributes-help-text').remove();
            */


            jQuery('.handle-actions').remove();
        });
    </script>
    <?php
}
function customBackendScripts() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            jQuery('#add_description h2').text('Meta Description');
            // customizeCarbonFieldsPlugin();
            customizeNestedPagesPlugin();
            flushSimplePagesCacheOnDrag();
            jQuery('.cf-complex__inserter-button').css('backgroundColor', '#b3edb3');
            // jQuery('.cf-complex__group-actions.cf-complex__group-actions--tabbed').css('backgroundColor', '#fde6e6');

            // function customizeCarbonFieldsPlugin() {
            //     let divStyles = {
            //         width: '100%',
            //         display: 'block',
            //     };
            //     let addBtnStyles = {
            //         backgroundColor : '#b3edb3',
            //         border: '2px solid #000',
            //         width: '100%',
            //         fontSize: '24px',
            //         color: '#000',
            //     };
            //     let collapseBtnStyles = {
            //         marginLeft: 'auto',
            //         marginTop: '10px',
            //         marginRight: '10px',
            //         padding: '0',
            //         paddingLeft: '5px',
            //         paddingRight: '5px',
            //         minHeight: '20px',
            //         lineHeight: '20px',
            //     };
            //     jQuery(document).ready(function($) {	
            //         jQuery('.cf-complex__inserter').css(divStyles);
            //         jQuery('.cf-complex__inserter-button').css(addBtnStyles).text('Content toevoegen');
            //         jQuery('.cf-complex__toggler').css(collapseBtnStyles);
            //         jQuery('.cf-container__fields').prepend(jQuery('.cf-complex__toggler'));
            //         jQuery('.cf-complex--grid').css('paddingTop', 0);
            //     });
            // }
        });
        function customizeNestedPagesPlugin() {
            jQuery(document).ready(function($) {	
                jQuery('.wrap.nestedpages .action-buttons').remove();
                jQuery('.wrap.nestedpages .nestedpages-list-header').remove();
                jQuery('.wrap.nestedpages .np-bulk-checkbox').remove();
                jQuery('.wrap.nestedpages .nestedpages-listing-title a.open-bulk-modal').remove();
                jQuery('.wrap.nestedpages .nestedpages-listing-title a.open-redirect-modal').remove();
            });
        }

        function flushSimplePagesCacheOnDrag() {
            let menuEls = document.querySelectorAll('.wrap.nestedpages .post-type-page');
            menuEls.forEach(el => {
                el.addEventListener("mousedown", function(event) {
                    // axios.get('/_mcfu638b-cms/wp-content/themes/wtcustom/ajax/flushSimplePagesRestCache.php');
                    jQuery.ajax('/_mcfu638b-cms/wp-content/themes/wtcustom/ajax/flushSimplePagesRestCache.php');
                });
            });
        }

    </script>
    <?php
}
function bundleProductImages($mainId, $galleryIds) {
    $images = array();
    if($mainId) $images[] = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', wp_get_attachment_url($mainId));
    foreach($galleryIds as $imgId) $images[] = str_replace('_mcfu638b-cms/wp-content/uploads', 'media', wp_get_attachment_url($imgId));
    return $images;
}
function remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
    remove_menu_page( 'options-general.php' );
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'edit.php' );
    global $submenu;
    $editor = get_role('editor');
    if(!$editor->has_cap('delete_pages'))
    unset($submenu['edit.php?post_type=page'][10]); // Removes 'Add New'.
}
function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
function remove_admin_bar_menus() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('site-name');
    $wp_admin_bar->remove_menu('view');
}
function add_admin_bar_menus() {
    global $wp_admin_bar;
    $wp_admin_bar->add_menu(
		array(
			'id'     => 'clear-response-cache',
			'parent' => null , // use 'top-secondary' for toggle menu position.
			'href'   => '/clear-response-cache-wt',
			'title'  => __( 'Clear Cache', 'text-domain' ),
            'meta' => ['target' => '_blank'],
		)
	);
}
function contextual_help_list_remove(){
    global $current_screen;
    $current_screen->remove_help_tabs();
}
function remove_screen_options() {
    return false;
}

<?php
use Carbon_Fields\Field;
use Carbon_Fields\Container;

require('inc/func.inc.php');
require('inc/endpoints.inc.php');
require('inc/callbacks.inc.php');
// require('inc/filterproducts.inc.php');
// require('inc/filter-job-offers.inc.php');

$editorCanAddAndRemovePages = true; // !!! may take 2 reloads for changes to take effect !!!
$editorCanAddAndRemovePosts = true; // !!! may take 2 reloads for changes to take effect !!!

$carbonFieldsArgs = array();
$websiteOptions = array();
// $websiteOptions[] = array('text', 'header_big', 'Website kop tekst GROOT');
// $websiteOptions[] = array('separator', 'separator1', 'One-pager titel/omschrijving');
// $websiteOptions[] = array('text', 'meta_title', 'Homepage pagina titel (<title>)');
// $websiteOptions[] = array('text', 'meta_description', 'Homepage pagina omschrijving (<meta name="description">)');
// $websiteOptions[] = array('separator', 'separator2', 'Social Media Pakketten');
// $websiteOptions[] = array('separator', 'separator2_1', 'Pakket 1');
// $websiteOptions[] = array('text', 'package1_title', 'Pakket 1 titel');
// $websiteOptions[] = array('rich_text', 'package1_text', 'Pakket 1 tekst');
// $websiteOptions[] = array('text', 'package1_price', 'Pakket 1 prijs');
// $websiteOptions[] = array('text', 'package1_link', 'Pakket 1 link');
// $websiteOptions[] = array('separator', 'separator2_2', 'Pakket 2');
// $websiteOptions[] = array('text', 'package2_title', 'Pakket 2 titel');
// $websiteOptions[] = array('rich_text', 'package2_text', 'Pakket 2 tekst');
// $websiteOptions[] = array('text', 'package2_price', 'Pakket 2 prijs');
// $websiteOptions[] = array('text', 'package2_link', 'Pakket 2 link');
// $websiteOptions[] = array('separator', 'separator2_3', 'Pakket 3');
// $websiteOptions[] = array('text', 'package3_title', 'Pakket 3 titel');
// $websiteOptions[] = array('rich_text', 'package3_text', 'Pakket 3 tekst');
// $websiteOptions[] = array('text', 'package3_price', 'Pakket 3 prijs');
// $websiteOptions[] = array('text', 'package3_link', 'Pakket 3 link');
// $websiteOptions[] = array('separator', 'separator3', 'Global texts');
// $websiteOptions[] = array('text', 'subscribe_text', 'Aanmelden tekst');
// $websiteOptions[] = array('text', 'facebook', 'Facebook link');
// $websiteOptions[] = array('text', 'linkedin', 'LinkedIn link');
// $websiteOptions[] = array('text', 'twitter', 'Twitter link');
// $websiteOptions[] = array('text', 'instagram', 'Instagram link');
// $websiteOptions[] = array('text', 'form_success', 'Contact formulier succes melding');
// $websiteOptions[] = array('separator', 'separator4', 'Form messages');
// $websiteOptions[] = array('text', 'form_subscription_success', 'Nieuwsbrief aanmeld formulier succes melding');
// $websiteOptions[] = array('text', 'form_error', 'Formulier error melding');
// $websiteOptions[] = array('text', 'apply_success', 'Sollicitatie succes melding');
// $websiteOptions[] = array('text', 'apply_error', 'Sollicitatie error melding');
// $websiteOptions[] = array('text', 'phone_number', 'Telefoonnummer (algemeen, o.a. gebruikt in "Call us"-box en header)');
// $websiteOptions[] = array('text', 'email_address', 'E-mail adres (algemeen, o.a. gebruikt in contact formulier en header)');
// $websiteOptions[] = array('textarea', 'wt_website_textarea1', 'Website textarea 1');
// $websiteOptions[] = array('office_assoc', 'footer_office_1', 'Footer office 1');
// $websiteOptions[] = array('office_assoc', 'footer_office_2', 'Footer office 2');
// $websiteOptions[] = array('rich_text', 'footer_tekst_3', 'Footer blok 3 tekst');
// $websiteOptions[] = array('rich_text', 'footer_tekst_2', 'Footer tekst rechts');
// $websiteOptions[] = array('file', 'wt_algemene_voorwaarden', 'Algemene voorwaarden');
// $websiteOptions[] = array('image', 'header_image', 'Header afbeelding');
// $websiteOptions[] = array('separator', 'separator5', 'Logo\'s en afbeeldingen');
// $websiteOptions[] = array('media_gallery', 'working_with', 'Partner logo\'s');
// $websiteOptions[] = array('separator', 'separator6', 'Statistieken');
// $websiteOptions[] = array('text', 'happy_clients', 'Tevreden klanten (web development dienstenpagina)');
// $websiteOptions[] = array('text', 'total_projects', 'Aantal projecten (web development dienstenpagina)');
// $websiteOptions[] = array('media_gallery', 'events', 'Events');
$carbonFieldsArgs['websiteOptions'] = $websiteOptions;

add_action('init', 'remove_editor_init'); // put this in comment when using a plugin, so the embed-code can be placed in the default editor (Default template)

// add_action( 'init', 'create_posttype_blog' );
// add_action( 'init', 'create_posttype_case' );
// add_action( 'init', 'create_posttype_review' );
// add_action( 'init', 'create_posttype_teammember' );
// add_action( 'init', 'create_posttype_professionals' );
// add_action( 'init', 'create_posttype_vessels' );
// add_action( 'init', 'register_taxonomy_vessel_type' );
// add_action( 'init', 'register_taxonomy_case_category' );

// add_filter( 'manage_case_posts_columns', 'set_custom_case_columns' );
// add_action( 'manage_case_posts_custom_column' , 'custom_case_column', 10, 2 );


function custom_registration_page() {
    add_menu_page(
        'Overzicht aanmeldingen',
        'Aanmeldingen',
        'publish_pages',
        'custom-registration-page',
        'renderRegistrationPage'
    );
}
add_action('admin_menu', 'custom_registration_page');

function renderRegistrationPage() {
    global $wpdb;
    $table_name = 'registrations';
    $selectedRegistrationPageId = false;
    $totalPartners = 0;
    $totalChildren = 0;
    $pages = get_pages();
    $results = [];
    if(isset($_GET['reg_page_id']) && trim($_GET['reg_page_id']) != '') {
        $selectedRegistrationPageId = trim($_GET['reg_page_id']);
        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id = $selectedRegistrationPageId", ARRAY_A);
    } else {
        // $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    }
    foreach($results as $res) { // this is not the way, but ok..
        $totalPartners += $res['partner'];
        $totalChildren += $res['children_amount'];
    }
    ?>
    <div class="wrap">
        <h1>Aanmeldingen</h1>
        <p>Een overzicht van alle aanmeldingen.</p>
        <form action="admin.php" method="GET" style="display:inline-block;border:1px solid #CCC;padding:10px;">
            <input type="hidden" name="page" value="custom-registration-page">
            <select name="reg_page_id" id="">
                <option value="">- Selecteer een pagina -</option>
                <?php
                    foreach($pages as $page) {
                        $selected = '';
                        if($selectedRegistrationPageId && $selectedRegistrationPageId == $page->ID) $selected = ' selected';
                        echo '<option value="' . $page->ID . '"' . $selected . '>' . $page->post_title . '</option>';
                    }
                ?>
            </select>
            <button type="submit">Filteren</button>
        </form>
        <?php
            if($selectedRegistrationPageId) {
                //test2
            ?>
            <p>
                Totaal aantal aanmeldingen: <strong><?php echo count($results) ?></strong><br>
                Totaal aantal partners: <strong><?php echo $totalPartners ?></strong><br>
                Totaal aantal kinderen: <strong><?php echo $totalChildren ?></strong>
            </p>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="font-size:11px">Aanwezig</th>
                        <th style="font-size:11px">Volledige Naam</th>
                        <th style="font-size:11px">Voornaam</th>
                        <th style="font-size:11px">Achternaam</th>
                        <th style="font-size:11px">Geboortedatum</th>
                        <th style="font-size:11px">E-mail</th>
                        <th style="font-size:11px">Bedrijfsnaam</th>
                        <th style="font-size:11px">Dieetwensen</th>
                        <th style="font-size:11px">Partner?</th>
                        <th style="font-size:11px">Naam partner</th>
                        <th style="font-size:11px">Aantal kinderen</th>
                        <th style="font-size:11px">Leeftijd kinderen</th>
                        <th style="font-size:11px">Aangemeld voor</th>
                        <th style="font-size:11px">Tijdstip aanmelding (UTC)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row) : ?>
                        <?php
                            $page_title = get_the_title($row['page_id']);
                        ?>
                        <tr>
                            <td style="font-size:11px"><?php echo ($row['participate']?'Ja':'Nee'); ?></td>
                            <td style="font-size:11px"><?php echo $row['full_name']; ?></td>
                            <td style="font-size:11px"><?php echo $row['first_name']; ?></td>
                            <td style="font-size:11px"><?php echo $row['last_name']; ?></td>
                            <td style="font-size:11px"><?php echo $row['birth_date']; ?></td>
                            <td style="font-size:11px"><?php echo $row['email']; ?></td>
                            <td style="font-size:11px"><?php echo $row['company']; ?></td>
                            <td style="font-size:11px"><?php echo $row['diet_wishes']; ?></td>
                            <td style="font-size:11px"><?php echo ($row['partner']?'Ja':'Nee') ?></td>
                            <td style="font-size:11px"><?php echo $row['partner_name']; ?></td>
                            <td style="font-size:11px"><?php echo $row['children_amount']; ?></td>
                            <td style="font-size:11px"><?php echo $row['children_ages']; ?></td>
                            <td style="font-size:11px"><?php echo ($page_title?$page_title:'- geen pagina gevonden, page_slug_at_registration: ' . $row['page_slug_at_registration'] . ' -'); ?></td>
                            <td style="font-size:11px"><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            } else {
                echo '<p><em>Selecteer eerst een event</em></p>';
            }
        ?>
    </div>
    <?php
}


// function set_custom_case_columns($columns) {
//     // unset( $columns['author'] );
//     $columns['highlighted_on_homepage'] = __( 'Highlighted', 'highlighted' );
//     // $columns['publisher'] = __( 'Publisher', 'your_text_domain' );
//     return $columns;
// }
// function custom_case_column( $column, $post_id ) {
//     switch ( $column ) {
//         case 'highlighted_on_homepage' :
//             // $terms = get_the_term_list( $post_id , 'book_author' , '' , ',' , '' );
//             $highlighted = carbon_get_post_meta( $post_id, 'highlighted' );
//             // if ( is_string( $terms ) )
//                 echo ($highlighted?'Yes':'');
//             // else
//                 // _e( 'Unable to get author(s)', 'your_text_domain' );
//             break;

//     }
// }

// Our custom post type function
// function create_posttype_blog() {
//     register_post_type( 'blog',
//         array(
//             'labels' => array(
//                 'name' => __( 'Blog' ),
//                 'singular_name' => __( 'Blog' ),
//                 'add_new_item' => __( 'Add New Blog-item' ),
//                 'add_new' => __( 'Add New Blog-item' ),
//                 'edit_item' => __( 'Edit Blog-item' ),
//                 'update_item' => __( 'Update Blog-item' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function create_posttype_case() {
//     register_post_type( 'case',
//         array(
//             'labels' => array(
//                 'name' => __( 'Cases' ),
//                 'singular_name' => __( 'Case' ),
//                 'add_new_item' => __( 'Add New Case' ),
//                 'add_new' => __( 'Add New Case' ),
//                 'edit_item' => __( 'Edit Case' ),
//                 'update_item' => __( 'Update Case' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function create_posttype_review() {
//     register_post_type( 'review',
//         array(
//             'labels' => array(
//                 'name' => __( 'Reviews' ),
//                 'singular_name' => __( 'Review' ),
//                 'add_new_item' => __( 'Add New Review' ),
//                 'add_new' => __( 'Add New Review' ),
//                 'edit_item' => __( 'Edit Review' ),
//                 'update_item' => __( 'Update Review' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function create_posttype_teammember() {
//     register_post_type( 'teammember',
//         array(
//             'labels' => array(
//                 'name' => __( 'Team members' ),
//                 'singular_name' => __( 'Team member' ),
//                 'add_new_item' => __( 'Add New Team member' ),
//                 'add_new' => __( 'Add New Team member' ),
//                 'edit_item' => __( 'Edit Team member' ),
//                 'update_item' => __( 'Update Team member' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function create_posttype_offices() {
//     register_post_type( 'office',
//         array(
//             'labels' => array(
//                 'name' => __( 'Offices' ),
//                 'singular_name' => __( 'Office' ),
//                 'add_new_item' => __( 'Add New Office' ),
//                 'add_new' => __( 'Add New Office' ),
//                 'edit_item' => __( 'Edit Office' ),
//                 'update_item' => __( 'Update Office' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function create_posttype_professionals() {
//     register_post_type( 'professional',
//         array(
//             'labels' => array(
//                 'name' => __( 'Professionals' ),
//                 'singular_name' => __( 'Professional' ),
//                 'add_new_item' => __( 'Add New Professional' ),
//                 'add_new' => __( 'Add New Professional' ),
//                 'edit_item' => __( 'Edit Professional' ),
//                 'update_item' => __( 'Update Professional' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function create_posttype_vessels() {
//     register_post_type( 'vessel',
//         array(
//             'labels' => array(
//                 'name' => __( 'Vessels' ),
//                 'singular_name' => __( 'Vessel' ),
//                 'add_new_item' => __( 'Add New Vessel' ),
//                 'add_new' => __( 'Add New Vessel' ),
//                 'edit_item' => __( 'Edit Vessel' ),
//                 'update_item' => __( 'Update Vessel' ),
//             ),
//             'public' => true,
//             // 'has_archive' => true,
//             // 'rewrite' => array('slug' => 'movies'),
//             'show_in_rest' => true,
//             // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
//             'supports'            => array( 'title'),
//             )
//     );
// }
// function register_taxonomy_vessel_type() {
//     $labels = array(
//         'name'              => _x( 'Types', 'taxonomy general name' ),
//         'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
//         'search_items'      => __( 'Search Types' ),
//         'all_items'         => __( 'All Types' ),
//         'parent_item'       => __( 'Parent Type' ),
//         'parent_item_colon' => __( 'Parent Type:' ),
//         'edit_item'         => __( 'Edit Type' ),
//         'update_item'       => __( 'Update Type' ),
//         'add_new_item'      => __( 'Add New Type' ),
//         'new_item_name'     => __( 'New Type Name' ),
//         'menu_name'         => __( 'Type' ),
//     );
//     $args   = array(
//         'hierarchical'      => true, // make it hierarchical (like categories)
//         'labels'            => $labels,
//         'show_ui'           => true,
//         'show_admin_column' => true,
//         'show_in_rest'      => true,
//         'query_var'         => true,
//         'rewrite'           => [ 'slug' => 'vessel_type' ],
//     );
//     register_taxonomy( 'vessel_type', [ 'vessel' ], $args );
// }
// function register_taxonomy_case_category() {
//     $labels = array(
//         'name'              => _x( 'Categories', 'taxonomy general name' ),
//         'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
//         'search_items'      => __( 'Search Categories' ),
//         'all_items'         => __( 'All Categories' ),
//         'parent_item'       => __( 'Parent Category' ),
//         'parent_item_colon' => __( 'Parent Category:' ),
//         'edit_item'         => __( 'Edit Category' ),
//         'update_item'       => __( 'Update Category' ),
//         'add_new_item'      => __( 'Add New Category' ),
//         'new_item_name'     => __( 'New Category Name' ),
//         'menu_name'         => __( 'Category' ),
//     );
//     $args   = array(
//         'hierarchical'      => true, // make it hierarchical (like categories)
//         'labels'            => $labels,
//         'show_ui'           => true,
//         'show_admin_column' => true,
//         'show_in_rest'      => true,
//         'query_var'         => true,
//         // 'rewrite'           => [ 'slug' => 'case_category' ],
//     );
//     register_taxonomy( 'case_category', [ 'case' ], $args );
// }

$editor = get_role('editor');
$capabilities_pages = array(
    'delete_others_pages',
    'delete_pages',
    'delete_private_pages',
    'delete_published_pages',
    'publish_pages',
);
$capabilities_posts = array(
    'delete_others_posts',
    'delete_posts',
    'delete_private_posts',
    'delete_published_posts',
    'publish_posts',
);
if($editorCanAddAndRemovePages && !$editor->has_cap('delete_pages')) {
    function change_editor_capabilities1($role, $caps) {
        foreach($caps as $cap) $role->add_cap($cap);
    }
    add_action( 'wt_cap_action', 'change_editor_capabilities1', 10, 2);
    do_action( 'wt_cap_action', $editor, $capabilities_pages );
}
if(!$editorCanAddAndRemovePages && $editor->has_cap('delete_pages')) {
    function change_editor_capabilities2($role, $caps) {
        foreach($caps as $cap) $role->remove_cap($cap);
    }
    add_action( 'wt_cap_action', 'change_editor_capabilities2', 10, 2);
    do_action( 'wt_cap_action', $editor, $capabilities_pages );
}


if($editorCanAddAndRemovePosts && !$editor->has_cap('delete_posts')) {
    function change_editor_capabilities3($role, $caps) {
        foreach($caps as $cap) $role->add_cap($cap);
    }
    add_action( 'wt_cap_action', 'change_editor_capabilities3', 10, 2);
    do_action( 'wt_cap_action', $editor, $capabilities_posts );
}
if(!$editorCanAddAndRemovePosts && $editor->has_cap('delete_posts')) {
    function change_editor_capabilities4($role, $caps) {
        foreach($caps as $cap) $role->remove_cap($cap);
    }
    add_action( 'wt_cap_action', 'change_editor_capabilities4', 10, 2);
    do_action( 'wt_cap_action', $editor, $capabilities_posts );
}

if (!current_user_can('administrator')) {

    if(!$editor->has_cap('delete_pages')) add_action('admin_footer', 'removePageActionsEditorRole');
    if(!$editor->has_cap('delete_posts')) add_action('admin_footer', 'removePostActionsEditorRole');

    

    add_filter('bulk_actions-edit-page', 'remove_from_bulk_actions');
    // add_filter('page_row_actions', 'remove_page_row_actions', 10, 2);
    
    // add_action('admin_head', 'customBackendStyles');
    

    add_action('admin_footer', 'customBackendScriptsEditorRol');
    add_filter('carbon_fields_theme_options_container_admin_only_access', '__return_false');
    add_filter('wp_rest_cache/settings_capability', 'wprc_change_settings_capability', 10, 1);

    add_action('admin_menu', 'remove_admin_menus' );
    add_action('init', 'remove_comment_support', 100);
    add_action('wp_before_admin_bar_render', 'remove_admin_bar_menus' );

    add_filter('contextual_help_list','contextual_help_list_remove');
    add_filter('screen_options_show_screen', 'remove_screen_options');
}

add_action('wp_before_admin_bar_render', 'add_admin_bar_menus' );

add_action('admin_enqueue_scripts', 'wt_admin_style');
function wt_admin_style() {
    wp_enqueue_style( 'admin-style', get_stylesheet_directory_uri() . '/css/wt.css' );
}

add_action('admin_footer', 'customBackendScripts');

add_action('add_meta_boxes', 'set_default_page_template', 1);
add_action('carbon_fields_register_fields', function() use ( $carbonFieldsArgs ) { crbRegisterFields( $carbonFieldsArgs ); });

// 28-8-2023. Leon Kuijf. Removed api-endpoint caching. Using Laravel Response Cache instead.
/*
add_action('carbon_fields_theme_options_container_saved', 'deleteWebsiteOptionsRestCache');

add_action('add_attachment', 'deleteSimpleMediaRestCache');
add_action('delete_attachment', 'deleteSimpleMediaRestCache');
add_action('attachment_updated', 'deleteSimpleMediaRestCache');

add_action('create_term', 'deleteSimpleTaxonomiesRestCache');
add_action('edit_term', 'deleteSimpleTaxonomiesRestCache');
add_action('delete_term', 'deleteSimpleTaxonomiesRestCache');

add_action('save_post_page', 'deleteSimplePagesRestCache');
add_action('save_post_blog', 'deleteSimpleCustomPostsRestCacheBlog');
add_action('save_post_case', 'deleteSimpleCustomPostsRestCacheCase');
add_action('save_post_review', 'deleteSimpleCustomPostsRestCacheReview');
add_action('save_post_teammember', 'deleteSimpleCustomPostsRestCacheTeammember');
*/


// add_action('save_post_page', 'deleteAllPostRestCache');
// add_action('save_post_blog', 'deleteAllPostRestCache');
// add_action('save_post', 'deleteAllPostRestCache');

// add_action( 'pre_post_update', 'deleteAllPostRestCache', 10, 3 );

// function deleteAllPostRestCache() {
//     \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-pages' );
//     \WP_Rest_Cache_Plugin\Includes\Caching\Caching::get_instance()->delete_cache_by_endpoint( '/_mcfu638b-cms/index.php/wp-json/wtcustom/simple-custom-posts?post_type=blog' );
// }

// add_action('admin_head', 'loadAxios');
// function loadAxios() {
//     echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js" integrity="sha512-u9akINsQsAkG9xjc1cnGF4zw5TFDwkxuc9vUp5dltDWYCSmyd0meygbvgXrlc/z7/o4a19Fb5V0OUE58J7dcyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
// }
function crbRegisterFields($args) {

    Container::make( 'post_meta', __( 'Page information' ) )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'template-section-based.php' )
        ->add_tab( __('Page content'), array(
            Field::make( 'complex', 'crb_sections', 'Sections' )->set_visible_in_rest_api($visible = true)
                ->set_layout( 'tabbed-vertical' )
                // ->add_fields( 'hero', 'Hero (big header)', array(
                //     Field::make( 'separator', 'separator1', __( 'Hero (big header)' ) ),
                //     Field::make( 'media_gallery', 'crb_media_gallery', __( 'Media Gallery' ) )
                //         ->set_type( array( 'image' ) )->set_duplicates_allowed( false ),
                //     Field::make( 'text', 'hero_title', __( 'Hero title (can use html-tags)' ) ),
                //     // Field::make( 'text', 'small_header', __( 'Small header text (can use html-tags)' ) ),
                //     Field::make( 'textarea', 'text', __( 'Text (can use html-tags)' ) ),
                //     Field::make( 'text', 'btn_email', __( 'E-mail address (leave blank to remove the button)' ) ),
                //     Field::make( 'text', 'btn_phone', __( 'Phone number (leave blank to remove the button)' ) ),
                //     // Field::make( 'checkbox', 'show_logo', __('Show Glomar Offshore logo') ),

                //     // Field::make( 'image', 'image', 'Afbeelding' )->set_value_type( 'url' ),
                //     // Field::make( 'select', 'color', __( 'Choose block letters color' ) )
                //     // ->set_options( array(
                //         // 'white' => __( 'White' ),
                //         // 'black' => __( 'Black' ),
                //     // ) ),
                //     // Field::make( 'checkbox', 'show_reserve_button', __( 'Show reserve button' ) ),
                //     // Field::make( 'image', 'image', 'Afbeelding' ),
                //     // Field::make( 'rich_text', 'text', 'Tekst' ),
                // ) )
                ->add_fields( 'registration_form', 'Aanmeld formulier', array(
                    Field::make( 'separator', 'separator1', __( 'Formulier gegevens' ) ),
                    Field::make( 'image', 'image', __( 'Hero image' ) ),
                    Field::make( 'text', 'title', __( 'Title' ) ),
                    Field::make( 'rich_text', 'text', __( 'Text' ) ),
                    Field::make( 'separator', 'separator2', __( 'Formulier opties' ) ),
                    Field::make( 'checkbox', 'show_participate', __( 'Show Participate y/n checkbox' ) ),
                    Field::make( 'checkbox', 'show_full_name', __( 'Show Full name field' ) ),
                    Field::make( 'checkbox', 'show_first_name', __( 'Show First name field' ) ),
                    Field::make( 'checkbox', 'show_last_name', __( 'Show Last name field' ) ),
                    Field::make( 'checkbox', 'show_birth_date', __( 'Show Birth date field' ) ),
                    // Field::make( 'checkbox', 'show_email', __( 'Show E-mail field' ) ),
                    Field::make( 'checkbox', 'show_company', __( 'Show Company field' ) ),
                    Field::make( 'checkbox', 'show_partner', __( 'Show Partner y/n checkbox' ) ),
                    Field::make( 'checkbox', 'show_partner_name', __( 'Show Partner name field' ) ),
                    Field::make( 'checkbox', 'show_children_amount', __( 'Show children amount drop-down' ) ),
                    Field::make( 'checkbox', 'show_children_ages', __( 'Show children ages field' ) ),
                    Field::make( 'checkbox', 'show_diet_wishes', __( 'Show Diet wishes checkboxes' ) ),
                    Field::make( 'separator', 'separator3', __( 'Formulier sluiten' ) ),
                    Field::make( 'checkbox', 'close_form', __( 'Sluit het formulier. Pagina is nog wel beschikbaar, maar er kan niet meer aangemeld worden.' ) ),
                ) )
                // ->add_fields( 'text', 'Tekst', array(
                //     Field::make( 'separator', 'separator1', __( 'Text section' ) ),

                //     // Field::make( 'text', 'header', __( 'Header (can use html-tags)' ) ),
                //     Field::make( 'rich_text', 'text', __( 'Text' ) ),
                //     // Field::make( 'image', 'image', __( 'Image' ) ),
                //     // Field::make( 'complex', 'cta_button', __( 'Add Button' ) )
                //     //     ->add_fields( array(
                //     //         Field::make( 'select', 'color', __( 'Color' ) )
                //     //         ->set_options( array(
                //     //             'full_orange' => __( 'Full orange' ),
                //     //             'orange_border' => __( 'White with orange border' ),
                //     //             'transparent' => __( 'Transparent' ),
                //     //         ) ),
                //     //         Field::make( 'select', 'icon', __( 'Icon' ) )
                //     //         ->set_options( array(
                //     //             'no_icon' => __( 'No icon' ),
                //     //             'icon_phone' => __( 'Phone' ),
                //     //             'icon_external_link' => __( 'External link' ),
                //     //         ) ),
                //     //         Field::make( 'text', 'text', __( 'Button Text (can use html-tags)' ) ),
                //     //         Field::make( 'text', 'url', __( 'Button URL' ) ),
                //     //         Field::make( 'select', 'target', __( 'Target' ) )
                //     //         ->set_options( array(
                //     //             'same_tab' => __( 'Same tab' ),
                //     //             'new_tab' => __( 'New tab' ),
                //     //         ) ),
                //     // ) ),

                //     // Field::make( 'separator', 'separator2', __( 'Text Section 2 (optional)' ) ),

                //     // Field::make( 'text', 'header_2', __( 'Header (can use html-tags)' ) ),
                //     // Field::make( 'rich_text', 'text_2', __( 'Text' ) ),
                //     // Field::make( 'image', 'image_2', __( 'Image' ) ),
                //     // Field::make( 'complex', 'cta_button_2', __( 'Add Button' ) )
                //     //     ->add_fields( array(
                //     //         Field::make( 'select', 'color', __( 'Color' ) )
                //     //         ->set_options( array(
                //     //             'full_orange' => __( 'Full orange' ),
                //     //             'orange_border' => __( 'White with orange border' ),
                //     //             'transparent' => __( 'Transparent' ),
                //     //         ) ),
                //     //         Field::make( 'select', 'icon', __( 'Icon' ) )
                //     //         ->set_options( array(
                //     //             'no_icon' => __( 'No icon' ),
                //     //             'icon_phone' => __( 'Phone' ),
                //     //             'icon_external_link' => __( 'External link' ),
                //     //         ) ),
                //     //         Field::make( 'text', 'text', __( 'Button Text (can use html-tags)' ) ),
                //     //         Field::make( 'text', 'url', __( 'Button URL' ) ),
                //     //         Field::make( 'select', 'target', __( 'Target' ) )
                //     //         ->set_options( array(
                //     //             'same_tab' => __( 'Same tab' ),
                //     //             'new_tab' => __( 'New tab' ),
                //     //         ) ),
                //     // ) ),
                // ) )
                // ->add_fields( 'office_boxes', 'Offices', array(
                //     Field::make( 'separator', 'separator1', __( 'Offices' ) ),
                //     Field::make( 'association', 'office_associations', __( 'Select offices' ))
                //     ->set_types( array(
                //         array(
                //             'type' => 'post',
                //             'post_type' => 'office',
                //         ),
                //     ) )
                // ) )
                // ->add_fields( 'team_specialists', 'Team specialists', array(
                //     Field::make( 'separator', 'separator1', __( 'Team specialists' ) ),
                //     Field::make( 'association', 'team_specialists_associations', __( 'Select team specialists' ))
                //     ->set_types( array(
                //         array(
                //             'type' => 'post',
                //             'post_type' => 'teammember',
                //         ),
                //     ) )
                // ) )
                // ->add_fields( 'news_boxes', 'News', array(
                //     Field::make( 'separator', 'separator1', __( 'News' ) ),
                //     Field::make( 'association', 'news_associations', __( 'Select news (max 3)' ))
                //     ->set_types( array(
                //         array(
                //             'type' => 'post',
                //             'post_type' => 'news',
                //         ),
                //     ) )
                // ) )
                // ->add_fields( 'vessel_boxes', 'Vessels', array(
                //     Field::make( 'separator', 'separator1', __( 'Vessels' ) ),
                //     Field::make( 'association', 'vessels_associations', __( 'Select vessels' ))
                //     ->set_types( array(
                //         array(
                //             'type' => 'post',
                //             'post_type' => 'vessel',
                //         ),
                //     ) )
                // ) )
                // ->add_fields( 'packages', 'Price Packages', array(
                //     Field::make( 'separator', 'separator1', __( 'Prijs Pakketten' ) ),
                //     Field::make( 'checkbox', 'show_packages', __( 'Show Pricing Packages' ) ),
                // ) )
                // ->add_fields( 'working_with', 'Clients', array(
                //     Field::make( 'separator', 'separator1', __( 'Clients' ) ),
                //     Field::make( 'checkbox', 'show_working_with', __( 'Show "Clients" section' ) ),
                // ) )
                // ->add_fields( 'reviews', 'Reviews', array(
                //     Field::make( 'separator', 'separator1', __( 'Reviews' ) ),
                //     Field::make( 'checkbox', 'show_reviews', __( 'Show Reviews' ) ),
                // ) )
                // ->add_fields( 'teammembers', 'Team members', array(
                //     Field::make( 'separator', 'separator1', __( 'Team members' ) ),
                //     Field::make( 'checkbox', 'show_teammembers', __( 'Show Team members carousel' ) ),
                // ) )
                // ->add_fields( 'service_page_text_header', 'Service page text header', array(
                //     Field::make( 'separator', 'separator1', __( 'Service page text header (H1)' ) ),
                //     // Field::make( 'checkbox', 'show_teammembers', __( 'Show Team members carousel' ) ),
                //     Field::make( 'text', 'title', __( 'Header text' ) ),
                // ) )
                // ->add_fields( 'marketing_terms', 'Marketing terms', array(
                //     Field::make( 'separator', 'separator1', __( 'Marketing page terms' ) ),
                //     // Field::make( 'checkbox', 'show_teammembers', __( 'Show Team members carousel' ) ),
                //     Field::make( 'image', 'image1', __( 'Term 1 image' ) ),
                //     Field::make( 'text', 'term1', __( 'Term 1 title' ) ),
                //     Field::make( 'text', 'term1_text', __( 'Term 1 text' ) ),
                //     Field::make( 'image', 'image2', __( 'Term 2 image' ) ),
                //     Field::make( 'text', 'term2', __( 'Term 2 title' ) ),
                //     Field::make( 'text', 'term2_text', __( 'Term 2 text' ) ),
                //     Field::make( 'image', 'image3', __( 'Term 3 image' ) ),
                //     Field::make( 'text', 'term3', __( 'Term 3 title' ) ),
                //     Field::make( 'text', 'term3_text', __( 'Term 4 text' ) ),
                //     Field::make( 'image', 'image4', __( 'Term 4 image' ) ),
                //     Field::make( 'text', 'term4', __( 'Term 4 title' ) ),
                //     Field::make( 'text', 'term4_text', __( 'Term 5 text' ) ),
                //     Field::make( 'image', 'image5', __( 'Term 5 image' ) ),
                //     Field::make( 'text', 'term5', __( 'Term 5 title' ) ),
                //     Field::make( 'text', 'term5_text', __( 'Term 6 text' ) ),
                //     Field::make( 'image', 'image6', __( 'Term 6 image' ) ),
                //     Field::make( 'text', 'term6', __( 'Term 6 title' ) ),
                //     Field::make( 'text', 'term6_text', __( 'Term 7 text' ) ),
                //     Field::make( 'image', 'image7', __( 'Term 7 image' ) ),
                //     Field::make( 'text', 'term7', __( 'Term 7 title' ) ),
                //     Field::make( 'text', 'term7_text', __( 'Term 8 text' ) ),
                //     Field::make( 'image', 'image8', __( 'Term 8 image' ) ),
                //     Field::make( 'text', 'term8', __( 'Term 8 title' ) ),
                //     Field::make( 'text', 'term8_text', __( 'Term 9 text' ) ),
                // ) )
                // ->add_fields( 'cases', 'Cases', array(
                //     Field::make( 'separator', 'separator1', __( 'Cases' ) ),
                //     Field::make( 'checkbox', 'show_cases_highlighted', __( 'Show highlighted cases' ) ),
                //     Field::make( 'checkbox', 'show_cases_online_marketing', __( 'Show online marketing cases' ) ),
                //     Field::make( 'checkbox', 'show_cases_web_development', __( 'Show web development cases' ) ),
                //     Field::make( 'checkbox', 'show_cases_events', __( 'Show event cases' ) ),
                // ) )
                // ->add_fields( 'schedule_call', 'Schedule a call Form', array(
                //     Field::make( 'separator', 'separator1', __( 'Schedule a call' ) ),
                //     Field::make( 'text', 'title', __( 'Title' ) ),
                //     Field::make( 'rich_text', 'text', __( 'Text' ) ),
                //     Field::make( 'text', 'email_to', __( 'E-mail to' ) ),
                //     Field::make( 'text', 'success_text', __( 'Text on success' ) ),
                // ) )
                // ->add_fields( 'get_in_touch', 'Get in touch', array(
                //     Field::make( 'separator', 'separator1', __( 'Get in touch' ) ),
                //     Field::make( 'checkbox', 'show_get_in_touch', __( 'Show "Get in touch" section' ) ),
                // ) )
                // ->add_fields( 'statistics', 'Statistics', array(
                //     Field::make( 'separator', 'separator1', __( 'Statistics' ) ),
                //     Field::make( 'complex', 'stats', __( 'Add statistic' ) )
                //         ->add_fields( array(
                //             Field::make( 'select', 'icon', __( 'Icon' ) )
                //             ->set_options( array(
                //                 'icon_world' => __( 'World' ),
                //                 'icon_crew' => __( 'Crew' ),
                //                 'icon_calendar' => __( 'Calendar' ),
                //                 'icon_thumb' => __( 'Thumb' ),
                //                 'icon_recycle' => __( 'Recycle' ),
                //                 'icon_wind' => __( 'Wind' ),
                //                 'icon_building' => __( 'Building' ),
                //             ) ),
                //             Field::make( 'text', 'number', __( 'Number' ) ),
                //             Field::make( 'text', 'headline', __( 'Headline' ) ),
                //             Field::make( 'text', 'subline', __( 'Subline' ) ),
                //     ) )
                // ) )

                // ->add_fields( '1column', 'Content', array(
                //     Field::make( 'complex', 'fullwidth', 'Content' )
                //         ->add_fields('tekst', array(
                //             // Field::make( 'separator', 'separator1', __( 'Text' ) ),
                //             Field::make( 'rich_text', 'text', 'Tekst' ),
                //         ) )
                //         ->add_fields('afbeelding', array(
                //             // Field::make( 'separator', 'separator1', __( 'Image' ) ),
                //             Field::make( 'image', 'image', 'Afbeelding' ),
                //         ) )
                //         ->add_fields('button', array(
                //             Field::make( 'text', 'title', 'Titel' ),
                //             Field::make( 'text', 'url', 'Link' ),
                //         ) )
                //         ->add_fields( 'services_buttons', 'Services buttons', array(
                //             // Field::make( 'separator', 'separator1', __( 'Services' ) ),
                //             Field::make( 'checkbox', 'show_services_buttons', __( 'Show services buttons' ) ),
                //         ) ),

                //         // ->add_fields('bestand', array(
                //         //     Field::make( 'file', 'file', 'Bestand' ),
                //         //     Field::make( 'text', 'title', 'Titel' ),
                //         // ) )
                //         // ->add_fields('nieuws-items', array(
                //         //     Field::make( 'association', 'news_associations', __( 'Select news items' ))
                //         //     ->set_types( array(
                //         //         array(
                //         //             'type' => 'post',
                //         //             'post_type' => 'news',
                //         //         ),
                //         //     ) )
                //         // ) ),
                // ) )

                // ->add_fields( '2column', 'Content (2 kolommen)', array(
                //     Field::make( 'select', 'column_direction', __( 'Column direction on mobile device' ) )
                //     ->set_options( array(
                //         'default' => __( 'Standaard' ),
                //         'reverse' => __( 'Omgekeerd' ),
                //     ) ),
                //     Field::make( 'complex', 'left', 'Linker kolom' )
                //         ->add_fields('tekst', array(
                //             Field::make( 'rich_text', 'text', 'Tekst' ),
                //         ) )
                //         ->add_fields('afbeelding', array(
                //             Field::make( 'image', 'image', 'Afbeelding' ),
                //         ) )
                //         ->add_fields('button', array(
                //             Field::make( 'text', 'title', 'Titel' ),
                //             Field::make( 'text', 'url', 'Link' ),
                //         ) )
                //         ->add_fields( 'services_buttons', 'Services buttons', array(
                //             // Field::make( 'separator', 'separator1', __( 'Services' ) ),
                //             Field::make( 'checkbox', 'show_services_buttons', __( 'Show services buttons' ) ),
                //         ) ),
                //         // ->add_fields('bestand', array(
                //         //     Field::make( 'file', 'file', 'Bestand' ),
                //         //     Field::make( 'text', 'title', 'Titel' ),
                //         // ) )
                //         // ->add_fields('nieuws-items', array(
                //         //     Field::make( 'association', 'news_associations', __( 'Select news items' ))
                //         //     ->set_types( array(
                //         //         array(
                //         //             'type' => 'post',
                //         //             'post_type' => 'news',
                //         //         ),
                //         //     ) )
                //         // ) ),
                //     Field::make( 'complex', 'right', 'Rechter kolom' )
                //         ->add_fields('tekst', array(
                //             Field::make( 'rich_text', 'text', 'Tekst' ),
                //         ) )
                //         ->add_fields('afbeelding', array(
                //             Field::make( 'image', 'image', 'Afbeelding' ),
                //         ) )
                //         ->add_fields('button', array(
                //             Field::make( 'text', 'title', 'Titel' ),
                //             Field::make( 'text', 'url', 'Link' ),
                //         ) )
                //         ->add_fields( 'services_buttons', 'Services buttons', array(
                //             // Field::make( 'separator', 'separator1', __( 'Services' ) ),
                //             Field::make( 'checkbox', 'show_services_buttons', __( 'Show services buttons' ) ),
                //         ) ),
                //         // ->add_fields('bestand', array(
                //         //     Field::make( 'file', 'file', 'Bestand' ),
                //         //     Field::make( 'text', 'title', 'Titel' ),
                //         // ) )
                //         // ->add_fields('nieuws-items', array(
                //         //     Field::make( 'association', 'news_associations', __( 'Select news items' ))
                //         //     ->set_types( array(
                //         //         array(
                //         //             'type' => 'post',
                //         //             'post_type' => 'news',
                //         //         ),
                //         //     ) )
                //         // ) )
                // ) )

        ))
        ->add_tab( __('Page information'), array(
            Field::make( 'separator', 'separator', __( 'Information about the page' ) ),
            Field::make( 'text', 'meta_title', __( 'Page title (shown in browser tab)' ))->set_visible_in_rest_api($visible = true),
            Field::make( 'text', 'meta_description', __( 'Page meta description (shown in search engines)' ))->set_visible_in_rest_api($visible = true),
            Field::make( 'checkbox', 'hide_from_menu', __('Hide page from menu (page is still available via permalink)') )->set_visible_in_rest_api($visible = true),
        ))
        ;

    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'blog' )
    //     ->add_fields(array(
    //         // Field::make( 'text', 'title', __( 'Title' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'image', 'small_image', __( 'Card image' ) )->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator1', __( 'Hero information' ) ),
    //         Field::make( 'media_gallery', 'gallery', __( 'Hero images' ) )->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'hero_title', __( 'Hero title (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'textarea', 'hero_text', __( 'Hero text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator2', __( 'Blog text' ) ),
    //         Field::make( 'rich_text', 'text', __( 'Text' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'textarea', 'card_text', __( 'Card text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator3', __( 'SEO information' ) ),
    //         Field::make( 'text', 'page_title', __( 'Blog title (shown in browser tab)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'page_meta_description', __( 'Blog meta description (shown in search engines)' ))->set_visible_in_rest_api($visible = true),
    //         )
    //     );
    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'case' )
    //     ->add_fields(array(
    //         // Field::make( 'separator', 'separator1', __( 'Images' ) ),
    //         Field::make( 'media_gallery', 'gallery', __( 'Images' ) )->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'text', 'hero_title', __( 'Hero title (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'textarea', 'hero_text', __( 'Hero text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator2', __( 'Case text' ) ),
    //         Field::make( 'rich_text', 'text', __( 'Text' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'textarea', 'card_text', __( 'Card text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator3', __( 'SEO information' ) ),
    //         Field::make( 'text', 'page_title', __( 'Blog title (shown in browser tab)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'page_meta_description', __( 'Blog meta description (shown in search engines)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator4', __( 'Extra options' ) ),
    //         Field::make( 'checkbox', 'highlighted', __('Show on homepage') ),
    //         )
    //     );
    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'review' )
    //     ->add_fields(array(
    //         // Field::make( 'separator', 'separator1', __( 'Images' ) ),
    //         Field::make( 'image', 'image', __( 'Image' ) ),
    //         // Field::make( 'media_gallery', 'gallery', __( 'Images' ) )->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'text', 'hero_title', __( 'Hero title (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'textarea', 'hero_text', __( 'Hero text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator2', __( 'Review text' ) ),
    //         Field::make( 'text', 'leading_title', __( 'Leading title' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'rich_text', 'text', __( 'Text' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'text', 'by', __( 'Review by' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'textarea', 'card_text', __( 'Card text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'separator', 'separator3', __( 'SEO information' ) ),
    //         // Field::make( 'separator', 'separator4', __( 'Extra options' ) ),
    //         // Field::make( 'checkbox', 'highlighted', __('Show on homepage') ),
    //         )
    //     );
    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'teammember' )
    //     ->add_fields(array(
    //         // Field::make( 'separator', 'separator1', __( 'Images' ) ),
    //         Field::make( 'image', 'image', __( 'Image' ) )->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'media_gallery', 'gallery', __( 'Images' ) )->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'text', 'hero_title', __( 'Hero title (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'textarea', 'hero_text', __( 'Hero text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'separator', 'separator2', __( 'Review text' ) ),
    //         Field::make( 'text', 'function', __( 'Function' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'rich_text', 'text', __( 'Text' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'text', 'by', __( 'Review by' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'textarea', 'card_text', __( 'Card text (can use html-tags)' ))->set_visible_in_rest_api($visible = true),
    //         // Field::make( 'separator', 'separator3', __( 'SEO information' ) ),
    //         // Field::make( 'separator', 'separator4', __( 'Extra options' ) ),
    //         // Field::make( 'checkbox', 'highlighted', __('Show on homepage') ),
    //         )
    //     );
    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'office' )
    //     ->add_fields(array(
    //         Field::make( 'text', 'country', __( 'Country' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'phone', __( 'Phone number' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'email', __( 'E-mail address' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'address1', __( 'Address line 1' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'address2', __( 'Address line 2' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'address3', __( 'Address line 3' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'address4', __( 'Address line 4' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'google_maps_address', __( 'Address for Google Maps Marker' ))->set_visible_in_rest_api($visible = true),
    //         )
    //     );
    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'professional' )
    //     ->add_fields(array(
    //         Field::make( 'text', 'function', __( 'Function' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'image', 'image', __( 'Image' ) )->set_visible_in_rest_api($visible = true),
    //         )
    //     );

    // Container::make( 'post_meta', __( 'Information' ) )
    //     ->where( 'post_type', '=', 'vessel' )
    //     ->add_fields(array(
    //         Field::make( 'image', 'small_image', __( 'Card image' ) )->set_visible_in_rest_api($visible = true),
    //         Field::make( 'image', 'large_image', __( 'Hero image' ) )->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'type_text', __( 'Type' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator1', __( 'General' ) ),
    //         Field::make( 'text', 'class', __( 'Class' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'flag', __( 'Flag' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'built', __( 'Built' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'rebuilt', __( 'Rebuilt' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'call-sign', __( 'Call Sign' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator2', __( 'Dimensions' ) ),
    //         Field::make( 'text', 'length', __( 'Length' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'breadth', __( 'Breadth' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'draft', __( 'Draft' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'depth', __( 'Depth' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'gt', __( 'GT' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'nt', __( 'NT' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator3', __( 'Performance' ) ),
    //         Field::make( 'text', 'bollard-pull', __( 'Bollard Pull' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'max-speed-fuel-consumption', __( 'Max Speed + Fuel Consumption' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'cruise-economic-speed-fuel-consumption', __( 'Cruise/Economic Speed + Fuel Consumption' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'standby-fuel-consumption', __( 'Standby Fuel Consumption' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator4', __( 'Machinery and Propulsion' ) ),
    //         Field::make( 'text', 'main-engine', __( 'Main Engine' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'gearbox', __( 'Gearbox' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'propellor-shafts', __( 'Propellor & shafts' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'auxiliary-engines', __( 'Auxiliary Engines' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'harbor-generator', __( 'Harbor generator' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'azimuth-thruster', __( 'Azimuth Thruster' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'sewage-plant', __( 'Sewage plant' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'engine-room-fire-protection', __( 'Engine room fire protection' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'bilge-water-separator', __( 'Bilge water separator' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'fuel-oil-separator', __( 'Fuel oil separator' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'electrical-installation', __( 'Electrical installation' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator5', __( 'Tank capacities and deck area' ) ),
    //         Field::make( 'text', 'total-fresh-water-tank', __( 'Total Fresh Water Tank' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'total-ballast-water-tank', __( 'Total Ballast Water Tank' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'total-fuel-tank', __( 'Total Fuel Tank' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'deck-area', __( 'Deck Area' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator6', __( 'Deck equipment' ) ),
    //         Field::make( 'text', 'deckcrane-ps', __( 'Deckcrane PS' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'safe-working-load', __( 'Safe Working Load' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'steel-wire-1', __( 'Steel Wire' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator7', __( 'Rescue equipment' ) ),
    //         Field::make( 'text', 'davit-sb', __( 'Davit SB' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'working-load-limit', __( 'Working Load Limit' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'steel-wire-2', __( 'Steel Wire' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'fast-rescue-craft', __( 'Fast Rescue Craft' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator8', __( 'Radio and navigation equipment' ) ),
    //         Field::make( 'text', 'compass', __( 'Compass' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'radar', __( 'Radar' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'gmdss', __( 'GMDSS' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'epirb', __( 'EPIRB' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'sart', __( 'SART' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'ais', __( 'AIS' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'lrit', __( 'LRIT' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator9', __( 'Accommodation' ) ),
    //         Field::make( 'text', 'total-accommodation', __( 'Total Accommodation (Berths)' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'cabins', __( 'Cabins' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'single-cabins', __( 'Single Cabins' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'double-cabins', __( 'Double Cabins' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'three-bed-cabins', __( 'Three Bed Cabins' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'text', 'six-bed-cabins', __( 'Six Bed Cabins' ))->set_visible_in_rest_api($visible = true),
    //         Field::make( 'separator', 'separator10', __( 'Downloads' ) ),
    //         Field::make( 'file', 'pdf-sheet', __( 'PDF Sheet' ) )->set_visible_in_rest_api($visible = true),
    //     )
    // );

    // Container::make('term_meta', 'Woo Category Options')
    //     ->where('term_taxonomy', '=', 'product_cat')
    //     // ->add_tab( __( 'Profile' ), array(
    //     ->add_fields( array(
    //         Field::make( 'radio', 'crb_catalogus_type', __( 'Choose catalogus type' ) )->set_visible_in_rest_api($visible = true)
    //         ->set_options( array(
    //             'shop' => 'Shop',
    //             'list' => 'List',
    //         ) ),
    //         Field::make( 'rich_text', 'crb_category_text', __( 'Text' ) )->set_visible_in_rest_api($visible = true),
    //     ));


    $fieldsToAdd = array();
    foreach($args['websiteOptions'] as $opt) {
        if($opt[0] == 'media_gallery') {
            $fieldsToAdd[] = Field::make($opt[0], $opt[1], __($opt[2]))->set_type( array( 'image' ) )->set_duplicates_allowed( false );
        } elseif($opt[0] == 'office_assoc') {
            $fieldsToAdd[] = Field::make( 'association', $opt[1], __($opt[2]))
                                ->set_types( array(
                                    array(
                                        'type' => 'post',
                                        'post_type' => 'office',
                                    ),
                                ) );
        } else {
            $fieldsToAdd[] = Field::make($opt[0], $opt[1], __($opt[2]));
        }
    }
    Container::make('theme_options', 'Website Options')->add_fields($fieldsToAdd );

    // Container::make('theme_options', 'Reserveringen')->add_fields(array(
        // Field::make( 'html', 'file_download')->set_html('<p><a href="/event_files/aanmeldingen.csv">Download Excel bestand</a></p>')
        // )
    // );
}
?>
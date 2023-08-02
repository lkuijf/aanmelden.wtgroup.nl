<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Helpers\ApiCall;
use App\Http\Helpers\SimplePagesApi;
use App\Http\Helpers\SimplePostsApi;
use App\Http\Helpers\SimpleCustomPostsApi;
use App\Http\Helpers\SimpleMediaApi;
use App\Http\Helpers\SimpleTaxonomiesApi;
use App\Http\Helpers\Menu;
use App\Http\Helpers\PageApi;
use App\Http\Helpers\PostApi;
use App\Http\Helpers\CustomPostApi;
use App\Http\Helpers\WebsiteOptionsApi;
use App\Http\Helpers\WooApiCall;
use App\Http\Helpers\WooCategoriesApi;
// use App\Http\Helpers\WooCategoryApi;
use App\Http\Helpers\WooFilterProductsApi;
use App\Http\Helpers\FilterJobOffersApi;
use Illuminate\Support\Facades\Crypt;

class PagesController extends Controller
{
    public $allMediaById = array();
    public $allTaxonomiesById;
    public $allPagesPerParent = array();

    public function home() {
        return view('page');
    }

    public function showPage(Request $request, $section, $page, $subpage) {
// echo "<br />\n" . (isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'') . "<br />\n";
        /*** WP Post to trash (also custom posts) referer hack (https://bestflex.wtgroup.nl/?trashed=1&ids=241) ***/
        if($request->query('trashed')) return redirect('_mcfu638b-cms/wp-admin/index.php');
        
        $simplePages = new SimplePagesApi();
        $htmlMenu = new Menu($simplePages->get());
        $htmlMenu->generateUlMenu();
        $this->allPagesPerParent = $simplePages->pagesPerParent;
        $allSlugsNested = $simplePages->getAllSlugs();
// dd($allSlugsNested);
        if(!isset($allSlugsNested[$section]) || ($page && !in_array($section, ['news','vessels']) && !isset($allSlugsNested[$section]['children'][$page])) || ($subpage && !isset($allSlugsNested[$section]['children'][$page]['children'][$subpage]))) {
            return abort(404);
        } else {
            $pageId = $allSlugsNested[$section];
            if($page && !in_array($section, ['news','vessels'])) $pageId = $allSlugsNested[$section]['children'][$page];
            if($subpage) $pageId = $allSlugsNested[$section]['children'][$page]['children'][$subpage];
            if(is_array($pageId)) $pageId = $pageId['id'];
        }

        $content = $this->getContent($pageId);
        $options = $this->getWebsiteOptions();

        // if(isset($options['header_image'])) $options['header_image'] = $this->generateMediaUrl($options['header_image']);
        if(isset($options->working_with)) $options->working_with = $this->getMediaGallery($options->working_with);
        $vessels = array();
        $news = array();
        $vessel = false;
        $newsItem = false;
        // if($section == 'vessels' || $section == 'news') {
        //     if(!$page) {
        //         if($section == 'vessels') $customPost = new CustomPostApi('vessel');
        //         if($section == 'news') $customPost = new CustomPostApi('news');
        //         $items = $customPost->get();
        //         foreach($items as $k => $item) {
        //             $items[$k]->small_image = $this->getMediaGallery($item->small_image, 'medium_large');
        //         }
        //         if($section == 'vessels') $vessels = $items;
        //         if($section == 'news') $news = $items;
        //     }
        //     if($page) {
        //         if($section == 'vessels') {
        //             $customPost = new CustomPostApi('vessel', false, $page);
        //             $simpleTaxonomies = new SimpleTaxonomiesApi();
        //             $simpleTaxonomies->get();
        //             $this->allTaxonomiesById = $simpleTaxonomies->makeListById();
        //         }
        //         if($section == 'news') $customPost = new CustomPostApi('news', false, $page);
        //         $item = $customPost->get();
        //         if(!$item) return abort(404);
        //         $item = $item[0];
        //         $item->large_image = $this->getMediaGallery($item->large_image, '2048x2048');
        //         $item->small_image = $this->getMediaGallery($item->small_image, 'medium_large');
        //         if(isset($item->{'pdf-sheet'})) $item->{'pdf-sheet'} = $this->generateMediaUrl($item->{'pdf-sheet'});
        //         if(isset($item->vessel_type)) $item->vessel_type = $this->getTerms($item->vessel_type);
        //         if($section == 'vessels') $vessel = $item;
        //         if($section == 'news') $newsItem = $item;
        //     }
        // }
// dd($vessel);
// dd($options);
        // $cartTotalItems = ShopController::getTotalCartItems();
        // $loggedInUserId = ShopController::getLoggedinUser();
// dd($content->contentSections);

        // $instagramFeedPage = new PageApi(1067);
        // $instagramFeedPageData = $instagramFeedPage->get();
        // $instaCode = $instagramFeedPageData->content->rendered;

        $data= [
            'head_title' => $content->pageTitle,
            'meta_description' => $content->pageMetaDescription,
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            // 'cart_total' => $cartTotalItems,
            // 'user_logged_in' => $loggedInUserId,
            'content_sections' => $content->contentSections,
            // 'vessels' => $vessels,
            // 'news' => $news,
            // 'vessel' => $vessel,
            // 'newsItem' => $newsItem,
            // 'instagram_widget_code' => $instaCode,
        ];
        if($vessel) {
            $data['head_title'] = $vessel->title->rendered . ' - ' . config('app_wt.metaTitle');
            $data['meta_description'] = $vessel->title->rendered . ', ' . $vessel->type_text . ' - a vessel of Glomar // Offshore';
            return view('vessel-detail-page')->with('data', $data);
        }
        if($newsItem) {
            $data['head_title'] = $newsItem->title->rendered . ' - ' . config('app_wt.metaTitle');
            $data['meta_description'] = $newsItem->card_text;
            return view('news-detail-page')->with('data', $data);
        }
        if($section == 'contact')
            return view('contact-page')->with('data', $data);
        if($section == 'vacatures')
            return view('vacatures-page')->with('data', $data);
        if($section == 'interviews') {
            $interviews = new CustomPostApi('interview');
            $allIns = $interviews->get();
            $allInterviews = $allIns;
            foreach($allInterviews as $i => $inter) {
                $url = $this->generateMediaUrl($inter->image);
                $alt = $this->generateMediaAlt($inter->image);
                $allInterviews[$i]->image = array('url' => $url, 'alt' => $alt);
            }
            $data['interviews'] = $allInterviews;
            // dd($allInterviews);
            return view('interviews-page')->with('data', $data);
        } else if($section == 'producten') {
            $mainCats = $this->getMainMenuItems();
            $data['shop_main_cats'] = $mainCats;
            return view('shop-root-category')->with('data', $data);
        } else if($section == 'afspraak-maken') {
            return view('bookly-page')->with('data', $data);
        } else
        return view('standard-page')->with('data', $data);
        // return view('onepager')->with('data', $data);
    }
    public function showBlog() {
        $simplePages = new SimplePagesApi();
        $htmlMenu = new Menu($simplePages->get());
        $htmlMenu->generateUlMenu();
        $options = $this->getWebsiteOptions();
        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();
        $content = $this->getContent(942);
        
        $blogItems = new SimpleCustomPostsApi('blog');
        $blogItems->get();
        $items = $blogItems->getItems();
// dd($items);
        foreach($items as &$blog) {
            $blog->gallery = $this->getMediaGallery($blog->gallery);
        }
// dd($items);

//$sec->crb_media_gallery = $this->getMediaGallery($sec->crb_media_gallery);
        $instagramFeedPage = new PageApi(1067);
        $instagramFeedPageData = $instagramFeedPage->get();
        $instaCode = $instagramFeedPageData->content->rendered;

        $data= [
            'head_title' => $content->pageTitle,
            'meta_description' => $content->pageMetaDescription,
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            // 'cart_total' => $cartTotalItems,
            // 'user_logged_in' => $loggedInUserId,
            'content_sections' => $content->contentSections,
            'blog_items' => $items,
            // 'blog_text' => $post[0]->text,
            // 'blog_hero_title' => $post[0]->hero_title,
            // 'blog_hero_text' => $post[0]->hero_text,
            // 'blog_hero_gallery' => $post[0]->hero_gallery,
            // 'blog_date' => date('d-m-Y', strtotime($post[0]->date)),
            // 'vessels' => $vessels,
            // 'news' => $news,
            // 'vessel' => $vessel,
            // 'newsItem' => $newsItem,
            'instagram_widget_code' => $instaCode,
        ];

        return view('blog-overview-page')->with('data', $data);
    }
    public function showPost($slug) {
        $simplePages = new SimplePagesApi();
        $htmlMenu = new Menu($simplePages->get());
        $htmlMenu->generateUlMenu();
        $options = $this->getWebsiteOptions();
        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();

        $cPost = new CustomPostApi('blog', false, $slug);
        $post = $cPost->get();
        if(!count($post)) return abort(404);

        $post[0]->gallery = $this->getMediaGallery($post[0]->gallery);
// dd($post);
        $instagramFeedPage = new PageApi(1067);
        $instagramFeedPageData = $instagramFeedPage->get();
        $instaCode = $instagramFeedPageData->content->rendered;

        $data= [
            'head_title' => $post[0]->page_title,
            'meta_description' => $post[0]->page_meta_description,
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            // 'cart_total' => $cartTotalItems,
            // 'user_logged_in' => $loggedInUserId,
            'blog_text' => $post[0]->text,
            'blog_hero_title' => $post[0]->hero_title,
            'blog_hero_text' => $post[0]->hero_text,
            'blog_hero_gallery' => $post[0]->gallery,
            'blog_date' => date('d-m-Y', strtotime($post[0]->date)),
            // 'vessels' => $vessels,
            // 'news' => $news,
            // 'vessel' => $vessel,
            // 'newsItem' => $newsItem,
            'instagram_widget_code' => $instaCode,
        ];

        return view('blog-detail-page')->with('data', $data);

    }
    public function showCase($slug) {
        $simplePages = new SimplePagesApi();
        $htmlMenu = new Menu($simplePages->get());
        $htmlMenu->generateUlMenu();
        $options = $this->getWebsiteOptions();
        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();

        $cPost = new CustomPostApi('case', false, $slug);
        $post = $cPost->get();
        if(!count($post)) return abort(404);

        $post[0]->gallery = $this->getMediaGallery($post[0]->gallery);
// dd($post);
        $instagramFeedPage = new PageApi(1067);
        $instagramFeedPageData = $instagramFeedPage->get();
        $instaCode = $instagramFeedPageData->content->rendered;

        $data= [
            'head_title' => $post[0]->page_title,
            'meta_description' => $post[0]->page_meta_description,
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            'text' => $post[0]->text,
            'hero_title' => $post[0]->title->rendered,
            // 'hero_text' => $post[0]->hero_text,
            'gallery' => $post[0]->gallery,
            // 'blog_date' => date('d-m-Y', strtotime($post[0]->date)),
            'instagram_widget_code' => $instaCode,
        ];

        return view('case-detail-page')->with('data', $data);

    }
    public function showVacature($slug, $apply) {
        $simplePages = new SimplePagesApi();
        $htmlMenu = new Menu($simplePages->get());
        $htmlMenu->generateUlMenu();
        $options = $this->getWebsiteOptions();
        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();
        $simpleTaxonomies = new SimpleTaxonomiesApi();
        $simpleTaxonomies->get();
        $this->allTaxonomiesById = $simpleTaxonomies->makeListById();
        $jobOffer = new CustomPostApi('job_offer', false, $slug);
        $jo = $jobOffer->get();
        if(!count($jo)) return abort(404);

        $otherJobs = new FilterJobOffersApi();
        $otherJobs->parameters = array();

        $sidebarJobs = $otherJobs->get();

        foreach($sidebarJobs as $k => $job) {
            if($job->image) {
                $url = $this->generateMediaUrl($job->image);
                $alt = $this->generateMediaAlt($job->image);
                $sidebarJobs[$k]->image = [];
                $sidebarJobs[$k]->image['url'] = $url;
                $sidebarJobs[$k]->image['alt'] = $alt;
            }
        }
        $randomKeys = array_rand($sidebarJobs, 4);
        $sideJobsToShow = array();
        for($x=0;$x<count($randomKeys);$x++) $sideJobsToShow[] = $sidebarJobs[$randomKeys[$x]];
        $data= [
            'head_title' => $jo[0]->title->rendered . ' - Solliciteer voor deze job - Best Flex',
            'meta_description' => $jo[0]->intro,
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            'apply_for_job' => $apply,
            'job_offer_title' => $jo[0]->title->rendered,
            'job_offer_content' => $jo[0]->text,
            'job_offer_img_url' => ($jo[0]->image?$this->generateMediaUrl((int)$jo[0]->image):''),
            'job_offer_img_alt' => ($jo[0]->image?$this->generateMediaAlt((int)$jo[0]->image):''),
            'job_offer_job_cat' => $this->getTerms($jo[0]->job_cat),
            'job_offer_uren_per_week' => $this->getTerms($jo[0]->uren_per_week),
            'job_offer_type_job' => $this->getTerms($jo[0]->type_job),
            'job_offer_locatie' => $this->getTerms($jo[0]->locatie),
            'other_jobs' => $sideJobsToShow,
        ];
        return view('vacature-detail-page')->with('data', $data);
    }

    public function getContent($id) {
        $res = new \stdClass();
        $metaDesc = config('app_wt.metaDescription');
        $hTitle = '';
        $sections = [];
        $reqPage = new PageApi($id);
        $pageData = $reqPage->get();    
// dd($pageData);
        // if($pageData->title->rendered == '[HOMEPAGE]') $hTitle = config('app_wt.metaTitle');
        // else
        $hTitle = config('app_wt.metaTitle');
        if($pageData->title->rendered) $hTitle = $pageData->title->rendered;
        if($pageData->meta_title) $hTitle = $pageData->meta_title;
        if($pageData->meta_description) $metaDesc = $pageData->meta_description;

        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();
        if($pageData->content->rendered) {
            $s = [];
            $s['type'] = 'text';
            $s['text'] = $pageData->content->rendered;
            $s['color'] = '';
            $s['orientation'] = 'text_left';
            $s['gallery'] = [];
            $sections[] = $s;
        }
        // if(isset($pageData->crb_sections) && count($pageData->crb_sections)) {
        //     $loadWoo_once = true;
        //     $secs = [];
        //     foreach($pageData->crb_sections as $sec) {



        //         if($sec->_type == 'hero') {
        //             $sec->crb_media_gallery = $this->getMediaGallery($sec->crb_media_gallery);
        //         }
        //         if($sec->_type == '1column') {
        //             if(count($sec->fullwidth)) {
        //                 foreach($sec->fullwidth as &$v) {
        //                     if($v->_type == 'afbeelding') {
        //                         $v->image = $this->getMediaGallery(array($v->image));
        //                     }
        //                 }
        //             }
        //         }
        //         if($sec->_type == '2column') {
        //             if(count($sec->left)) {
        //                 foreach($sec->left as &$v) {
        //                     if($v->_type == 'afbeelding') {
        //                         $v->image = $this->getMediaGallery(array($v->image));
        //                     }
        //                 }
        //             }
        //             if(count($sec->right)) {
        //                 foreach($sec->right as &$v) {
        //                     if($v->_type == 'afbeelding') {
        //                         $v->image = $this->getMediaGallery(array($v->image));
        //                     }
        //                 }
        //             }
        //         }
        //         $secs[] = $sec;
    



        //         // if($sec->_type == 'hero') {
        //         //     $sec->crb_media_gallery = $this->getMediaGallery($sec->crb_media_gallery, '2048x2048');
        //         // }
        //         // if($sec->_type == 'text') {
        //         //     $sec->image = $this->getMediaGallery($sec->image, 'medium_large');
        //         //     $sec->image_2 = $this->getMediaGallery($sec->image_2, 'medium_large');
        //         // }
        //         // if($sec->_type == 'office_boxes') {
        //         //     $aValuesToRetreive = array('title', 'country', 'phone', 'email', 'address1', 'address2', 'address3', 'address4', 'google_maps_address');
        //         //     foreach($sec->office_associations as $k => $assoc) {
        //         //         $oCustPostType = $this->getCustomPostTypeViaRestApi($assoc->subtype, $assoc->id, $aValuesToRetreive);
        //         //         if(!$oCustPostType) unset($sec->office_associations[$k]);
        //         //         else $sec->office_associations[$k] = $oCustPostType;
        //         //     }
        //         // }
        //         // if($sec->_type == 'professional_boxes') {
        //         //     $aValuesToRetreive = array('title', 'function', 'image');
        //         //     foreach($sec->professional_associations as $k => $assoc) {
        //         //         $oCustPostType = $this->getCustomPostTypeViaRestApi($assoc->subtype, $assoc->id, $aValuesToRetreive);
        //         //         if(!$oCustPostType) unset($sec->professional_associations[$k]);
        //         //         else {
        //         //             if($oCustPostType->image) $oCustPostType->image = $this->getMediaGallery($oCustPostType->image, 'medium_large');
        //         //             $sec->professional_associations[$k] = $oCustPostType;
        //         //         }
        //         //     }
        //         // }
        //         // if($sec->_type == 'vessel_boxes') {
        //         //     $aValuesToRetreive = array('title', 'small_image', 'type_text', 'class', 'length', 'breadth', 'slug');
        //         //     foreach($sec->vessels_associations as $k => $assoc) {
        //         //         $oCustPostType = $this->getCustomPostTypeViaRestApi($assoc->subtype, $assoc->id, $aValuesToRetreive);
        //         //         if(!$oCustPostType) unset($sec->vessels_associations[$k]);
        //         //         else {
        //         //             if($oCustPostType->small_image) $oCustPostType->small_image = $this->getMediaGallery($oCustPostType->small_image, 'medium_large');
        //         //             $sec->vessels_associations[$k] = $oCustPostType;
        //         //         }
        //         //     }
        //         // }
        //         // if($sec->_type == 'news_boxes') {
        //         //     $aValuesToRetreive = array('title', 'card_text', 'small_image', 'date', 'slug');
        //         //     foreach($sec->news_associations as $k => $assoc) {
        //         //         $oCustPostType = $this->getCustomPostTypeViaRestApi($assoc->subtype, $assoc->id, $aValuesToRetreive);
        //         //         if(!$oCustPostType) unset($sec->news_associations[$k]);
        //         //         else {
        //         //             if($oCustPostType->small_image) $oCustPostType->small_image = $this->getMediaGallery($oCustPostType->small_image, 'medium_large');
        //         //             $sec->news_associations[$k] = $oCustPostType;
        //         //         }
        //         //     }
        //         // }

        //         // if($sec->_type == '1column') {
        //         //     if(isset($sec->fullwidth) && count($sec->fullwidth)) {
        //         //         $s['1column'] = array();
        //         //         foreach($sec->fullwidth as $fullWidthItem) {
        //         //             if($fullWidthItem->_type == 'afbeelding') {
        //         //                 $fullWidthItem->img = $this->generateMediaUrl($fullWidthItem->image);
        //         //                 $fullWidthItem->alt = $this->generateMediaAlt($fullWidthItem->image);
        //         //                 unset($fullWidthItem->image);
        //         //             }
        //         //             if($fullWidthItem->_type == 'bestand') {
        //         //                 $fullWidthItem->file = $this->generateMediaUrl($fullWidthItem->file);
        //         //             }

        //         //             if($fullWidthItem->_type == 'nieuws-items') {
        //         //                 $aValuesToRetreive = array('title', 'site_title', 'news_url', 'text', 'image');
        //         //                 if(isset($fullWidthItem->news_associations) && count($fullWidthItem->news_associations)) {
        //         //                     foreach($fullWidthItem->news_associations as $k => $newsItem) {
        //         //                         $oCustPostType = $this->getCustomPostTypeViaRestApi($newsItem->subtype, $newsItem->id, $aValuesToRetreive);
        //         //                         if($oCustPostType->image) $oCustPostType->image = $this->getMediaGallery(array($oCustPostType->image));
        //         //                         $fullWidthItem->news_associations[$k] = $oCustPostType;
        //         //                     }
        //         //                 }
        //         //             }

        //         //             $s['1column'][] =  $fullWidthItem;
        //         //         }
        //         //     }
        //         // }
        //         // if($sec->_type == '2column') {
        //         //     $s['2column']['left'] = array();
        //         //     $s['2column']['right'] = array();
        //         //     if(isset($sec->left) && count($sec->left)) {
        //         //         foreach($sec->left as $leftItem) {
        //         //             if($leftItem->_type == 'afbeelding') {
        //         //                 $leftItem->img = $this->generateMediaUrl($leftItem->image);
        //         //                 $leftItem->alt = $this->generateMediaAlt($leftItem->image);
        //         //                 unset($leftItem->image);
        //         //             }
        //         //             if($leftItem->_type == 'bestand') {
        //         //                 $leftItem->file = $this->generateMediaUrl($leftItem->file);
        //         //             }
        //         //             if($leftItem->_type == 'nieuws-items') {
        //         //                 $aValuesToRetreive = array('title', 'site_title', 'news_url', 'text', 'image');
        //         //                 if(isset($leftItem->news_associations) && count($leftItem->news_associations)) {
        //         //                     foreach($leftItem->news_associations as $k => $newsItem) {
        //         //                         $oCustPostType = $this->getCustomPostTypeViaRestApi($newsItem->subtype, $newsItem->id, $aValuesToRetreive);
        //         //                         if($oCustPostType->image) $oCustPostType->image = $this->getMediaGallery(array($oCustPostType->image));
        //         //                         $leftItem->news_associations[$k] = $oCustPostType;
        //         //                     }
        //         //                 }
        //         //             }
        //         //             $s['2column']['left'][] = $leftItem;
        //         //         }
        //         //     }
        //         //     if(isset($sec->right) && count($sec->right)) {
        //         //         foreach($sec->right as $rightItem) {
        //         //             if($rightItem->_type == 'afbeelding') {
        //         //                 $rightItem->img = $this->generateMediaUrl($rightItem->image);
        //         //                 $rightItem->alt = $this->generateMediaAlt($rightItem->image);
        //         //                 unset($rightItem->image);
        //         //             }
        //         //             if($rightItem->_type == 'bestand') {
        //         //                 $rightItem->file = $this->generateMediaUrl($rightItem->file);
        //         //             }
        //         //             if($rightItem->_type == 'nieuws-items') {
        //         //                 $aValuesToRetreive = array('title', 'site_title', 'news_url', 'text', 'image');
        //         //                 if(isset($rightItem->news_associations) && count($rightItem->news_associations)) {
        //         //                     foreach($rightItem->news_associations as $k => $newsItem) {
        //         //                         $oCustPostType = $this->getCustomPostTypeViaRestApi($newsItem->subtype, $newsItem->id, $aValuesToRetreive);
        //         //                         if($oCustPostType->image) $oCustPostType->image = $this->getMediaGallery(array($oCustPostType->image));
        //         //                         $rightItem->news_associations[$k] = $oCustPostType;
        //         //                     }
        //         //                 }
        //         //             }
        //         //             $s['2column']['right'][] = $rightItem;
        //         //         }
        //         //     }
        //         // }
        //         // $sections[] = $sec;
        //     }
        // }
// dd($sections);

        $sections = $this->handleCrbSections($pageData->crb_sections);

        $res->pageMetaDescription = $metaDesc;
        $res->pageTitle = $hTitle;
        $res->contentSections = $sections;
        return $res;
    }
    public function getTerms($termIds) {
        $aRes = array();
        foreach($termIds as $id) {
            $aRes[] = $this->allTaxonomiesById->$id;
        }
        return $aRes;
    }
    public function getCustomPostTypeViaRestApi($customPostType, $id, $valsToReturn) {
        $res = new \stdClass();
        $call = new ApiCall();
        $call->endpoint = '/index.php/wp-json/wp/v2/' . $customPostType . '/' . $id;
        $oReturned = $call->get();
        if(isset($oReturned->data->status) && ($oReturned->data->status == 401 || $oReturned->data->status == 404)) return false; // item deleted or non existend
        foreach($valsToReturn as $val) {
            if($val == 'title') $res->{$val} = $oReturned->{$val}->rendered;
            else $res->{$val} = $oReturned->{$val};
        }
        return $res;
    }
    public function getMediaGallery($gall, $size = false) {
        $res = [];

        if(!$gall) {
            $i['url'] = 'https://via.placeholder.com/800x600?text=Geen+afbeelding+gevonden';
            $i['sizes'] = array( // all sizes added 2-8-23, so there is no need to make a check on the front-end
                'thumbnail' => 'https://via.placeholder.com/200x150?text=Geen+afbeelding+gevonden',
                'medium' => 'https://via.placeholder.com/400x300?text=Geen+afbeelding+gevonden',
                'medium_large' => 'https://via.placeholder.com/800x600?text=Geen+afbeelding+gevonden',
                'large' => 'https://via.placeholder.com/1000x750?text=Geen+afbeelding+gevonden',
                '1536x1536' => 'https://via.placeholder.com/1200x900?text=Geen+afbeelding+gevonden',
                '2048x2048' => 'https://via.placeholder.com/1600x1200?text=Geen+afbeelding+gevonden',
            );
            $i['alt'] = 'Geen afbeelding gevonden';
            $res[] = $i;
            return $res;
        }

        if(!is_array($gall)) $gall = array($gall);

        foreach($gall as $mediaId) {
            // $url = $this->generateMediaUrl($mediaId, $size);
            $url = $this->generateMediaUrl($mediaId);
            $sizes = $this->generateMediaSizes($mediaId);
            $alt = $this->generateMediaAlt($mediaId);
            if(isset($this->allMediaById[$mediaId]) && isset($this->allMediaById[$mediaId]->alt) && $this->allMediaById[$mediaId]->alt) $alt = $this->allMediaById[$mediaId]->alt;
            $i['url'] = $url;
            $i['sizes'] = $sizes;
            $i['alt'] = $alt;
            $res[] = $i;
        }
        return $res;
    }
    public function generateMediaUrl($mediaId) {
        if(isset($this->allMediaById[$mediaId])) {
            $url = $this->allMediaById[$mediaId]->url;
            return str_replace(array('http://', '_mcfu638b-cms/wp-content/uploads'), array('https://', 'media'), $url);
        }
        else {
            return 'https://via.placeholder.com/800x600?text=Geen+afbeelding+gevonden';
        }
    }
    public function generateMediaSizes($mediaId) {
        $sizes = [];
        if(isset($this->allMediaById[$mediaId]) && isset($this->allMediaById[$mediaId]->sizes)) {
            foreach($this->allMediaById[$mediaId]->sizes as $size => $url) {
                $sizes[$size] = str_replace(array('http://', '_mcfu638b-cms/wp-content/uploads'), array('https://', 'media'), $url);
            }
            return $sizes;
        }
        else {
            return false;
        }
    }
    public function generateMediaAlt($mediaId) {
        if(isset($this->allMediaById[$mediaId]))
            return str_replace(['-', '_'], ' ', pathinfo($this->allMediaById[$mediaId]->url, PATHINFO_FILENAME));
        else
            return 'Placeholder image';
    }
    public function getWebsiteOptions() {
        $allWebsiteOptions = new WebsiteOptionsApi();
        $websiteOptions = $allWebsiteOptions->get();

        // $footerOffice1Assoc = $websiteOptions->footer_office_1[0];
        // $footerOffice2Assoc = $websiteOptions->footer_office_2[0];
        
        // $aValuesToRetreive = array('title', 'country', 'phone', 'email', 'address1', 'address2', 'address3', 'address4', 'google_maps_address');
        // $oCustPostType1 = $this->getCustomPostTypeViaRestApi($footerOffice1Assoc->subtype, $footerOffice1Assoc->id, $aValuesToRetreive);
        // $oCustPostType2 = $this->getCustomPostTypeViaRestApi($footerOffice2Assoc->subtype, $footerOffice2Assoc->id, $aValuesToRetreive);
        // $websiteOptions->footer_office_1[0] = $oCustPostType1;
        // $websiteOptions->footer_office_2[0] = $oCustPostType2;

        return $websiteOptions;
    }
    public function getMainMenuItems() {
        $cats = array();
        $wooCats = new WooCategoriesApi();
        $wooCats->setHttpBasicAuth();
        $wooCats->get();
        $wooCats->setCategoriesPerParent();
        foreach($wooCats->res[0] as $rootCats) {
            if($rootCats->slug == 'uncategorized') continue;
            $cats[$rootCats->slug] = $rootCats->name;
        }
        return $cats;
    }
    public function showOnePager($orderId = false) {
        $simplePages = new SimplePagesApi();
        $spages = $simplePages->get();
        $htmlMenu = new Menu($spages);
        $htmlMenu->generateUlMenu();
        $options = $this->getWebsiteOptions();

        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();
// dd($this->allMediaById);

        if(isset($options->working_with)) $options->working_with = $this->getMediaGallery($options->working_with);
        if(isset($options->events)) $options->events = $this->getMediaGallery($options->events);

        $casesHighlighted = new SimpleCustomPostsApi('case');
        $casesHighlighted->parameters['highlighted'] = '1';
        $casesHighlighted->get();
        $homepageCases = $casesHighlighted->getItems();
        foreach($homepageCases as &$case) {
            $case->gallery = $this->getMediaGallery($case->gallery);
        }
        $reviewPosts = new SimpleCustomPostsApi('review');
        $reviewPosts->get();
        $reviews = $reviewPosts->getItems();
        foreach($reviews as &$review) {
            $review->image = $this->getMediaGallery($review->image);
        }
        $teamPosts = new SimpleCustomPostsApi('teammember');
        $teamPosts->get();
        $teamMembers = $teamPosts->getItems();
        foreach($teamMembers as &$member) {
            $member->image = $this->getMediaGallery($member->image);
        }
// dd($homepageCases);
// dd($teamMembers);


        $instagramFeedPage = new PageApi(1067);
        $instagramFeedPageData = $instagramFeedPage->get();
        $instaCode = $instagramFeedPageData->content->rendered;
// dd($instagramFeedPageData);


        $allCrbSections = array();
        foreach($spages[0] as $sPage) {
            if($sPage->title == 'Blog') continue;
            $pageA = new \stdClass;
            $pageA->_type = '_anchor';
            $pageA->value = $sPage->slug;
            $allCrbSections[] = $pageA;

            /* check rotterdamsehorecawandeling.nl for details */
            
            $crbSecs = $this->getPageCrbSections($sPage->id);
            $allCrbSections = array_merge($allCrbSections, $crbSecs);
        }

// dd($allCrbSections);
// dd($options);
        $data= [
            'head_title' => 'title test',
            'meta_description' => 'meta test',
            'html_menu' => $htmlMenu->html,
            'website_options' => $options,
            'content_sections' => $allCrbSections,
            'cases_highlighted' => $homepageCases,
            'reviews' => $reviews,
            'team_members' => $teamMembers,
            'instagram_widget_code' => $instaCode,
        ];
        return view('onepager')->with('data', $data);
    }
    public function getPageCrbSections($id) {
        $reqPage = new PageApi($id);
        $pageData = $reqPage->get();
        $allSections = [];
        if(isset($pageData->crb_sections) && count($pageData->crb_sections)) {
            $sections = $this->handleCrbSections($pageData->crb_sections);
            $allSections = array_merge($allSections, $sections); 
        }
        return $allSections;
    }
    public function handleCrbSections($pCrbSecs) {
        $secs = [];
        foreach($pCrbSecs as $sec) {
            if($sec->_type == 'hero') {
                $sec->crb_media_gallery = $this->getMediaGallery($sec->crb_media_gallery);
            }
            if($sec->_type == '1column') {
                if(count($sec->fullwidth)) {
                    foreach($sec->fullwidth as &$v) {
                        if($v->_type == 'afbeelding') {
                            $v->image = $this->getMediaGallery(array($v->image));
                        }
                    }
                }
            }
            if($sec->_type == '2column') {
                if(count($sec->left)) {
                    foreach($sec->left as &$v) {
                        if($v->_type == 'afbeelding') {
                            $v->image = $this->getMediaGallery(array($v->image));
                        }
                    }
                }
                if(count($sec->right)) {
                    foreach($sec->right as &$v) {
                        if($v->_type == 'afbeelding') {
                            $v->image = $this->getMediaGallery(array($v->image));
                        }
                    }
                }
            }
            // if($sec->_type == 'cases') {

            //     $caseItems = new SimpleCustomPostsApi('case');

            //     if($sec->show_cases_online_marketing) $caseItems->parameters['category'] = 'online-marketing';
            //     if($sec->show_cases_web_development) $caseItems->parameters['category'] = 'web-development';
            //     if($sec->show_cases_events) $caseItems->parameters['category'] = 'events';
                
            //     $caseItems->get();
            //     $cases = $caseItems->getItems();
            //     foreach($cases as &$case) {
            //         $case->gallery = $this->getMediaGallery($case->gallery);
            //     }
            //     $sec->cases = $cases;
            // }
            // if($sec->_type == 'schedule_call') {
            //     $sec->email_to = Crypt::encryptString($sec->email_to);
            //     $sec->success_text = Crypt::encryptString($sec->success_text);
            // }
            // if($sec->_type == 'team_specialists' && count($sec->team_specialists_associations)) {
            //     foreach($sec->team_specialists_associations as &$specialist) {
            //         $cTeamMember = new CustomPostApi('teammember', $specialist->id, false);
            //         $teamMember = $cTeamMember->get();
            //         $specialist = $teamMember;

            //         if(isset($specialist->image) && $specialist->image) {
            //             $specialist->image = $this->getMediaGallery($specialist->image);
            //         }
            //     }
            // }
            // if($sec->_type == 'marketing_terms') {
            //     $sec->image1 = $this->getMediaGallery($sec->image1);
            //     $sec->image2 = $this->getMediaGallery($sec->image2);
            //     $sec->image3 = $this->getMediaGallery($sec->image3);
            //     $sec->image4 = $this->getMediaGallery($sec->image4);
            //     $sec->image5 = $this->getMediaGallery($sec->image5);
            //     $sec->image6 = $this->getMediaGallery($sec->image6);
            //     $sec->image7 = $this->getMediaGallery($sec->image7);
            //     $sec->image8 = $this->getMediaGallery($sec->image8);
            // }
            if($sec->_type == 'registration_form') {
                $sec->image = $this->getMediaGallery($sec->image);
            }
            $secs[] = $sec;
        }
// dd($secs);
        return $secs;
    }
}

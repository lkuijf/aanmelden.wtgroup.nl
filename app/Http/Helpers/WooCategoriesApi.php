<?php
namespace App\Http\Helpers;

class WooCategoriesApi extends WooApiCall {
    public $categoriesPerParent = array();
    public $categoriesById = array();
    public function __construct($id = false) {
        $idUrlPart = '';
        if($id) $idUrlPart = '/' . $id;
        $this->endpoint = '/index.php/wp-json/wc/v3/products/categories' . $idUrlPart;
        if(!$id) $this->parameters['per_page'] = 99;
    }
    // public function postProcess() {
    public function setCategoriesPerParent() {
        /* Group children */
        foreach($this->res as $cat) {
            $order = $cat->menu_order;
            if(isset($this->categoriesPerParent[$cat->parent][$order])) $order = count($this->categoriesPerParent[$cat->parent]); /* als de order 0,0,0,0 is bijvoorbeeld */
            $this->categoriesPerParent[$cat->parent][$order] = $cat;
        }
        foreach($this->categoriesPerParent as &$arr) {
            ksort($arr); // Sort an array by key in ascending order
            $arr = array_values($arr); // make logical 0,1,2,3,4 key values (no gaps)
        }
        $this->res = $this->categoriesPerParent;
    }
    public function getAllSlugs($parentId = 0, $url = '') {
        foreach($this->categoriesPerParent[$parentId] as $cat) {
            $slugs[$cat->slug]['id'] = $cat->id;
            $slugs[$cat->slug]['name'] = $cat->name;
            $slugs[$cat->slug]['count'] = $cat->count;
            if(isset($this->categoriesPerParent[$cat->id])) $slugs[$cat->slug]['children'] = $this->getAllSlugs($cat->id);
        }
        return $slugs;
    }
    public function getAllCatsById($parentId = 0) {
        foreach($this->categoriesPerParent[$parentId] as $cat) {
            $this->categoriesById[$cat->id]['slug'] = $cat->slug;
            $this->categoriesById[$cat->id]['name'] = $cat->name;
            $this->categoriesById[$cat->id]['parent'] = $cat->parent;
            if(isset($this->categoriesPerParent[$cat->id])) $this->getAllCatsById($cat->id);
        }
    }
    public function getBreadCrumbUrls($catId = false) {
        if(!$catId) $catId = $this->id; // nog te gebruiken

        $breadCrumbs = array();
        $urlBuilder = '';
        $aCrumbs = array();
        $aCrumbs[0]['slug'] = $this->categoriesById[$catId]['slug'];
        $aCrumbs[0]['name'] = $this->categoriesById[$catId]['name'];
        $parentId = $this->categoriesById[$catId]['parent'];
        while($parentId != 0) {
            $aC = array();
            $aC['slug'] = $this->categoriesById[$parentId]['slug'];
            $aC['name'] = $this->categoriesById[$parentId]['name'];
            $aCrumbs[] = $aC;
            $parentId = $this->categoriesById[$parentId]['parent'];
        }
        $aCrumbs = array_reverse($aCrumbs);
        $urlBuilder = '/producten';
        $breadCrumbs[$urlBuilder] = 'Producten';
        foreach($aCrumbs as $crumb) {
            $urlBuilder .= '/' . $crumb['slug'];
            $breadCrumbs[$urlBuilder] = $crumb['name'];
        }
        return $breadCrumbs;
    }

//     public function getSlug() {
//         $aCrumbs = array();
//         $catId = $this->id;
// // dd($catId, $wooCategories->categoriesById, $temp);
//         $aCrumbs[0]['slug'] = $wooCategories->categoriesById[$catId]['slug'];
//         $aCrumbs[0]['name'] = $wooCategories->categoriesById[$catId]['name'];
//         $parentId = $wooCategories->categoriesById[$catId]['parent'];
//         while($parentId != 0) {
//             $aC = array();
//             $aC['slug'] = $wooCategories->categoriesById[$parentId]['slug'];
//             $aC['name'] = $wooCategories->categoriesById[$parentId]['name'];
//             $aCrumbs[] = $aC;
//             $parentId = $wooCategories->categoriesById[$parentId]['parent'];
//         }
//         $aCrumbs = array_reverse($aCrumbs);
//     }
}

<?php
namespace App\Http\Helpers;

class SimplePagesApi extends ApiCall {
    public $pagesPerParent = array();
    public function __construct() {
        $this->endpoint = '/index.php/wp-json/wtcustom/simple-pages';
    }
    public function postProcess() {
        /* Group children */

        /* edit 17-1-2013  */
        /* if $this->res === null SSL-certificate is probably missing set CURLOPT_SSL_VERIFYHOST & CURLOPT_SSL_VERIFYPEER in ApiCall.php */
        /* if $this->res === null Or domain is not resolvable */
        foreach($this->res as $page) {
            $order = $page->order;
            if(isset($this->pagesPerParent[$page->parent][$order])) $order = count($this->pagesPerParent[$page->parent]); /* als de order 0,0,0,0 is bijvoorbeeld */
            $this->pagesPerParent[$page->parent][$order] = $page;
        }
        foreach($this->pagesPerParent as &$arr) {
            ksort($arr); // Sort an array by key in ascending order
            $arr = array_values($arr); // make logical 0,1,2,3,4 key values (no gaps)
        }
        $this->res = $this->pagesPerParent;
    }
    public function getAllSlugs($parentId = 0, $url = '') {
        foreach($this->pagesPerParent[$parentId] as $page) {
            if(isset($this->pagesPerParent[$page->id])) {
                $slugs[$page->slug]['id'] = $page->id;
                $slugs[$page->slug]['children'] = $this->getAllSlugs($page->id);
            } else {
                $slugs[$page->slug] = $page->id;
            }
        }
        return $slugs;
    }
}

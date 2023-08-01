<?php
namespace App\Http\Helpers;

class SimpleTaxonomiesApi extends ApiCall {
    public function __construct() {
        $this->endpoint = '/index.php/wp-json/wtcustom/simple-taxonomies';
    }
    public function makeListById() {
        /* Make available by ID */
        $oList = new \stdClass();
        foreach($this->res as $taxonomy) {
            // $aList[$media->id] = $media;
            foreach($taxonomy->terms as $i => $term) {
                $oList->{$i} = $term;
            }
        }
        return $oList;
    }
}

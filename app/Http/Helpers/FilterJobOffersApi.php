<?php
namespace App\Http\Helpers;

class FilterJobOffersApi extends ApiCall {
    public function __construct() {
        $this->endpoint = '/index.php/wp-json/wtcustom/filter-job-offers';
    }
}

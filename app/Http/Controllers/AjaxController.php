<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Helpers\ApiCall;
use App\Http\Helpers\FilterJobOffersApi;
use App\Http\Helpers\SimpleMediaApi;

class AjaxController extends Controller
{
    public $allMediaById = array();

    public function searchJobOffer($string, $location, $type) {
        $filterJobs = new FilterJobOffersApi();
        $filterJobs->parameters = array();
        if($string != '-')      $filterJobs->parameters['string'] = $string;
        if($location != '-')    $filterJobs->parameters['location'] = $location;
        if($type != '-')        $filterJobs->parameters['type'] = $type;
        
        $jobs = $filterJobs->get();

        $simpleMedia = new SimpleMediaApi();
        $simpleMedia->get();
        $this->allMediaById = $simpleMedia->makeListById();

        foreach($jobs as $k => $job) {
            if($job->image) {
                $url = $this->generateImageUrl($job->image);
                $alt = $this->generateImageAlt($job->image);
                $jobs[$k]->image = [];
                $jobs[$k]->image['url'] = $url;
                $jobs[$k]->image['alt'] = $alt;
            }
        }

// dd($jobs);

        return view('sections.jobOffers')->with('jobOffers', $jobs);
    }




    /******* Gekopieerd van PagesController. Beter om hier een helper-class van te maken... maar moet maar even zo nu. *****/
    public function generateImageUrl($mediaId) {
        if(isset($this->allMediaById[$mediaId]))
            return str_replace(array('http://', '_mcfu638b-cms/wp-content/uploads'), array('https://', 'media'), $this->allMediaById[$mediaId]->url);
        else
            return 'https://via.placeholder.com/800x600?text=Geen+afbeelding+gevonden';
    }
    public function generateImageAlt($mediaId) {
        if(isset($this->allMediaById[$mediaId]))
            return str_replace(['-', '_'], ' ', pathinfo($this->allMediaById[$mediaId]->url, PATHINFO_FILENAME));
        else
            return 'Placeholder image';
    }
    /***************************/


}
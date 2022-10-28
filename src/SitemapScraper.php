<?php

require(__DIR__ . '/../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SitemapScraper {

  protected $sitemap_uri = "";
  protected $site_origin = "";
  protected $sitemap_content = "";
  protected $sitemap_dom_object = null;
  protected $sitemap_list_object = null;
  protected $page_urls = [];
  protected $page_content_arr = [];
  protected $sitemap_urls = [];

  public function __construct($site_origin,$sitemap_uri,$http_options,$parse_mode) {
    //
    $this->client = new GuzzleHttp\Client();
    $this->site_origin = $site_origin;
    $this->sitemap_uri = $sitemap_uri;

    $this->http_options = is_array($http_options) ?
      $http_options : ["headers" => [], "body" => ''];

    $site_origin_slugs = explode('//',$this->site_origin);
    $this->site_www_origin = $site_origin_slugs[0] . '//www.' . $site_origin_slugs[1];

    if ($this->getSitemapContent()) {

      $this->parseSitemap($parse_mode);
    }
  }

  public function getPageURLs() {
    return $this->page_urls;
  }

  protected function getSitemapContent() {
    $response = $this->getResponse($this->site_origin . $this->sitemap_uri);
    if ($response) {
      $this->sitemap_content = $response;
    }
    return $this->sitemap_content;
  }

  protected function getResponse($request_url) {
    $response = '';
    try {
      $http_response = $this->client->request(
        'GET',
        $request_url,
        $this->http_options
      );
      $response = $http_response->getBody();
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        $err_response = $e->hasResponse();
        error_log('HTTP request returned an error');
        error_log($err_response);
      }
    }
    return $response;
  }

  protected function parseSitemap($mode) {
    if ($this->sitemap_content) {
      switch($mode) {
        case 'HTML' :
          $this->sitemap_dom_object = new DOMDocument();
          $this->sitemap_dom_object->loadHTML($this->sitemap_content);
        // Use the HTML sitemap for the main site -- in order to connect to schools and departments
          $h2s = $this->sitemap_dom_object->getElementsByTagName('h2');
          $this->sitemap_list_object = $h2s[0]->nextElementSibling->getElementsByTagName('a');
          for ($i = 0; $i < $this->sitemap_list_object->length; $i++) {
            //
            if (strpos($this->sitemap_list_object[$i]->attributes->getNamedItem('href')->value,$this->site_origin)===0 ||
              strpos($this->sitemap_list_object[$i]->attributes->getNamedItem('href')->value,$this->site_www_origin)===0)
            {
              $this_url = $this->sitemap_list_object[$i]->attributes->getNamedItem('href')->value;
            }
            else
            {
              $this_url = $this->site_origin .  str_replace(['/..','..'],'',$this->sitemap_list_object[$i]->attributes->getNamedItem('href')->value);
            }
            //
            $this->page_urls[] = $this_url;
          }
          break;
        case 'XML' :
          try {
            //print($this->sitemap_content);
            $this->sitemap_dom_object = new SimpleXMLElement($this->sitemap_content);

            if ($this->sitemap_dom_object->url) {
              foreach ($this->sitemap_dom_object->url as $url) {
                //print_r($url->loc->__toString());
                $this->page_urls[] = $url->loc->__toString();
              }
            }
          } catch (Exception $e) {
            error_log('sitemap request returned invalid XML');
            error_log( $e->getMessage() );
            continue;
          }
          break;
        default:
          error_log('invalid sitemap parse mode');
      }
    } else {
      error_log('invalid DOM object');
    }
  }

  public function resetSitemap($site_origin,$sitemap_uri,$http_options,$parse_mode) {
    $this->site_origin = $site_origin;
    $this->sitemap_uri = $sitemap_uri;

    $this->http_options = is_array($http_options) ?
      $http_options : ["headers" => [], "body" => ''];

    $site_origin_slugs = explode('//',$this->site_origin);
    $this->site_www_origin = $site_origin_slugs[0] . '//www.' . $site_origin_slugs[1];

    if ($this->getSitemapContent()) {
      $this->sitemap_dom_object = new DOMDocument();
      $this->sitemap_dom_object->loadHTML($this->sitemap_content);
      $this->parseSitemap($parse_mode);
    }
  }

  public function getPageContentAll() {
    foreach( $this->page_urls as $page_url) {
      $response = $this->getResponse($page_url);
      if ($response) {
        $this->page_content_arr[$page_url] = $response;
        print("\r\ngot HTTP response from {$page_url}\r\n");
      }
    }
    return $this->page_content_arr;
  }

  public function getPageContentByPaths($paths_arr) {
    foreach( $paths_arr as $page_path) {
      $page_url = $this->site_origin . $page_path;
      $response = $this->getResponse($page_url);
      if ($response) {
        $this->page_content_arr[$page_url] = $response;
        print("\r\ngot HTTP response from {$page_url}\r\n");
      }
    }
    return $this->page_content_arr;
  }

  public function parseSitemapsAll($subsite_urls) {

    foreach( $subsite_urls as $page_url) {
      print("listening to response from {$page_url}/sitemap.xml");
      $response = $this->getResponse($page_url . '/sitemap,xml');
      if ($response) {
        $this->sitemap_dom_object = new DOMDocument();
        $this->sitemap_dom_object->loadHTML($response);
        if ($this->sitemap_dom_object->getElementsByTagName('main')) {
          print('this is HTML');
          continue;
        } else {
          try {
            $test_dom = new SimpleXMLElement($response);
            print('this is XML');
            if ($test_dom->url) {
              $this->sitemap_urls[] = $page_url . '/sitemap,xml';
            }
          } catch (Exception $e) {
            error_log( $e->getMessage() );
            continue;
          }
        }
      //print_r($response);
      }
    }
    return $this->sitemap_urls;
  }
}

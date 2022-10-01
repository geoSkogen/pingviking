<?php


class DOMScraper {

  protected $class_names = [];
  protected $inventory = [];

  public function __construct($class_names) {
    //
    $this->class_names = $class_names;
  }

  protected function scrapeHTML($html_string) {
    foreach ($this->class_names as $class_name) {
      $this->inventory[$class_name] = strpos($html_string,$class_name) ? 1 : 0;
    }
    return $this->inventory;
  }

  public function getPageInventory($html_string) {
    return $this->scrapeHTML($html_string);
  }

}

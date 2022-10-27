<?php

class EntityInventory {

  protected $inventory;

  public function __construct() {
    $this->inventory = [];
    $this->table = [];
    $this->total_pages_crawled = 0;
  }

  public function tally($inventory_arr) {
    foreach($inventory_arr as $item_type => $count) {

      $this->inventory[$item_type] = empty($this->inventory[$item_type]) ? intval($count) :
        intval($this->inventory[$item_type]) + intval($count);
    }
    $this->total_pages_crawled++;
  }

  public function flattenTally($url,$inventory_arr) {
    if (count($inventory_arr)) {
      $this->inventory[$url] = $inventory_arr;
      $this->table[] = array_merge([$url],$inventory_arr);
    }
    $this->total_pages_crawled++;
  }

  protected function makeExportTable() {
    foreach($this->inventory as $key => $val) {
      $this->table[] = [$key,$val];
    }
    return $this->table;
  }

  public function getExportTable() {
    //$export_arr = $this->makeExportTable();
    $export_arr = $this->table;
    $export_arr[] = ['TOTAL PAGES CRAWLED',$this->total_pages_crawled];
    return $export_arr;
  }
}

<?php

class DOMScraper {

  protected $class_names;
  protected $class_inventory;
  public $unicode_ranges_by_charset_name;

  public function __construct() {
    $this->class_inventory = [];
    $this->class_names = [];
    $this->unicode_ranges_by_charset_name = [];
  }

  protected function inventoryClassNames($html_string) {
    foreach ($this->class_names as $class_name) {
      $this->class_inventory[$class_name] = strpos($html_string,$class_name) ? 1 : 0;
    }
    return $this->class_inventory;
  }

  protected function scrapeCharsByUnicodeRange($html_string,$lower_bound,$upper_bound) {
    $result = 0;
    $char_arr = str_split($html_string);
    foreach($char_arr as $char) {
      if ( (mb_ord($char,'ISO-8859-1') >= hexdec(str_replace('0x','',$lower_bound)) &&
        mb_ord($char,'ISO-8859-1') <= hexdec(str_replace('0x','',$upper_bound)) )
      )
      {
        $result++;
      }
    }
    return $result;
  }

  protected function scrapeForUnknownChars($html_string) {
    $result = 0;
    $char_arr = str_split($html_string);
    foreach($char_arr as $char) {
      if (!mb_ord($char)) {
        $result++;
        //print("\r\n{$char}\r\n");
      }
    }
    return $result;
  }

  public function getClassNamesInventory($html_string,$class_names) {
    $this->class_names = $class_names;
    return $this->inventoryClassNames($html_string);
  }

  public function getCharsetInventory($html_string,$unicode_table) {
    $row = [];
    foreach( $unicode_table as $unicode_row ) {
      //print_r($unicode_row);
      $this->unicode_ranges_by_charset_name[$unicode_row[0]] =
        ['start' => $unicode_row[1],'end'=> $unicode_row[2]];
      $row_result = $this->scrapeCharsByUnicodeRange($html_string,$unicode_row[1],$unicode_row[2]);
      if ($row_result) {
        $row[] = $unicode_row[0];
        $row[] = $row_result;
      }
    }
    $unknown_count = $this->scrapeForUnknownChars($html_string);
    if ($unknown_count > 49) {
      $row[] = 'unknown';
      $row[] = $unknown_count;
    } else {
      error_log("\r\n < 50 unknown characters in HTML\r\n");
      if (!$unknown_count) { error_log("\r\n0 unknown characters in HTML\r\n"); }
    }
    return $row;
  }
}

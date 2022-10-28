<?php

require(__DIR__ . '/src/Schema.php');

$filenames = scandir(__DIR__ . '/exports/charset_inventory');
$charsets_of_interest = [
  'CJK Unified Ideographs',
  'CJK Symbols and Punctuation',
  'Kangxi Radicals',
  'Halfwidth and Fullwidth Forms',
  'Latin Extended Additional',
  'Latin Extended-A',
  'Latin Extended-B',
  'Cyrillic',
  'Greek and Coptic',
  'Thai',
  'Arabic',
  'Ethiopic',
  'Armenian',
  'Hebrew'
];
$tally = [];
if ($filenames && is_array($filenames)) {
  foreach($filenames as $filename) {
    $file_schema = new Schema(str_replace('.csv','',$filename),'../exports/charset_inventory');
    foreach($file_schema->data_index as $data_row) {
      if (in_array($data_row[0],$charsets_of_interest)) {
        if (intval($data_row[1]) > 10) {
          $prop_name = str_replace('--','/',str_replace(['ondeck.pdx.edu','_charset_inventory.csv'],'',$filename));
          if (!empty($tally[$data_row[0]])) {
            $tally[$data_row[0]][ $prop_name ] = $data_row[1];
          } else {
            $tally[$data_row[0]] = [
              $prop_name => $data_row[1]
            ];
          }
        }
      }
    }
  }
}
print_r($tally);
foreach ($tally as $charset_label => $inventory_arr) {
  $export_table = [];
  $export_str = [];
  foreach($inventory_arr as $path => $char_count) {
    $export_table[] = [$path,$char_count];
  }
  $export_str = Schema::make_export_str($export_table);
  Schema::export_csv($export_str, str_replace(' ','_',$charset_label), '../exports/charset_summary');
}
?>

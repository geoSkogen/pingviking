<?php
require(__DIR__ . '/src/EntityInventory.php');
require(__DIR__ . '/src/SitemapScraper.php');
require(__DIR__ . '/src/DOMScraper.php');
require(__DIR__ . '/src/Schema.php');

$site_domain = $argv[1] ?  $argv[1] : 'pdx.edu';
$sitemap_filename = $argv[2] ? '/' .  $argv[2] : '/sitemap.xml';
$parse_mode = $argv[3] ? $argv[3] : 'XML';

$entity_inventory = new EntityInventory();
/*
$class_import_schema = new Schema('pdxd8_classnames','../imports');
$class_names = [];

foreach($class_import_schema->data_index as $row) {
  $class_names[] = $row[0];
}
*/

$domains_import_schema = new Schema('pdxd8_domains','../imports');
//$charsets_import_schema = new Schema('charset_ranges','../imports');

//$dom_scraper = new DOMScraper();

foreach($domains_import_schema->data_index as $domain_row) {

  $sitemap_scraper = new SitemapScraper('https://' . $domain_row[0],$sitemap_filename, null, $parse_mode);

  //$html_strings_by_page_url = $sitemap_scraper->getPageContentAll();
  /*
  foreach($html_strings_by_page_url as $url => $page_content) {

    $entity_inventory->flattenTally(
      $url,
      $dom_scraper->getCharsetInventory($page_content,$charsets_import_schema->data_index),
    );
  }
  print_r($entity_inventory->getExportTable());
  */
  /*
  foreach($html_strings_by_page_url as $url => $page_content) {

    $entity_inventory->tally(
      $dom_scraper->getClassNamesInventory($page_content,$class_names),
    );
  }
  */
  $export_str = Schema::make_export_str( $sitemap_scraper->getPageURLs() );
  print("exporting URLs for {$domain_row[0]}\r\n");
  Schema::export_csv($export_str,  str_replace(['pdx.edu/','www.'],'',$domain_row[0]) . '_page_inventory', '../exports/sitemaps');
}





?>

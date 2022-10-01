<?php
require(__DIR__ . '/src/EntityInventory.php');
require(__DIR__ . '/src/SitemapScraper.php');
require(__DIR__ . '/src/DOMScraper.php');
require(__DIR__ . '/src/Schema.php');

$entity_inventory = new EntityInventory();

$class_import_schema = new Schema('pdxd8_classnames','../imports');
$class_names = [];

foreach($class_import_schema->data_index as $row) {
  $class_names[] = $row[0];
}

$dom_scraper = new DOMScraper($class_names);

$sitemap_scraper = new SitemapScraper('https://pdx.edu','/sitemap', null, 'HTML');

$html_strings_by_page_url = $sitemap_scraper->getPageContentAll();
//$sub_sitemap_urls_all = $sitemap_scraper->parseSitemapsAll($subsite_urls);

print_r($sub_sitemap_urls_all);

foreach($html_strings_by_page_url as $url => $page_content) {
  $entity_inventory->tally(
    $dom_scraper->getPageInventory($page_content),
  );
}

$export_str = Schema::make_export_str( $entity_inventory->getExportTable() );

Schema::export_csv($export_str, 'pdxd8_class_inventory', '../exports');


?>

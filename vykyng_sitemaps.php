<?php
require(__DIR__ . '/src/EntityInventory.php');
require(__DIR__ . '/src/SitemapScraper.php');
require(__DIR__ . '/src/DOMScraper.php');
require(__DIR__ . '/src/Schema.php');

$site_domain = $argv[1] ?  $argv[1] : 'pdx.edu';
$sitemap_filename = $argv[2] ? '/' .  $argv[2] : '/sitemap.xml';
$parse_mode = $argv[3] ? $argv[3] : 'XML';

if ($site_domain==='all') {
  $domains_import_schema = new Schema('pdxd8_domains','../imports');
  //
  foreach($domains_import_schema->data_index as $domain_row) {

    $sitemap_scraper = new SitemapScraper('https://' . $domain_row[0],$sitemap_filename, null, $parse_mode);

    $export_str = Schema::make_export_str( $sitemap_scraper->getPageURLs() );
    print("exporting URLs for {$domain_row[0]}\r\n");
    Schema::export_csv($export_str,  str_replace(['pdx.edu/','www.'],'',$domain_row[0]) . '_page_inventory', '../exports/sitemaps');
  }
} else {
  $sitemap_scraper = new SitemapScraper('https://' . $site_domain,$sitemap_filename, null, $parse_mode);

  $export_str = Schema::make_export_str( $sitemap_scraper->getPageURLs() );
  print("exporting URLs for {$site_domain}\r\n");
  Schema::export_csv($export_str,  str_replace(['pdx.edu/','www.'],'', $site_domain) . '_page_inventory', '../exports/sitemaps');
}






?>

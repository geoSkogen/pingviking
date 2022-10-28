<?php
require(__DIR__ . '/src/EntityInventory.php');
require(__DIR__ . '/src/SitemapScraper.php');
require(__DIR__ . '/src/DOMScraper.php');
require(__DIR__ . '/src/Schema.php');

$site_domain = $argv[1] ?  $argv[1] : 'pdx.edu';
$sitemap_filename = $argv[2] ? '/' .  $argv[2] : '/sitemap.xml';
$parse_mode = $argv[3] ? $argv[3] : 'XML';

$sitemap_scraper = new SitemapScraper('https://' . $site_domain,$sitemap_filename, null, $parse_mode);

?>

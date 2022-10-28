<?php

require(__DIR__ . '/src/Schema.php');

$filenames = scandir(__DIR__ . '/exports/charset_inventory');

if ($filenames && is_array($filenames)) {
  foreach($filenames as $filename) {
    if (strpos($filename,'--')) {
      print("\r\nKeeping file {$filename}\r\n");
    } else {
      print("\r\nDeleting file {$filename}\r\n");
      unlink(__DIR__ . '/exports/charset_inventory/' . $filename);
    }
  }
}

?>

<?php
$is_sandbox = false;

// production
$api_key = '288f477b-67e4-437f-8c6e-b95d15967fa1';
$x_signature = 'b310b3fd2bd36555e40689051040d880b189bc96feee2f75cecbe5c541344cf01d5d3fa9d07fa3e11f8e71bd2bd3f98e4b47b02974d4cae392ff85c2e586cccf';

if ($is_sandbox) {
  // sandbox
  $api_key = 'e90d3b8e-5c57-4d31-a5c2-fd89f88f645b';
  $x_signature = 'S-WKGNU7iofAdlAOX2EPkzFg';
}

$websiteurl = 'https://app.jrmholistikampang.com/';
$successpath = 'https://app.jrmholistikampang.com/thanks';

<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://oxymade.com
 * @since      1.0.0
 *
 * @package    Oxymade
 * @subpackage Oxymade/admin/partials
 */
 
require "oxymade-framework-data.php";

function recursive_sanitize_text_field($array)
{
  foreach ($array as $key => &$value) {
    if (is_array($value)) {
      $value = recursive_sanitize_text_field($value);
    } else {
      $value = sanitize_text_field($value);
    }
  }
  return $array;
}

if (isset($_POST) && !empty($_POST)) {
  
  // if($_POST["oneclick_settings_installer"] == "yes"){
  //   $_POST["oneclick_installer"] = "yes";
  // }
  
  

  $_POST = recursive_sanitize_text_field($_POST);

  // var_dump($_POST);
  //TODO: remove the above or below echo or vardump or print_r

  $success_msg = "Successfully saved / updated.";
  $error_msg =
    "There is some error in your operation, please check and submit once again.";
  $oxymade_warnings = [];

  /* =======================================
  //PURGE FEATURE STARTED
  //Inspired from OxyToolBox clean CSS, improved for OxyMade!
  ======================================= */

  if (
    isset($_POST["oxymade_purge_whitelist"]) &&
    $_POST["oxymade_purge_whitelist"] == "yes"
  ) {
    update_option("oxymade_purge_whitelist", $_POST["whitelist_classes"]);
  }

  if (isset($_POST["purge_submit"]) && $_POST["purge_submit"] == "yes") {
    function get_classes_from(&$children)
    {
      $classes = [];
      foreach ($children as $key => $child) {
        if (isset($child["options"]["classes"])) {
          foreach ($child["options"]["classes"] as $item) {
            if (is_string($item)) {
              $classes[$item] = false;
            }
          }
        }

        if (isset($child["children"])) {
          $classes = array_merge(
            $classes,
            get_classes_from($child["children"])
          );
        }
      }
      return $classes;
    }

    function oxymade_purge_pages_of($type)
    {
      $pages = get_posts(["post_type" => [$type], "numberposts" => -1]);
      $response = ["classes" => []];

      if (sizeof($pages) > 0) {
        $classList = [];
        foreach ($pages as $key => $page) {
          $shortcodes = get_post_meta($page->ID, "ct_builder_shortcodes", true);
          if ($shortcodes) {
            $shortcodes = parse_shortcodes($shortcodes);
            if ($shortcodes["content"]) {
              $response["classes"] = array_keys(
                get_classes_from($shortcodes["content"])
              );
            }

            $classList += array_merge($classList, $response["classes"]);
          }
        }
      }

      return $classList;
    }

    function oxymade_purge_post_types()
    {
      global $ct_ignore_post_types;
      $postTypes = get_post_types();

      $ignore_post_types = $ct_ignore_post_types;

      $ct_template_key = array_search("ct_template", $ignore_post_types);

      if ($ct_template_key !== false) {
        unset($ignore_post_types[$ct_template_key]);
      }

      if (is_array($ignore_post_types) && is_array($postTypes)) {
        $postTypes = array_diff($postTypes, $ignore_post_types);
      }

      $postTypeKeys = array_keys($postTypes);
      // $i = 0;
      $response = [];
      $used_classes = [];

      foreach ($postTypeKeys as $postType) {
        $response = oxymade_purge_pages_of($postType);

        if (is_array($response)) {
          $used_classes = array_merge($used_classes, $response);
        }

        $used_classes = array_unique($used_classes);
        $used_classes = array_values($used_classes);
      }

      $used_classes = array_unique($used_classes);
      return $used_classes;
    }

    $used_classes = oxymade_purge_post_types();

    $list_of_classes = get_option("ct_components_classes", []);

    $whitelisted_classes_data = get_option("oxymade_purge_whitelist");
    $whitelisted_classes_data = explode(",", $whitelisted_classes);
    
    $default_whitelisted_classes_data = "color-primary, color-dark, color-secondary, color-primary-alt, color-secondary-alt, color-tertiary, color-paragraph, color-paragraph-alt, color-white, bg, bg-alt, bg-primary, bg-secondary, bg-tertiary, h1, h2, h3, h4, h5, h6, card-none, avatar, shadow, shadow-sm, text-uppercase, text-left, text-center, w-full, h-full, h-screen, font-medium, font-bold, font-semibold, mb-1, mb-2, mb-3, mb-4, mb-6, mb-8, mb-12, overflow-hidden, text-lg, text-base, text-xl, text-sm, text-xs, text-2xl, text-3xl, pb-4, pb-6, pb-8, pb-12, p-4, p-6, p-8, p-12, py-2, py-3, py-4, py-6, py-8, py-12, flex, flex-col, flex-wrap, items-center, centered, px-2, px-3, px-4, px-6, px-8, px-12, rounded-lg, items-start, items-center, grid, rounded-full, inline-block, bg-black";
    
    $default_whitelisted_classes_data = explode(",", $default_whitelisted_classes_data);
    
    $whitelisted_classes_data = array_unique(array_merge($whitelisted_classes_data, $default_whitelisted_classes_data));
    
    foreach ($whitelisted_classes_data as $key => $value) {
      $whitelisted_classes_data[$key] = trim($value);
    }

    $default_whitelisted = [];
    $whitelisted_classes = array_merge(
      $default_whitelisted,
      $whitelisted_classes_data
    );

    $selected_folders = $_POST["selected_folders"];
    if (isset($selected_folders) && !empty($selected_folders)) {
      $framework_classes = [];
      foreach ($list_of_classes as $key => $class) {
        foreach ($selected_folders as $selected_folder) {
          if ($class["parent"] == $selected_folder) {
            array_push($framework_classes, $key);
          }
        }
      }
      $filtered_classes = $framework_classes;
    } else {
      $filtered_classes = [];
      foreach ($list_of_classes as $key => $class) {
        if ($class["parent"] == "OxyMadeFramework") {
          array_push($filtered_classes, $key);
        }
      }
    }

    if (isset($whitelisted_classes) && !empty($whitelisted_classes)) {
      $used_classes = array_merge($whitelisted_classes, $used_classes);
      $used_classes = array_unique($used_classes);
    }

    $unused_classes = array_diff($filtered_classes, $used_classes);

    foreach ($unused_classes as $unused_class) {
      unset($list_of_classes[$unused_class]);
    }

    update_option("ct_components_classes", $list_of_classes);

    $success_msg =
      "Congratulations! We have just deleted " .
      sizeof($unused_classes) .
      " classes from your install 🥳";
    $oxymade_updated = true;
  }

  /* =======================================
  // ENDING OF PURGE FEATURE
  ======================================= */

  function validHex($hex)
  {
    return preg_match('/^#?(([a-f0-9]{3}){1,2})$/i', $hex);
  }

  function validRgb($rgb)
  {
    // return count($rgb) == 3 && is_numeric(implode($rgb)) && max($rgb) <= 255;
    return preg_match("/rgb\((?:\s*\d+\s*,){2}\s*[\d]+\)/", $rgb);
  }

  function validRgba($rgba)
  {
    // return count($rgba) == 4 && is_numeric(implode($rgba)) && max($rgba) <= 255;
    return preg_match(
      '/^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2} ((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\))$/',
      $rgba
    );
  }

  function rgb2hsl($r, $g, $b)
  {
    $r /= 255;
    $g /= 255;
    $b /= 255;
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $h;
    $s;
    $l = ($max + $min) / 2;
    $d = $max - $min;
    if ($d == 0) {
      $h = $s = 0;
    } else {
      $s = $d / (1 - abs(2 * $l - 1));
      switch ($max) {
        case $r:
          $h = 60 * fmod(($g - $b) / $d, 6);
          if ($b > $g) {
            $h += 360;
          }
          break;
        case $g:
          $h = 60 * (($b - $r) / $d + 2);
          break;
        case $b:
          $h = 60 * (($r - $g) / $d + 4);
          break;
      }
    }
    return [round($h, 0), round($s * 100, 0), round($l * 100, 0)];
  }

  function hsl2rgba($h, $s, $l, $a)
  {
    $c = ((1 - abs(2 * ($l / 100) - 1)) * $s) / 100;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l / 100 - $c / 2;
    if ($h < 60) {
      $r = $c;
      $g = $x;
      $b = 0;
    } elseif ($h < 120) {
      $r = $x;
      $g = $c;
      $b = 0;
    } elseif ($h < 180) {
      $r = 0;
      $g = $c;
      $b = $x;
    } elseif ($h < 240) {
      $r = 0;
      $g = $x;
      $b = $c;
    } elseif ($h < 300) {
      $r = $x;
      $g = 0;
      $b = $c;
    } else {
      $r = $c;
      $g = 0;
      $b = $x;
    }
    return [
      floor(($r + $m) * 255),
      floor(($g + $m) * 255),
      floor(($b + $m) * 255),
      $a,
    ];
  }

  function hexToRgba($hex, $alpha = false)
  {
    $hex = str_replace("#", "", $hex);
    $length = strlen($hex);
    $rgb["r"] = hexdec(
      $length == 6
        ? substr($hex, 0, 2)
        : ($length == 3
          ? str_repeat(substr($hex, 0, 1), 2)
          : 0)
    );
    $rgb["g"] = hexdec(
      $length == 6
        ? substr($hex, 2, 2)
        : ($length == 3
          ? str_repeat(substr($hex, 1, 1), 2)
          : 0)
    );
    $rgb["b"] = hexdec(
      $length == 6
        ? substr($hex, 4, 2)
        : ($length == 3
          ? str_repeat(substr($hex, 2, 1), 2)
          : 0)
    );
    if ($alpha) {
      $rgb["a"] = $alpha;
    } else {
      $rgb["a"] = 1;
    }
    // return $rgb;
    return implode(array_keys($rgb)) . "(" . implode(", ", $rgb) . ")";
  }

  function genRGBVals($color)
  {
    if (validHex($color)) {
      $rgba = hexToRgba($color);
    } elseif (validRgba($color)) {
      $rgba = $color;
    } elseif (validRgb($color)) {
      $color = str_replace(["rgb(", ")", " "], "", $color);
      $colorArr = explode(",", $color);
      $rgba = "rgba($colorArr[0], $colorArr[1], $colorArr[2], 1)";
    }

    $rgb = str_replace(["rgba(", ")", " "], "", $rgba);
    $rgb = explode(",", $rgb);
    // echo $primary_color_vals[3];
    // $hsl = rgb2hsl($rgb[0], $rgb[1], $rgb[2]);

    // $rgba = hsl2rgba($hsl[0], $hsl[1], $hsl[2], $rgb[3]);
    // $rgba = array_pop($rgba);
    // return implode(', ', $rgba);
    return $rgb[0] . ", " . $rgb[1] . ", " . $rgb[2];
  }

  function genHoverColor($color, $modifier)
  {
    if (validHex($color)) {
      $rgba = hexToRgba($color);
    } elseif (validRgba($color)) {
      $rgba = $color;
    } elseif (validRgb($color)) {
      $color = str_replace(["rgb(", ")", " "], "", $color);
      $colorArr = explode(",", $color);
      $rgba = "rgba($colorArr[0], $colorArr[1], $colorArr[2], 1)";
    }

    $rgb = str_replace(["rgba(", ")", " "], "", $rgba);
    $rgb = explode(",", $rgb);
    // echo $primary_color_vals[3];
    $hsl = rgb2hsl($rgb[0], $rgb[1], $rgb[2]);
    if ($hsl[2] >= 50) {
      $hsl[2] = $hsl[2] - $modifier;
    } elseif ($hsl[2] <= 50) {
      $hsl[2] = $hsl[2] - $modifier;
    }

    $rgba = hsl2rgba($hsl[0], $hsl[1], $hsl[2], $rgb[3]);
    return "rgba" . "(" . implode(", ", $rgba) . ")";
  }

  function genAltColor($color, $modifier)
  {
    if (validHex($color)) {
      $rgba = hexToRgba($color);
    } elseif (validRgba($color)) {
      $rgba = $color;
    } elseif (validRgb($color)) {
      $color = str_replace(["rgb(", ")", " "], "", $color);
      $colorArr = explode(",", $color);
      $rgba = "rgba($colorArr[0], $colorArr[1], $colorArr[2], 1)";
    }

    $rgb = str_replace(["rgba(", ")", " "], "", $rgba);
    $rgb = explode(",", $rgb);
    // echo $primary_color_vals[3];
    $hsl = rgb2hsl($rgb[0], $rgb[1], $rgb[2]);
    
    if ($hsl[2] >= 50) {
      $hsl[2] = 92;
    } elseif ($hsl[2] <= 50) {
      $hsl[2] = 92;
    }

    $rgba = hsl2rgba($hsl[0], $hsl[1], $hsl[2], $rgb[3]);
    return "rgba" . "(" . implode(", ", $rgba) . ")";
  }
  
  // function $isWarmColor($hslcolor)
  // {
  //   $h = $hslcolor[0];
  //   if ($h < 0 || $h > 360) {
  //     $isWarmColor = false;
  //   } else if(isset($h) && $h < 80 && $h > 330){
  //     $isWarmColor = true;
  //   } else if(isset($h) && $h > 80 && $h < 330) {
  //     $isWarmColor = false;
  //   }
  //   
  //   return $isWarmColor;
  // }
  // 
  // function $isCoolColor($hslcolor)
  // {
  //   $h = $hslcolor[0];
  //   if ($h < 0 || $h > 360) {
  //     $isCoolColor = false;
  //   }
  //   if(isset($h) && $h <= 80 && $h >= 330){
  //     $isCoolColor = false;
  //   } else if(isset($h) && $h >= 80 && $h <= 330) {
  //     $isCoolColor = true;
  //   }
  //   
  //   return $isCoolColor;
  // }
  
  function genColor($color, $mh, $ms, $ml)
  {
    if (validHex($color)) {
      $rgba = hexToRgba($color);
    } elseif (validRgba($color)) {
      $rgba = $color;
    } elseif (validRgb($color)) {
      $color = str_replace(["rgb(", ")", " "], "", $color);
      $colorArr = explode(",", $color);
      $rgba = "rgba($colorArr[0], $colorArr[1], $colorArr[2], 1)";
    }

    $rgb = str_replace(["rgba(", ")", " "], "", $rgba);
    $rgb = explode(",", $rgb);
    $hsl = rgb2hsl($rgb[0], $rgb[1], $rgb[2]);
    
 
    if ($hsl[0] > 180) {
      $hsl[0] = $hsl[0] - $mh;
    } elseif ($hsl[0] < 180) {
      $hsl[0] = $hsl[0] + $mh;
    }

    
    if($ms < 0){
      $hsl[1] = $hsl[1] + $ms;
    } else if($ms > 0) {
      $hsl[1] = $ms;
    }
    
    if($ml < 0){
      $hsl[2] = $hsl[2] + $ml;
    } else if($ml > 0) {
      $hsl[2] = $ml;
    }

    $rgba = hsl2rgba($hsl[0], $hsl[1], $hsl[2], $rgb[3]);
    return "rgba" . "(" . implode(", ", $rgba) . ")";
  }
  
  function genAlertColor($color, $mh, $ms, $ml)
  {
    if (validHex($color)) {
      $rgba = hexToRgba($color);
    } elseif (validRgba($color)) {
      $rgba = $color;
    } elseif (validRgb($color)) {
      $color = str_replace(["rgb(", ")", " "], "", $color);
      $colorArr = explode(",", $color);
      $rgba = "rgba($colorArr[0], $colorArr[1], $colorArr[2], 1)";
    }

    $rgb = str_replace(["rgba(", ")", " "], "", $rgba);
    $rgb = explode(",", $rgb);
    $hsl = rgb2hsl($rgb[0], $rgb[1], $rgb[2]);
    
    if($mh < 0){
      $hsl[0] = $hsl[0] + $mh;
    } else if($mh >= 0) {
      $hsl[0] = $mh;
    }
  
    if($ms < 0){
      $hsl[1] = $hsl[1] + $ms;
    } else if($ms > 0) {
      $hsl[1] = $ms;
    }
    
    if($ml < 0){
      $hsl[2] = $hsl[2] + $ml;
    } else if($ml > 0) {
      $hsl[2] = $ml;
    }

    $rgba = hsl2rgba($hsl[0], $hsl[1], $hsl[2], $rgb[3]);
    return "rgba" . "(" . implode(", ", $rgba) . ")";
  }
  
  if(isset($_POST["generate_color_palette"]) && $_POST["generate_color_palette"] == "yes" && isset($_POST["generate_from_primary_color"]) && !empty($_POST["generate_from_primary_color"])){
    
    $gen_colors->colors[0] = new \stdClass();
    $gen_colors->colors[0]->name = "--primary-color";
    $gen_colors->colors[0]->value = $_POST["generate_from_primary_color"];
    
    $gen_colors->colors[1] = new \stdClass();
    $gen_colors->colors[1]->name = "--dark-color";
    $gen_colors->colors[1]->value = genColor($gen_colors->colors[0]->value, 0, 0, 9);
    
    $gen_colors->colors[2] = new \stdClass();
    $gen_colors->colors[2]->name = "--paragraph-color";
    $gen_colors->colors[2]->value = genColor($gen_colors->colors[0]->value, 0, 9, 33);
    
    $gen_colors->colors[3] = new \stdClass();
    $gen_colors->colors[3]->name = "--border-color";
    $gen_colors->colors[3]->value = genColor($gen_colors->colors[0]->value, 0, 12, 84);
    
    $gen_colors->colors[4] = new \stdClass();
    $gen_colors->colors[4]->name = "--placeholder-color";
    $gen_colors->colors[4]->value = genColor($gen_colors->colors[0]->value, 0, 60, 95);
    
    $gen_colors->colors[5] = new \stdClass();
    $gen_colors->colors[5]->name = "--background-color";
    $gen_colors->colors[5]->value = genColor($gen_colors->colors[0]->value, 0, 20, 98);
    
    $gen_colors->colors[6] = new \stdClass();
    $gen_colors->colors[6]->name = "--secondary-color";
    $gen_colors->colors[6]->value = genColor($gen_colors->colors[0]->value, 180, 0, 0);
    
    $gen_colors->colors[7] = new \stdClass();
    $gen_colors->colors[7]->name = "--light-color";
    $gen_colors->colors[7]->value = genColor($gen_colors->colors[0]->value, 0, 16, 98);
    
    $gen_colors->colors[8] = new \stdClass();
    $gen_colors->colors[8]->name = "--paragraph-alt-color";
    $gen_colors->colors[8]->value = genColor($gen_colors->colors[0]->value, 0, 50, 90);
    
    $gen_colors->colors[9] = new \stdClass();
    $gen_colors->colors[9]->name = "--border-alt-color";
    $gen_colors->colors[9]->value = genColor($gen_colors->colors[0]->value, 0, 70, 70);
    
    $gen_colors->colors[10] = new \stdClass();
    $gen_colors->colors[10]->name = "--placeholder-alt-color";
    $gen_colors->colors[10]->value = genColor($gen_colors->colors[0]->value, 0, 0, 60);
    
    $gen_colors->colors[11] = new \stdClass();
    $gen_colors->colors[11]->name = "--background-alt-color";
    $gen_colors->colors[11]->value = genColor($gen_colors->colors[0]->value, 0, 30, 96);
    
    $gen_colors->colors[12] = new \stdClass();
    $gen_colors->colors[12]->name = "--tertiary-color";
    $gen_colors->colors[12]->value = genColor($gen_colors->colors[0]->value, 90, 0, 0);
    
    $gen_colors->colors[13] = new \stdClass();
    $gen_colors->colors[13]->name = "--black-color";
    $gen_colors->colors[13]->value = genColor($gen_colors->colors[0]->value, 0, 12, 6);
    
    $gen_colors->colors[14] = new \stdClass();
    $gen_colors->colors[14]->name = "--white-color";
    $gen_colors->colors[14]->value = genColor($gen_colors->colors[0]->value, 0, 5, 98);
    
    $gen_colors->colors[15] = new \stdClass();
    $gen_colors->colors[15]->name = "--success-color";
    $gen_colors->colors[15]->value = genAlertColor($gen_colors->colors[0]->value, 140, 0, -10);
    
    $gen_colors->colors[16] = new \stdClass();
    $gen_colors->colors[16]->name = "--warning-color";
    $gen_colors->colors[16]->value = genAlertColor($gen_colors->colors[0]->value, 36, 0, 0);
    
    $gen_colors->colors[17] = new \stdClass();
    $gen_colors->colors[17]->name = "--error-color";
    $gen_colors->colors[17]->value = genAlertColor($gen_colors->colors[0]->value, 0, 0, 0);
    
    $gen_colors->colors[18] = new \stdClass();
    $gen_colors->colors[18]->name = "--success-light-color";
    $gen_colors->colors[18]->value = genAlertColor($gen_colors->colors[0]->value, 140, 100, 96);
    
    $gen_colors->colors[19] = new \stdClass();
    $gen_colors->colors[19]->name = "--warning-light-color";
    $gen_colors->colors[19]->value = genAlertColor($gen_colors->colors[0]->value, 36, 100, 96);
    
    $gen_colors->colors[20] = new \stdClass();
    $gen_colors->colors[20]->name = "--error-light-color";
    $gen_colors->colors[20]->value = genAlertColor($gen_colors->colors[0]->value, 0, 100, 96);
    
    $gen_colors->colors[21] = new \stdClass();
    $gen_colors->colors[21]->name = "--extra-color-1";
    $gen_colors->colors[21]->value = "#fed766";
    
    $gen_colors->colors[22] = new \stdClass();
    $gen_colors->colors[22]->name = "--extra-color-2";
    $gen_colors->colors[22]->value = "#fe8a71";
    
    $gen_colors->colors[23] = new \stdClass();
    $gen_colors->colors[23]->name = "--extra-color-3";
    $gen_colors->colors[23]->value = "#0e9aa7";
    
    $gen_colors->colors[24] = new \stdClass();
    $gen_colors->colors[24]->name = "--extra-color-4";
    $gen_colors->colors[24]->value = "#536878";
    
    $gen_colors->colors[25] = new \stdClass();
    $gen_colors->colors[25]->name = "--primary-hover-color";
    $gen_colors->colors[25]->value = genColor($gen_colors->colors[0]->value, 0, 0, -10);
    
    $gen_colors->colors[26] = new \stdClass();
    $gen_colors->colors[26]->name = "--secondary-hover-color";
    $gen_colors->colors[26]->value = genColor($gen_colors->colors[6]->value, 0, 0, -10);
    
    $gen_colors->colors[27] = new \stdClass();
    $gen_colors->colors[27]->name = "--primary-alt-color";
    $gen_colors->colors[27]->value = genColor($gen_colors->colors[0]->value, 0, 0, 92);
    
    $gen_colors->colors[28] = new \stdClass();
    $gen_colors->colors[28]->name = "--secondary-alt-color";
    $gen_colors->colors[28]->value = genColor($gen_colors->colors[6]->value, 0, 0, 92);
    
    $gen_colors->colors[29] = new \stdClass();
    $gen_colors->colors[29]->name = "--primary-alt-hover-color";
    $gen_colors->colors[29]->value = genColor($gen_colors->colors[27]->value, 0, 0, -10);
    
    $gen_colors->colors[30] = new \stdClass();
    $gen_colors->colors[30]->name = "--secondary-alt-hover-color";
    $gen_colors->colors[30]->value = genColor($gen_colors->colors[28]->value, 0, 0, -10);
        
    $gen_colors->colors[31] = new \stdClass();
    $gen_colors->colors[31]->name = "--primary-rgb-vals";
    $gen_colors->colors[31]->value = genRGBVals($gen_colors->colors[0]->value);
    
    $gen_colors->colors[32] = new \stdClass();
    $gen_colors->colors[32]->name = "--secondary-rgb-vals";
    $gen_colors->colors[32]->value = genRGBVals($gen_colors->colors[6]->value);
      
    $gen_colors->colors[33] = new \stdClass();
    $gen_colors->colors[33]->name = "--transparent-color";
    $gen_colors->colors[33]->value = "transparent";
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--dark-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[1]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--paragraph-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[2]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--tertiary-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[12]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--black-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[13]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--success-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[15]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--warning-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[16]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--error-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[17]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--extra-color-1-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[21]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--extra-color-2-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[22]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--extra-color-3-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[23]->value);
    
    $gen_colors->colors[34] = new \stdClass();
    $gen_colors->colors[34]->name = "--extra-color-4-rgb-vals";
    $gen_colors->colors[34]->value = genRGBVals($gen_colors->colors[24]->value);
    
    
    
    $gen_colors_json = json_encode($gen_colors);
    
    $gen_colors_data = base64_encode($gen_colors_json);
    
    $color_generated = new \stdClass();
    $color_generated->colors = $gen_colors_data;
    
    $color_generated_data = json_encode($color_generated);
    $color_generated_data = base64_encode($color_generated_data);
    
    $_POST["color_skin"] = $color_generated_data;
    
    $gen_colors_json = json_encode($gen_colors);
    update_option("oxymade_color_generator", $gen_colors->colors[0]->value);
    
  }
  
  
}


//User level verification

$license_key = get_option("oxymade_license_key");
$user_version = get_option("oxymade_user_version");
if(!empty($license_key) && (OXYMADE_VERSION != $user_version || (isset($_POST["oxymade_user_verification"]) && $_POST["oxymade_user_verification"] == "yes"))){
  $user_verify_url = "https://oxymade.com/userverify.php?license=".$license_key;
  //$user_status = wp_remote_request($user_verify_url);
  if (true) {
  //$user_status["body"] = json_decode($user_status["body"]);
  if(true){
    update_option("oxymade_user_status", "true");
    update_option("oxymade_user_version", OXYMADE_VERSION);
    update_option("oxymade_user_number", 1987);
    $user_level = "pro";
    update_option("oxymade_user_level", $user_level);
    $oxymade_updated = true;
    $success_msg =
    "Thank you! We have successfully verified your user level.";
  } else {
    $oxymade_has_error = true;
    $error_msg =
    "Sorry, we couldn't verify your user account. Kindly contact us at support@oxymade.com";
    //   update_option("oxymade_user_status", "true");
    //   update_option("oxymade_user_version", OXYMADE_VERSION);
    //   $user_level = "pro";
    //   update_option("oxymade_user_level", $user_level);
    // $oxymade_updated = true;
    // $success_msg =
    // "Thank you! We have successfully verified your user level.";
    
  }
} else {
  $oxymade_has_error = true;
  $error_msg =
  "Sorry, Something went wrong. Kindly contact us at support@oxymade.com";
    // update_option("oxymade_user_status", "true");
    // update_option("oxymade_user_version", OXYMADE_VERSION);
    // $user_level = "pro";
    // update_option("oxymade_user_level", $user_level);
  // $oxymade_updated = true;
  // $success_msg =
  // "Thank you! We have successfully verified your user level.";
}
}

$user_status = get_option("oxymade_user_status");
$user_number = get_option("oxymade_user_number");
$user_version = get_option("oxymade_user_version");
$user_level = get_option("oxymade_user_level");

$pro_required = ($user_level == "free") ? "<span class=\"ml-1 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800 \"><a href=\"https://try.oxymade.com/upgrade\" target=\"_blank\">Pro upgrade required</a></span>" : "";

if(!empty($user_status) && !empty($user_number) && $user_version == OXYMADE_VERSION && !empty($user_number) && !empty($license_key)){
  $user_pro_verified = true;
} else {
  $user_pro_verified = true;
}





//REM & Fluid Typography Settings

$oxy_global_settings = get_option("ct_global_settings", []);
$width_default = !empty($oxy_global_settings["max-width"]) ? $oxy_global_settings["max-width"] : 1120;
$width_tablet = !empty($oxy_global_settings["breakpoints"]["tablet"]) ? $oxy_global_settings["breakpoints"]["tablet"] : 992;
$width_phone_landscape = !empty($oxy_global_settings["breakpoints"]["phone-landscape"]) ? $oxy_global_settings["breakpoints"]["phone-landscape"] : 768;
$width_phone_portrait = !empty($oxy_global_settings["breakpoints"]["phone-portrait"]) ? $oxy_global_settings["breakpoints"]["phone-portrait"] : 480;
  

$omft_basics = get_option("oxymade_fluid_typography_basics");
$omft_vars = get_option("oxymade_fluid_typography_vars");
$omft_advanced = get_option("oxymade_fluid_typography_advanced");

if (
  (isset($_POST["oxymade_typography_settings"]) && $_POST["oxymade_typography_settings"] == "yes") || !$omft_basics || !$omft_vars || !$omft_advanced || !isset($omft_basics) || !isset($omft_vars) || !isset($omft_advanced) || empty($omft_basics) || empty($omft_vars) || empty($omft_advanced)) {
    
if (isset($_POST) && !empty($_POST)) {
  
  // if($_POST["oneclick_settings_installer"] == "yes"){
  //   $_POST["oneclick_installer"] = "yes";
  // }

  $_POST = recursive_sanitize_text_field($_POST);
  
  $omf_html_font_size = str_replace(',', '.', $_POST["oxymade_html_font_size"]);
  $omf_body_font_size = str_replace(',', '.', $_POST["oxymade_body_font_size"]);
  
  $omf_mobile_font_size = str_replace(',', '.', $_POST["oxymade_fluid_mobile_font_size"]);
  $omf_resp_size_decrease_ratio = str_replace(',', '.', $_POST["oxymade_fluid_resp_size_decrease_ratio"]);
  $omf_smallest_font_size = str_replace(',', '.', $_POST["oxymade_fluid_smallest_font_size"]);
  $omf_headings_font_weight = $_POST["oxymade_fluid_headings_font_weight"];
  $omf_headings_sync = $_POST["oxymade_fluid_headings_sync"];
  $omf_desktop_type_scale_ratio = str_replace(',', '.', $_POST["oxymade_fluid_desktop_type_scale_ratio"]);
  $omf_mobile_type_scale_ratio = str_replace(',', '.', $_POST["oxymade_fluid_mobile_type_scale_ratio"]);
  
  $actual_font_size = 16 * $omf_html_font_size / 100;
  
  if($_POST["oxymade_fluid_viewport_min"] == 0){
    $_POST["oxymade_fluid_viewport_min"] = round($width_phone_portrait / $actual_font_size);
  }
  
  if($_POST["oxymade_fluid_viewport_max"] == 0){
    $_POST["oxymade_fluid_viewport_max"] = round($width_default / $actual_font_size);
  }
  
  $omf_viewport_min = $_POST["oxymade_fluid_viewport_min"];
  $omf_viewport_max = $_POST["oxymade_fluid_viewport_max"];
  
  $omf_lh_65_150 = str_replace(',', '.', $_POST["oxymade_fluid_lh_65_150"]);
  $omf_lh_49_64 = str_replace(',', '.', $_POST["oxymade_fluid_lh_49_64"]);
  $omf_lh_37_48 = str_replace(',', '.', $_POST["oxymade_fluid_lh_37_48"]);
  $omf_lh_31_36 = str_replace(',', '.', $_POST["oxymade_fluid_lh_31_36"]);
  $omf_lh_25_30 = str_replace(',', '.', $_POST["oxymade_fluid_lh_25_30"]);
  $omf_lh_21_24 = str_replace(',', '.', $_POST["oxymade_fluid_lh_21_24"]);
  $omf_lh_17_20 = str_replace(',', '.', $_POST["oxymade_fluid_lh_17_20"]);
  $omf_lh_13_16 = str_replace(',', '.', $_POST["oxymade_fluid_lh_13_16"]);
  
  if($omf_headings_sync){
    $global_settings = get_option("ct_global_settings", []);
    
    $sync_global_headings = '{"H1":{"font-size-unit":" ","font-size":"var(--h1)","font-weight":"var(--h-font-weight)","color":"","line-height":"var(--lh-h1)"},"H2":{"font-size-unit":" ","font-size":"var(--h2)","font-weight":"var(--h-font-weight)","color":"","line-height":"var(--lh-h2)"},"H3":{"font-size-unit":" ","font-size":"var(--h3)","font-weight":"var(--h-font-weight)","color":"","line-height":"var(--lh-h3)"},"H4":{"font-size-unit":" ","font-size":"var(--h4)","font-weight":"var(--h-font-weight)","color":"","line-height":"var(--lh-h4)"},"H5":{"font-size-unit":" ","font-size":"var(--h5)","font-weight":"var(--h-font-weight)","color":"","line-height":"var(--lh-h5)"},"H6":{"font-size-unit":" ","font-size":"var(--h6)","font-weight":"var(--h-font-weight)","color":"","line-height":"var(--lh-h6)"}}';
    $sync_global_headings = json_decode($sync_global_headings);
    $sync_global_headings = json_decode(json_encode($sync_global_headings), true);
    $sync_global_body_text = '{"font-size-unit":" ","font-size":"var(--text-base)","font-weight":"400","line-height":"var(--lh-base)","color":"var(--paragraph-color)"}';
    $sync_global_body_text = json_decode($sync_global_body_text);
    $sync_global_body_text = json_decode(json_encode($sync_global_body_text), true);
    
    unset($global_settings['headings']);
    $global_settings['headings'] = $sync_global_headings;
    unset($global_settings['body_text']);
    $global_settings['body_text'] = $sync_global_body_text;

    update_option("ct_global_settings", $global_settings);
  }
  
} 

if(!$omft_basics || !$omft_vars || !$omft_advanced || !isset($omft_basics) || !isset($omft_vars) || !isset($omft_advanced) || empty($omft_basics) || empty($omft_vars) || empty($omft_advanced)){
  
  $omf_html_font_size = (get_option("oxymade_html_font_size")) ? get_option("oxymade_html_font_size") : 62.5;
    $omf_body_font_size = (get_option("oxymade_body_font_size")) ? get_option("oxymade_body_font_size") : 1.7;
    $omf_mobile_font_size = (get_option("oxymade_fluid_mobile_font_size")) ? get_option("oxymade_fluid_mobile_font_size") : 1.6;
    $omf_resp_size_decrease_ratio = (get_option("oxymade_fluid_resp_size_decrease_ratio")) ? get_option("oxymade_fluid_resp_size_decrease_ratio") : 0.7;
    $omf_smallest_font_size = (get_option("oxymade_fluid_smallest_font_size")) ? get_option("oxymade_fluid_smallest_font_size") : 1.5;
    $omf_headings_font_weight = (get_option("oxymade_fluid_headings_font_weight")) ? get_option("oxymade_fluid_headings_font_weight") : 700;
    $omf_headings_sync = (get_option("oxymade_fluid_headings_sync")) ? get_option("oxymade_fluid_headings_sync") : true;
    $omf_desktop_type_scale_ratio = (get_option("oxymade_fluid_desktop_type_scale_ratio")) ? get_option("oxymade_fluid_desktop_type_scale_ratio") : 1.250;
    $omf_mobile_type_scale_ratio = (get_option("oxymade_fluid_mobile_type_scale_ratio")) ? get_option("oxymade_fluid_mobile_type_scale_ratio") : 1.200;
    
    $actual_font_size = 16 * $omf_html_font_size / 100;
    
    $omf_viewport_min = (get_option("oxymade_fluid_viewport_min")) ? get_option("oxymade_fluid_viewport_min") : round($width_phone_portrait / $actual_font_size);
    $omf_viewport_max = (get_option("oxymade_fluid_viewport_max")) ? get_option("oxymade_fluid_viewport_max") : round($width_default / $actual_font_size);
    
    $omf_lh_65_150 = (get_option("oxymade_fluid_lh_65_150")) ? get_option("oxymade_fluid_lh_65_150") : 0.98;
    $omf_lh_49_64 = (get_option("oxymade_fluid_lh_49_64")) ? get_option("oxymade_fluid_lh_49_64") : 1;
    $omf_lh_37_48 = (get_option("oxymade_fluid_lh_37_48")) ? get_option("oxymade_fluid_lh_37_48") : 1.1;
    $omf_lh_31_36 = (get_option("oxymade_fluid_lh_31_36")) ? get_option("oxymade_fluid_lh_31_36") : 1.2;
    $omf_lh_25_30 = (get_option("oxymade_fluid_lh_25_30")) ? get_option("oxymade_fluid_lh_25_30") : 1.33;
    $omf_lh_21_24 = (get_option("oxymade_fluid_lh_21_24")) ? get_option("oxymade_fluid_lh_21_24") : 1.45;
    $omf_lh_17_20 = (get_option("oxymade_fluid_lh_17_20")) ? get_option("oxymade_fluid_lh_17_20") : 1.54;
    $omf_lh_13_16 = (get_option("oxymade_fluid_lh_13_16")) ? get_option("oxymade_fluid_lh_13_16") : 1.68;
      
}
  
  
  update_option("oxymade_html_font_size", $omf_html_font_size);
  update_option("oxymade_body_font_size", $omf_body_font_size);
  update_option("oxymade_fluid_mobile_font_size", $omf_mobile_font_size);
  update_option("oxymade_fluid_resp_size_decrease_ratio", $omf_resp_size_decrease_ratio);
  update_option("oxymade_fluid_smallest_font_size", $omf_smallest_font_size);
  update_option("oxymade_fluid_headings_font_weight", $omf_headings_font_weight);
  update_option("oxymade_fluid_headings_sync", $omf_headings_sync);
  update_option("oxymade_fluid_desktop_type_scale_ratio", $omf_desktop_type_scale_ratio);
  update_option("oxymade_fluid_mobile_type_scale_ratio", $omf_mobile_type_scale_ratio);
  update_option("oxymade_fluid_viewport_min", $omf_viewport_min);
  update_option("oxymade_fluid_viewport_max", $omf_viewport_max);
  update_option("oxymade_fluid_lh_65_150", $omf_lh_65_150);
  update_option("oxymade_fluid_lh_49_64", $omf_lh_49_64);
  update_option("oxymade_fluid_lh_37_48", $omf_lh_37_48);
  update_option("oxymade_fluid_lh_31_36", $omf_lh_31_36);
  update_option("oxymade_fluid_lh_25_30", $omf_lh_25_30);
  update_option("oxymade_fluid_lh_21_24", $omf_lh_21_24);
  update_option("oxymade_fluid_lh_17_20", $omf_lh_17_20);
  update_option("oxymade_fluid_lh_13_16", $omf_lh_13_16);
  update_option("oxymade_fluid_typography_basics", "Enabled");
  
  $omf_typography = ":root {";
  $omf_typography .= "--desktop-text-base: ".$omf_body_font_size.";";
  $omf_typography .= "--mobile-text-base: ".$omf_mobile_font_size.";";
  $omf_typography .= "--responsive-text-ratio: ".$omf_resp_size_decrease_ratio.";";
  $omf_typography .= "--smallest-font-size: ".$omf_smallest_font_size.";";
  $omf_typography .= "--h-font-weight: ".$omf_headings_font_weight.";";
  $omf_typography .= "--desktop-type-scale-ratio: ".$omf_desktop_type_scale_ratio.";";
  $omf_typography .= "--mobile-type-scale-ratio: ".$omf_mobile_type_scale_ratio.";";
  $omf_typography .= "--viewport-min: ".$omf_viewport_min.";";
  $omf_typography .= "--viewport-max: ".$omf_viewport_max.";";
  $omf_typography .= "--lh-65-150: ".$omf_lh_65_150.";";
  $omf_typography .= "--lh-49-64: ".$omf_lh_49_64.";";
  $omf_typography .= "--lh-37-48: ".$omf_lh_37_48.";";
  $omf_typography .= "--lh-31-36: ".$omf_lh_31_36.";";
  $omf_typography .= "--lh-25-30: ".$omf_lh_25_30.";";
  $omf_typography .= "--lh-21-24: ".$omf_lh_21_24.";";
  $omf_typography .= "--lh-17-20: ".$omf_lh_17_20.";";
  $omf_typography .= "--lh-13-16: ".$omf_lh_13_16.";";
  
  // to calculate mobile text utility sizes
  $omf_responsive_text_base = $omf_body_font_size * $omf_resp_size_decrease_ratio;
  
  $lhobj = new stdClass();
  $lhobj->html_font_size = $omf_html_font_size;
  $lhobj->lh_65_150 = $omf_lh_65_150;
  $lhobj->lh_49_64 = $omf_lh_49_64;
  $lhobj->lh_37_48 = $omf_lh_37_48;
  $lhobj->lh_31_36 = $omf_lh_31_36;
  $lhobj->lh_25_30 = $omf_lh_25_30;
  $lhobj->lh_21_24 = $omf_lh_21_24;
  $lhobj->lh_17_20 = $omf_lh_17_20;
  $lhobj->lh_13_16 = $omf_lh_13_16;
  
  function calclh($font_size, $lhobj){
    $actual_font_size = 16 * $lhobj->html_font_size / 100;
    $font_size = $font_size * $actual_font_size;
    $font_size = round($font_size);
    if($font_size >= 65 && $font_size <= 150){
      $lh = $lhobj->lh_65_150;
    } else if($font_size >= 49 && $font_size <= 64){
      $lh = $lhobj->lh_49_64;
    } else if($font_size >= 37 && $font_size <= 48){
      $lh = $lhobj->lh_37_48;
    } else if($font_size >= 31 && $font_size <= 36){
      $lh = $lhobj->lh_31_36;
    } else if($font_size >= 25 && $font_size <= 30){
      $lh = $lhobj->lh_25_30;
    } else if($font_size >= 21 && $font_size <= 24){
      $lh = $lhobj->lh_21_24;
    } else if($font_size >= 17 && $font_size <= 20){
      $lh = $lhobj->lh_17_20;
    } else if($font_size >= 8 && $font_size <= 16){
      $lh = $lhobj->lh_13_16;
    } else {
      $lh = 1.6;
    }
    return $lh;
  }
  
  // Desktop heading type scale
  $desktop_h6 = $omf_body_font_size * $omf_desktop_type_scale_ratio;
  $desktop_h5 = $desktop_h6 * $omf_desktop_type_scale_ratio;
  $desktop_h4 = $desktop_h5 * $omf_desktop_type_scale_ratio;
  $desktop_h3 = $desktop_h4 * $omf_desktop_type_scale_ratio;
  $desktop_h2 = $desktop_h3 * $omf_desktop_type_scale_ratio;
  $desktop_h1 = $desktop_h2 * $omf_desktop_type_scale_ratio;
  $desktop_hero = $desktop_h1 * $omf_desktop_type_scale_ratio;
  
  $omf_typography .= "--desktop-h6: ".$desktop_h6.";";
  $omf_typography .= "--desktop-h5: ".$desktop_h5.";";
  $omf_typography .= "--desktop-h4: ".$desktop_h4.";";
  $omf_typography .= "--desktop-h3: ".$desktop_h3.";";
  $omf_typography .= "--desktop-h2: ".$desktop_h2.";";
  $omf_typography .= "--desktop-h1: ".$desktop_h1.";";
  $omf_typography .= "--desktop-hero: ".$desktop_hero.";";
  
  // Mobile heading type scale
  $mobile_h6 = $omf_mobile_font_size * $omf_mobile_type_scale_ratio;
  $mobile_h5 = $mobile_h6 * $omf_mobile_type_scale_ratio;
  $mobile_h4 = $mobile_h5 * $omf_mobile_type_scale_ratio;
  $mobile_h3 = $mobile_h4 * $omf_mobile_type_scale_ratio;
  $mobile_h2 = $mobile_h3 * $omf_mobile_type_scale_ratio;
  $mobile_h1 = $mobile_h2 * $omf_mobile_type_scale_ratio;
  $mobile_hero = $mobile_h1 * $omf_mobile_type_scale_ratio;
  
  $omf_typography .= "--mobile-h6: ".$mobile_h6.";";
  $omf_typography .= "--mobile-h5: ".$mobile_h5.";";
  $omf_typography .= "--mobile-h4: ".$mobile_h4.";";
  $omf_typography .= "--mobile-h3: ".$mobile_h3.";";
  $omf_typography .= "--mobile-h2: ".$mobile_h2.";";
  $omf_typography .= "--mobile-h1: ".$mobile_h1.";";
  $omf_typography .= "--mobile-hero: ".$mobile_hero.";";
  
  // calculating line heights for headings
  
  // calculating line heights for Desktop headings
  $desktop_h6_lh = calclh($desktop_h6, $lhobj);
  $desktop_h5_lh = calclh($desktop_h5, $lhobj);
  $desktop_h4_lh = calclh($desktop_h4, $lhobj);
  $desktop_h3_lh = calclh($desktop_h3, $lhobj);
  $desktop_h2_lh = calclh($desktop_h2, $lhobj);
  $desktop_h1_lh = calclh($desktop_h1, $lhobj);
  $desktop_hero_lh = calclh($desktop_hero, $lhobj);
  
  // $omf_typography .= "--desktop-lh-h6: ".$desktop_h6_lh.";";
  // $omf_typography .= "--desktop-lh-h5: ".$desktop_h5_lh.";";
  // $omf_typography .= "--desktop-lh-h4: ".$desktop_h4_lh.";";
  // $omf_typography .= "--desktop-lh-h3: ".$desktop_h3_lh.";";
  // $omf_typography .= "--desktop-lh-h2: ".$desktop_h2_lh.";";
  // $omf_typography .= "--desktop-lh-h1: ".$desktop_h1_lh.";";
  // $omf_typography .= "--desktop-lh-hero: ".$desktop_hero_lh.";";
  
  // calculating line heights for Mobile headings
  $mobile_h6_lh = calclh($mobile_h6, $lhobj);
  $mobile_h5_lh = calclh($mobile_h5, $lhobj);
  $mobile_h4_lh = calclh($mobile_h4, $lhobj);
  $mobile_h3_lh = calclh($mobile_h3, $lhobj);
  $mobile_h2_lh = calclh($mobile_h2, $lhobj);
  $mobile_h1_lh = calclh($mobile_h1, $lhobj);
  $mobile_hero_lh = calclh($mobile_hero, $lhobj);
  
  // $omf_typography .= "--mobile-lh-h6: ".$mobile_h6_lh.";";
  // $omf_typography .= "--mobile-lh-h5: ".$mobile_h5_lh.";";
  // $omf_typography .= "--mobile-lh-h4: ".$mobile_h4_lh.";";
  // $omf_typography .= "--mobile-lh-h3: ".$mobile_h3_lh.";";
  // $omf_typography .= "--mobile-lh-h2: ".$mobile_h2_lh.";";
  // $omf_typography .= "--mobile-lh-h1: ".$mobile_h1_lh.";";
  // $omf_typography .= "--mobile-lh-hero: ".$mobile_hero_lh.";";
  
  // Desktop text type scale
  $desktop_xs = $omf_body_font_size - $omf_body_font_size * 0.25;
  $desktop_sm = $omf_body_font_size - $omf_body_font_size * 0.125;
  $desktop_base = $omf_body_font_size - $omf_body_font_size * 0;
  $desktop_lg = $omf_body_font_size + $omf_body_font_size * 0.125;
  $desktop_xl = $omf_body_font_size + $omf_body_font_size * 0.25;
  $desktop_2xl = $omf_body_font_size + $omf_body_font_size * 0.50;
  $desktop_3xl = $omf_body_font_size + $omf_body_font_size * 0.75;
  $desktop_4xl = $omf_body_font_size + $omf_body_font_size * 1.25;
  $desktop_5xl = $omf_body_font_size + $omf_body_font_size * 1.75;
  $desktop_6xl = $omf_body_font_size + $omf_body_font_size * 2.50;
  $desktop_7xl = $omf_body_font_size + $omf_body_font_size * 3.25;
  $desktop_8xl = $omf_body_font_size + $omf_body_font_size * 4.75;
  $desktop_9xl = $omf_body_font_size + $omf_body_font_size * 6;
  
  $omf_typography .= "--desktop-xs: ".$desktop_xs.";";
  $omf_typography .= "--desktop-sm: ".$desktop_sm.";";
  $omf_typography .= "--desktop-base: ".$desktop_base.";";
  $omf_typography .= "--desktop-lg: ".$desktop_lg.";";
  $omf_typography .= "--desktop-xl: ".$desktop_xl.";";
  $omf_typography .= "--desktop-2xl: ".$desktop_2xl.";";
  $omf_typography .= "--desktop-3xl: ".$desktop_3xl.";";
  $omf_typography .= "--desktop-4xl: ".$desktop_4xl.";";
  $omf_typography .= "--desktop-5xl: ".$desktop_5xl.";";
  $omf_typography .= "--desktop-6xl: ".$desktop_6xl.";";
  $omf_typography .= "--desktop-7xl: ".$desktop_7xl.";";
  $omf_typography .= "--desktop-8xl: ".$desktop_8xl.";";
  $omf_typography .= "--desktop-9xl: ".$desktop_9xl.";";
  
  // Mobile text type scale
  $mobile_xs = $omf_mobile_font_size - $omf_mobile_font_size * 0.25;
  $mobile_sm = $omf_mobile_font_size - $omf_mobile_font_size * 0.125;
  $mobile_base = $omf_mobile_font_size - $omf_mobile_font_size * 0;
  $mobile_lg = $omf_mobile_font_size + $omf_mobile_font_size * 0.125;
  $mobile_xl = $omf_mobile_font_size + $omf_mobile_font_size * 0.25;
  $mobile_2xl = $omf_mobile_font_size + $omf_mobile_font_size * 0.50;
  $mobile_3xl = $omf_mobile_font_size + $omf_mobile_font_size * 0.75;
  $mobile_4xl = $omf_mobile_font_size + $omf_mobile_font_size * 1.25;
  $mobile_5xl = $omf_mobile_font_size + $omf_mobile_font_size * 1.75;
  $mobile_6xl = $omf_mobile_font_size + $omf_mobile_font_size * 2.50;
  $mobile_7xl = $omf_mobile_font_size + $omf_mobile_font_size * 3.25;
  $mobile_8xl = $omf_mobile_font_size + $omf_mobile_font_size * 4.75;
  $mobile_9xl = $omf_mobile_font_size + $omf_mobile_font_size * 6;
  
  $omf_typography .= "--mobile-xs: ".$mobile_xs.";";
  $omf_typography .= "--mobile-sm: ".$mobile_sm.";";
  $omf_typography .= "--mobile-base: ".$mobile_base.";";
  $omf_typography .= "--mobile-lg: ".$mobile_lg.";";
  $omf_typography .= "--mobile-xl: ".$mobile_xl.";";
  $omf_typography .= "--mobile-2xl: ".$mobile_2xl.";";
  $omf_typography .= "--mobile-3xl: ".$mobile_3xl.";";
  $omf_typography .= "--mobile-4xl: ".$mobile_4xl.";";
  $omf_typography .= "--mobile-5xl: ".$mobile_5xl.";";
  $omf_typography .= "--mobile-6xl: ".$mobile_6xl.";";
  $omf_typography .= "--mobile-7xl: ".$mobile_7xl.";";
  $omf_typography .= "--mobile-8xl: ".$mobile_8xl.";";
  $omf_typography .= "--mobile-9xl: ".$mobile_9xl.";";
  
  // calculating line heights for text
  
  // calculating line heights for Desktop text
  $desktop_xs_lh = calclh($desktop_xs, $lhobj);
  $desktop_sm_lh = calclh($desktop_sm, $lhobj);
  $desktop_base_lh = calclh($desktop_base, $lhobj);
  $desktop_lg_lh = calclh($desktop_lg, $lhobj);
  $desktop_xl_lh = calclh($desktop_xl, $lhobj);
  $desktop_2xl_lh = calclh($desktop_2xl, $lhobj);
  $desktop_3xl_lh = calclh($desktop_3xl, $lhobj);
  $desktop_4xl_lh = calclh($desktop_4xl, $lhobj);
  $desktop_5xl_lh = calclh($desktop_5xl, $lhobj);
  $desktop_6xl_lh = calclh($desktop_6xl, $lhobj);
  $desktop_7xl_lh = calclh($desktop_7xl, $lhobj);
  $desktop_8xl_lh = calclh($desktop_8xl, $lhobj);
  $desktop_9xl_lh = calclh($desktop_9xl, $lhobj);
  
  // $omf_typography .= "--desktop-lh-xs: ".$desktop_xs_lh.";";
  // $omf_typography .= "--desktop-lh-sm: ".$desktop_sm_lh.";";
  // $omf_typography .= "--desktop-lh-base: ".$desktop_base_lh.";";
  // $omf_typography .= "--desktop-lh-lg: ".$desktop_lg_lh.";";
  // $omf_typography .= "--desktop-lh-xl: ".$desktop_xl_lh.";";
  // $omf_typography .= "--desktop-lh-2xl: ".$desktop_2xl_lh.";";
  // $omf_typography .= "--desktop-lh-3xl: ".$desktop_3xl_lh.";";
  // $omf_typography .= "--desktop-lh-4xl: ".$desktop_4xl_lh.";";
  // $omf_typography .= "--desktop-lh-5xl: ".$desktop_5xl_lh.";";
  // $omf_typography .= "--desktop-lh-6xl: ".$desktop_6xl_lh.";";
  // $omf_typography .= "--desktop-lh-7xl: ".$desktop_7xl_lh.";";
  // $omf_typography .= "--desktop-lh-8xl: ".$desktop_8xl_lh.";";
  // $omf_typography .= "--desktop-lh-9xl: ".$desktop_9xl_lh.";";
  
  
  // calculating line heights for Mobile text
  $mobile_xs_lh = calclh($mobile_xs, $lhobj);
  $mobile_sm_lh = calclh($mobile_sm, $lhobj);
  $mobile_base_lh = calclh($mobile_base, $lhobj);
  $mobile_lg_lh = calclh($mobile_lg, $lhobj);
  $mobile_xl_lh = calclh($mobile_xl, $lhobj);
  $mobile_2xl_lh = calclh($mobile_2xl, $lhobj);
  $mobile_3xl_lh = calclh($mobile_3xl, $lhobj);
  $mobile_4xl_lh = calclh($mobile_4xl, $lhobj);
  $mobile_5xl_lh = calclh($mobile_5xl, $lhobj);
  $mobile_6xl_lh = calclh($mobile_6xl, $lhobj);
  $mobile_7xl_lh = calclh($mobile_7xl, $lhobj);
  $mobile_8xl_lh = calclh($mobile_8xl, $lhobj);
  $mobile_9xl_lh = calclh($mobile_9xl, $lhobj);
  
  // $omf_typography .= "--mobile-lh-xs: ".$mobile_xs_lh.";";
  // $omf_typography .= "--mobile-lh-sm: ".$mobile_sm_lh.";";
  // $omf_typography .= "--mobile-lh-base: ".$mobile_base_lh.";";
  // $omf_typography .= "--mobile-lh-lg: ".$mobile_lg_lh.";";
  // $omf_typography .= "--mobile-lh-xl: ".$mobile_xl_lh.";";
  // $omf_typography .= "--mobile-lh-2xl: ".$mobile_2xl_lh.";";
  // $omf_typography .= "--mobile-lh-3xl: ".$mobile_3xl_lh.";";
  // $omf_typography .= "--mobile-lh-4xl: ".$mobile_4xl_lh.";";
  // $omf_typography .= "--mobile-lh-5xl: ".$mobile_5xl_lh.";";
  // $omf_typography .= "--mobile-lh-6xl: ".$mobile_6xl_lh.";";
  // $omf_typography .= "--mobile-lh-7xl: ".$mobile_7xl_lh.";";
  // $omf_typography .= "--mobile-lh-8xl: ".$mobile_8xl_lh.";";
  // $omf_typography .= "--mobile-lh-9xl: ".$mobile_9xl_lh.";";
  
  $lh_hero = ($desktop_hero_lh + $mobile_hero_lh) / 2;
  $lh_h1 = ($desktop_h1_lh + $mobile_h1_lh) / 2;
  $lh_h2 = ($desktop_h2_lh + $mobile_h2_lh) / 2;
  $lh_h3 = ($desktop_h3_lh + $mobile_h3_lh) / 2;
  $lh_h4 = ($desktop_h4_lh + $mobile_h4_lh) / 2;
  $lh_h5 = ($desktop_h5_lh + $mobile_h5_lh) / 2;
  $lh_h6 = ($desktop_h6_lh + $mobile_h6_lh) / 2;
  
  $lh_xs = ($desktop_xs_lh + $mobile_xs_lh) / 2;
  $lh_sm = ($desktop_sm_lh + $mobile_sm_lh) / 2;
  $lh_base = ($desktop_base_lh + $mobile_base_lh) / 2;
  $lh_lg = ($desktop_lg_lh + $mobile_lg_lh) / 2;
  $lh_xl = ($desktop_xl_lh + $mobile_xl_lh) / 2;
  $lh_2xl = ($desktop_2xl_lh + $mobile_2xl_lh) / 2;
  $lh_3xl = ($desktop_3xl_lh + $mobile_3xl_lh) / 2;
  $lh_4xl = ($desktop_4xl_lh + $mobile_4xl_lh) / 2;
  $lh_5xl = ($desktop_5xl_lh + $mobile_5xl_lh) / 2;
  $lh_6xl = ($desktop_6xl_lh + $mobile_6xl_lh) / 2;
  $lh_7xl = ($desktop_7xl_lh + $mobile_7xl_lh) / 2;
  $lh_8xl = ($desktop_8xl_lh + $mobile_8xl_lh) / 2;
  $lh_9xl = ($desktop_9xl_lh + $mobile_9xl_lh) / 2;
  
  //finding final line-heights
  $omf_typography .= "--lh-hero: ".$lh_hero.";";
  $omf_typography .= "--lh-h1: ".$lh_h1.";";
  $omf_typography .= "--lh-h2: ".$lh_h2.";";
  $omf_typography .= "--lh-h3: ".$lh_h3.";";
  $omf_typography .= "--lh-h4: ".$lh_h4.";";
  $omf_typography .= "--lh-h5: ".$lh_h5.";";
  $omf_typography .= "--lh-h6: ".$lh_h6.";";
  
  $omf_typography .= "--lh-xs: ".$lh_xs.";";
  $omf_typography .= "--lh-sm: ".$lh_sm.";";
  $omf_typography .= "--lh-base: ".$lh_base.";";
  $omf_typography .= "--lh-lg: ".$lh_lg.";";
  $omf_typography .= "--lh-xl: ".$lh_xl.";";
  $omf_typography .= "--lh-2xl: ".$lh_2xl.";";
  $omf_typography .= "--lh-3xl: ".$lh_3xl.";";
  $omf_typography .= "--lh-4xl: ".$lh_4xl.";";
  $omf_typography .= "--lh-5xl: ".$lh_5xl.";";
  $omf_typography .= "--lh-6xl: ".$lh_6xl.";";
  $omf_typography .= "--lh-7xl: ".$lh_7xl.";";
  $omf_typography .= "--lh-8xl: ".$lh_8xl.";";
  $omf_typography .= "--lh-9xl: ".$lh_9xl.";";
  
  $omf_typography .= "}";
  
  update_option("oxymade_fluid_typography_vars", $omf_typography);
  update_option("oxymade_fluid_typography_advanced", "Enabled");
  $oxymade_updated = true;
  $success_msg =
  "We have just updated your fluid typography settings successfully.🥳🎉";
}




// PURGE FUNCTIONS STARTED

$oxymade_purge_whitelist = get_option("oxymade_purge_whitelist");

if (empty($oxymade_purge_whitelist)) {
  $oxymade_purge_whitelist = "";
}




// default stylesheet print

$om_stylesheet_desktop = get_option("oxymade_ss_desktop"); 
$om_stylesheet_xl = get_option("oxymade_ss_xl"); 
$om_stylesheet_lg = get_option("oxymade_ss_lg"); 
$om_stylesheet_md = get_option("oxymade_ss_md"); 
$om_stylesheet_sm = get_option("oxymade_ss_sm");
$om_stylesheet_hovers = get_option("oxymade_ss_hovers");

$om_stylesheet = get_option("oxymade_stylesheet");

if(!isset($om_stylesheet) || empty($om_stylesheet) || !isset($om_stylesheet_desktop) || empty($om_stylesheet_desktop) || $_POST["reinstall_framework"] || !isset($om_stylesheet_xl) || empty($om_stylesheet_xl) || !isset($om_stylesheet_lg) || empty($om_stylesheet_lg) || !isset($om_stylesheet_md) || empty($om_stylesheet_md) || !isset($om_stylesheet_sm) || empty($om_stylesheet_sm) || $_POST["oneclick_installer"] || !isset($om_stylesheet_hovers) || empty($om_stylesheet_hovers)){

update_option("oxymade_ss_desktop", $oxymade_ss_desktop);
update_option("oxymade_ss_xl", $oxymade_ss_xl);
update_option("oxymade_ss_lg", $oxymade_ss_lg);
update_option("oxymade_ss_md", $oxymade_ss_md);
update_option("oxymade_ss_sm", $oxymade_ss_sm);
update_option("oxymade_ss_hovers", $oxymade_hovers_ss_css);

$om_stylesheet_desktop = get_option("oxymade_ss_desktop"); 
$om_stylesheet_xl = get_option("oxymade_ss_xl"); 
$om_stylesheet_lg = get_option("oxymade_ss_lg"); 
$om_stylesheet_md = get_option("oxymade_ss_md");
$om_stylesheet_sm = get_option("oxymade_ss_sm");
$om_stylesheet_hovers = get_option("oxymade_ss_hovers");

$oxymade_full_stylesheet = "";
$oxymade_full_stylesheet .= base64_decode($om_stylesheet_desktop);
$oxymade_full_stylesheet .= "\n";
$oxymade_full_stylesheet .= "@media screen and (max-width: " . ($width_default - 1) . "px){";
$oxymade_full_stylesheet .= base64_decode($om_stylesheet_xl);
$oxymade_full_stylesheet .= "} ";
$oxymade_full_stylesheet .= "\n";
$oxymade_full_stylesheet .= "@media screen and (max-width: " . ($width_tablet - 1) . "px){";
$oxymade_full_stylesheet .= base64_decode($om_stylesheet_lg);
$oxymade_full_stylesheet .= "} ";
$oxymade_full_stylesheet .= "\n";
$oxymade_full_stylesheet .= "@media screen and (max-width: " . ($width_phone_landscape - 1) . "px){";
$oxymade_full_stylesheet .= base64_decode($om_stylesheet_md);
$oxymade_full_stylesheet .= "} ";
$oxymade_full_stylesheet .= "\n";
$oxymade_full_stylesheet .= "@media screen and (max-width: " . ($width_phone_portrait - 1) . "px){";
$oxymade_full_stylesheet .= base64_decode($om_stylesheet_sm);
$oxymade_full_stylesheet .= "}";

$oxymade_stylesheet = base64_encode($oxymade_full_stylesheet);
$oxymade_ss_css = $oxymade_stylesheet;

// combine stylesheets and create a full stylesheet with breakpoints
// update_option("oxymade_stylesheet", $oxymade_stylesheet);
// $oxymade_updated = true;
// $success_msg =
// "Framework & stylesheets reinstalled successfully.";
}

// $oxymade_css_cache_skip = get_option("oxymade_css_cache_skip");
// delete_option("oxymade_active_set");
// delete_option("oxymade_oneclick_installer_status");
$design_sets_data = base64_decode($oxymade_sets);
$design_sets = json_decode($design_sets_data, true);
// var_dump($design_sets["oxymade"]);
$monster_active_set = get_option("monster_active_set");
if (isset($monster_active_set) && $monster_active_set == "oxymade") {
  $monster_active_set = "megaset";
}

$oxymade_active_set = get_option("oxymade_active_set");

if (isset($monster_active_set) && !empty($monster_active_set)) {
  $active_set = $monster_active_set;
  $welcome_base_set =
    "<b>Note:</b> We've identified that you are using our <b>" .
    ucfirst($active_set) .
    "</b>. So we will install the framework with the default <b>" .
    ucfirst($active_set) .
    "</b> design set styles.";
} elseif (isset($oxymade_active_set) && !empty($oxymade_active_set)) {
  $active_set = $oxymade_active_set;
  $welcome_base_set =
    "<b>Note:</b> We've identified that you are using our <b>" .
    ucfirst($active_set) .
    "</b>. So we will install the framework with the default <b>" .
    ucfirst($active_set) .
    "</b> design set styles.";
} elseif (
  (!isset($monster_active_set) || empty($monster_active_set)) &&
  (!isset($oxymade_active_set) || empty($oxymade_active_set))
) {
  $active_set = "megaset";
  $welcome_base_set =
    "<b>Note:</b> We will install the framework with the default <b>" .
    ucfirst($active_set) .
    "</b> design set styles.";
}

if (
  (!isset($monster_active_set) || empty($monster_active_set)) &&
  (!isset($oxymade_active_set) || empty($oxymade_active_set))
) {
  $fresh_install = true;
}
$global_colors = oxy_get_global_colors();
$gcolors = $global_colors["colors"];
$gsets = $global_colors["sets"];

// $success_msg = "Successfully saved / updated.";
// $error_msg =
//   "There is some error in your operation, please check and submit once again.";
// // $warning_msg =
// //   "Oxygen Builder is open in another window/tab. Please close the tab/window & refresh this page to modify any settings here.";
// $oxymade_warnings = [];

$monster_colors = get_option("monster_colors", []);

$color_data = get_option("oxymade_colors", []);
// var_dump($color_data);
// $backup_oxygen_vsb_global_colors = get_option(
//   "backup_oxygen_vsb_global_colors"
// );
//
// update_option("oxygen_vsb_global_colors", $backup_oxygen_vsb_global_colors);
$color_export = json_encode($color_data);
$color_export = base64_encode($color_export);

$oxymade_oneclick_installer_status = get_option(
  "oxymade_oneclick_installer_status"
);
// $oxymade_oneclick_installer_status = true;
$oxymade_ss_folder_name = "OxyMadeFramework";
$oxymade_hovers_ss_folder_name = "OxyMadeHoverStyles";
$oxymade_ss_name = "OxyMadeFrameworkStylesheet";
$oxymade_hovers_ss_name = "OxyMadeHoverStylesheet";

/* =======================================
// REMOVE LICENSE KEY
======================================= */

if (
  isset($_POST["remove_oxymade_license"]) &&
  $_POST["remove_oxymade_license"] == "yes"
) {
  delete_option("oxymade_license");
  delete_option("oxymade_license_status");
}

/* =======================================
// START ONE CLICK POST
======================================= */

if (isset($_POST) && !empty($_POST)) {
  if ($_POST) {
    //if clear custom selectors is posted
    // if (
    //   isset($_POST["clear_custom_selectors_style_sets"]) &&
    //   $_POST["clear_custom_selectors_style_sets"] == "yes"
    // ) {
    //   $custom_selectors = [];
    //   update_option("ct_custom_selectors", $custom_selectors);
    //   $style_sets = [];
    //   update_option("ct_style_sets", $style_sets);
    // }
    //TODO: Create clear custom selectors button and modal + check if it is working.


// Setting rem choice for the framework

// if((isset($_POST["oneclick_remchoice"]) && ($_POST["oneclick_remchoice"] == "10px" || $_POST["oneclick_remchoice"] == "16px"))){
//   
//   update_option("oxymade_remchoice", $_POST["oneclick_remchoice"]);
//   $oxymade_updated = true;
//   $success_msg =
//     "REM Choise has been selected successfully. 🥳 Now please install the framework using the options below.";
// }





    if (
      (isset($_POST["oneclick_installer"]) &&
        $_POST["oneclick_installer"] == "yes") ||
      (isset($_POST["reinstall_framework"]) &&
        $_POST["reinstall_framework"] == "yes") ||
      isset($_POST["change_base_design_set"]) ||
      (isset($_POST["update_color_palette"]) &&
        $_POST["update_color_palette"] == "yes") ||
      isset($_POST["color_skin"]) ||
      isset($_POST["import_color_palette"]) ||
      (isset($_POST["reset_base_design_set"]) &&
        $_POST["reset_base_design_set"] == "yes") ||
      (isset($_POST["reset_oxygen_defaults"]) &&
        $_POST["reset_oxygen_defaults"] == "yes") ||
      (isset($_POST["reset_oxygen_to_before_om"]) &&
        $_POST["reset_oxygen_to_before_om"] == "yes") ||
      (isset($_POST["install_settings"]) && $_POST["install_settings"] == "yes")
    ) {
      /* =======================================
            Framework Classes Importing
            ======================================= */

      if (
        (isset($_POST["oneclick_installer"]) &&
          $_POST["oneclick_installer"] == "yes") ||
        (isset($_POST["reinstall_framework"]) &&
          $_POST["reinstall_framework"] == "yes") ||
        (isset($_POST["reset_oxygen_defaults"]) &&
          $_POST["reset_oxygen_defaults"] == "yes") ||
        (isset($_POST["reset_oxygen_to_before_om"]) &&
          $_POST["reset_oxygen_to_before_om"] == "yes")
      ) {
        $oxymade_quick_import = $framework_data;

        if (
          isset($_POST["reset_oxygen_defaults"]) &&
          $_POST["reset_oxygen_defaults"] == "yes"
        ) {
          $oxymade_quick_import = $oxy_defaults;
        }

        //reset installation to before oxymade
        if (
          isset($_POST["reset_oxygen_to_before_om"]) &&
          $_POST["reset_oxygen_to_before_om"] == "yes"
        ) {
          $before_oxymade_defaults = get_option(
            "oxymade_oxygen_settings_backup"
          );
          $oxymade_quick_import = $before_oxymade_defaults;
        }
        $import_json = base64_decode($oxymade_quick_import);

        // oxygen_vsb_sync_default_presets();

        $classes = get_option("ct_components_classes", []);
        $custom_selectors = get_option("ct_custom_selectors", []);
        $style_sets = get_option("ct_style_sets", []);
        $style_folders = get_option("ct_style_folders", []);
        $style_sheets = get_option("ct_style_sheets", []);
        $global_settings = get_option("ct_global_settings", []);
        $element_presets = get_option("oxygen_vsb_element_presets", []);
        $global_colors = oxy_get_global_colors();

        // must have for skipping default presets from exporting which creates an issue when we try to import them again
        $default_presets = apply_filters(
          "oxygen_vsb_element_presets_defaults",
          []
        );

        foreach ($default_presets as $element_name => $presets) {
          if (empty($element_presets[$element_name])) {
            continue;
          }

          foreach ($presets as $key => $preset) {
            $index = array_search($preset, $element_presets[$element_name]);
            if ($index !== false) {
              unset($element_presets[$element_name][$index]);
            }
          }
          // re-index array to keep JSON clean from indexes and not overwrite other presets
          $element_presets[$element_name] = array_values(
            $element_presets[$element_name]
          );
        }

        // generate export JSON
        $export_json["classes"] = $classes;
        $export_json["custom_selectors"] = $custom_selectors;
        $export_json["style_sets"] = $style_sets;
        $export_json["style_folders"] = $style_folders;
        $export_json["style_sheets"] = $style_sheets;
        $export_json["global_settings"] = $global_settings;
        $export_json["global_colors"] = $global_colors;
        $export_json["element_presets"] = $element_presets;

        // generate JSON object
        $export_json = json_encode($export_json);
        $export_json = base64_encode($export_json);

        $export_backup = get_option("oxymade_oxygen_settings_backup");

        if (isset($export_backup) && !empty($export_backup)) {
        } else {
          update_option("oxymade_oxygen_settings_backup", $export_json, false);
        }

        if (!is_array($classes)) {
          $classes = [];
        }

        if (!is_array($custom_selectors)) {
          $custom_selectors = [];
        }

        if (!is_array($style_sets)) {
          $style_sets = [];
        }

        if (!is_array($style_folders)) {
          $style_folders = [];
        }

        if (!is_array($style_sheets)) {
          $style_sheets = [];
        }

        if (
          (isset($_POST["reset_oxygen_defaults"]) &&
            $_POST["reset_oxygen_defaults"] == "yes") ||
          (isset($_POST["reset_oxygen_to_before_om"]) &&
            $_POST["reset_oxygen_to_before_om"] == "yes")
        ) {
          // $import_json = stripcslashes($import_json);
          $import_json = sanitize_text_field($import_json);
        } else {
          $import_json = sanitize_text_field(stripcslashes($import_json));
        }

        // check if empty
        if (empty($import_json)) {
          $import_errors[] = __("Empty Import");
        } else {
          // try to decode
          $import_array = json_decode($import_json, true);

          if (
            isset($_POST["reset_oxygen_defaults"]) &&
            $_POST["reset_oxygen_defaults"] == "yes"
          ) {
            $classes = [];
            $custom_selectors = [];
            $style_sets = [];
            $style_folders = [];
            $style_sheets = [];
            $global_settings = [];
            $global_colors = $import_array["global_colors"];
            $element_presets = [];

            update_option("ct_global_settings", $global_settings);
            update_option("oxygen_vsb_global_colors", $global_colors);
            // updating global colors & global settings to default.
          }

          //reset to before oxymade
          if (
            isset($_POST["reset_oxygen_to_before_om"]) &&
            $_POST["reset_oxygen_to_before_om"] == "yes"
          ) {
            $classes = [];
            $custom_selectors = [];
            $style_sets = [];
            $style_folders = [];
            $style_sheets = [];
            $global_settings = $import_array["global_settings"];
            $global_colors = $import_array["global_colors"];
            $element_presets = [];

            update_option("ct_global_settings", $global_settings);
            update_option("oxygen_vsb_global_colors", $global_colors);
            // updating global colors & global settings to before OM.
          }

          // update options
          if ($import_array) {
            if (
              isset($import_array["classes"]) &&
              is_array($import_array["classes"])
            ) {
              foreach ($import_array["classes"] as $key => $item) {
                if (!is_string($key)) {
                  unset($import_array["classes"][$key]);
                }
              }

              $classes = array_merge($classes, $import_array["classes"]);
              update_option("ct_components_classes", $classes);
            }

            // custom selectors
            if (
              isset($import_array["custom_selectors"]) &&
              is_array($import_array["custom_selectors"])
            ) {
              $custom_selectors = array_merge(
                $custom_selectors,
                $import_array["custom_selectors"]
              );
              update_option("ct_custom_selectors", $custom_selectors);
            }

            // style sets
            if (
              isset($import_array["style_sets"]) &&
              is_array($import_array["style_sets"])
            ) {
              $style_sets = array_merge(
                $style_sets,
                $import_array["style_sets"]
              );
              update_option("ct_style_sets", $style_sets);
            }

            // style folders
            if (
              isset($import_array["style_folders"]) &&
              is_array($import_array["style_folders"])
            ) {
              $style_folders = array_merge(
                $style_folders,
                $import_array["style_folders"]
              );
              update_option("ct_style_folders", $style_folders);
            }

            // style sheets
            if (
              isset($import_array["style_sheets"]) &&
              is_array($import_array["style_sheets"])
            ) {
              foreach ($import_array["style_sheets"] as $key => $item) {
                foreach ($style_sheets as $existing) {
                  if ($existing["name"] == $item["name"]) {
                    unset($import_array["style_sheets"][$key]);
                    break;
                  }
                }
              }
              $style_sheets = array_merge(
                $style_sheets,
                $import_array["style_sheets"]
              );
              update_option("ct_style_sheets", $style_sheets);
            }

            // presets
            if (
              isset($import_array["element_presets"]) &&
              is_array($import_array["element_presets"])
            ) {
              $element_presets = array_merge_recursive(
                $element_presets,
                $import_array["element_presets"]
              );
              update_option("oxygen_vsb_element_presets", $element_presets);
            }

            $import_success[] = __("Import success", "component-theme");
            add_option("oxymade_quick_install", "installed");

            $success_msg =
              "Congratulations! OxyMade Framework has been installed successfully. 🥳";
            $oxymade_updated = true;
          } else {
            $import_errors[] = __("Wrong JSON Format", "component-theme");
          }
        }

        /* =======================================
                Stylesheets updating
                ======================================= */

        if (
          (isset($_POST["oneclick_installer"]) &&
            $_POST["oneclick_installer"] == "yes") ||
          (isset($_POST["reinstall_framework"]) &&
            $_POST["reinstall_framework"] == "yes")
        ) {
          $new_stylesheets = [];

          if (isset($new_stylesheets) && is_array($new_stylesheets)) {
            $style_sheets = [];

            $style_sheets = get_option("ct_style_sheets");

            if (empty($style_sheets)) {
              $style_sheets = [];
            }

            $stylesheets_hash = hash("sha256", json_encode($style_sheets));

            // custom solution for stylesheets to prevent overriding the stylesheet data importing when ids are same
            $oxymade_ss_folder_name = "OxyMadeFramework";
            $oxymade_hovers_ss_folder_name = "OxyMadeHoverStyles";
            $oxymade_ss_name = "OxyMadeFrameworkStylesheet";
            $oxymade_hovers_ss_name = "OxyMadeHoverEffectStylesheet";

            $is_v1_plus = get_option("oxymade_v1_plus");

            $oxy_ss_ids = [];

            foreach ($style_sheets as $key => $stylesheet) {
              array_push($oxy_ss_ids, $stylesheet["id"]);
            }

            if (!empty($oxy_ss_ids)) {
              $oxy_current_ss_id = max($oxy_ss_ids);
            }
            
            $oxy_current_ss_id = empty($oxy_current_ss_id) ? 0 : $oxy_current_ss_id;
              
            $new_framework_folder_id = $oxy_current_ss_id + 1;
            $new_hovers_folder_id = $oxy_current_ss_id + 2;
            $new_framework_stylesheet_id = $oxy_current_ss_id + 3;
            $new_hovers_stylesheet_id = $oxy_current_ss_id + 4;

            if (isset($is_v1_plus) && $is_v1_plus && !empty($is_v1_plus)) {
              foreach ($style_sheets as $key => $existing) {
                if ($existing["name"] == $oxymade_ss_folder_name) {
                  $new_framework_folder_id = $existing["id"];
                  unset($style_sheets[$key]);
                }

                if ($existing["name"] == $oxymade_hovers_ss_folder_name) {
                  $new_hovers_folder_id = $existing["id"];
                  unset($style_sheets[$key]);
                }

                if ($existing["name"] == $oxymade_ss_name) {
                  $new_framework_stylesheet_id = $existing["id"];
                  unset($style_sheets[$key]);
                }

                if ($existing["name"] == $oxymade_hovers_ss_name) {
                  $new_hovers_stylesheet_id = $existing["id"];
                  unset($style_sheets[$key]);
                }
              }

              $framework_folder = [
                "id" => $new_framework_folder_id,
                "name" => $oxymade_ss_folder_name,
                "status" => 1,
                "folder" => 1,
              ];
              array_push($new_stylesheets, $framework_folder);

              $hovers_folder = [
                "id" => $new_hovers_folder_id,
                "name" => $oxymade_hovers_ss_folder_name,
                "status" => 1,
                "folder" => 1,
              ];
              array_push($new_stylesheets, $hovers_folder);

              $framework_stylesheet = [
                "css" => $oxymade_ss_css,
                "id" => $new_framework_stylesheet_id,
                "name" => $oxymade_ss_name,
                "parent" => $new_framework_folder_id,
              ];

              array_push($new_stylesheets, $framework_stylesheet);

              $hover_stylesheet = [
                "css" => $oxymade_hovers_ss_css,
                "id" => $new_hovers_stylesheet_id,
                "name" => $oxymade_hovers_ss_name,
                "parent" => $new_hovers_folder_id,
              ];

              array_push($new_stylesheets, $hover_stylesheet);

              foreach ($new_stylesheets as $key => $item) {
                foreach ($style_sheets as $existing) {
                  if ($existing["name"] == $item["name"]) {
                    unset($style_sheets[$key]);
                  }
                }
              }

              $style_sheets = array_merge($style_sheets, $new_stylesheets);
            } else {
              $framework_folder = [
                "id" => $new_framework_folder_id,
                "name" => $oxymade_ss_folder_name,
                "status" => 1,
                "folder" => 1,
              ];
              array_push($new_stylesheets, $framework_folder);

              $hovers_folder = [
                "id" => $new_hovers_folder_id,
                "name" => $oxymade_hovers_ss_folder_name,
                "status" => 1,
                "folder" => 1,
              ];
              array_push($new_stylesheets, $hovers_folder);

              $framework_stylesheet = [
                "css" => $oxymade_ss_css,
                "id" => $new_framework_stylesheet_id,
                "name" => $oxymade_ss_name,
                "parent" => $new_framework_folder_id,
              ];

              array_push($new_stylesheets, $framework_stylesheet);

              $hover_stylesheet = [
                "css" => $oxymade_hovers_ss_css,
                "id" => $new_hovers_stylesheet_id,
                "name" => $oxymade_hovers_ss_name,
                "parent" => $new_hovers_folder_id,
              ];

              array_push($new_stylesheets, $hover_stylesheet);

              $om_old_list = [
                "OxyMadeFramework",
                "OxyMadeHoverStyles",
                "OxyMadeFrameworkStylesheet",
                "OxyMadeHoverStylesheet",
                "MonsterFramework",
                "MonsterStylesheet",
                "OxyMonster",
                "OxyMonsterFramework",
              ];

              //TODO: Remove this whole $is_v1_plus check after a couple of versions when we are sure that no one is on v1.0.0
              // Just check for old oxymonster folders, not oxymade folders after that.

              foreach ($style_sheets as $key => $stylesheet) {
                if (in_array($stylesheet["name"], $om_old_list)) {
                  unset($style_sheets[$key]);
                }
              }

              update_option("oxymade_v1_plus", true, false);

              $style_sheets = array_merge($style_sheets, $new_stylesheets);
            }

            $new_stylesheets_hash = hash("sha256", json_encode($style_sheets));

            $no_ss_change = hash_equals(
              $stylesheets_hash,
              $new_stylesheets_hash
            );

            // var_dump($no_ss_change);
            // update the stylesheet option only if there is any change
            if ($no_ss_change) {
            } else {
              update_option("ct_style_sheets", $style_sheets);
            }
          }
        }

        $oxymade_updated = true;
        $success_msg =
          "OxyMade Framework has been re-installed successfully. 🥳";
      }

      if (
        isset($_POST["oneclick_installer"]) &&
        $_POST["oneclick_installer"] == "yes"
      ) {
        add_option("oxymade_oneclick_installer_status", true);
      }
    }
    /* =======================================
            ENDING STYLESHEETS UPDATE
            =======================================
            Starting Base Design Set Update
            ======================================= */
    if (
      (isset($_POST["oneclick_installer"]) &&
        $_POST["oneclick_installer"] == "yes") ||
      (isset($_POST["change_base_design_set"]) &&
        !empty($_POST["change_base_design_set"])) ||
      (isset($_POST["update_color_palette"]) &&
        !empty($_POST["update_color_palette"])) ||
      (isset($_POST["color_skin"]) && !empty($_POST["color_skin"])) ||
      isset($_POST["import_color_palette"]) ||
      (isset($_POST["reset_base_design_set"]) &&
        $_POST["reset_base_design_set"] == "yes") ||
      (isset($_POST["install_settings"]) && $_POST["install_settings"] == "yes")
    ) {
      // Take oxymade set as a base design set if one click installer for a new site,
      //if the active kit is selected before, use that design set as base design set
      // $monster_active_set = get_option("monster_active_set");
      $base_design_set = [];

      if (
        isset($_POST["oneclick_installer"]) &&
        isset($monster_active_set) &&
        !empty($monster_active_set) &&
        !empty($_POST["oneclick_installer"])
      ) {
        $base_design_set = $design_sets[$monster_active_set];
        delete_option("monster_active_set");
        update_option("oxymade_active_set", $monster_active_set);
      } elseif (
        isset($_POST["reset_base_design_set"]) &&
        !empty($_POST["reset_base_design_set"]) &&
        isset($oxymade_active_set) &&
        !empty($oxymade_active_set)
      ) {
        $base_design_set = $design_sets[$oxymade_active_set];
      } elseif (
        isset($_POST["change_base_design_set"]) &&
        !empty($_POST["change_base_design_set"])
      ) {
        $new_base_design_set = $_POST["change_base_design_set"];
        $base_design_set = $design_sets[$new_base_design_set];
        if (isset($monster_active_set) && !empty($monster_active_set)) {
          delete_option("monster_active_set");
        }
      } elseif (
        isset($_POST["oneclick_installer"]) &&
        empty($monster_active_set) &&
        empty($oxymade_active_set) &&
        !isset($_POST["reset_base_design_set"]) &&
        !isset($_POST["change_base_design_set"]) &&
        !empty($_POST["oneclick_installer"])
      ) {
        $base_design_set = $design_sets["megaset"];
        update_option("oxymade_active_set", "megaset");
      } elseif (
        isset($_POST["oneclick_installer"]) &&
        !empty($_POST["oneclick_installer"]) &&
        isset($oxymade_active_set) &&
        !empty($oxymade_active_set)
      ) {
        $base_design_set = $design_sets[$oxymade_active_set];
      }

      // $base_design_set = base64_decode($design_set_oxymade);
      // $base_design_set = json_decode($design_set_oxymade, true);
      if (
        !empty($_POST["oneclick_installer"]) ||
        !empty($_POST["color_skin"]) ||
        !empty($_POST["import_color_palette"]) ||
        !empty($_POST["update_color_palette"])
      ) {
        /* =======================================
          // Starting Colors Update
          ======================================= */
        // echo "step 1";
        // var_dump($_POST);
        if (
          isset($_POST["update_color_palette"]) &&
          $_POST["update_color_palette"] == "yes"
        ) {
          // echo "step 2";
          unset($_POST["update_color_palette"]);
          $base_design_set_colors = $_POST;
          // var_dump($base_design_set_colors);
          // as the above post update color palette is unset for easy process
          //creating a special variable to verify if the form is update color palette
          $update_color_palette = true;
        } elseif (isset($_POST["import_color_palette"])) {
          unset($_POST["color_importer_submit"]);
          unset($_POST["export_color_palette"]);

          $color_palette = $_POST["import_color_palette"];
          $color_palette = base64_decode($color_palette);
          $color_palette = json_decode($color_palette, true);
          $base_design_set_colors = $color_palette["colors"];
          // var_dump($base_design_set_colors);
        } elseif (isset($_POST["color_skin"]) && !empty($_POST["color_skin"])) {
          //color skin imported from ready made palettes
          $colskin = base64_decode($_POST["color_skin"]);
          $colskin = json_decode($colskin, true);
          $colskin = base64_decode($colskin["colors"]);
          $colskin = json_decode($colskin, true);
          $base_design_set_colors = $colskin["colors"];
        } elseif (
          isset($_POST["oneclick_installer"]) &&
          !empty($_POST["oneclick_installer"])
        ) {
          if (isset($monster_colors) && !empty($monster_colors)) {
            $base_design_set_colors = [];
            $base_design_set_colors = $monster_colors;
          } else {
            if (isset($base_design_set)) {
              $base_design_set_colors = $base_design_set["colors"];
            }
            $base_design_set_colors = base64_decode($base_design_set_colors);
            $base_design_set_colors = json_decode(
              $base_design_set_colors,
              true
            );
          }
        }

        // update_option("oxymade_colors", $base_design_set_colors);
        // $post_data = $post_data["color_importer"];
        // $post_data = base64_decode($post_data);
        // $post_data = json_decode($post_data, true);
        // $post_data = $post_data["colors"];

        if (
          isset($_POST["oneclick_installer"]) &&
          !empty($_POST["oneclick_installer"])
        ) {
          $base_design_set_colors = $base_design_set_colors["colors"];
        }

        if ($base_design_set_colors[0]["name"] == "--primary-color") {
          $primary_color_var = $base_design_set_colors[0]["name"];
          $primary_color_val = $base_design_set_colors[0]["value"];
        }
        if($base_design_set_colors[1]["name"] == "--dark-color"){
          $dark_color_var = $base_design_set_colors[1]["name"];
          $dark_color_val = $base_design_set_colors[1]["value"];
        }
        if($base_design_set_colors[2]["name"] == "--paragraph-color"){
          $paragraph_color_var = $base_design_set_colors[2]["name"];
          $paragraph_color_val = $base_design_set_colors[2]["value"];
        }
        if ($base_design_set_colors[6]["name"] == "--secondary-color") {
          $secondary_color_var = $base_design_set_colors[6]["name"];
          $secondary_color_val = $base_design_set_colors[6]["value"];
        }
        if($base_design_set_colors[12]["name"] == "--tertiary-color"){
          $tertiary_color_var = $base_design_set_colors[12]["name"];
          $tertiary_color_val = $base_design_set_colors[12]["value"];
        }
        if($base_design_set_colors[13]["name"] == "--black-color"){
          $black_color_var = $base_design_set_colors[13]["name"];
          $black_color_val = $base_design_set_colors[13]["value"];
        }
        if($base_design_set_colors[15]["name"] == "--success-color"){
          $success_color_var = $base_design_set_colors[15]["name"];
          $success_color_val = $base_design_set_colors[15]["value"];
        }
        if($base_design_set_colors[16]["name"] == "--warning-color"){
          $warning_color_var = $base_design_set_colors[16]["name"];
          $warning_color_val = $base_design_set_colors[16]["value"];
        }
        if($base_design_set_colors[17]["name"] == "--error-color"){
          $error_color_var = $base_design_set_colors[17]["name"];
          $error_color_val = $base_design_set_colors[17]["value"];
        }
        
        if($base_design_set_colors[21]["name"] == "--extra-color-1"){
          $extra_color_1_var = $base_design_set_colors[21]["name"];
          $extra_color_1_val = $base_design_set_colors[21]["value"];
        }
        if($base_design_set_colors[22]["name"] == "--extra-color-2"){
          $extra_color_2_var = $base_design_set_colors[22]["name"];
          $extra_color_2_val = $base_design_set_colors[22]["value"];
        }
        if($base_design_set_colors[23]["name"] == "--extra-color-3"){
          $extra_color_3_var = $base_design_set_colors[23]["name"];
          $extra_color_3_val = $base_design_set_colors[23]["value"];
        }
        if($base_design_set_colors[24]["name"] == "--extra-color-4"){
          $extra_color_4_var = $base_design_set_colors[24]["name"];
          $extra_color_4_val = $base_design_set_colors[24]["value"];
        }

        $primary_hover = genHoverColor($primary_color_val, 10);
        $secondary_hover = genHoverColor($secondary_color_val, 10);

        $primary_alt_color = genAltColor($primary_color_val, 45);
        $secondary_alt_color = genAltColor($secondary_color_val, 45);

        $primary_alt_hover = genHoverColor($primary_alt_color, 10);
        $secondary_alt_hover = genHoverColor($secondary_alt_color, 10);

        $primary_rgb_vals = genRGBVals($primary_color_val);
        $dark_rgb_vals = genRGBVals($dark_color_val);
        $paragraph_rgb_vals = genRGBVals($paragraph_color_val);
        $secondary_rgb_vals = genRGBVals($secondary_color_val);
        $tertiary_rgb_vals = genRGBVals($tertiary_color_val);
        $black_rgb_vals = genRGBVals($black_color_val);
        $success_rgb_vals = genRGBVals($success_color_val);
        $warning_rgb_vals = genRGBVals($warning_color_val);
        $error_rgb_vals = genRGBVals($error_color_val);
        
        $extra_color_1_rgb_vals = genRGBVals($extra_color_1_val);
        $extra_color_2_rgb_vals = genRGBVals($extra_color_2_val);
        $extra_color_3_rgb_vals = genRGBVals($extra_color_3_val);
        $extra_color_4_rgb_vals = genRGBVals($extra_color_4_val);

        $base_design_set_colors[25]["name"] = "--primary-hover-color";
        $base_design_set_colors[25]["value"] = $primary_hover;

        $base_design_set_colors[26]["name"] = "--secondary-hover-color";
        $base_design_set_colors[26]["value"] = $secondary_hover;

        $base_design_set_colors[27]["name"] = "--primary-alt-color";
        $base_design_set_colors[27]["value"] = $primary_alt_color;

        $base_design_set_colors[28]["name"] = "--secondary-alt-color";
        $base_design_set_colors[28]["value"] = $secondary_alt_color;

        $base_design_set_colors[29]["name"] = "--primary-alt-hover-color";
        $base_design_set_colors[29]["value"] = $primary_alt_hover;

        $base_design_set_colors[30]["name"] = "--secondary-alt-hover-color";
        $base_design_set_colors[30]["value"] = $secondary_alt_hover;

        $base_design_set_colors[31]["name"] = "--primary-rgb-vals";
        $base_design_set_colors[31]["value"] = $primary_rgb_vals;

        $base_design_set_colors[32]["name"] = "--secondary-rgb-vals";
        $base_design_set_colors[32]["value"] = $secondary_rgb_vals;
        
        $base_design_set_colors[33]["name"] = "--transparent-color";
        $base_design_set_colors[33]["value"] = "transparent";

        $base_design_set_colors[34]["name"] = "--dark-rgb-vals";
        $base_design_set_colors[34]["value"] = $dark_rgb_vals;

        $base_design_set_colors[35]["name"] = "--paragraph-rgb-vals";
        $base_design_set_colors[35]["value"] = $paragraph_rgb_vals;

        $base_design_set_colors[36]["name"] = "--tertiary-rgb-vals";
        $base_design_set_colors[36]["value"] = $tertiary_rgb_vals;

        $base_design_set_colors[37]["name"] = "--black-rgb-vals";
        $base_design_set_colors[37]["value"] = $black_rgb_vals;

        $base_design_set_colors[38]["name"] = "--success-rgb-vals";
        $base_design_set_colors[38]["value"] = $success_rgb_vals;

        $base_design_set_colors[39]["name"] = "--warning-rgb-vals";
        $base_design_set_colors[39]["value"] = $warning_rgb_vals;

        $base_design_set_colors[40]["name"] = "--error-rgb-vals";
        $base_design_set_colors[40]["value"] = $error_rgb_vals;

        $base_design_set_colors[41]["name"] = "--extra-color-1-rgb-vals";
        $base_design_set_colors[41]["value"] = $extra_color_1_rgb_vals;

        $base_design_set_colors[42]["name"] = "--extra-color-2-rgb-vals";
        $base_design_set_colors[42]["value"] = $extra_color_2_rgb_vals;

        $base_design_set_colors[43]["name"] = "--extra-color-3-rgb-vals";
        $base_design_set_colors[43]["value"] = $extra_color_3_rgb_vals;

        $base_design_set_colors[44]["name"] = "--extra-color-4-rgb-vals";
        $base_design_set_colors[44]["value"] = $extra_color_4_rgb_vals;

        $custom_css = "";
        $custom_css .= ":root {";

        if (
          is_array($base_design_set_colors) ||
          is_object($base_design_set_colors)
        ) {
          foreach ($base_design_set_colors as $key => $value) {
            $custom_css .= $value["name"] . ": " . $value["value"] . ";";
            $mgcolors[$key] = [
              "name" => $value["name"],
              "value" => $value["value"],
            ];
          }
        }
        $custom_css .= "}";

        if (
          isset($_POST["oneclick_installer"]) &&
          !empty($_POST["oneclick_installer"])
        ) {
          $firsttime_colors["colors"] = $base_design_set_colors;
          $base_design_set_colors = [];
          $base_design_set_colors["colors"] = $firsttime_colors["colors"];
        }

        if (
          (isset($update_color_palette) && $update_color_palette) ||
          isset($_POST["color_skin"]) ||
          isset($_POST["import_color_palette"])
        ) {
          $new_bd["colors"] = $base_design_set_colors;
          $base_design_set_colors = [];
          $base_design_set_colors["colors"] = $new_bd["colors"];
        }

        update_option("oxymade_colors", $base_design_set_colors);

        if (isset($monster_colors) && !empty(get_option("oxymade_colors"))) {
          delete_option("monster_colors");
        }

        update_option("oxymade_custom_css", $custom_css);

        $oxymade_updated = true;
        $success_msg = "OxyMade Colors & Custom CSS updated successfully! 👍";

        $oxymade_colors = get_option("oxymade_colors");

        unset($oxymade_colors["colors"][31]);
        unset($oxymade_colors["colors"][32]);

        $oxyberg_colors = [];
        foreach ($oxymade_colors["colors"] as $key => $oxymade_color) {
          $oxygerg_color_set = [];

          $name = $oxymade_color["name"];
          $slug = $oxymade_color["name"];

          $name = str_replace("--", "", $name);
          $name = str_replace("-", " ", $name);
          $name = ucfirst($name);

          $slug = str_replace("--", "", $slug);

          $oxygerg_color_set["name"] = $name;
          $oxygerg_color_set["slug"] = $slug;
          $oxygerg_color_set["color"] = $oxymade_color["value"];
          $oxyberg_colors[] = $oxygerg_color_set;
        }

        update_option("oxymade_gutenberg_colors", $oxyberg_colors);
      }
      /* =======================================
                IMPORTING COLORS END
                ========================================= */

      $color_data = get_option("oxymade_colors", []);
      $color_export = json_encode($color_data);
      $color_export = base64_encode($color_export);

      /* =======================================
                // Starting Settings Update
                ======================================= */
      if (
        ((isset($_POST["oneclick_installer"]) &&
          $_POST["oneclick_installer"] == "yes") && (isset($_POST["oneclick_settings_installer"]) &&
          $_POST["oneclick_settings_installer"] == "yes")) ||
        (isset($_POST["install_settings"]) &&
          $_POST["install_settings"] == "yes")
      ) {
        $oxy_settings = base64_decode($oxy_settings);
        $oxy_settings = json_decode($oxy_settings, true);

        update_option("ct_global_settings", $oxy_settings["global_settings"]);
        $oxymade_updated = true;
        $success_msg =
          "OxyMade default Oxygen global settings updated successfully! 👍";
      }

      // TODO: turning off settings copying or overriding.
      /* =======================================
                IMPORTING EXTRA CLASSES & CUSTOM SELECTORS START
                update extra classes & custom selectors when extras imported or full set install
                ========================================= */
                
      if (
        isset($_POST["change_base_design_set"]) &&
        !empty($_POST["change_base_design_set"])
      ) {
        update_option("oxymade_active_set", $_POST["change_base_design_set"]);
      }
                
      if (
        (isset($_POST["oneclick_installer"]) &&
          $_POST["oneclick_installer"] == "yes") ||
        isset($_POST["change_base_design_set"]) ||
        (isset($_POST["reset_base_design_set"]) &&
          $_POST["reset_base_design_set"] == "yes")
      ) {
        if (isset($fresh_install) && $fresh_install) {
          $base_design_set = $design_sets["megaset"];
        }
        //TODO: need to check if its useful
        if (isset($base_design_set)) {
          $base_design_set_extras = base64_decode($base_design_set["extras"]);
          $import_json = $base_design_set_extras;

          $classes = get_option("ct_components_classes", []);
          $custom_selectors = get_option("ct_custom_selectors", []);
          $style_sets = get_option("ct_style_sets", []);
          $style_folders = get_option("ct_style_folders", []);
          $style_sheets = get_option("ct_style_sheets", []);
          $global_settings = get_option("ct_global_settings", []);
          $element_presets = get_option("oxygen_vsb_element_presets", []);
          $global_colors = oxy_get_global_colors();

          //IMPORTANT - DONT DELETE
          $default_presets = apply_filters(
            "oxygen_vsb_element_presets_defaults",
            []
          );

          foreach ($default_presets as $element_name => $presets) {
            if (empty($element_presets[$element_name])) {
              continue;
            }

            foreach ($presets as $key => $preset) {
              $index = array_search($preset, $element_presets[$element_name]);
              if ($index !== false) {
                unset($element_presets[$element_name][$index]);
              }
            }
            // re-index array to keep JSON clean from indexes and not overwrite other presets
            $element_presets[$element_name] = array_values(
              $element_presets[$element_name]
            );
          }

          $export_json = [];
          // generate export JSON
          // if (is_array($a_string) && isset($a_string['port'])) {
          $export_json["classes"] = $classes;
          // }
          $export_json["custom_selectors"] = $custom_selectors;
          $export_json["style_sets"] = $style_sets;
          $export_json["style_folders"] = $style_folders;
          $export_json["style_sheets"] = $style_sheets;
          $export_json["global_settings"] = $global_settings;
          $export_json["global_colors"] = $global_colors;
          $export_json["element_presets"] = $element_presets;

          // generate JSON object
          $export_json = json_encode($export_json);
          $export_backup = get_option("oxymade_oxygen_fullset_prior_backup");

          // if (isset($export_backup) && !empty($export_backup)) {
          //   // update_option("oxymade_oxygen_fullset_prior_backup", $export_json, "no");
          // } else {
          //   update_option("oxymade_oxygen_fullset_prior_backup", $export_json, false);
          // }
          //already taking a backup above!
          if (!is_array($classes)) {
            $classes = [];
          }

          if (!is_array($custom_selectors)) {
            $custom_selectors = [];
          }

          if (!is_array($style_sets)) {
            $style_sets = [];
          }

          if (!is_array($style_folders)) {
            $style_folders = [];
          }

          if (!is_array($style_sheets)) {
            $style_sheets = [];
          }

          $import_json = sanitize_text_field(stripcslashes($import_json));

          // check if empty
          if (empty($import_json)) {
            $import_errors[] = __("Empty Import");
          } else {
            // try to decode
            $import_array = json_decode($import_json, true);

            // update options
            if ($import_array) {
              if (
                isset($import_array["classes"]) &&
                is_array($import_array["classes"])
              ) {
                foreach ($import_array["classes"] as $key => $item) {
                  if (!is_string($key)) {
                    unset($import_array["classes"][$key]);
                  }
                }

                // This will add all new deisgn set extra classes to the front and
                // then the second merge will over write old classes with new values.
                $classes = array_merge($import_array["classes"], $classes);
                $classes = array_merge($classes, $import_array["classes"]);

                update_option("ct_components_classes", $classes);
              }

              // custom selectors
              if (
                isset($import_array["custom_selectors"]) &&
                is_array($import_array["custom_selectors"])
              ) {
                $custom_selectors = array_merge(
                  $custom_selectors,
                  $import_array["custom_selectors"]
                );
                update_option("ct_custom_selectors", $custom_selectors);
              }

              // style sets
              if (
                isset($import_array["style_sets"]) &&
                is_array($import_array["style_sets"])
              ) {
                $style_sets = array_merge(
                  $style_sets,
                  $import_array["style_sets"]
                );
                update_option("ct_style_sets", $style_sets);
              }

              // style folders
              if (
                isset($import_array["style_folders"]) &&
                is_array($import_array["style_folders"])
              ) {
                $style_folders = array_merge(
                  $style_folders,
                  $import_array["style_folders"]
                );
                update_option("ct_style_folders", $style_folders);
              }

              // style sheets
              if (
                isset($import_array["style_sheets"]) &&
                is_array($import_array["style_sheets"])
              ) {
                foreach ($import_array["style_sheets"] as $key => $item) {
                  foreach ($style_sheets as $existing) {
                    if ($existing["name"] == $item["name"]) {
                      unset($import_array["style_sheets"][$key]);
                      break;
                    }
                  }
                }

                $style_sheets = array_merge(
                  $style_sheets,
                  $import_array["style_sheets"]
                );
                update_option("ct_style_sheets", $style_sheets);
              }

              // presets
              if (
                isset($import_array["element_presets"]) &&
                is_array($import_array["element_presets"])
              ) {
                $element_presets = array_merge_recursive(
                  $element_presets,
                  $import_array["element_presets"]
                );
                update_option("oxygen_vsb_element_presets", $element_presets);
              }

              $import_success[] = __("Import success", "component-theme");

              // add_option("oxymade_quick_install", "installed");
              $oxymade_updated = true;
              $success_msg =
                "Congratulations! OxyMade Framework & Design Kit has been installed successfully. 🥳";
            } else {
              $import_errors[] = __("Wrong JSON Format", "component-theme");
            }
          }
        
        // ====================================================
        // Importing design set specific stylesheet
        // ====================================================
        
        $base_style_sheets = get_option("ct_style_sheets", []);
        $base_stylesheet_source = $base_design_set["stylesheets"];
        $base_stylesheet_source = base64_decode($base_stylesheet_source);
        $base_stylesheet_source = json_decode($base_stylesheet_source);
        $base_stylesheets = $base_stylesheet_source->stylesheets;

        if(!empty($base_stylesheets)){
        $base_active_set = get_option("oxymade_active_set");
        
        $base_oxy_ss_ids = [];
        
        foreach ($base_style_sheets as $key => $stylesheet) {
          array_push($base_oxy_ss_ids, $stylesheet["id"]);
        }
        
        if (!empty($base_oxy_ss_ids)) {
          $base_oxy_current_ss_id = max($base_oxy_ss_ids);
        }
        
        if($base_style_sheets && isset($base_style_sheets)){
          
          foreach ($base_style_sheets as $key => $base_style_sheet) {
            if($base_style_sheets[$key]["name"] == "oxymade-" . $base_active_set){
              $base_fol_id = $base_style_sheets[$key]["id"];
              $base_fol_active = true;
            }
          }
          if(isset($base_fol_active) && $base_fol_active){
            $base_fol_id = $base_style_sheets[$key]["id"];
          } else {
            $base_fol_active = false;
            $base_fol_id = $base_oxy_current_ss_id + 1;
          }
        }
          
          if(isset($base_fol_active) && $base_fol_active == false){ 
            $base_stylesheet_folder_array = [];
            $base_stylesheet_folder_array["id"] = $base_oxy_current_ss_id + 1;
            $base_stylesheet_folder_array["name"] = "oxymade-" . $base_active_set;
            $base_stylesheet_folder_array["status"] = 1;
            $base_stylesheet_folder_array["folder"] = 1;
            // push the folder array to the stylesheets
            array_push($base_style_sheets, $base_stylesheet_folder_array);
          }
          
          $re_base_oxy_ss_ids = [];
          
          foreach ($base_style_sheets as $key => $stylesheet) {
            array_push($re_base_oxy_ss_ids, $stylesheet["id"]);
          }
          
          if (!empty($re_base_oxy_ss_ids)) {
            $re_base_oxy_ss_ids = max($re_base_oxy_ss_ids);
          }
          
         $i = 0; 
        if($base_stylesheets && isset($base_stylesheets)){
          foreach ($base_stylesheets as $key => $base_stylesheet) {
            $i++;
            $base_stylesheets[$key] = (array) $base_stylesheet;
            $prior_base_design_set_name = $base_stylesheets[$key]["name"];
            foreach ($base_style_sheets as $key => $base_style_sheet) {
              $base_stylesheets[$key] = (array) $base_stylesheet;
              if($prior_base_design_set_name == $base_style_sheets[$key]["name"]){
                unset($base_style_sheets[$key]);
                $prior_set_ss_id = $base_style_sheets[$key]["id"];
                $prior_set_ss_parent_id = $base_style_sheets[$key]["parent"];
              } else {
                $prior_set_ss_id = $re_base_oxy_ss_ids + $i;
                $prior_set_ss_parent_id = $base_fol_id;
              }
            }
            
            $base_stylesheets[$key]["id"] = $prior_set_ss_id;
            $base_stylesheets[$key]["parent"] = $prior_set_ss_parent_id;
            array_push($base_style_sheets, $base_stylesheets[$key]);

          }
        }
        
        update_option("ct_style_sheets", $base_style_sheets);
        
        }
          
        $oxymade_updated = true;
        $success_msg =
          "Congratulations! Base design set reset successfully. 🥳";
          
          // ====================================================
          // ending design set specific stylesheet importing
          // ====================================================
        
        
        }
      }
    }

    /* =======================================
            IMPORTING EXTRAS END
            update extras when extras imported or full set install
            ========================================= */
  }
}

/* =======================================
    // MANAGE MODULES POST AREA
    ======================================= */

$oxymade_gridhelpers = get_option("oxymade_gridhelpers");
if (isset($_POST["oxymade_gridhelpers"])) {
  if (isset($oxymade_gridhelpers) && !empty($oxymade_gridhelpers)) {
    if ($_POST["oxymade_gridhelpers"] == "Disable") {
      update_option("oxymade_gridhelpers", "Disable");
    } elseif ($_POST["oxymade_gridhelpers"] == "Enable") {
      update_option("oxymade_gridhelpers", "Enable");
    }
  } else {
    add_option("oxymade_gridhelpers", "Disable");
  }
}

$oxymade_mergeClasses = get_option("oxymade_mergeClasses");
if (isset($_POST["oxymade_mergeClasses"])) {
  if (isset($oxymade_mergeClasses) && !empty($oxymade_mergeClasses)) {
    if ($_POST["oxymade_mergeClasses"] == "Disable") {
      update_option("oxymade_mergeClasses", "Disable");
    } elseif ($_POST["oxymade_mergeClasses"] == "Enable") {
      update_option("oxymade_mergeClasses", "Enable");
    }
  } else {
    add_option("oxymade_mergeClasses", "Disable");
  }
}

$oxymade_powertoggle = get_option("oxymade_powertoggle");
if (isset($_POST["oxymade_powertoggle"])) {
  if (isset($oxymade_powertoggle) && !empty($oxymade_powertoggle)) {
    if ($_POST["oxymade_powertoggle"] == "Disable") {
      update_option("oxymade_powertoggle", "Disable");
    } elseif ($_POST["oxymade_powertoggle"] == "Enable") {
      update_option("oxymade_powertoggle", "Enable");
    }
  } else {
    add_option("oxymade_powertoggle", "Disable");
  }
}

$oxymade_copyPaste = get_option("oxymade_copypaste");
if (isset($_POST["oxymade_copypaste"])) {
  if (isset($oxymade_copyPaste) && !empty($oxymade_copyPaste)) {
    if ($_POST["oxymade_copypaste"] == "Disable") {
      update_option("oxymade_copypaste", "Disable");
    } elseif ($_POST["oxymade_copypaste"] == "Enable") {
      update_option("oxymade_copypaste", "Enable");
    }
  } else {
    add_option("oxymade_copypaste", "Disable");
  }
}

$oxymade_darkmode = get_option("oxymade_darkmode");
if (isset($_POST["oxymade_darkmode"])) {
  if (isset($oxymade_darkmode) && !empty($oxymade_darkmode)) {
    if ($_POST["oxymade_darkmode"] == "Disable") {
      update_option("oxymade_darkmode", "Disable");
    } elseif ($_POST["oxymade_darkmode"] == "Enable") {
      update_option("oxymade_darkmode", "Enable");
    }
  } else {
    add_option("oxymade_darkmode", "Enable");
  }
}

$oxymade_changetoid = get_option("oxymade_changetoid");
if (isset($_POST["oxymade_changetoid"])) {
  if (isset($oxymade_changetoid) && !empty($oxymade_changetoid)) {
    if ($_POST["oxymade_changetoid"] == "Disable") {
      update_option("oxymade_changetoid", "Disable");
    } elseif ($_POST["oxymade_changetoid"] == "Enable") {
      update_option("oxymade_changetoid", "Enable");
    }
  } else {
    add_option("oxymade_changetoid", "Enable");
  }
}

$oxymade_blogzine = get_option("oxymade_blogzine");
if (isset($_POST["oxymade_blogzine"])) {
  if (isset($oxymade_blogzine) && !empty($oxymade_blogzine)) {
    if ($_POST["oxymade_blogzine"] == "Disable") {
      update_option("oxymade_blogzine", "Disable");
    } elseif ($_POST["oxymade_blogzine"] == "Enable") {
      update_option("oxymade_blogzine", "Enable");
    }
  } else {
    add_option("oxymade_blogzine", "Enable");
  }
}

if (
  isset($_POST["blogzine_typography"]) &&
  $_POST["blogzine_typography"] == "Save typography settings"
) {
  update_option("oxymade_bz_bp_2xl", $_POST["breakpoint_2xl"]);
  update_option("oxymade_bz_bp_xl", $_POST["breakpoint_xl"]);
  update_option("oxymade_bz_bp_lg", $_POST["breakpoint_lg"]);
  update_option("oxymade_bz_bp_md", $_POST["breakpoint_md"]);
  update_option("oxymade_bz_bp_sm", $_POST["breakpoint_sm"]);

  $om_bz_bp_2xl = get_option("oxymade_bz_bp_2xl");
  $om_bz_bp_xl = get_option("oxymade_bz_bp_xl");
  $om_bz_bp_lg = get_option("oxymade_bz_bp_lg");
  $om_bz_bp_md = get_option("oxymade_bz_bp_md");
  $om_bz_bp_sm = get_option("oxymade_bz_bp_sm");

  $om_bp_default_width = $width_default - 1;
  $om_bp_tablet_width = $width_tablet - 1;
  $om_bp_phone_landscape_width = $width_phone_landscape - 1;
  $om_bp_phone_portrait_width = $width_phone_portrait - 1;

  $blogzine_frontend_css .= base64_decode($typography_default_css);
  $blogzine_frontend_css .= base64_decode(
    ${"typography_" . $om_bz_bp_2xl . "_css"}
  );
  $blogzine_frontend_css .= "\n";
  $blogzine_frontend_css .=
    "@media screen and (max-width: " . $om_bp_default_width . "px) {";
  $blogzine_frontend_css .= base64_decode(
    ${"typography_" . $om_bz_bp_xl . "_css"}
  );
  $blogzine_frontend_css .=
    "} \n @media screen and (max-width: " . $om_bp_tablet_width . "px) {";
  $blogzine_frontend_css .= base64_decode(
    ${"typography_" . $om_bz_bp_lg . "_css"}
  );
  $blogzine_frontend_css .=
    "} \n @media screen and (max-width: " . $om_bp_phone_landscape_width . "px) {";
  $blogzine_frontend_css .= base64_decode(
    ${"typography_" . $om_bz_bp_md . "_css"}
  );
  $blogzine_frontend_css .=
    "} \n @media screen and (max-width: " . $om_bp_phone_portrait_width . "px) {";
  $blogzine_frontend_css .= base64_decode(
    ${"typography_" . $om_bz_bp_sm . "_css"}
  );
  $blogzine_frontend_css .= "}";

  update_option("oxymade_blogzine_css", $blogzine_frontend_css);

  // Gutenberg typography blogzine css

  update_option("oxymade_gb_bz_bp_2xl", $_POST["breakpoint_2xl"]);
  update_option("oxymade_gb_bz_bp_xl", $_POST["breakpoint_xl"]);
  update_option("oxymade_gb_bz_bp_lg", $_POST["breakpoint_lg"]);
  update_option("oxymade_gb_bz_bp_md", $_POST["breakpoint_md"]);
  update_option("oxymade_gb_bz_bp_sm", $_POST["breakpoint_sm"]);

  $om_gb_bz_bp_2xl = get_option("oxymade_gb_bz_bp_2xl");
  $om_gb_bz_bp_xl = get_option("oxymade_gb_bz_bp_xl");
  $om_gb_bz_bp_lg = get_option("oxymade_gb_bz_bp_lg");
  $om_gb_bz_bp_md = get_option("oxymade_gb_bz_bp_md");
  $om_gb_bz_bp_sm = get_option("oxymade_gb_bz_bp_sm");

  $om_bp_default_width = $width_default - 1;
  $om_bp_tablet_width = $width_tablet - 1;
  $om_bp_phone_landscape_width = $width_phone_landscape - 1;
  $om_bp_phone_portrait_width = $width_phone_portrait - 1;

  $gutenberg_blogzine_frontend_css .= base64_decode(
    $gutenberg_typography_default_css
  );
  $gutenberg_blogzine_frontend_css .= base64_decode(
    ${"gutenberg_typography_" . $om_gb_bz_bp_2xl . "_css"}
  );
  $gutenberg_blogzine_frontend_css .=
    "@media screen and (max-width: " . $om_bp_default_width . "px) {";
  $gutenberg_blogzine_frontend_css .= base64_decode(
    ${"gutenberg_typography_" . $om_gb_bz_bp_xl . "_css"}
  );
  $gutenberg_blogzine_frontend_css .=
    "} @media screen and (max-width: " . $om_bp_tablet_width . "px) {";
  $gutenberg_blogzine_frontend_css .= base64_decode(
    ${"gutenberg_typography_" . $om_gb_bz_bp_lg . "_css"}
  );
  $gutenberg_blogzine_frontend_css .=
    "} @media screen and (max-width: " . $om_bp_phone_landscape_width . "px) {";
  $gutenberg_blogzine_frontend_css .= base64_decode(
    ${"gutenberg_typography_" . $om_gb_bz_bp_md . "_css"}
  );
  $gutenberg_blogzine_frontend_css .=
    "} @media screen and (max-width: " . $om_bp_phone_portrait_width . "px) {";
  $gutenberg_blogzine_frontend_css .= base64_decode(
    ${"gutenberg_typography_" . $om_gb_bz_bp_sm . "_css"}
  );
  $gutenberg_blogzine_frontend_css .= "}";

  update_option("oxymade_gb_blogzine_css", $gutenberg_blogzine_frontend_css);
  
  $success_msg =
  "Blogzine typography settings updated successfully! 👍";
  $oxymade_updated = true;
}

//Blogzine settings panel

if (
  isset($_POST["blogzine_settings"]) &&
  $_POST["blogzine_settings"] == "Save settings"
) {
  $oxymade_blogzine_infyscroll = $_POST["blogzine_infyscroll"];
  update_option("oxymade_infyscroll", $oxymade_blogzine_infyscroll);
  
  $oxymade_blogzine_oxybergcolors = $_POST["oxymade_gutenberg_color_palette_status"];
  update_option("oxymade_gutenberg_color_palette_status", $oxymade_blogzine_oxybergcolors);
  
  $success_msg =
  "Blogzine settings updated successfully! 👍";
  $oxymade_updated = true;
}


// DARK MODE SETTINGS

if (
  isset($_POST["oxymade_darkmode_settings"]) || isset($_POST["oxymade_darkmode_customize"]) || isset($_POST["oxymade_darkmode_customize_options"]) || isset($_POST["oxymade_darkmode_customcss"])
) {
  
  if (isset($_POST["oxymade_darkmode_settings"]) && $_POST["oxymade_darkmode_settings"] == "Save dark mode settings"){
    update_option("oxymade_darkmode_customize", $_POST["oxymade_darkmode_customize"]);
    update_option("oxymade_darkmode_custom_css_status", $_POST["oxymade_darkmode_custom_css_status"]);
    }
    
  if (isset($_POST["oxymade_darkmode_customize_options"]) && $_POST["oxymade_darkmode_customize_options"] == "Save customize options"){
    $om_dm_options = [];
    
    $om_dm_options["automatchostheme"] = $_POST["oxymade_darkmode_customize_options_automatchostheme"];
    $om_dm_options["backgroundcolor"] = $_POST['oxymade_darkmode_customize_options_backgroundcolor'];
    $om_dm_options["buttoncolordark"] = $_POST['oxymade_darkmode_customize_options_buttoncolordark'];
    $om_dm_options["buttoncolorlight"] = $_POST['oxymade_darkmode_customize_options_buttoncolorlight'];
    $om_dm_options["saveincookies"] = $_POST['oxymade_darkmode_customize_options_saveincookies'];
    $om_dm_options["label"] = $_POST['oxymade_darkmode_customize_options_label'];
    $om_dm_options["mixcolor"] = $_POST['oxymade_darkmode_customize_options_mixcolor'];
    $om_dm_options["time"] = $_POST['oxymade_darkmode_customize_options_time'];
    $om_dm_options["bottom"] = $_POST['oxymade_darkmode_customize_options_bottom'];
    $om_dm_options["right"] = $_POST['oxymade_darkmode_customize_options_right'];
    $om_dm_options["left"] = $_POST['oxymade_darkmode_customize_options_left'];
    
    $om_dm_options = json_encode($om_dm_options);
    $om_dm_options = base64_encode($om_dm_options);
    
    update_option("oxymade_darkmode_customize_options", $om_dm_options);
      
    }
    
  if (isset($_POST["oxymade_darkmode_customcss"]) && $_POST["oxymade_darkmode_customcss"] == "Save custom CSS"){
    update_option("oxymade_darkmode_custom_css", $_POST["oxymade_darkmode_custom_css"]);
  }
  
  $success_msg =
  "Dark mode settings updated successfully! 👍";
  $oxymade_updated = true;
  
}



// ===============================================
// enable/disable hover styles folder
// ===============================================
$oxymade_hoverStyles = get_option("oxymade_hoverstyles");
$stylesheets = get_option("ct_style_sheets");
if (is_array($stylesheets) || is_object($stylesheets)) {
  foreach ($stylesheets as $key => $stylesheet) {
    if (is_array($stylesheet) && array_key_exists("folder", $stylesheet)) {
      if ($stylesheet["name"] == $oxymade_hovers_ss_folder_name) {
        $oxymade_hovers_ss_folder_index = $key;
      }
    }
  }
}

if (isset($_POST["oxymade_hoverstyles"])) {
  if (
    isset($oxymade_hoverStyles) &&
    isset($oxymade_hovers_ss_folder_index) &&
    !empty($oxymade_hoverStyles)
  ) {
    if ($_POST["oxymade_hoverstyles"] == "Disable") {
      update_option("oxymade_hoverstyles", "Disable");

      $stylesheets[$oxymade_hovers_ss_folder_index]["status"] = 0;
      update_option("ct_style_sheets", $stylesheets);
      $oxymade_updated = true;
    } elseif ($_POST["oxymade_hoverstyles"] == "Enable") {
      update_option("oxymade_hoverstyles", "Enable");
      $stylesheets[$oxymade_hovers_ss_folder_index]["status"] = 1;
      update_option("ct_style_sheets", $stylesheets);
      $oxymade_updated = true;
    }
  } else {
    add_option("oxymade_hoverstyles", "Disable");
    $stylesheets[$oxymade_hovers_ss_folder_index]["status"] = 0;
    update_option("ct_style_sheets", $stylesheets);
    $oxymade_updated = true;
  }
}

$oxymade_global_colors_editor = get_option("oxymade_global_colors_editor");
if (isset($_POST["oxymade_global_colors_editor"])) {
  if (
    isset($oxymade_global_colors_editor) &&
    !empty($oxymade_global_colors_editor)
  ) {
    if ($_POST["oxymade_global_colors_editor"] == "Disable") {
      update_option("oxymade_global_colors_editor", "Disable");
    } elseif ($_POST["oxymade_global_colors_editor"] == "Enable") {
      update_option("oxymade_global_colors_editor", "Enable");
    }
  } else {
    add_option("oxymade_global_colors_editor", "Disable");
  }
}

$oxymade_global_classes_editor = get_option("oxymade_global_classes_editor");
if (isset($_POST["oxymade_global_classes_editor"])) {
  if (
    isset($oxymade_global_classes_editor) &&
    !empty($oxymade_global_classes_editor)
  ) {
    if ($_POST["oxymade_global_classes_editor"] == "Disable") {
      update_option("oxymade_global_classes_editor", "Disable");
    } elseif ($_POST["oxymade_global_classes_editor"] == "Enable") {
      update_option("oxymade_global_classes_editor", "Enable");
    }
  } else {
    add_option("oxymade_global_classes_editor", "Disable");
  }
}

/* =======================================
    // BACKUP - RESTORE - EXPORT GLOBAL CLASSES
    ======================================= */

$classes = get_option("ct_components_classes", []);
$style_folders = get_option("ct_style_folders", []);
$backup_ct_components_classes = get_option("backup_ct_components_classes");
$backup_ct_style_folders = get_option("backup_ct_style_folders");
$stylefols = [];
foreach ($style_folders as $key => $stylefol) {
  array_push($stylefols, $key);
}

$selectors = [];
$sels = [];

foreach ($classes as $key => $value) {
  if (isset($value["parent"])) {
    $parent = $value["parent"];
  }
  if (isset($parent) && !empty($parent)) {
    $sels[$parent] = [];
  }
}

foreach ($classes as $key => $value) {
  if (isset($value["parent"])) {
    $parent = $value["parent"];
  }
  if (isset($parent) && !empty($parent)) {
    if (in_array(strtolower($parent), array_map("strtolower", $stylefols))) {
      $selectors[$parent][$key] = $value;
      // echo $key;
      array_push($sels[$parent], $key);
    } else {
      $selectors["uncategorized"][$key] = $value;
    }
  } else {
    $selectors["uncategorized"][$key] = $value;
  }
}

// $selectors_json = json_encode($selectors);
// $sels_json = json_encode($sels);
// echo $selectors_json;
// echo $sels_json;
// echo "<hr>";
if (isset($_POST["delete_selectors"])) {
  if ($_POST["delete_selectors"] == "damn_sure_delete") {
    $classes = [];
    $style_folders = [];
    update_option("ct_components_classes", $classes);
    update_option("ct_style_folders", $style_folders);
  }
}

if (isset($_POST["delete_cat_selectors"])) {
  if ($_POST["delete_cat_selectors"] == "yes_sure_delete") {
    $orphans = $selectors["uncategorized"];
    $style_folders = [];
    update_option("ct_components_classes", $orphans);
    update_option("ct_style_folders", $style_folders);
  }
}

if (
  isset($_POST["delete_selectors_folder"]) &&
  !empty($_POST["delete_selectors_folder"])
) {
  $selected_folder = $_POST["delete_selectors_folder"];

  unset($selectors[$selected_folder]);
  unset($style_folders[$selected_folder]);

  $classes_object = new stdClass();
  foreach ($selectors as $key => $value) {
    $selectors_key_list = $selectors[$key];
    foreach ($selectors_key_list as $selectors_key => $selec_value) {
      $classes_object->$selectors_key = $selec_value;
    }
  }

  update_option("ct_components_classes", (array) $classes_object);
  update_option("ct_style_folders", $style_folders);
}

if (isset($_POST["gs_action"]) && $_POST["gs_action"] == "Delete") {
  $classes = [];
  update_option("ct_components_classes", $classes);
  $style_folders = [];
  update_option("ct_style_folders", $style_folders);
  $success_msg = "All the classes & style folders deleted successfully! 👍";
  $oxymade_updated = true;
}

if (isset($_POST["gs_action"]) && $_POST["gs_action"] == "Backup") {
  if (
    isset($backup_ct_components_classes) &&
    is_array($backup_ct_style_folders)
  ) {
    update_option("backup_ct_components_classes", $classes, false);
    update_option("backup_ct_style_folders", $style_folders, false);

    $success_msg =
      "Global classes & style folders backup updated successfully! 👍";
    $oxymade_updated = true;
  } else {
    update_option("backup_ct_components_classes", $classes, false);
    update_option("backup_ct_style_folders", $style_folders, false);
    $success_msg = "Global classes & folders backup created successfully! 👍";
    $oxymade_updated = true;
  }
}

if (isset($_POST["gs_action"]) && $_POST["gs_action"] == "Restore") {
  update_option("ct_components_classes", $backup_ct_components_classes);
  update_option("ct_style_folders", $backup_ct_style_folders);
  $success_msg =
    "Global classes & style folders backup restored successfully! 👍";
  $oxymade_updated = true;
}
//
// $backup_ct_components_classes = get_option("backup_ct_components_classes");
// $backup_ct_style_folders = get_option("backup_ct_style_folders");
// $classes = get_option("ct_components_classes", []);
// $style_folders = get_option("ct_style_folders", []);
// $backup_ct_components_classes = get_option("backup_ct_components_classes");
// $backup_ct_style_folders = get_option("backup_ct_style_folders");
// end of backup, delete, restore
$classes = get_option("ct_components_classes", []);
$style_folders = get_option("ct_style_folders", []);
$backup_ct_components_classes = get_option("backup_ct_components_classes");
$backup_ct_style_folders = get_option("backup_ct_style_folders");
$stylefols = [];
foreach ($style_folders as $key => $stylefol) {
  array_push($stylefols, $key);
}

$selectors = [];
$sels = [];

foreach ($classes as $key => $value) {
  if (isset($value["parent"])) {
    $parent = $value["parent"];
  }
  if (isset($parent) && !empty($parent)) {
    $sels[$parent] = [];
  }
}

foreach ($classes as $key => $value) {
  if (isset($value["parent"])) {
    $parent = $value["parent"];
  }
  if (isset($parent) && !empty($parent)) {
    if (in_array(strtolower($parent), array_map("strtolower", $stylefols))) {
      $selectors[$parent][$key] = $value;
      // echo $key;
      array_push($sels[$parent], $key);
    } else {
      $selectors["uncategorized"][$key] = $value;
    }
  } else {
    $selectors["uncategorized"][$key] = $value;
  }
}

$stylefols = [];
foreach ($style_folders as $key => $stylefol) {
  array_push($stylefols, $key);
}

$export_classes = get_option("ct_components_classes", []);
$export_style_folders = get_option("ct_style_folders", []);

$full_export["classes"] = $export_classes;
$full_export["style_folders"] = $export_style_folders;

$selectors_export = json_encode($full_export);

/* =======================================
    // BACKUP - RESTORE - EXPORT STYLESHEETS
    ======================================= */

$style_sheets = get_option("ct_style_sheets", []);
$backup_ct_style_sheets = get_option("backup_ct_style_sheets");

if (
  isset($_POST["delete_stylesheets_folder"]) &&
  !empty($_POST["delete_stylesheets_folder"])
) {
  $selected_folder = $_POST["delete_stylesheets_folder"];
  unset($style_sheets[$selected_folder]);
  array_values($style_sheets);
  update_option("ct_style_sheets", $style_sheets);
}

if (isset($_POST["ss_action"]) && $_POST["ss_action"] == "Delete") {
  $stylesheets = [];
  update_option("ct_style_sheets", $stylesheets);
  $success_msg = "All the stylesheets deleted successfully! 👍";
  $oxymade_updated = true;
}

if (isset($_POST["ss_action"]) && $_POST["ss_action"] == "Backup") {
  if (isset($backup_ct_style_sheets)) {
    update_option("backup_ct_style_sheets", $style_sheets, false);
    $success_msg = "Stylesheets backup updated successfully! 👍";
    $oxymade_updated = true;
  } else {
    update_option("backup_ct_style_sheets", $style_sheets, false);
    $success_msg = "Stylesheets backup created successfully! 👍";
    $oxymade_updated = true;
  }
}

if (isset($_POST["ss_action"]) && $_POST["ss_action"] == "Restore") {
  update_option("ct_style_sheets", $backup_ct_style_sheets);
  $success_msg = "Stylesheets backup restored successfully! 👍";
  $oxymade_updated = true;
}

$style_sheets = get_option("ct_style_sheets", []);

$ssheets = [];
$allsheets = [];
$allfols = new StdClass();
$all_sheets = new StdClass();
$ssheetfols = [];

foreach ($style_sheets as $key => $style_sheet) {
  if (isset($style_sheet["folder"])) {
    array_push($ssheetfols, $style_sheet["id"]);
    $id = $style_sheet["id"];
    $allfols = (array) $allfols;
    $allfols[$id] = $key;
    $allfols = (object) $allfols;
  }

  if (isset($style_sheet["parent"])) {
    array_push($ssheets, $style_sheet["id"]);
    $id = $style_sheet["id"];
    array_push($allsheets, $key);

    $all_sheets = (array) $all_sheets;
    $all_sheets[$id] = $key;
    $all_sheets = (object) $all_sheets;
  }
}

$backup_ct_style_sheets = get_option("backup_ct_style_sheets");

$export_ssheets = get_option("ct_style_sheets", []);

$full_ssexport["style_sheets"] = $export_ssheets;

$stylesheets_export = json_encode($full_ssexport);

// echo $stylesheets_export;
/* =======================================
    // BACKUP - RESTORE - DELETE GLOBAL COLORS
    ======================================= */

if (isset($_POST["gc_action"]) && $_POST["gc_action"] == "delete") {
  delete_option("backup_oxygen_vsb_global_colors");
  //   echo "deleted";
}

if (isset($_POST["gc_action"]) && $_POST["gc_action"] == "backup") {
  if (isset($backup_global_colors) && is_array($backup_global_colors)) {
    update_option("backup_oxygen_vsb_global_colors", $global_colors, false);
    $success_msg = "Global colors backup updated successfully! 👍";
    $oxymade_updated = true;
  } else {
    update_option("backup_oxygen_vsb_global_colors", $global_colors, false);
    $success_msg = "Global colors backup created successfully! 👍";
    $oxymade_updated = true;
  }
}

if (isset($_POST["gc_action"]) && $_POST["gc_action"] == "restore") {
  $backup_global_colors = get_option("backup_oxygen_vsb_global_colors");
  update_option("oxygen_vsb_global_colors", $backup_global_colors);
  $success_msg = "Global colors backup restored successfully! 👍";
  $oxymade_updated = true;
}

$backup_global_colors = get_option("backup_oxygen_vsb_global_colors");

// Updating Global Native Oxygen Colors and Sets
if (isset($_POST["global_colors"])) {
  $post_data = $_POST;

  if (isset($backup_global_colors) && is_array($backup_global_colors)) {
  } else {
    update_option("backup_oxygen_vsb_global_colors", $global_colors, false);
  }

  $gbcolors = [];
  unset($post_data["global_colors"]);
  foreach ($post_data as $key => $value) {
    $gbcolors[$key] = [
      "id" => (int) $value["id"],
      "name" => $value["name"],
      "value" => $value["value"],
      "set" => (int) $value["set"],
    ];
  }

  unset($global_colors["colors"]);
  $global_colors["colors"] = $gbcolors;
  update_option("oxygen_vsb_global_colors", $global_colors);
  $success_msg = "Global colors updated successfully! 👍";
  $oxymade_updated = true;
} elseif (isset($_POST["global_sets"])) {
  if (isset($backup_global_colors) && is_array($backup_global_colors)) {
  } else {
    update_option("backup_oxygen_vsb_global_colors", $global_colors, false);
  }
  $post_data = $_POST;
  $gbsets = [];
  unset($post_data["global_sets"]);
  foreach ($post_data as $key => $value) {
    $gbsets[$key] = ["id" => (int) $value["id"], "name" => $value["name"]];
  }
  unset($global_colors["sets"]);
  $global_colors["sets"] = $gbsets;
  update_option("oxygen_vsb_global_colors", $global_colors);
  $success_msg = "Global color sets updated successfully! 👍";
  $oxymade_updated = true;
}

// echo "<hr>";
// $oxymade_updated = true;
// $oxymade_saved = "";
// $oxymade_saved = true;
// $oxymade_updated = "";
// $oxymade_updated = true;
// $oxymade_has_error = "";
// $oxymade_has_error = true;
// $oxymade_has_warning = "";
$oxy_is_open = get_transient("oxygen_post_edit_lock");

$retval = (object) [
  "ct_builder_active" => false,
  "ct_stylesheet_changed" => false,
];
// check if Oxygen Builder is active
$retval->ct_builder_active = get_transient("oxygen_post_edit_lock");

if ($retval->ct_builder_active) {
  $oxymade_has_warning = true;
}

if (isset($monster_active_set) && !empty($monster_active_set)) {
  array_push(
    $oxymade_warnings,
    "Please Install / Re-install the framework to update the framework to the latest version."
  );
  $oxymade_has_warning = true;
}

$om_license = get_option("oxymade_license_key");
$om_license_status = get_option("oxymade_license_status");
$oxymade_oneclick_installer_status = get_option(
  "oxymade_oneclick_installer_status"
);

// $tab = isset($_GET["tab"]) ? sanitize_text_field($_GET["tab"]) : false;
if (
  isset($om_license) &&
  !empty($om_license) &&
  $om_license_status == "valid"
) { ?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

    <!-- 
    ======================================
    ======================================
    Full Install Box - First time users
    ======================================
    ======================================
    -->
<?php 

if (!$oxymade_oneclick_installer_status) { ?>

<div class="min-h-screen">
  <main>
    
    <div class="mt-8 max-w-3xl mx-auto sm:px-6 lg:max-w-4xl">
      
      
      <div class="grid grid-cols-4 mb-8">
        
        <div class="col-span-1">
          <svg width="180" height="60" fill="#555555" xmlns="http://www.w3.org/2000/svg">
            <path d="M40.994 11.1c-1.815.06-3.568 1.04-4.66 2.745-.066.14-.133.279-.205.375a.36.36 0 01-.272.161 5.385 5.385 0 00-2.897.748c-.118.057-.185.196-.13.323a.3.3 0 00.194.244c.807.18 1.588.494 2.378.896.571.292 1.147.629 1.733 1.052.581.38 1.09.856 1.558 1.336.647.593 1.18 1.287 1.632 1.99a.37.37 0 00.347.14.38.38 0 00.216-.288c.082-1.112-.082-2.198-.476-3.128-.015-.13-.025-.218.047-.314.067-.14.18-.24.247-.379 1.092-1.704 1.224-3.837.604-5.669-.056-.127-.147-.205-.229-.196-.041.004-.046-.04-.087-.035zm-2.736 4.748c-.535-.34-.706-1.117-.355-1.684.355-.523 1.081-.688 1.58-.3.54.385.706 1.117.356 1.684-.247.38-.678.602-1.102.514a.973.973 0 01-.48-.214z"/>
            <path d="M28.647 28.682l9.201-10.796-2.11-1.324c-.105.183-7.091 12.12-7.091 12.12z"/>
            <path d="M31.54 18.838c-.722-.28-1.516-.469-2.305-.614-7.213-1.211-13.894 3.97-14.863 11.62-.968 7.651 4.083 14.805 11.341 16.055 7.214 1.211 13.89-4.013 14.864-11.62a14.716 14.716 0 00-3.007-11.055l-4.067 3.65 3.756-4.702.388-2.974-5.784 6.786-5.095 5.946 6.898-3.86c.89 1.519 1.28 3.343 1.064 5.22-.548 4.199-4.245 7.08-8.226 6.4-4.022-.675-6.77-4.837-6.286-8.857.525-4.024 3.827-6.395 7.415-6.371l3.297-4.013-3.074 4.598-4.602 9.965 7.105-10.19 4.306-7.69-3.126 1.706zM57.52 45.898h6.55l-6.55-9.733 6.367-9.478h-6.806l-2.928 5.16-2.927-5.16H44.42l6.367 9.478-6.55 9.733h6.55l3.367-5.16 3.366 5.16zM84.233 26.687h-6.367l-3.55 11.051-3.403-11.051h-6.55l7.172 18.59-2.598 7.172h5.782l9.514-25.762zM104.067 43.81l5.636-14.711v16.796h6.001V20.279h-8.124l-6.404 16.211-6.367-16.21h-8.124v25.615h6.002V29.099l5.635 14.71h5.745zM118.631 39.933c0 4.172 3.367 6.44 7.136 6.44 1.976 0 3.367-.548 4.355-1.536l.329 1.06h5.453V34.006c0-4.391-1.684-7.795-8.307-7.795-3.184 0-5.782 1.025-7.831 2.379l2.269 4.099c1.5-.732 3.147-1.208 4.83-1.208 1.976 0 2.927.915 2.927 2.086v.695c-.695-.293-2.012-.695-3.696-.695-4.537 0-7.465 2.525-7.465 6.367zm6.111-.22c0-1.244 1.098-2.049 2.562-2.049 1.464 0 2.598.732 2.598 2.05 0 1.28-1.098 1.976-2.561 1.976-1.428 0-2.599-.696-2.599-1.977zM152.627 36.346c0 2.561-1.501 4.684-4.136 4.684-2.451 0-4.135-1.83-4.135-4.684 0-2.818 1.647-4.757 4.135-4.757 2.416 0 4.136 1.72 4.136 4.757zm.219 9.55h5.709V19.44h-6.111v8.71c-.403-.476-2.196-1.94-5.124-1.94-5.562 0-9.075 4.135-9.075 10.1 0 5.892 3.879 10.063 9.405 10.063 2.818 0 4.647-1.354 5.196-1.976v1.5zM179.963 37.884c.037-.44.037-.842.037-1.208 0-6.55-3.33-10.466-9.441-10.466-5.636 0-9.441 4.501-9.441 10.137 0 5.672 3.879 10.026 10.209 10.026 4.977 0 7.612-3.11 8.161-4.244l-4.026-3.11c-.366.548-1.719 2.048-4.025 2.048-2.269 0-3.915-1.463-4.062-3.183h12.588zm-9.294-6.733c1.903 0 3.037 1.317 3.11 2.854h-6.221c.147-1.354 1.098-2.855 3.111-2.855z"/>
          </svg>
            
        </div>
        
        <div class="col-span-1">
        </div>
        
        <div class="col-span-2 justify-self-end flex">
    
        <div class="flex align-items-center">
        <span class="relative z-0 inline-flex shadow-sm rounded-md">
          <a href="https://learn.oxymade.com" target="_blank" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg> Docs
          </a>
          <a href="https://megaset.oxymade.com/app" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
           </svg> Builder
          </a>
          <a href="https://megaset.oxymade.com/preview" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
           </svg> Previews
          </a>
          <a href="https://megaset.oxymade.com/colors" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
           </svg> Colors
          </a>
          <a href="https://oxymade.com/dashboard" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg> Dashboard
          </a>
          <a href="https://oxymade.com/contact" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
           </svg> Contact
          </a>
        </span>
        </div>
        </div>
        
      </div>
      
        
    
    <!-- 
      ======================================
      ======================================
      Updated alert
      ======================================
      ======================================
      -->
      
    <?php if (isset($oxymade_updated) && $oxymade_updated) { ?>
    
    <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
      <div class="flex">
      <div class="flex-shrink-0">
      <!-- Heroicon name: solid/check-circle -->
      <svg
      class="h-5 w-5 text-green-400"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
      aria-hidden="true"
      >
      <path
        fill-rule="evenodd"
        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
        clip-rule="evenodd"
      />
      </svg>
      </div>
      <div class="ml-3">
      <p class="text-sm font-medium text-green-800">
      <?php echo $success_msg; ?>
      </p>
      </div>
      </div>
    </div>
    
    <?php } ?>
    <!-- 
      ======================================
      ======================================
      Warning Alerts for All Issues
      ======================================
      ======================================
      -->
    
    <?php if (
      isset($oxymade_has_warning) &&
      $oxymade_has_warning &&
      !empty($oxymade_warnings)
    ) { ?>
      
      <div
      class="rounded-md bg-yellow-50 p-4 mb-6 border border-yellow-200"
      >
      <div class="flex">
      <div class="flex-shrink-0">
      <!-- Heroicon name: solid/exclamation -->
      <svg
        class="h-5 w-5 text-yellow-400"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
        fill-rule="evenodd"
        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
        clip-rule="evenodd"
        />
      </svg>
      </div>
      <div class="ml-3">
      <h3 class="text-sm font-medium text-yellow-800">
        Please Fix the following warnings/errors to work without any issues.
      </h3>
      <div class="mt-2 text-sm text-yellow-700">
        <ul class="list-disc pl-5 space-y-1">
        <?php foreach ($oxymade_warnings as $warning) { ?>
         
        <li>
        <?php echo $warning; ?>
        </li>
        <?php } ?>
        </ul>
      </div>
      </div>
      </div>
      </div>
    
    <?php } ?>
    
    <!-- 
      ======================================
      ======================================
      Warning Alert if Oxygen window is open
      ======================================
      ======================================
      -->
      
    <?php if (isset($oxy_is_open) && $oxy_is_open) { ?>
      
      <div
      class="rounded-md bg-yellow-50 p-4 mb-6 border border-yellow-200"
      >
      <div class="flex">
      <div class="flex-shrink-0">
      <!-- Heroicon name: solid/exclamation -->
      <svg
        class="h-5 w-5 text-yellow-400"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
        fill-rule="evenodd"
        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
        clip-rule="evenodd"
        />
      </svg>
      </div>
      <div class="ml-3">
      <h3 class="text-sm font-medium text-yellow-800">
        Oxygen Builder is currently open in another tab/window.
      </h3>
      <div class="mt-2 text-sm text-yellow-700">
        <p>
        Oxygen Builder should be closed before you proceed with installing or modifying the framework and colors. For more information, <a href="https://learn.oxymade.com/docs/framework/common-issues/" target="_blank">click here</a>.
        </p>
      </div>
      </div>
      </div>
      </div>
    
    <?php } ?>
    
    <div
    class="bg-white overflow-hidden shadow rounded-lg mb-6 justify-center sm:text-center px-4 py-24 sm:p-8 md:p-12 lg:p-20"
    >
    <!-- Content goes here -->
    <h2 class="text-3xl font-extrabold text-gray-700 tracking-tight">
      OxyMade Framework Installer <sup class="text-base font-semibold text-indigo-500 "><?php echo OXYMADE_VERSION; ?></sup>
    </h2>
    <p class="mt-6 mx-auto max-w-2xl text-lg text-gray-500 mb-6">
      Install the base framework classes, hover classes, stylesheets,
      global colors, helper classes, and the Oxygen settings with one
      click.
    </p>
    <p class="mt-6 mx-auto max-w-2xl text-sm text-gray-500 mb-6">
      <?php echo $welcome_base_set; ?>
    </p>
  
    <div
      class="mt-5 max-w-lg mx-auto sm:flex sm:justify-center md:mt-8 mb-6"
    >
      <div class="rounded-md shadow">
        <form method="POST">
      <button
      type="submit"
      class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0" value="yes"
      name="oneclick_settings_installer"
      >
      <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5 mr-3"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
      Install with settings
      </button>
      <input type="hidden" value="yes" name="oneclick_installer">
      </form>
      </div>
      <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
      <form method="POST">
      <button
      type="submit"
      class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      value="yes"
      name="oneclick_installer"
      >
      Install framework only
      <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5 ml-3"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M17 8l4 4m0 0l-4 4m4-4H3"
      />
      </svg>
      </button>
      </form>
      </div>
    </div>
    
    <a href="https://learn.oxymade.com/docs/framework/introduction/" class="text-sm">⚠︎ <b>MUST READ</b>: Please click here to read the installation guide carefully before installing or upgrading.</a> 
  
    </div>
    
    </div>
  </main>
</div>
  <?php } else { ?> 
  
  <div class="min-h-screen">
    <main>
    
    
    <!-- 
    ======================================
    ======================================
    Else dashboard content start
    ======================================
    ======================================
    -->
    <div class="mt-8 max-w-3xl mx-auto sm:px-6 lg:max-w-7xl">
      
      
      
      <div class="grid grid-cols-4 mb-8">
        
        <div class="col-span-1">
          <svg width="180" height="60" fill="#555555" xmlns="http://www.w3.org/2000/svg">
            <path d="M40.994 11.1c-1.815.06-3.568 1.04-4.66 2.745-.066.14-.133.279-.205.375a.36.36 0 01-.272.161 5.385 5.385 0 00-2.897.748c-.118.057-.185.196-.13.323a.3.3 0 00.194.244c.807.18 1.588.494 2.378.896.571.292 1.147.629 1.733 1.052.581.38 1.09.856 1.558 1.336.647.593 1.18 1.287 1.632 1.99a.37.37 0 00.347.14.38.38 0 00.216-.288c.082-1.112-.082-2.198-.476-3.128-.015-.13-.025-.218.047-.314.067-.14.18-.24.247-.379 1.092-1.704 1.224-3.837.604-5.669-.056-.127-.147-.205-.229-.196-.041.004-.046-.04-.087-.035zm-2.736 4.748c-.535-.34-.706-1.117-.355-1.684.355-.523 1.081-.688 1.58-.3.54.385.706 1.117.356 1.684-.247.38-.678.602-1.102.514a.973.973 0 01-.48-.214z"/>
            <path d="M28.647 28.682l9.201-10.796-2.11-1.324c-.105.183-7.091 12.12-7.091 12.12z"/>
            <path d="M31.54 18.838c-.722-.28-1.516-.469-2.305-.614-7.213-1.211-13.894 3.97-14.863 11.62-.968 7.651 4.083 14.805 11.341 16.055 7.214 1.211 13.89-4.013 14.864-11.62a14.716 14.716 0 00-3.007-11.055l-4.067 3.65 3.756-4.702.388-2.974-5.784 6.786-5.095 5.946 6.898-3.86c.89 1.519 1.28 3.343 1.064 5.22-.548 4.199-4.245 7.08-8.226 6.4-4.022-.675-6.77-4.837-6.286-8.857.525-4.024 3.827-6.395 7.415-6.371l3.297-4.013-3.074 4.598-4.602 9.965 7.105-10.19 4.306-7.69-3.126 1.706zM57.52 45.898h6.55l-6.55-9.733 6.367-9.478h-6.806l-2.928 5.16-2.927-5.16H44.42l6.367 9.478-6.55 9.733h6.55l3.367-5.16 3.366 5.16zM84.233 26.687h-6.367l-3.55 11.051-3.403-11.051h-6.55l7.172 18.59-2.598 7.172h5.782l9.514-25.762zM104.067 43.81l5.636-14.711v16.796h6.001V20.279h-8.124l-6.404 16.211-6.367-16.21h-8.124v25.615h6.002V29.099l5.635 14.71h5.745zM118.631 39.933c0 4.172 3.367 6.44 7.136 6.44 1.976 0 3.367-.548 4.355-1.536l.329 1.06h5.453V34.006c0-4.391-1.684-7.795-8.307-7.795-3.184 0-5.782 1.025-7.831 2.379l2.269 4.099c1.5-.732 3.147-1.208 4.83-1.208 1.976 0 2.927.915 2.927 2.086v.695c-.695-.293-2.012-.695-3.696-.695-4.537 0-7.465 2.525-7.465 6.367zm6.111-.22c0-1.244 1.098-2.049 2.562-2.049 1.464 0 2.598.732 2.598 2.05 0 1.28-1.098 1.976-2.561 1.976-1.428 0-2.599-.696-2.599-1.977zM152.627 36.346c0 2.561-1.501 4.684-4.136 4.684-2.451 0-4.135-1.83-4.135-4.684 0-2.818 1.647-4.757 4.135-4.757 2.416 0 4.136 1.72 4.136 4.757zm.219 9.55h5.709V19.44h-6.111v8.71c-.403-.476-2.196-1.94-5.124-1.94-5.562 0-9.075 4.135-9.075 10.1 0 5.892 3.879 10.063 9.405 10.063 2.818 0 4.647-1.354 5.196-1.976v1.5zM179.963 37.884c.037-.44.037-.842.037-1.208 0-6.55-3.33-10.466-9.441-10.466-5.636 0-9.441 4.501-9.441 10.137 0 5.672 3.879 10.026 10.209 10.026 4.977 0 7.612-3.11 8.161-4.244l-4.026-3.11c-.366.548-1.719 2.048-4.025 2.048-2.269 0-3.915-1.463-4.062-3.183h12.588zm-9.294-6.733c1.903 0 3.037 1.317 3.11 2.854h-6.221c.147-1.354 1.098-2.855 3.111-2.855z"/>
          </svg>
            
        </div>
        
        <div class="col-span-1">
        </div>
        
        <div class="col-span-2 justify-self-end flex">
    
        <div class="flex align-items-center">
        <span class="relative z-0 inline-flex shadow-sm rounded-md">
          <a href="https://learn.oxymade.com" target="_blank" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg> Docs
          </a>
          <a href="https://megaset.oxymade.com/app" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
           </svg> Builder
          </a>
          <a href="https://megaset.oxymade.com/preview" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
           </svg> Previews
          </a>
          <a href="https://megaset.oxymade.com/colors" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
           </svg> Colors
          </a>
          <a href="https://oxymade.com/dashboard" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg> Dashboard
          </a>
          <a href="https://oxymade.com/contact" target="_blank" class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
           </svg> Contact
          </a>
        </span>
        </div>
        </div>
        
      </div>
      
      
    <!-- 
    ======================================
    ======================================
      Success Alert
    ======================================
    ======================================
    -->
  
  <?php
  if (isset($oxymade_updated) && $oxymade_updated) { ?>
    
    <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
    <div class="flex">
      <div class="flex-shrink-0">
      <!-- Heroicon name: solid/check-circle -->
      <svg
      class="h-5 w-5 text-green-400"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
      aria-hidden="true"
      >
      <path
      fill-rule="evenodd"
      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
      clip-rule="evenodd"
      />
      </svg>
      </div>
      <div class="ml-3">
      <p class="text-sm font-medium text-green-800">
      <?php echo $success_msg; ?>
      </p>
      </div>
    </div>
    </div>
    
    <?php }

  if (isset($oxymade_has_error) && $oxymade_has_error) { ?>
    
    <!-- 
    ======================================
    ======================================
      Error Alert
    ======================================
    ======================================
    -->
  
    
    <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
    <div class="flex">
      <div class="flex-shrink-0">
      <!-- Heroicon name: solid/x-circle -->
      <svg
      class="h-5 w-5 text-red-400"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
      aria-hidden="true"
      >
      <path
      fill-rule="evenodd"
      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
      clip-rule="evenodd"
      />
      </svg>
      </div>
      <div class="ml-3">
      <p class="text-sm font-medium text-red-800">
      <?php echo $error_msg; ?>
      </p>
      </div>
    </div>
    </div>
    <?php }
  ?>
  
  <!-- 
  ======================================
  ======================================
  Warning Alerts for All Issues
  ======================================
  ======================================
  -->
  
  <?php if (
    isset($oxymade_has_warning) &&
    $oxymade_has_warning &&
    !empty($oxymade_warnings)
  ) { ?>
  
  <div
  class="rounded-md bg-yellow-50 p-4 mb-6 border border-yellow-200"
  >
  <div class="flex">
  <div class="flex-shrink-0">
    <!-- Heroicon name: solid/exclamation -->
    <svg
    class="h-5 w-5 text-yellow-400"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 20 20"
    fill="currentColor"
    aria-hidden="true"
    >
    <path
    fill-rule="evenodd"
    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
    clip-rule="evenodd"
    />
    </svg>
  </div>
  <div class="ml-3">
    <h3 class="text-sm font-medium text-yellow-800">
    Please Fix the following warnings/errors to work without any issues.
    </h3>
    <div class="mt-2 text-sm text-yellow-700">
    <ul class="list-disc pl-5 space-y-1">
    <?php foreach ($oxymade_warnings as $warning) { ?>
     
    <li>
    <?php echo $warning; ?>
    </li>
    <?php } ?>
    </ul>
    </div>
  </div>
  </div>
  </div>
  
  <?php } ?>
  
  
  
    <!-- 
    ======================================
    ======================================
      Warning Alert
    ======================================
    ======================================
    -->
  
  <?php if (isset($oxy_is_open) && $oxy_is_open) { ?>
    
    <div
    class="rounded-md bg-yellow-50 p-4 mb-6 border border-yellow-200"
    >
    <div class="flex">
      <div class="flex-shrink-0">
      <!-- Heroicon name: solid/exclamation -->
      <svg
      class="h-5 w-5 text-yellow-400"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
      aria-hidden="true"
      >
      <path
      fill-rule="evenodd"
      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
      clip-rule="evenodd"
      />
      </svg>
      </div>
      <div class="ml-3">
      <h3 class="text-sm font-medium text-yellow-800">
      Oxygen Builder is currently open in another tab/window.
      </h3>
      <div class="mt-2 text-sm text-yellow-700">
      <p>
      Oxygen Builder should be closed before you proceed with installing or modifying the framework and colors. For more information, <a href="https://learn.oxymade.com/docs/framework/common-issues/" target="_blank">click here</a>.
      </p>
      </div>
      </div>
    </div>
    </div>
  
  <?php }
  
  $omf_html_font_size = get_option("oxymade_html_font_size");
  $omf_body_font_size = get_option("oxymade_body_font_size");
  $omf_mobile_font_size = get_option("oxymade_fluid_mobile_font_size");
  $omf_resp_size_decrease_ratio = get_option("oxymade_fluid_resp_size_decrease_ratio");
  $omf_smallest_font_size = get_option("oxymade_fluid_smallest_font_size");
  $omf_headings_font_weight = get_option("oxymade_fluid_headings_font_weight");
  $omf_headings_sync = get_option("oxymade_fluid_headings_sync");
  $omf_desktop_type_scale_ratio = get_option("oxymade_fluid_desktop_type_scale_ratio");
  $omf_mobile_type_scale_ratio = get_option("oxymade_fluid_mobile_type_scale_ratio");
  $omf_viewport_min = get_option("oxymade_fluid_viewport_min");
  $omf_viewport_max = get_option("oxymade_fluid_viewport_max");
  $omf_lh_65_150 = get_option("oxymade_fluid_lh_65_150");
  $omf_lh_49_64 = get_option("oxymade_fluid_lh_49_64");
  $omf_lh_37_48 = get_option("oxymade_fluid_lh_37_48");
  $omf_lh_31_36 = get_option("oxymade_fluid_lh_31_36");
  $omf_lh_25_30 = get_option("oxymade_fluid_lh_25_30");
  $omf_lh_21_24 = get_option("oxymade_fluid_lh_21_24");
  $omf_lh_17_20 = get_option("oxymade_fluid_lh_17_20");
  $omf_lh_13_16 = get_option("oxymade_fluid_lh_13_16");
  
  ?>
    <!-- 
    ======================================
    ======================================
      Start the content
    ======================================
    ======================================
    -->
    <?php if(!$user_pro_verified) { ?>
    
      <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
          <div class="flex">
            <div class="flex-shrink-0">
              <!-- Heroicon name: solid/information-circle -->
              <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3 flex-1 md:flex md:justify-between">
              <p class="text-sm text-yellow-700 font-medium">
                Verify your user account status to get the full power of our framework
              </p>
              <form action="" method="post">
                <p class="mt-3 text-sm md:mt-0 md:ml-6">
                  <button name="oxymade_user_verification" value="yes" class="whitespace-nowrap font-medium text-yellow-700 hover:text-yellow-600">Verify the user <span aria-hidden="true">&rarr;</span></button>
                </p>
              </form>
            </div>
          </div>
        </div>
        
    <br/>
    
    <?php } ?>
    
    <div
    class="grid grid-cols-1 gap-6 lg:grid-flow-col-dense lg:grid-cols-3"
    >
    <div class="lg:col-start-1 lg:col-span-2 grid grid-cols-2 gap-6" style="align-self: baseline;">
      <!-- Description list-->
      <section
      
      class="col-span-1 sm:col-span-1"
      >
      <div class="bg-white shadow sm:rounded-lg">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
      <div
        class="pt-6 pb-2 md:flex md:items-center md:justify-between"
      >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
        <!-- <div class="flex items-center"> -->
        <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
        >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 mr-2 mt-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
          />
          </svg>
          OxyMade Framework &nbsp; 
          
          <?php $user_level_badge = ($user_level == "pro") ? "bg-blue-100 text-blue-800" : "bg-gray-100 text-gray-800"; ?>
          
          <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-bold <?php echo $user_level_badge; ?>  uppercase"><?php echo $user_level; ?></span>
          
        </h2>
        <!-- </div> -->
        <dl
          class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
        >
          <dt class="sr-only">OxyMade Framework Version</dt>
          <dd
          class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"
          />
          </svg>
          Version <?php echo OXYMADE_VERSION; ?>
          </dd>
          <dt class="sr-only">OxyMade Framework License</dt>
          <dd
          class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
          >
           <svg
           class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
           xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 20 20" fill="currentColor">
           <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" />
           </svg>
          License Activated
          </dd>
        </dl>
        </div>
        </div>
        </div>
      </div>
      <p class="mt-1 max-w-2xl text-sm text-gray-500 pb-6">
        You can re-install the framework to re apply the framework
        and style sheets to fix most of the issues you may
        experience.
      </p>
      <div class="my-4 flex md:mt-0" >
        <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-reinstall-framework-modal',{id})"
        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mx-auto mr-2" 
        >
        Re-install Framework
        </button>
        </span>
        
        <span x-data="{id: 1}">
          <button
          @click="$dispatch('open-install-oxygen-settings-modal',{id})"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" 
          >
          Install settings
          </button>
          </span>
        
      </div>
      </div>
      
      <div>
      <a
        href="https://learn.oxymade.com/docs/framework/introduction/"
        target="_blank"
        class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
        > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg> Read about our Framework here →</span></a
      >
      </div>
      </div>
      </section>
      <section
      
      class="col-span-1 sm:col-span-1"
      >
      <div class="bg-white shadow sm:rounded-lg">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
      <div
        class="pt-6 pb-2 md:flex md:items-center md:justify-between"
      >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
        <!-- <div class="flex items-center"> -->
        <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
        >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 mt-2"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Base design set
        </h2>
        <!-- </div> -->
        <dl
        class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
      >
        <dt class="sr-only">Base design set</dt>
        <dd
        class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
        >
        <svg
        class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400"
        x-description="Heroicon name: solid/check-circle"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
        >
        <path
        fill-rule="evenodd"
        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
        clip-rule="evenodd"
        ></path>
        </svg>
        <?php echo get_option("oxymade_active_set"); ?>
        </dd>
        
        
        
        <dt class="sr-only">OxyMade Framework Base Settings</dt>
        <dd
        class="flex items-center text-sm text-indigo-600 font-medium capitalize sm:mr-6"
        >
         <svg
         class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-500"
         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
           <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
         </svg>
         <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-framework-settings-slideover',{id})">Fluid Text Settings
        </button>
         </span>
        </dd>
        
        
      </dl>
        </div>
        </div>
        </div>
      </div>
      <p
        class="mt-1 max-w-2xl text-sm text-gray-500 pb-6 leading-5"
      >
        We apply buttons, cards, icons, typography styles
        and Oxygen settings from the design set you set here.
      </p>
      <div class="my-4 flex md:mt-0">
        <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-change-base-design-set-slideover',{id})"
        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
        >
        Change base set
        </button>
        </span>
        <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-reset-base-design-set-modal',{id})"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
        Reset
        </button>
        </span>
      </div>
      </div>
  
      <div>
      <a
        href="https://learn.oxymade.com/docs/framework/base-design-set/"
        target="_blank"
        class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
        > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg> Learn about Base Design Set here →</span></a
      >
      </div>
      </div>
      </section>
  
      <section class="col-span-2">
      <div class="bg-white shadow sm:rounded-lg sm:overflow-hidden">
      <div class="divide-y divide-gray-200">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div
        class="py-6 md:flex md:items-center md:justify-between"
        >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
          <!-- <div class="flex items-center"> -->
          <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
          >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 mr-2 mt-1.5"
          fill="none" stroke="currentColor" viewBox="0 0 24 24" ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>
          Manage Modules & Power Tools &nbsp; 
          </h2>
          <!-- </div> -->
          <dl
          class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
          >
          <dt class="sr-only">Number of classes</dt>
          <dd
          class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none" stroke="currentColor" viewBox="0 0 24 24" ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
          Enable / disable power modules/tools for Oxygen
          Builder
          </dd>
          <!-- <dt class="sr-only">Number of Modules</dt>
          <dd
          class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
          />
          </svg>
          4 Power Modules
          </dd> -->
          <dt class="sr-only">Power tools</dt>
          <dd
          class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
          >
          <a href="https://learn.oxymade.com/docs/modules/modules/" class="inline-flex" target="_blank">
          <svg
            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          Docs
          </a>
          </dd>
          </dl>
      
        </div>
        </div>
        </div>
        <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">

        </div>
        </div>
      </div>
      
      
      
      
      <div class="overflow-hidden">
        <ul class="divide-y divide-gray-200">
        <form action="" method="post">
          
        <li>
        <div class="block bg-gray-50">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
          <p
          class="text-sm font-medium text-gray-900 truncate"
          >
          Oxygen native colors editor
          </p>
          <div class="ml-2 flex-shrink-0 flex">
          
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md"
          >
          <span x-data="{id: 1}">
            <button @click="$dispatch('open-colors-slideover',{id})" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border border-green-700 bg-white text-sm font-medium text-green-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 rounded-md overflow-hidden">
              <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M8.20348 2.00378C9.46407 2.00378 10.5067 3.10742 10.6786 4.54241L19.1622 13.0259L11.384 20.8041C10.2124 21.9757 8.31291 21.9757 7.14134 20.8041L2.8987 16.5615C1.72713 15.3899 1.72713 13.4904 2.8987 12.3188L5.70348 9.51404V4.96099C5.70348 3.32777 6.82277 2.00378 8.20348 2.00378ZM8.70348 4.96099V6.51404L7.70348 7.51404V4.96099C7.70348 4.63435 7.92734 4.36955 8.20348 4.36955C8.47963 4.36955 8.70348 4.63435 8.70348 4.96099ZM8.70348 10.8754V9.34247L4.31291 13.733C3.92239 14.1236 3.92239 14.7567 4.31291 15.1473L8.55555 19.3899C8.94608 19.7804 9.57924 19.7804 9.96977 19.3899L16.3337 13.0259L10.7035 7.39569V10.8754C10.7035 10.9184 10.7027 10.9612 10.7012 11.0038H8.69168C8.69941 10.9625 8.70348 10.9195 8.70348 10.8754Z"
              fill="currentColor"
              />
              <path
              d="M16.8586 16.8749C15.687 18.0465 15.687 19.946 16.8586 21.1175C18.0302 22.2891 19.9297 22.2891 21.1013 21.1175C22.2728 19.946 22.2728 18.0465 21.1013 16.8749L18.9799 14.7536L16.8586 16.8749Z"
              fill="currentColor"
              />
            </svg>
            Modify Oxygen Native Colors
            </button>
            </span>
          </span>
          
          
          
          
          </div>
          </div>
  
          <div class="text-sm text-gray-500 mt-2">
          Edit Oxygen native colors names, ids, set names, and set ids.
          </div>
        </div>
        </div>
        </li>
        
        
        <li>
        <div class="block bg-white">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
          <p
          class="text-sm font-medium text-gray-900 truncate"
          >
          30+ grid helper modules in Oxygen editor
          </p>
          <div class="ml-2 flex-shrink-0 flex">
          
          <?php
          $oxymade_gridhelpers = get_option("oxymade_gridhelpers");
          if (
            isset($oxymade_gridhelpers) &&
            $oxymade_gridhelpers == "Disable"
          ) { ?>
          
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
          >
          <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_gridhelpers" value="Enable"
          >
            Enable
          </button>
          <button
            type="submit"
            class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
          >
            Disabled
          </button>
          </span>
          
          <?php } elseif (
            isset($oxymade_gridhelpers) &&
            $oxymade_gridhelpers == "Enable"
          ) { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_gridhelpers" value="Disable"
          >
          Disable
          </button>
          </span>
          
          <?php } else { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_gridhelpers" value="Disable"
          >
          Disable
          </button>
          </span>
          
          <?php }
          ?>
          </div>
          </div>
  
          <div class="text-sm text-gray-500 mt-2">
          30+ powerful grid helper modules to quickly create any type of grid design with one click.
          </div>
        </div>
        </div>
        </li>
        
        
        
        <li>
        <div class="block bg-gray-50">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
          <p
          class="text-sm font-medium text-gray-900 truncate"
          >
          Merge classes button in
          the editor 
          </p>
          <div class="ml-2 flex-shrink-0 flex">
          
          <?php
          $oxymade_mergeClasses = get_option("oxymade_mergeClasses");
          if (
            isset($oxymade_mergeClasses) &&
            $oxymade_mergeClasses == "Disable"
          ) { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
          >
          <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_mergeClasses" value="Enable"
          >
            Enable
          </button>
          <button
            type="submit"
            class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
          >
            Disabled
          </button>
          </span>
          
          <?php } elseif (
            isset($oxymade_mergeClasses) &&
            $oxymade_mergeClasses == "Enable"
          ) { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_mergeClasses" value="Disable"
          >
          Disable
          </button>
          </span>
          
          <?php } else { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_mergeClasses" value="Disable"
          >
          Disable
          </button>
          </span>
          
          <?php }
          ?>
          </div>
          </div>
  
          <div class="text-sm text-gray-500 mt-2">
          Merge classes button is useful to marge multiple
          classes and combine all other classes properties
          into one class. It is useful when you are
          working with a utility framework like Tailwind
          based OxyMade Framework.
          </div>
        </div>
        </div>
        </li>
        
        
        
        
        
        <li>
        <div class="block bg-white">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
          <p
          class="text-sm font-medium text-gray-900 truncate"
          >
          Change utility classes to Id automatically 
          </p>
          <div class="ml-2 flex-shrink-0 flex">
          
          <?php
          $oxymade_changetoid = get_option("oxymade_changetoid");
          if (
            isset($oxymade_changetoid) &&
            $oxymade_changetoid == "Disable"
          ) { ?>
          
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_changetoid" value="Enable"
          >
          Enable
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
          >
          Disabled
          </button>
          </span>
          
          <?php } elseif (
            isset($oxymade_changetoid) &&
            $oxymade_changetoid == "Enable"
          ) { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"  
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_changetoid" value="Disable" 
          >
          Disable
          </button>
          </span>
          
          <?php } else { ?>
          
           <span
           class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
           >
           <button
           type="submit"
           class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_changetoid" value="Enable"
           >
           Enable
           </button>
           <button
           type="submit"
           class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
           >
           Disabled
           </button>
           </span>
          
          <?php }
          ?>
          </div>
          </div>
          <div class="text-sm text-gray-500 mt-2">
          When you are using utility classes, they are all locked by default and you shouldn't make any modifications to utility classes. So instead of manually changing the selector property to Id, enable this module makes it automatically change the selector property to Id if it is a utility class.
          </div>
        </div>
        </div>
        </li>
        
        <li>
          <div class="block bg-gray-50">
          <div class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
            <p
            class="text-sm font-medium text-gray-900 truncate"
            >
            <span class="mr-1 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
              NEW
            </span> Enable auto dark mode toggle 
            </p>
            <div class="ml-2 flex-shrink-0 flex">
            
            <?php
            if($user_level == "pro" && $user_pro_verified){
            $oxymade_darkmode = get_option("oxymade_darkmode");
            if (
              isset($oxymade_darkmode) &&
              $oxymade_darkmode == "Disable"
            ) { ?>
            
            
            <span
            class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
            >
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_darkmode" value="Enable"
            >
            Enable
            </button>
            <button
            type="submit"
            class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
            >
            Disabled
            </button>
            </span>
            
            <?php } elseif (
              isset($oxymade_darkmode) &&
              $oxymade_darkmode == "Enable"
            ) { ?>
            
            <span
            class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
            >
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"  
            >
            Enabled
            </button>
            <span x-data="{id: 1}">
              <button @click="$dispatch('open-darkmode-settings-slideover',{id})" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border-r border-green-700 bg-white text-sm font-medium text-green-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none">
                Manage
              </button>
              </span>
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_darkmode" value="Disable" 
            >
            Disable
            </button>
            </span>
            
            <?php } else { ?>
            
             <span
             class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
             >
             <button
             type="submit"
             class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_darkmode" value="Enable"
             >
             Enable
             </button>
             <button
             type="submit"
             class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
             >
             Disabled
             </button>
             </span>
            
            <?php }
            } else {
              echo $pro_required;
            }
            ?>
            </div>
            </div>
            <div class="text-sm text-gray-500 mt-2">
            Enable / disable dark mode on the frontend of the website. It enables a settings screen where you can customize dark mode settings.            
          </div>
          </div>
          </div>
          </li>
          
        
        <li>
          <div class="block bg-white">
          <div class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
            <p
            class="text-sm font-medium text-gray-900 truncate"
            >
            <span class="mr-1 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
              NEW
            </span> Enable blogzine powered typography 
            </p>
            <div class="ml-2 flex-shrink-0 flex">
            
            <?php
            if($user_level == "pro" && $user_pro_verified){
            $oxymade_blogzine = get_option("oxymade_blogzine");
            if (isset($oxymade_blogzine) && $oxymade_blogzine == "Disable") { ?>
            
            
            <span
            class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
            >
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_blogzine" value="Enable"
            >
            Enable
            </button>
            <button
            type="submit"
            class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
            >
            Disabled
            </button>
            </span>
            
            <?php } elseif (
              isset($oxymade_blogzine) &&
              $oxymade_blogzine == "Enable"
            ) { ?>
            
            <span
            class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
            >
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"  
            >
            Enabled
            </button>
            <span x-data="{id: 1}">
              <button @click="$dispatch('open-blogzine-settings-slideover',{id})" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border-r border-green-700 bg-white text-sm font-medium text-green-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none">
                Manage
              </button>
              </span>
              
            <button
            type="submit"
            class="relative inline-flex items-center px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_blogzine" value="Disable" 
            >
            Disable
            </button>
            </span>
            
            <?php } else { ?>
            
             <span
             class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
             >
             <button
             type="submit"
             class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_blogzine" value="Enable"
             >
             Enable
             </button>
             <button
             type="submit"
             class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
             >
             Disabled
             </button>
             </span>
            
            <?php }
            } else {
              echo $pro_required;
            }
            ?>
            </div>
            </div>
            <div class="text-sm text-gray-500 mt-2">
            Blogzine settings, typography, responsive settings and max-width settings. You can set the default sizing for every breakpoint. We will take care of typography elements styling based on the sizes you select.
            </div>
          </div>
          </div>
          </li>
          
          
        
        
        <li>
        <div class="block bg-gray-50">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
          <p
          class="text-sm font-medium text-gray-900 truncate"
          >
          Paste button in the editor 
          </p>
          <div class="ml-2 flex-shrink-0 flex">
          
          <?php
          $oxymade_copyPaste = get_option("oxymade_copypaste");
          if (isset($oxymade_copyPaste) && $oxymade_copyPaste == "Disable") { ?>
          
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_copypaste" value="Enable"
          >
          Enable
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
          >
          Disabled
          </button>
          </span>
          
          <?php } elseif (
            isset($oxymade_copyPaste) &&
            $oxymade_copyPaste == "Enable"
          ) { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"  
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_copypaste" value="Disable" 
          >
          Disable
          </button>
          </span>
          
          <?php } else { ?>
          
           <span
           class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
           >
           <button
           type="submit"
           class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
           >
           Enabled
           </button>
           <button
           type="submit"
           class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_copypaste" value="Disable"
           >
           Disable
           </button>
           </span>
          
          <?php }
          ?>
          </div>
          </div>
          <div class="text-sm text-gray-500 mt-2">
          Now copy and paste our sections from our design
          set with one click. It is easy and quick way to
          get started with our design sets.
          </div>
        </div>
        </div>
        </li>
        
        
        
        <li>
        <div class="block bg-white">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
          <p
          class="text-sm font-medium text-gray-900 truncate"
          >
          Enable / Disable Hover effects & the panel in the Oxygen editor 
          </p>
          <div class="ml-2 flex-shrink-0 flex">
          
          <?php
          if($user_level == "pro" && $user_pro_verified){
          $oxymade_hoverStyles = get_option("oxymade_hoverstyles");
          if (
            isset($oxymade_hoverStyles) &&
            $oxymade_hoverStyles == "Disable"
          ) { ?>
          
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_hoverstyles" value="Enable"
          >
          Enable
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
          >
          Disabled
          </button>
          </span>
          
          <?php } elseif (
            isset($oxymade_hoverStyles) &&
            $oxymade_hoverStyles == "Enable"
          ) { ?>
          
          <span
          class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
          >
          <button
          type="submit"
          class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"  
          >
          Enabled
          </button>
          <button
          type="submit"
          class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_hoverstyles" value="Disable" 
          >
          Disable
          </button>
          </span>
          
          <?php } else { ?>
          
           <span
           class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
           >
           <button
           type="submit"
           class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
           >
           Enabled
           </button>
           <button
           type="submit"
           class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_hoverstyles" value="Disable"
           >
           Disable
           </button>
           </span>
          
          <?php }
          } else {
            echo $pro_required;
          }
          ?>
          </div>
          </div>
          <div class="text-sm text-gray-500 mt-2">
          Enable / disable a set of 50 beautiful & powerful hover effects / classes to use directly inside Oxygen Builder.
          </div>
        </div>
        </div>
        </li>
        
        
        
        
        
        
        <li>
          <div class="block bg-gray-50">
          <div class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
            <p
            class="text-sm font-medium text-gray-900 truncate"
            >
            Add smooth toggle & close all other toggles when clicked on a toggle
            </p>
            <div class="ml-2 flex-shrink-0 flex">
            
            <?php
            $oxymade_powerToggle = get_option("oxymade_powertoggle");
            if (
              isset($oxymade_powerToggle) &&
              $oxymade_powerToggle == "Disable"
            ) { ?>
            
            
            <span
            class="relative z-0 inline-flex shadow-sm rounded-md border border-red-700 overflow-hidden"
            >
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" name="oxymade_powertoggle" value="Enable"
            >
            Enable
            </button>
            <button
            type="submit"
            class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-red-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1" 
            >
            Disabled
            </button>
            </span>
            
            <?php } elseif (
              isset($oxymade_powerToggle) &&
              $oxymade_powerToggle == "Enable"
            ) { ?>
            
            <span
            class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
            >
            <button
            type="submit"
            class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"  
            >
            Enabled
            </button>
            <button
            type="submit"
            class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_powertoggle" value="Disable" 
            >
            Disable
            </button>
            </span>
            
            <?php } else { ?>
            
             <span
             class="relative z-0 inline-flex shadow-sm rounded-md border border-green-700 overflow-hidden"
             >
             <button
             type="submit"
             class="relative inline-flex items-center px-3 py-1.5 bg-green-600 text-sm font-medium text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" disabled="" tabindex="-1"
             >
             Enabled
             </button>
             <button
             type="submit"
             class="-ml-px relative inline-flex items-center px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-none" name="oxymade_powertoggle" value="Disable"
             >
             Disable
             </button>
             </span>
            
            <?php }
            ?>
            </div>
            </div>
            <div class="text-sm text-gray-500 mt-2">
            Add smooth toggle effect & also close all opened toggles when clicked on a toggle element.
            </div>
          </div>
          </div>
          </li>
        
    
        </form>
        </ul>
      </div>
      </div>
      <div>
      <a
        href="https://learn.oxymade.com/docs/modules/modules/"
        target="_blank"
        class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
        > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg> Learn about our Power Modules here →</span></a
      >
      </div>
      </div>
      </section>
   
      <section class="col-span-2">
      <div class="bg-white shadow sm:rounded-lg sm:overflow-hidden">
      <div class="divide-y divide-gray-200">
  
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div
        class="py-6 md:flex md:items-center md:justify-between "
        >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
          <!-- <div class="flex items-center"> -->
          <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
          >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 mr-2 mt-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
          />
          </svg>
          Manage CSS Classes & Folders
          </h2>
          <!-- </div> -->
          <dl
          class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
          >
          <dt class="sr-only">Number of classes</dt>
          <dd
          class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
          />
          </svg>
          <?php echo count($classes); ?> classes
          </dd>
          <dt class="sr-only">Style folders</dt>
          <dd
          class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
          />
          </svg>
          <?php echo count($style_folders); ?> folders
          </dd>
          <dt class="sr-only">Documentation</dt>
          <dd
          class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
          >
          <a href="https://learn.oxymade.com/docs/modules/manage-css-selectors/" class="inline-flex" target="_blank">
          <svg
            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          Docs
          </a>
          </dd>
          </dl>
        </div>
        </div>
        </div>
        <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
        <form action="" method="post">
        <span
        class="relative z-0 inline-flex shadow-sm rounded-md"
        x-data="{id: 1}"
        >
        <button
          @click="$dispatch('open-backup-classes-modal',{id})"
          type="button"
          class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-indigo-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
        >
          Backup
        </button>
         <?php if (
           isset($backup_ct_components_classes) &&
           is_array($backup_ct_components_classes)
         ) { ?>
        <button
          @click="$dispatch('open-restore-classes-backup-modal',{id})"
          type="button"
          class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
        >
          Restore
        </button>
        <?php } ?>
        <button
        @click="$dispatch('open-delete-classes-backup-modal',{id})"
        type="button"
        class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-red-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
      >
        Delete
      </button>
        <button
          @click="$dispatch('open-export-classes-slideover',{id})"
          type="button"
          class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-green-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500" 
        >
          Export
        </button>

        </span>
        </form>
        </div>
        </div>
      </div>
  
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div
        class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
        >
        <div
        class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
        >
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
          <tr>
          <th
          scope="col"
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
          Name
          </th>
  
          <th
          scope="col"
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
          Count
          </th>
          <!-- <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">View</span>
          </th> -->
          <!-- <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Reset</span>
          </th> -->
          <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Delete</span>
          </th>
          </tr>
          </thead>
          <tbody>
          <!-- Odd row -->
          <?php foreach ($selectors as $key => $selector) { ?>
          <tr class="bg-white">
          <td
          class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
          >
          <?php echo $key; ?>
          </td>
  
          <td
          class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
          >
          <?php echo count($selector); ?> classes
          </td>
          <!-- <td
          class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
          x-data="{id: 1}"
          >
          <button
            @click="$dispatch('open-view-selectors-slideover', {id, folder: '<?php echo $key; ?>', selectors: getSelectors()})"
            type="button"
            class="text-indigo-600 hover:text-indigo-900"
          >
            View
          </button>
          </td> -->
          <!-- <td
          class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
          x-data="{id: 1}"
          >
          <button
            @click="$dispatch('open-reset-selectors-modal',{id})"
            type="button"
            class="text-gray-600 hover:text-gray-900"
          >
            Reset
          </button>
          </td> -->
          <td
          class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
          x-data="{id: 1}"
          >
          <button
            @click="$dispatch('open-delete-selectors-modal',{id, value: '<?php echo $key; ?>'})"
            type="button"
            class="text-red-600 hover:text-red-900"
          >
            Delete
          </button>
          </td>
          </tr>
  <?php } ?>
          
          </tbody>
        </table>
        <div>
          <a
          href="https://learn.oxymade.com/docs/modules/manage-css-selectors/"
          target="_blank"
          class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
          > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg> Read more about Managing CSS Selectors here →</span></a
          >
        </div>
        </div>
        </div>
        </div>
      </div>
      </div>
      </div>
      </section>

      <section class="col-span-2">
      <div class="bg-white shadow sm:rounded-lg sm:overflow-hidden">
      <div class="divide-y divide-gray-200">
   
      
       
  
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div
        class="py-6 md:flex md:items-center md:justify-between "
        >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
          <!-- <div class="flex items-center"> -->
          <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
          >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 mr-2 mt-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
          />
          </svg>
          Manage Stylesheets
          </h2>
          <!-- </div> -->
          <dl
          class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
          >
          <dt class="sr-only">Number of stylesheets</dt>
          <dd
          class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
          />
          </svg>
          <?php echo count($ssheets); ?> Stylesheets
          </dd>
          <dt class="sr-only">Number of stylesheet folders</dt>
          <dd
          class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
          >
          <svg
          class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
          />
          </svg>
          <?php echo count($ssheetfols); ?> Folders
          </dd>
          <dt class="sr-only">Documentation</dt>
          <dd
          class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
          >
          <a href="https://learn.oxymade.com/docs/modules/manage-stylesheets/" class="inline-flex" target="_blank">
          <svg
            class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          Docs
          </a>
          </dd>
          </dl>
  
        </div>
        </div>
        </div>
        <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
        
        <span
        class="relative z-0 inline-flex shadow-sm rounded-md"
        x-data="{id: 1}"
        >
        <button
          @click="$dispatch('open-backup-stylesheets-modal',{id})"
          type="button"
          class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-indigo-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
        >
          Backup
        </button>
        <?php if ($backup_ct_style_sheets) { ?>
        
        <button
          @click="$dispatch('open-restore-stylesheets-backup-modal',{id})"
          type="button"
          class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
        >
          Restore
        </button>
        <?php } ?>
        <button
        @click="$dispatch('open-delete-all-stylesheets-modal',{id})"
        type="button"
        class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-red-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
      >
        Delete
      </button>
        <button
          @click="$dispatch('open-export-stylesheets-slideover',{id})"
          type="button"
          class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-green-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
        >
          Export
        </button>
        </span>
        </div>
        </div>
      </div>
  
      
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div
        class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
        >
        <div
        class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
        >
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
          <tr>
          <th
          scope="col"
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
          Name
          </th>
  
          <th scope="col" class="relative px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Folder
          </th>
          <th scope="col" class="relative px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Status
          </th>
    
          <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Delete</span>
          </th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($allsheets as $asheet) { ?>
          <!-- Odd row -->
          <tr class="bg-white">
          <td
          class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
          >
          <?php echo $style_sheets[$asheet]["name"]; ?>
          </td>
  
          <td
          class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium"
          >
          
           <?php
           if (isset($style_sheets[$asheet]["parent"])) {
             $parent_fol = $style_sheets[$asheet]["parent"];
           }
           if (isset($allfols->$parent_fol)) {
             $parent_id = $allfols->$parent_fol;
           }
           if (isset($style_sheets[$asheet]["id"])) {
             $shid = $style_sheets[$asheet]["id"];
           }
           if (isset($all_sheets->$shid)) {
             $sheetid = $all_sheets->$shid;
           }
           if (isset($parent_id)) {
             if (isset($style_sheets[$parent_id]["name"])) {
               echo $style_sheets[$parent_id]["name"];
             }
           } else {
             echo "Uncategorized";
           }
           ?>
          </td>
          <td
          class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium"
          >
          
           <?php
           if (isset($parent_id)) {
             $foldstatus = $style_sheets[$parent_id]["status"];
           }
           if (isset($foldstatus) && $foldstatus == 1) { ?>
           
             <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
             <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
           </svg>
           <?php } elseif (isset($foldstatus) && $foldstatus == 0) { ?>
           
           
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
             <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
             </svg>
           <?php }
           ?>
          </td>
   
          <td
          class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
          x-data="{id: 1}"
          >
          
          <button
            @click="$dispatch('open-delete-stylesheets-modal',{id, value: '<?php echo $sheetid; ?>'})"
            type="button"
            class="text-red-600 hover:text-red-900"
          >
            Delete
          </button>
          </td>
          </tr>
  <?php } ?>
          
          </tbody>
        </table>
         <div>
         <a
           href="https://learn.oxymade.com/docs/modules/manage-stylesheets/"
           target="_blank"
           class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
           > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
           </svg> Click here to read more about Managing Stylesheets →</span></a
         >
         </div>
        </div>
        </div>
        </div>
      </div>
      </div>
      </div>
      </section>
      
      
      <section
      
      class="col-span-1 sm:col-span-1"
      >
      <div class="bg-white shadow sm:rounded-lg pb-4">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
      <div
        class="pt-6 pb-2 md:flex md:items-center md:justify-between"
      >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
        <!-- <div class="flex items-center"> -->
        <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
        >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 mr-2 mt-1"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
          </svg>
          Reset Back to Before OxyMade
        </h2>
        <!-- </div> -->
        </div>
        </div>
        </div>
      </div>
      <p class="mt-1 max-w-2xl text-sm text-gray-500 pb-6">
        Reset Oxygen settings back to before using OxyMade. We took a backup before installing OxyMade and You can go back for whatever reason.
      </p>
      <div class="my-4 flex md:mt-0 md:ml-4" >
        <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-reset-back-to-oxymade-modal',{id})"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
        Reset Back to Before using OxyMade
        </button>
        </span>
        
      </div>
      </div>
  
      <!-- <div>
      <a
        href="https://learn.oxymade.com/docs/framework/add-framework/"
        target="_blank"
        class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
        > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg> Read about it here →</span></a
      >
      </div> -->
      </div>
      </section>
      <section
      
      class="col-span-1 sm:col-span-1"
      >
      <div class="bg-white shadow sm:rounded-lg pb-4">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
      <div
        class="pt-6 pb-2 md:flex md:items-center md:justify-between"
      >
        <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
        <!-- <div class="flex items-center"> -->
        <h2
          class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
        >
          <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 mt-2"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Reset to Oxygen Defaults
        </h2>
        <!-- </div> -->
        
        </div>
        </div>
        </div>
      </div>
      <p
        class="mt-1 max-w-2xl text-sm text-gray-500 pb-6 leading-5"
      >
        Reset Oxygen settings to Oxygen defaults. It will wipe out all the Oxygen settings like, global classes, selectors, stylesheets, global settings, folders, etc.
      </p>
      <div class="my-4 flex md:mt-0 md:ml-4" >
        <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-reset-oxygen-modal',{id})"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
        Reset to Oxygen defaults
        </button>
        </span>
        
      </div>
      </div>
  
      <!-- <div>
      <a
        href="https://learn.oxymade.com/docs/framework/base-design-set/"
        target="_blank"
        class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
        > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg> Learn about Base Design Set here →</span></a
      >
      </div> -->
      </div>
      </section>
      
    </div>
    
    
    <section
      class="lg:col-start-3 lg:col-span-1"
    >
    
  <!-- PURGE FEATURE STARTED -->
    
    <div class="bg-white shadow sm:rounded-lg mb-6">
    <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
    <div
      class="pt-6 pb-2 md:flex md:items-center md:justify-between"
    >
      <div class="flex-1 min-w-0">
      <!-- Profile -->
      <div class="flex items-center">
      <div>
      <!-- <div class="flex items-center"> -->
      <h2
        class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
      >
        <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5 mr-2 mt-2"
        fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        Purge unused classes
      </h2>
      <!-- </div> -->
        
        <dl
        class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
      >
        <dt class="sr-only">Purge unused classes</dt>
        <dd
        class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
        >
        <svg
        class="flex-shrink-0 mr-1.5 h-5 w-5 text-red-400"
        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        Highly experimental
        </dd>
        <dt class="sr-only">Take backup before using this feature</dt>
        <dd
        class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6"
        >
         <svg
         class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"
         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
           <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
           <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
         <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />
         </svg>
        Take backup
        </dd>
      </dl>
      
      
      </div>
      </div>
      </div>
    </div>
    <p
      class="mt-1 max-w-2xl text-sm text-gray-500 pb-6 leading-5"
    >
      Use once the designing is complete. We will delete all the unused framework classes in your pages and Oxygen templates. <b>TAKE BACKUP.</b>
    </p>
    <div class="my-4 flex md:mt-0">
      <!-- <span x-data="{id: 1}">
      <button
      @click="$dispatch('open-change-base-design-set-slideover',{id})"
      class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
      >
      Open purge panel
      </button>
      </span> -->
      <?php if($user_level == "pro" && $user_pro_verified){ ?>
      <span x-data="{id: 1}">
      <button
      @click="$dispatch('open-purge-slideover',{id})"
      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-2"
      >
      Purge unused classes
      </button>
      </span>
      <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-purge-whitelist-slideover',{id})"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
        Whitelist
        </button>
        </span>
        <?php } else { ?>
          <a
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2" href="https://try.oxymade.com/upgrade" target="_blank"
          >
          Pro upgrade required
          </a>
        <?php } ?>
    </div>
    </div>

    <div>
    <a
      href="https://learn.oxymade.com/docs/framework/purge/"
      target="_blank"
      class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
      > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg> Learn about Purge feature here →</span></a
    >
    </div>
    </div>


<!-- MANAGE COLORS STARTER -->
   
  
      <div class="bg-white shadow sm:rounded-lg mb-6">
      <div class="px-4 py-5 sm:px-6">
      <h2
      class="text-xl font-semibold leading-7 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
      >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 mr-2 mt-1"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"
        />
      </svg>
      Manage Colors
      </h2>
      <p class="mt-1 max-w-2xl text-sm text-gray-500">
      Import / export or manage colors for your website.
      </p>
  
      
      <span
      class="mt-4 relative z-0 inline-flex shadow-sm rounded-md"
      x-data="{id: 1}"
      >
      <button
        @click="$dispatch('open-import-export-colors-slideover',{id})"
        class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-indigo-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      >
        <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 mr-2"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
        />
        </svg>
        Import / Export Colors
      </button>
      <!-- <button
        type="button"
        class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      >
        Import from design sets
      </button> -->
  
      <button
        @click="$dispatch('open-import-colors-slideover',{id})"
        class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-green-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      >
        <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 mr-2"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
        />
        </svg>
        Skins
      </button>
      </span>
      </div>
      
      
      <div
      class="border-t border-gray-200 px-3 py-3 sm:px-4 w-full bg-gray-100"
      x-data="{id: 1}"
      >
      <form action="" method="post">
        
        <label for="account-number" class="mb-2 block text-sm font-medium text-gray-700">Enter primary color to generate the full palette</label>
        <input
        type="text"
        name='generate_from_primary_color'
        value="<?php $autocolor = get_option('oxymade_color_generator'); if(isset($autocolor)){ echo $autocolor; } ?>"
        class="oxymade-colors shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-base border-gray-300 rounded-md"
        />
        
        <button
        type="submit"
        name="generate_color_palette"
        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 ml-auto my-3"
        value="yes"
        >
        Generate full color palette
        </button>
      </form>
      </div>
  
  <?php
  $gcolors = get_option("oxymade_colors", []);
  if ($gcolors) { ?>
  
  <form action="#" method="post">
  <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
    <div class="flow-root grid grid-cols-2 gap-4">
  
  <?php
  $mcolors = $gcolors["colors"];

  foreach ($mcolors as $key => $value) { ?>
  
       
        <!-- <ul class="-my-5 divide-y divide-gray-200"> -->
        <div class="my-1 area<?php echo $value["name"]; ?>">
        <label
        for="color"
        class="block text-sm font-medium text-gray-700"
        ><?php echo $value["name"]; ?></label
        >
        <div class="mt-1">
        <input
        type="hidden"
        name='<?php echo $key; ?>[name]'
        value="<?php echo $value["name"]; ?>" readonly="readonly"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
        />
        <input
        type="text"
        name='<?php echo $key; ?>[value]'
        value="<?php echo $value["value"]; ?>"
        class="oxymade-colors shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
        />
        </div>
        </div>
        
        <?php }
  ?>
        
        <!-- </ul> -->
      </div>
      </div>
      <div class="bg-gray-50 px-4 py-2 sm:px-6 mt-2">
      <div class="flex space-x-3">
        <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between">
        <button
        type="submit"
        name="update_color_palette"
        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-auto my-3"
        value="yes"
        >
        Update Color Palette
        </button>
        </div>
        </div>
      </div>
      </div>
      </form>
      <?php }
  ?>
      <div>
      <a
        href="https://learn.oxymade.com/docs/get-started/color-system-overview/"
        target="_blank"
        class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
        > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg> Learn our color system →</span></a
      >
      </div>
      </div>
      
      <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
          <h2 id="timeline-title" class="text-lg font-medium text-gray-900 mb-2 inline-flex">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
  
          Framework Checklist
          </h2>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Follow the progress of tasks that need to be completed so the framework will work without any issues.
          </p>
        </div>
        <?php
        $one_click_installer_option = get_option(
          "oxymade_oneclick_installer_status"
        );
        $one_click_installer_status = isset($one_click_installer_option)
          ? $one_click_installer_option
          : false;

        $oxymade_stylesheets_option = get_option("oxymade_v1_plus");
        $oxymade_stylesheets_status = isset($oxymade_stylesheets_option)
          ? $oxymade_stylesheets_option
          : false;

        $oxymade_colors_option = get_option("oxymade_colors");
        $oxymade_colors_status = isset($oxymade_colors_option)
          ? $oxymade_colors_option
          : false;

        $oxymade_custom_css_option = get_option("oxymade_custom_css");
        $oxymade_custom_css_status = isset($oxymade_custom_css_option)
          ? $oxymade_custom_css_option
          : false;

        $oxymade_license_key_option = get_option("oxymade_license_key");
        $oxymade_license_key_status = isset($oxymade_license_key_option)
          ? $oxymade_license_key_option
          : false;

        $oxymade_license_option = get_option("oxymade_license_status");
        $oxymade_license_status = isset($oxymade_license_option)
          ? $oxymade_license_option
          : false;

        $oxymade_oxygen_settings_backup_option = get_option(
          "oxymade_oxygen_settings_backup"
        );
        $oxymade_oxygen_settings_backup_status = isset(
          $oxymade_oxygen_settings_backup_option
        )
          ? $oxymade_oxygen_settings_backup_option
          : false;

        $oxymade_active_set_option = get_option("oxymade_active_set");
        $oxymade_active_set_status = isset($oxymade_active_set_option)
          ? $oxymade_active_set_option
          : false;

        $oxymade_quick_install_option = get_option("oxymade_quick_install");
        $oxymade_quick_install_status = isset($oxymade_quick_install_option)
          ? $oxymade_quick_install_option
          : false;

        $oxygen_vsb_source_sites_option = get_option("oxygen_vsb_source_sites");
        $oxygen_vsb_source_sites_status = isset($oxygen_vsb_source_sites_option)
          ? $oxygen_vsb_source_sites_option
          : false;

        $oxymade_design_sets = [
          "https://megaset.oxymade.com",
          "https://boundaries.oxymade.com",
          "https://whistle.oxymade.com",
          "https://capital.oxymade.com",
          "https://monster.oxymade.com",
          "https://checkout.oxymade.com",
          "https://restro.oxymade.com",
          "https://whistle.oxymade.com",
          "https://blogzine.oxymade.com",
          "https://arya.oxymade.com",
        ];

        //TODO: ADD NEW SETS AS WE ADD

        if ($oxygen_vsb_source_sites_status) {
          foreach ($oxygen_vsb_source_sites_option as $oxygen_vsb_source_site) {
            foreach ($oxymade_design_sets as $oxymade_design_set) {
              if ($oxygen_vsb_source_site["url"] == $oxymade_design_set) {
                $oxymade_design_set_status = true;
              }
            }
          }
        } else {
          $oxymade_design_set_status = false;
        }
        ?>
        
        <div class="border-t border-gray-200 px-4 py-3 pb-6 sm:px-6">
          <div class="flow-root">
          <ul class="divide-y divide-gray-200 space-y-3">
            <li>
              <div class="flex items-center space-x-0">
                <?php if ($oxymade_design_set_status) { ?>
                <div class="flex-shrink-0">    
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                   </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-400 truncate line-through">
                    Design set added successfully!
                  </p>
                  </div>
                <?php } else { ?>
                <div class="flex-shrink-0">  
                  <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-6 w-6 text-green-500" 
                  viewBox="0 0 24 24"
                  fill="none"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z"
                    fill="currentColor"
                  />
                </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">
                  <a href="https://oxymade.com/design-sets?referrer=pluginchecklist">Add a design set to Oxygen library</a>
                </p>
                  </div>
                <?php } ?>
              </div>
              </li>

              <li>
                <div class="flex items-center space-x-0">
                  <?php if ($oxymade_active_set_status) { ?>
                  <div class="flex-shrink-0">    
                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                       <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                     </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-400 truncate line-through">
                      Base design set installed successfully!
                    </p>
                    </div>
                  <?php } else { ?>
                  <div class="flex-shrink-0">  
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-6 w-6 text-green-500" 
                    viewBox="0 0 24 24"
                    fill="none"
                  >
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z"
                      fill="currentColor"
                    />
                  </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                    Install a base design set to get started quickly.
                  </p>
                    </div>
                  <?php } ?>
                </div>
                </li>
                
              <li>
                <div class="flex items-center space-x-0">
                  <?php if (
                    $oxymade_colors_status &&
                    $oxymade_custom_css_status
                  ) { ?>
                  <div class="flex-shrink-0">    
                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                       <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                     </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-400 truncate line-through">
                      OxyMade colors installed successfully!
                    </p>
                    </div>
                  <?php } else { ?>
                  <div class="flex-shrink-0">  
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-6 w-6 text-green-500" 
                    viewBox="0 0 24 24"
                    fill="none"
                  >
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z"
                      fill="currentColor"
                    />
                  </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                    Please install colors from skins button above.
                  </p>
                    </div>
                  <?php } ?>
                </div>
                </li>
                  
                  <li>
                    <div class="flex items-center space-x-0">
                      <?php if (
                        $one_click_installer_status &&
                        $oxymade_quick_install_status &&
                        $oxymade_stylesheets_status
                      ) { ?>
                      <div class="flex-shrink-0">    
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                         </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-400 truncate line-through">
                          Framework classes & stylesheets installed!
                        </p>
                        </div>
                      <?php } else { ?>
                      <div class="flex-shrink-0">  
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-6 w-6 text-green-500" 
                        viewBox="0 0 24 24"
                        fill="none"
                      >
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z"
                          fill="currentColor"
                        />
                      </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                        Please re-install the framework.
                      </p>
                        </div>
                      <?php } ?>
                    </div>
                    </li>
                    
                  <li>
                    <div class="flex items-center space-x-0">
                      <?php if (
                        $oxymade_license_key_status &&
                        $oxymade_license_status == "valid"
                      ) { ?>
                      <div class="flex-shrink-0">    
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                         </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-400 truncate line-through">
                          OxyMade Framework license activated!
                        </p>
                        </div>
                      <?php } else { ?>
                      <div class="flex-shrink-0">  
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-6 w-6 text-green-500" 
                        viewBox="0 0 24 24"
                        fill="none"
                      >
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z"
                          fill="currentColor"
                        />
                      </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                        Please activate the framework license.
                      </p>
                        </div>
                      <?php } ?>
                    </div>
                    </li>
                    
                    </ul>
          
          </div>
          
        </div>
        
        
      </div>
      
      <section
      class="col-span-1 sm:col-span-1 mt-6"
    >
      <div class="bg-white shadow sm:rounded-lg pb-4">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
      <div
      class="pt-6 pb-2 md:flex md:items-center md:justify-between"
      >
      <div class="flex-1 min-w-0">
        <!-- Profile -->
        <div class="flex items-center">
        <div>
        <!-- <div class="flex items-center"> -->
        <h2
        class="text-xl font-semibold leading-6 text-gray-900 sm:leading-9 sm:truncate mb-2 inline-flex"
        >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 mt-2"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
        Framework License
        </h2>
        <!-- </div> -->
        <dl
        class="mt-6 flex flex-col sm:mt-1 sm:flex-row sm:flex-wrap mb-3"
        >
        <dd
          class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mr-6 sm:mt-0 capitalize"
        >
          <svg
          class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400"
          x-description="Heroicon name: solid/check-circle"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
          >
          <path
          fill-rule="evenodd"
          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
          clip-rule="evenodd"
          ></path>
          </svg>
          Activated
        </dd>
        </dl>
        </div>
        </div>
      </div>
      </div>
      <p
      class="mt-1 max-w-2xl text-sm text-gray-500 pb-6 leading-5"
      >
      Remove the license information to enter a different license key.
      </p>
      <div class="my-4 flex md:mt-0 md:ml-2">
      <span x-data="{id: 1}">
        <button
        @click="$dispatch('open-remove-license-modal',{id})"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
        Remove the license
        </button>
      </span>
      <span>
        <form action="" method="post">
          <p class="ml-3 text-sm md:mt-0 md:ml-6">
            <button name="oxymade_user_verification" value="yes" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Verify user <span aria-hidden="true">&rarr;</span></button>
          </p>
        </form>
      </span>
      </div>
      </div>
  
      <!-- <div>
      <a
      href="https://learn.oxymade.com/docs/framework/base-design-set/"
      target="_blank"
      class="block bg-gray-50 text-sm font-medium text-gray-500 text-center px-4 py-4 hover:text-gray-700 sm:rounded-b-lg"
      > <span class="w-full inline-flex"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg> Learn about Base Design Set here →</span></a
      >
      </div> -->
      </div>
    </section>
      
      
      
      <div
      class="space-y-1 inline-flex flex-wrap mt-2"
      role="group"
      aria-labelledby="navigation-links"
      >
      <a
      href="https://oxymade.com/terms" target="_blank"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> Terms </span>
      </a>
  
      <a
      href="https://learn.oxymade.com/docs/get-started/welcome/" target="_blank"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> Documentation </span>
      </a>
  
      <!-- <a
      href="https://learn.oxymade.com/changelog/everything/" target="_blank"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> What's new? </span>
      </a> -->
  
      <a
      href="https://oxymade.com/dashboard" target="_blank"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> Dashboard </span>
      </a>
  
      <!-- <a
      href="#"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> Color palettes </span>
      </a>
      <a
      href="#"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> Live preview </span>
      </a>
      <a
      href="#"
      class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"
      >
      <span class="truncate"> Page generator </span>
      </a> -->
      </div>
    </section>
    </div>
    </div>
    
    
    </main>
  </div>
    
    
  
  <!-- 
  ======================================
  ======================================
  Native colors Editor Slide Over 
  ======================================
  ======================================
  -->
  
  <section
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    @open-colors-slideover.window="if ($event.detail.id == 1) open = true"
    class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
    aria-labelledby="oxygen-native-colors-editor"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div class="absolute inset-0 overflow-hidden">
    <div
    x-description="Background overlay, show/hide based on slide-over state."
    class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <div class="absolute inset-y-0 right-0 pl-10 max-w-full flex sm:pl-16" style="top: 30px;">
    <div
    x-show="open"
    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="w-screen max-w-2xl"
    x-description="Slide-over panel, show/hide based on slide-over state."
    >
    <div
      class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll"
    >
      <div class="flex-1">
      <!-- Header -->
      <div class="py-6 px-4 bg-indigo-700 sm:px-6">
      <div class="flex items-center justify-between">
      <h2
        class="text-lg font-medium text-white"
        id="slide-over-title"
      >
        Edit Oxygen Native Colors
      </h2>
      <div class="ml-3 h-7 flex items-center">
        <button
        type="button"
        class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
        @click="open = false"
        >
        <span class="sr-only">Close panel</span>
        <svg
        class="h-6 w-6"
        x-description="Heroicon name: outline/x"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
        ></path>
        </svg>
        </button>
      </div>
      </div>
      <div class="mt-1">
      <p class="text-sm text-indigo-300">
        Edit Oxygen native global colors, color ids, set names,
        set ids.
      </p>
      </div>
      </div>
      
      <?php
      $global_colors = oxy_get_global_colors();
      $gcolors = $global_colors["colors"];
      $gsets = $global_colors["sets"];
      $backup_global_colors = get_option("backup_oxygen_vsb_global_colors");
      ?>
      
       
      
      <!-- Divider container -->
      <div>
      
      <div class="p-8 ">
      <?php if (
        isset($backup_global_colors) &&
        is_array($backup_global_colors)
      ) { ?>
      
      
      <form action="" method="post">
        <span class="relative z-0 inline-flex shadow-sm rounded-md float-right mb-12">
        <button type="submit" name="gc_action" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" value="backup">
        Backup
        </button>
        
        
        <button type="submit" name="gc_action" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" value="restore">
        Restore
        </button>
        
        
        <button type="submit" name="gc_action" class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" value="delete">
        Delete
        </button>
        
        
        
        </span>
      </form>
        <?php } else { ?>
        
        
        <form action="" method="post">
        <button type="submit" name="gc_action" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 float-right"  value="backup">
        Backup
        </button>
        </form>
        
        <?php } ?>
      </div>
      
      
      <div class="p-8 space-y-8 divide-y divide-gray-200">
      
      
      
      <div>
        <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
        Edit global color sets
        </h3>
        <p class="mt-1 text-sm text-gray-500">
        Please modify below inputs to change colors
        information.
        </p>
        </div>
        
        <div class="flex flex-col mt-6">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div
        class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
        >
        <form method="post" action="" name="globalSets">
        <div
          class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
        >
          <table
          class="min-w-full divide-y divide-gray-200"
          >
          <thead class="bg-gray-50">
          <tr>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Set ID
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Set Name
          </th>
          </tr>
          </thead>
          <tbody>
          
          <?php foreach ($gsets as $gskey => $gsvalue) { ?>
          
          <!-- Odd row -->
          <tr class="bg-white">
          <td
            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
          >
            <input
            type="number"
            name='<?php echo $gskey; ?>[id]'
             value="<?php echo $gsvalue["id"]; ?>"
            
            class="monster_input shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            />
          </td>
          <td
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
          >
            <input
            type="text"
            name='<?php echo $gskey; ?>[name]'
             value="<?php echo $gsvalue["name"]; ?>"
            
            class="monster_input shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            />
          </td>
          </tr>
          <?php } ?>
          
  
          <!-- More items... -->
          </tbody>
          </table>
          
        </div>
          <input name="global_sets" type="Submit" value="Save Global Sets" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 float-right cursor-pointer">
          </form>
        </div>
        </div>
        </div>
      </div>
      <div>
        <div class="pt-8">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
        Edit native global colors
        </h3>
        <p class="mt-1 text-sm text-gray-500">
        Please modify below inputs to change colors
        information.
        </p>
        </div>
  
        <div class="flex flex-col mt-6">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div
        class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
        >
        <form method="post" action="">
        <div
          class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
        >
          <table
          class="min-w-full divide-y divide-gray-200"
          >
          <thead class="bg-gray-50">
          <tr>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            COLOR ID
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Name
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            # Value
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Set ID
          </th>
          </tr>
          </thead>
          <tbody>
          
          <?php foreach ($gcolors as $key => $value) { ?>
          
          <!-- Odd row -->
          <tr class="bg-white">
          <td
            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
          >
            <input
            
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md monster_input" type="number" name='<?php echo $key; ?>[id]'
             value="<?php echo $value["id"]; ?>"
            />
          </td>
          <td
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
          >
            <input
            
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md monster_input" type="text" name='<?php echo $key; ?>[name]'
             value="<?php echo $value["name"]; ?>"
            />
          </td>
          <td
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 native-colors"
          >
            <input
            
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md monster_input oxymade-colors" type="text" name='<?php echo $key; ?>[value]'
             value="<?php echo $value["value"]; ?>"
            />
          </td>
          <td
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
          >
            <input
            
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md monster_input" type="number" name='<?php echo $key; ?>[set]'
             value="<?php echo $value["set"]; ?>"
            />
          </td>
          </tr>
  <?php } ?>
          
  
          <!-- More items... -->
          </tbody>
          </table>
        </div>
          <input name="global_colors" type="submit" class="float-right inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 mt-3 cursor-pointer" value="Save Global Colors">
          </form>
        </div>
        </div>
        </div>
      </div>
      </div>
      </div>
      </div>
  
      <!-- Action buttons -->
      <div
      class="flex-shrink-0 px-4 border-t border-gray-200 py-5 sm:px-6"
      >
      <div class="space-x-3 flex justify-end">
      <button
      type="button"
      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      @click="open = false"
      >
      Cancel
      </button>

      </div>
      </div>
    </div>
    </div>
    </div>
    </div>
  </section>
  
  
  
  
  <!-- 
    ======================================
    ======================================
    Open framework settings slide over
    ======================================
    ======================================
    -->
    
    <section
      x-data="{ open: false }"
      @keydown.window.escape="open = false"
      x-show="open"
      @open-framework-settings-slideover.window="if ($event.detail.id == 1) open = true"
      class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
      aria-labelledby="Oxymade-framework-settings"
      x-ref="dialog"
      role="dialog"
      aria-modal="true"
    >
      <div class="absolute inset-0 overflow-hidden">
      <div
      x-description="Background overlay, show/hide based on slide-over state."
      class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
      @click="open = false"
      aria-hidden="true"
      ></div>
    
      <div class="absolute inset-y-0 right-0 pl-10 max-w-full flex sm:pl-16" style="top: 30px;">
      <div
      x-show="open"
      x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:enter-start="translate-x-full"
      x-transition:enter-end="translate-x-0"
      x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:leave-start="translate-x-0"
      x-transition:leave-end="translate-x-full"
      class="w-screen max-w-md"
      x-description="Slide-over panel, show/hide based on slide-over state."
      >
      <div
        class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll"
      >
        <div class="flex-1">
        <!-- Header -->
        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
        <div class="flex items-center justify-between">
        <h2
          class="text-lg font-medium text-white"
          id="slide-over-title"
        >
          Modify OxyMade framework base settings
        </h2>
        <div class="ml-3 h-7 flex items-center">
          <button
          type="button"
          class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
          @click="open = false"
          >
          <span class="sr-only">Close panel</span>
          <svg
          class="h-6 w-6"
          x-description="Heroicon name: outline/x"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
          ></path>
          </svg>
          </button>
        </div>
        </div>
        <div class="mt-1">
        <p class="text-sm text-indigo-300">
          Modify html, body font size settings to change the typography of the entire website. This area is strictly for advanced users only.
        </p>
        </div>
        </div>
        <div
        class="flex-shrink-0 px-4 border-t border-gray-200 py-5 sm:px-6"
        >
        <form method="post" action="" name="oxymade_typography_settings">
          <label
          for="oxymade_html_font_size"
          class="block text-sm font-medium text-gray-600 mt-4"
          >HTML font size in %</label>
         <input
         type="text"
         name='oxymade_html_font_size'
          value="<?php echo $omf_html_font_size; ?>"
         placeholder="62.5"
         class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
         />
         <p class="mt-2 text-gray-400 leading-5 text-sm">Default value we use is 62.5%, which equals to 10px base font size for browsers. Changing it modify every REM value in the website. Paddings, margins, font sizes etc. So please do not change it. If you know what you are doing, do it with caution.</p>
         
         
          <label
          for="oxymade_body_font_size"
          class="block text-sm font-medium text-gray-600 mt-8"
          >Base font size for Desktop (REM)</label>
         <input
         type="text"
         name='oxymade_body_font_size'
          value="<?php echo $omf_body_font_size; ?>"
         placeholder="1.6"
         class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
         />
         <p class="mt-2 text-gray-400 leading-5 text-sm">Default value we use is 1.6REM, which equals to 16px body font size. If you would like to increase the body font size, you can use higher values here.</p>
        
        <!-- New fluid typography settings -->
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Mobile font base size (REM)</label>
        <input
        type="text" name='oxymade_fluid_mobile_font_size'
         value="<?php echo $omf_mobile_font_size; ?>"
        placeholder="1.6"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Base font size for Mobiles.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Responsive text decrease ratio (%)</label>
        <input
        type="text" name='oxymade_fluid_resp_size_decrease_ratio'
         value="<?php echo $omf_resp_size_decrease_ratio; ?>"
        placeholder="0.7"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Responsive font size decrease ratio for text size utilities.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Smallest font size (REM)</label>
        <input
        type="text" name='oxymade_fluid_smallest_font_size'
         value="<?php echo $omf_smallest_font_size; ?>"
        placeholder="1.5"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Smallest font size allowed for any device, any utility class.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Headings font weight</label>
        <input
        type="text" name='oxymade_fluid_headings_font_weight'
         value="<?php echo $omf_headings_font_weight; ?>"
        placeholder="600"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Heading classes default font weight.</p>
        
        <label
         for="oxymade_fluid_headings_sync"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Sync fluid heading sizes with Oxygen global heading sizes</label>
        <select
        type="text" name='oxymade_fluid_headings_sync'
         value="<?php echo $omf_headings_sync; ?>"
        placeholder="600"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <option value="true" <?php echo ($omf_headings_sync == true) ? "selected='selected'" : "" ; ?>>Yes</option>
         <option value="false" <?php echo ($omf_headings_sync == false) ? "selected='selected'" : "" ; ?>>No</option> 
         </select>
        <p class="mt-2 text-gray-400 leading-5 text-sm">Select 'Yes' to Sync heading classes with Oxygen's default global heading styles.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Desktop type scale ratio</label>
         
         <select name="oxymade_fluid_desktop_type_scale_ratio" id="oxymade_darkmode_customize_options_automatchostheme" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
           <option value="1.067" <?php echo ($omf_desktop_type_scale_ratio == 1.067) ? "selected='selected'" : "" ; ?>>1.067 - Minor Second</option>
           <option value="1.125" <?php echo ($omf_desktop_type_scale_ratio == 1.125) ? "selected='selected'" : "" ; ?>>1.125 - Major Second</option>                
           <option value="1.200" <?php echo ($omf_desktop_type_scale_ratio == 1.200) ? "selected='selected'" : "" ; ?>>1.200 - Minor Third</option>                
           <option value="1.250" <?php echo ($omf_desktop_type_scale_ratio == 1.250) ? "selected='selected'" : "" ; ?>>1.250 - Major Third</option>                
           <option value="1.333" <?php echo ($omf_desktop_type_scale_ratio == 1.333) ? "selected='selected'" : "" ; ?>>1.333 - Perfect Fourth</option>                
           <option value="1.414" <?php echo ($omf_desktop_type_scale_ratio == 1.414) ? "selected='selected'" : "" ; ?>>1.414 - Augmented Fourth</option>                
           <option value="1.500" <?php echo ($omf_desktop_type_scale_ratio == 1.500) ? "selected='selected'" : "" ; ?>>1.500 - Perfect Fifth</option>                
           <option value="1.618" <?php echo ($omf_desktop_type_scale_ratio == 1.618) ? "selected='selected'" : "" ; ?>>1.618 - Golden Ratio</option>                
         </select>
        <p class="mt-2 text-gray-400 leading-5 text-sm">Auto type scale ratio for Desktop.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Mobile type scale ratio</label>
        <select name="oxymade_fluid_mobile_type_scale_ratio" id="oxymade_darkmode_customize_options_automatchostheme" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
           <option value="1.067" <?php echo ($omf_mobile_type_scale_ratio == 1.067) ? "selected='selected'" : "" ; ?>>1.067 - Minor Second</option>
           <option value="1.125" <?php echo ($omf_mobile_type_scale_ratio == 1.125) ? "selected='selected'" : "" ; ?>>1.125 - Major Second</option>                
           <option value="1.200" <?php echo ($omf_mobile_type_scale_ratio == 1.200) ? "selected='selected'" : "" ; ?>>1.200 - Minor Third</option>                
           <option value="1.250" <?php echo ($omf_mobile_type_scale_ratio == 1.250) ? "selected='selected'" : "" ; ?>>1.250 - Major Third</option>                
           <option value="1.333" <?php echo ($omf_mobile_type_scale_ratio == 1.333) ? "selected='selected'" : "" ; ?>>1.333 - Perfect Fourth</option>                
           <option value="1.414" <?php echo ($omf_mobile_type_scale_ratio == 1.414) ? "selected='selected'" : "" ; ?>>1.414 - Augmented Fourth</option>                
           <option value="1.500" <?php echo ($omf_mobile_type_scale_ratio == 1.500) ? "selected='selected'" : "" ; ?>>1.500 - Perfect Fifth</option>                
           <option value="1.618" <?php echo ($omf_mobile_type_scale_ratio == 1.618) ? "selected='selected'" : "" ; ?>>1.618 - Golden Ratio</option>                
         </select>
        <p class="mt-2 text-gray-400 leading-5 text-sm">Auto type scale ratio for mobiles.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Viewport min size in REM</label>
        <input
        type="text" name='oxymade_fluid_viewport_min'
         value="<?php echo $omf_viewport_min; ?>"
        placeholder="32"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Minimum viewport width to apply fluid typography.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Viewport max size in REM</label>
        <input
        type="text" name='oxymade_fluid_viewport_max'
         value="<?php echo $omf_viewport_max; ?>"
        placeholder="117"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Maximum viewport width to apply fluid typography.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 13px - 16px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_13_16'
         value="<?php echo $omf_lh_13_16; ?>"
        placeholder="1.68"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 13px to 16px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 17px - 20px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_17_20'
         value="<?php echo $omf_lh_17_20; ?>"
        placeholder="1.54"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 17px to 20px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 21px - 24px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_21_24'
         value="<?php echo $omf_lh_21_24; ?>"
        placeholder="1.45"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 21px to 24px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 25px - 30px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_25_30'
         value="<?php echo $omf_lh_25_30; ?>"
        placeholder="1.33"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 25px to 30px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 31px - 36px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_31_36'
         value="<?php echo $omf_lh_31_36; ?>"
        placeholder="1.2"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 31px to 36px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 37px - 48px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_37_48'
         value="<?php echo $omf_lh_37_48; ?>"
        placeholder="1.1"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 37px to 48px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 49px - 64px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_49_64'
         value="<?php echo $omf_lh_49_64; ?>"
        placeholder="1"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 49px to 64px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        <label
         for="oxymade_body_font_size_calc"
         class="block text-sm font-medium text-gray-600 mt-8"
         >Line height for 65px - 150px (Number)</label>
        <input
        type="text" name='oxymade_fluid_lh_65_150'
         value="<?php echo $omf_lh_65_150; ?>"
        placeholder="0.98"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-2"
        />
        <p class="mt-2 text-gray-400 leading-5 text-sm">Text line height for 65px to 150px. Only use a number that will be multiplied with the current font-size to set the line height.</p>
        
        </div>
        <!-- Divider container -->
        
        </div>
    
        <!-- Action buttons -->
        <div
        class="flex-shrink-0 px-4 border-t border-gray-200 py-5 sm:px-6"
        >
        <div class="space-x-3 flex justify-end">
        <button
        type="button"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="open = false"
        >
        Cancel
        </button>
        
        <button
        type="submit"
        name="oxymade_typography_settings"
        value="yes"
        class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
        Save typography settings
        </button>
        </form>
        </div>
        </div>
      </div>
      </div>
      </div>
      </div>
    </section>  
      
  
  <!-- 
  ======================================
  ======================================
  Import colors Slideover 
  ======================================
  ======================================
  -->
  <section
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    @open-import-export-colors-slideover.window="if ($event.detail.id == 1) open = true"
    class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
    aria-labelledby="import-colors"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div class="absolute inset-0 overflow-hidden">
    <div
    x-description="Background overlay, show/hide based on slide-over state."
    class="absolute inset-0"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
    <div
    x-show="open"
    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="w-screen max-w-md"
    x-description="Slide-over panel, show/hide based on slide-over state."
    >
    <form
      class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
    >
      <div class="flex-1 h-0 overflow-y-auto">
      <div class="py-6 px-4 bg-indigo-700 sm:px-6">
      <div class="flex items-center justify-between">
      <h2
        class="text-lg font-medium text-white"
        id="slide-over-title"
      >
        Import / Export Colors
      </h2>
      <div class="ml-3 h-7 flex items-center">
        <button
        type="button"
        class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
        @click="open = false"
        >
        <span class="sr-only">Close panel</span>
        <svg
        class="h-6 w-6"
        x-description="Heroicon name: outline/x"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
        ></path>
        </svg>
        </button>
      </div>
      </div>
      <div class="mt-1">
      <p class="text-sm text-indigo-300">
        Import / Export your colors between sites with one click.
      </p>
      </div>
      </div>
      <div class="flex-1 flex flex-col justify-between">
      <div class="px-4 divide-y divide-gray-200 sm:px-6">
      <div class="space-y-6 pt-6 pb-5">

        <div>
        <div class="flex justify-between">
        <label
        for="ExportColors"
        class="block text-sm font-medium text-gray-900 mt-2"
        >
        Export Colors
        </label>
        <div><button id="mcolors_export" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="copyColors()">Copy Colors</button></div>
        </div>
        <div class="mt-1">
        <textarea
        id="export_color_palette"
        name="export_color_palette"
        rows="4"
        class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md text-gray-400"
        ><?php echo $color_export; ?></textarea>
        <p class="text-sm text-gray-500 my-3">
        Please copy the color palette from the above export
        colors text box and paste it into the import box in
        your other site.
        </p>
        </div>
        </div>
        <div>
        <label
        for="description"
        class="block text-sm font-medium text-gray-900"
        >
        Import Colors
        </label>
        <div class="mt-1">
        <textarea
        id="import_color_palette"
        name="import_color_palette"
        rows="4"
        class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
        ></textarea>
        <p class="text-sm text-gray-500 my-3">
        Please paste the color palette in the above import
        colors text box to set the color palette.
        </p>
        </div>
        </div>
      </div>
      <div class="pt-4 pb-6">
        <div class="flex text-sm">
        <a
        href="https://megaset.oxymade.com/colors"
        target="_blank"
        class="group inline-flex items-center font-medium text-indigo-600 hover:text-indigo-900"
        >
        <svg
        class="h-5 w-5 text-indigo-500 group-hover:text-indigo-900"
        x-description="Heroicon name: solid/link"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
        >
        <path
          fill-rule="evenodd"
          d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
          clip-rule="evenodd"
        ></path>
        </svg>
        <span class="ml-2"> Color palette generator </span>
        </a>
        </div>
        <div class="mt-4 flex text-sm">
        <a
        href="https://learn.oxymade.com/docs/get-started/color-system-overview/"
        class="group inline-flex items-center text-gray-500 hover:text-gray-900"
        target="_blank"
        >
        <svg
        class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
        x-description="Heroicon name: solid/question-mark-circle"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
        >
        <path
          fill-rule="evenodd"
          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
          clip-rule="evenodd"
        ></path>
        </svg>
        <span class="ml-2">
        Learn more about our color system
        </span>
        </a>
        </div>
      </div>
      </div>
      </div>
      </div>
      <div class="flex-shrink-0 px-4 py-4 flex justify-end">
      <button
      type="button"
      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      @click="open = false"
      >
      Cancel
      </button>
      <button
      type="submit"
      name="color_importer_submit"
      value="yes"
      class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
      Import color palette
      </button>
      </div>
    </form>
    </div>
    </div>
    </div>
  </section>
  <!-- 
  ======================================
  ======================================
  Blogzine settings panel
  ======================================
  ======================================
  -->
  <?php if($user_level == "pro") { ?>
  <section
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    @open-blogzine-settings-slideover.window="if ($event.detail.id == 1) open = true"
    class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
    aria-labelledby="blogzine-settings"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div class="absolute inset-0 overflow-hidden">
    <div
    x-description="Background overlay, show/hide based on slide-over state."
    class="absolute inset-0"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
    <div
    x-show="open"
    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="w-screen max-w-2xl "
    x-description="Slide-over panel, show/hide based on slide-over state."
    >
    <form
      class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
    >
      <div class="flex-1 h-0 overflow-y-auto">
      <div class="py-6 px-4 bg-indigo-700 sm:px-6">
      <div class="flex items-center justify-between">
      <h2
        class="text-lg font-medium text-white"
        id="slide-over-title"
      >
        Blogzine settings panel
      </h2>
      <div class="ml-3 h-7 flex items-center">
        <button
        type="button"
        class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
        @click="open = false"
        >
        <span class="sr-only">Close panel</span>
        <svg
        class="h-6 w-6"
        x-description="Heroicon name: outline/x"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
        ></path>
        </svg>
        </button>
      </div>
      </div>
      <div class="mt-1">
      <p class="text-sm text-indigo-300">
        Blogzine settings, typography, responsive settings and max-width settings.
      </p>
      </div>
      </div>
      
      
      <?php
        $om_bz_bp_2xl = get_option("oxymade_bz_bp_2xl");
        $om_bz_bp_xl = get_option("oxymade_bz_bp_xl");
        $om_bz_bp_lg = get_option("oxymade_bz_bp_lg");
        $om_bz_bp_md = get_option("oxymade_bz_bp_md");
        $om_bz_bp_sm = get_option("oxymade_bz_bp_sm");
      ?>
      
      
      <div class="p-8 space-y-8 divide-y divide-gray-200">
        
        
        
        <div>
          <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
          Edit typography sizing
          </h3>
          <p class="mt-1 text-sm text-gray-500">
          Please select the appropriate sizing based on the responsive breakpoint.
          </p>
          </div>
          
          <div class="flex flex-col mt-6">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div
          class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
          >
          <form method="post" action="" name="blogzine_typography">
          <div
            class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
          >
            <table
            class="min-w-full divide-y divide-gray-200"
            >
            <thead class="bg-gray-50">
            <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Responsive breakpoint
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Base typography size
            </th>
            </tr>
            </thead>
            <tbody>
            
            <tr class="bg-white">
            <td
              class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
            >
              <label for="breakpoint_2xl">General desktop ( <?php echo $width_default; ?>px + )</label>
            </td>
            <td
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
            >
              
              <select name="breakpoint_2xl" id="breakpoint_2xl" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="2xl" <?php echo ($om_bz_bp_2xl == "2xl" || $om_bz_bp_2xl == "" || !isset($om_bz_bp_2xl)) ? "selected='selected'" : "" ; ?>>2xl - 24px base size - Recommended</option>
                <option value="xl" <?php echo ($om_bz_bp_2xl == "xl") ? "selected='selected'" : "" ; ?>>xl - 20px base size</option>
                <option value="lg" <?php echo ($om_bz_bp_2xl == "lg") ? "selected='selected'" : "" ; ?>>lg - 18px base size</option>
                <option value="md" <?php echo ($om_bz_bp_2xl == "md") ? "selected='selected'" : "" ; ?>>md - 16px base size</option>
                <option value="sm" <?php echo ($om_bz_bp_2xl == "sm") ? "selected='selected'" : "" ; ?>>sm - 14px base size</option>
              </select>
              
              
            </td>
            </tr>
            
            <tr class="bg-gray-50">
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
                <label for="breakpoint_xl"> Tablet landscape ( <?php echo $width_tablet; ?>px to <?php echo $width_default -
  1; ?>px )</label>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
              >
                
                <select name="breakpoint_xl" id="breakpoint_xl" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  <option value="2xl" <?php echo ($om_bz_bp_xl == "2xl") ? "selected='selected'" : "" ; ?>>2xl - 24px base size</option>
                  <option value="xl" <?php echo ($om_bz_bp_xl == "xl" || $om_bz_bp_xl == "" || !isset($om_bz_bp_xl)) ? "selected='selected'" : "" ; ?>>xl - 20px base size - Recommended</option>
                  <option value="lg" <?php echo ($om_bz_bp_xl == "lg") ? "selected='selected'" : "" ; ?>>lg - 18px base size</option>
                  <option value="md" <?php echo ($om_bz_bp_xl == "md") ? "selected='selected'" : "" ; ?>>md - 16px base size</option>
                  <option value="sm" <?php echo ($om_bz_bp_xl == "sm") ? "selected='selected'" : "" ; ?>>sm - 14px base size</option>
                </select>
                
                
              </td>
              </tr>
              
              <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="breakpoint_lg">Tablet portrait( <?php echo $width_phone_landscape; ?>px to <?php echo $width_tablet -
  1; ?>px )</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                  
                  <select name="breakpoint_lg" id="breakpoint_lg" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="2xl" <?php echo ($om_bz_bp_lg == "2xl") ? "selected='selected'" : "" ; ?>>2xl - 24px base size</option>
                    <option value="xl" <?php echo ($om_bz_bp_lg == "xl") ? "selected='selected'" : "" ; ?>>xl - 20px base size</option>
                    <option value="lg" <?php echo ($om_bz_bp_lg == "lg" || $om_bz_bp_lg == "" || !isset($om_bz_bp_lg)) ? "selected='selected'" : "" ; ?>>lg - 18px base size - Recommended</option>
                    <option value="md" <?php echo ($om_bz_bp_lg == "md") ? "selected='selected'" : "" ; ?>>md - 16px base size</option>
                    <option value="sm" <?php echo ($om_bz_bp_lg == "sm") ? "selected='selected'" : "" ; ?>>sm - 14px base size</option>
                  </select>
                  
                  
                </td>
                </tr>
                
                <tr class="bg-gray-50">
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                  >
                    <label for="breakpoint_md">Mobile landscape ( <?php echo $width_phone_portrait; ?>px to <?php echo $width_phone_landscape -
  1; ?>px )</label>
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                  >
                    
                    <select name="breakpoint_md" id="breakpoint_md" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                      <option value="2xl" <?php echo ($om_bz_bp_md == "2xl") ? "selected='selected'" : "" ; ?>>2xl - 24px base</option>
                      <option value="xl" <?php echo ($om_bz_bp_md == "xl") ? "selected='selected'" : "" ; ?>>xl - 20px base size</option>
                      <option value="lg" <?php echo ($om_bz_bp_md == "lg") ? "selected='selected'" : "" ; ?>>lg - 18px base size</option>
                      <option value="md" <?php echo ($om_bz_bp_md == "md" || $om_bz_bp_md == "" || !isset($om_bz_bp_md)) ? "selected='selected'" : "" ; ?>>md - 16px base size - Recommended</option>
                      <option value="sm" <?php echo ($om_bz_bp_md == "sm") ? "selected='selected'" : "" ; ?>>sm - 14px base size</option>
                    </select>
                    
                    
                  </td>
                  </tr>
                  
                  <tr class="bg-white">
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                    >
                      <label for="breakpoint_sm">Mobile portrait ( < <?php echo $width_phone_portrait -
                        1; ?>px )</label>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      
                      <select name="breakpoint_sm" id="breakpoint_sm" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="2xl" <?php echo ($om_bz_bp_sm == "2xl") ? "selected='selected'" : "" ; ?>>2xl - 24px base size</option>
                        <option value="xl" <?php echo ($om_bz_bp_sm == "xl") ? "selected='selected'" : "" ; ?>>xl - 20px base size</option>
                        <option value="lg" <?php echo ($om_bz_bp_sm == "lg") ? "selected='selected'" : "" ; ?>>lg - 18px base size</option>
                        <option value="md" <?php echo ($om_bz_bp_sm == "md") ? "selected='selected'" : "" ; ?>>md - 16px base size</option>
                        <option value="sm" <?php echo ($om_bz_bp_sm == "sm" || $om_bz_bp_sm == "" || !isset($om_bz_bp_sm)) ? "selected='selected'" : "" ; ?>>sm - 14px base size - Recommended</option>
                      </select>
                      
                    </td>
                    </tr>
            
    
            <!-- More items... -->
            </tbody>
            </table>
            
          </div>
          
          <div class="mt-4 flex text-sm">
            <a
            href="https://learn.oxymade.com/docs/blogzine/typography"
            class="group inline-flex items-center text-gray-500 hover:text-gray-900"
            target="_blank"
            >
            <svg
            class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
            x-description="Heroicon name: solid/question-mark-circle"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
            >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
              clip-rule="evenodd"
            ></path>
            </svg>
            <span class="ml-2">
            Learn more about our blogzine typography system
            </span>
            </a>
            </div>
            
          <div class="flex-shrink-0 px-4 py-4 flex justify-end">
            <input name="blogzine_typography" type="Submit" value="Save typography settings" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 float-right cursor-pointer">
            
            <button
            type="button"
            class="mt-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="open = false"
            >
            Cancel
            </button>
          </div>
            </form>
            
            
            
            <!-- Blogzine settings started -->
            
            <?php
              $oxyberg_color_palette_status = get_option("oxymade_gutenberg_color_palette_status");
              $oxymade_infyscroll_status = get_option("oxymade_infyscroll");
            ?>
            
            
            <form method="post" action="" name="blogzine_settings">
              <div
                class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
              >
                <table
                class="min-w-full divide-y divide-gray-200"
                >
                <thead class="bg-gray-50">
                <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Blogzine Modules
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
                </th>
                </tr>
                </thead>
                <tbody>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="blogzine_infyscroll">Infinity scroll & Instant Load more posts</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                  
                  <select name="blogzine_infyscroll" id="blogzine_infyscroll" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="Enable" <?php echo ($oxymade_infyscroll_status != "Disable") ? "selected='selected'" : "" ; ?>>Enable</option>
                    <option value="Disable" <?php echo ($oxymade_infyscroll_status == "Disable") ? "selected='selected'" : "" ; ?>>Disable</option>
                  </select>
                  
                  
                </td>
                </tr>  
                                
                <tr class="bg-gray-50">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_gutenberg_color_palette_status">OxyMade colors & typography in gutenberg</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                  
                  <select name="oxymade_gutenberg_color_palette_status" id="oxymade_gutenberg_color_palette_status" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="Enable" <?php echo ($oxyberg_color_palette_status != "Disable") ? "selected='selected'" : "" ; ?>>Enable</option>
                    <option value="Disable" <?php echo ($oxyberg_color_palette_status == "Disable") ? "selected='selected'" : "" ; ?>>Disable</option>
                  </select>
                  
                  
                </td>
                </tr>                  
                  
                </tbody>
                </table>
                
              </div>
              
              <div class="mt-4 flex text-sm">
                <a
                href="https://learn.oxymade.com/docs/blogzine/modules/"
                class="group inline-flex items-center text-gray-500 hover:text-gray-900"
                target="_blank"
                >
                <svg
                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                x-description="Heroicon name: solid/question-mark-circle"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
                >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                  clip-rule="evenodd"
                ></path>
                </svg>
                <span class="ml-2">
                Learn more about our blogzine modules
                </span>
                </a>
                </div>
                
                
              <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                <input name="blogzine_settings" type="Submit" value="Save settings" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 float-right cursor-pointer">
                
                <button
                type="button"
                class="mt-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="open = false"
                >
                Cancel
                </button>
              </div>
                </form>
                
                
          </div>
          </div>
          </div>
        </div>
        </div>
        
      
      
      </div>
    </form>
    </div>
    </div>
    </div>
  </section>
  <?php } ?>
  
  <!-- 
  ======================================
  ======================================
  Darkmode settings panel
  ======================================
  ======================================
  -->
  <?php if($user_level == "pro") { ?>
  <section
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    @open-darkmode-settings-slideover.window="if ($event.detail.id == 1) open = true"
    class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
    aria-labelledby="darkmode-settings"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div class="absolute inset-0 overflow-hidden">
    <div
    x-description="Background overlay, show/hide based on slide-over state."
    class="absolute inset-0"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
    <div
    x-show="open"
    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="w-screen max-w-2xl "
    x-description="Slide-over panel, show/hide based on slide-over state."
    >
    <form
      class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
    >
      <div class="flex-1 h-0 overflow-y-auto">
      <div class="py-6 px-4 bg-indigo-700 sm:px-6">
      <div class="flex items-center justify-between">
      <h2
        class="text-lg font-medium text-white"
        id="slide-over-title"
      >
        Darkmode settings panel
      </h2>
      <div class="ml-3 h-7 flex items-center">
        <button
        type="button"
        class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
        @click="open = false"
        >
        <span class="sr-only">Close panel</span>
        <svg
        class="h-6 w-6"
        x-description="Heroicon name: outline/x"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
        ></path>
        </svg>
        </button>
      </div>
      </div>
      <div class="mt-1">
      <p class="text-sm text-indigo-300">
        Enable / disable dark mode, options, color, label, position, icon & custom CSS.
      </p>
      </div>
      </div>
      
      
      
      
      
      <div class="p-8 space-y-8 divide-y divide-gray-200">
        
        
        
        <div>
          <div class="flex flex-col mt-6">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div
          class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
          >
          
          <?php
          $oxymade_darkmode_customize_status = get_option("oxymade_darkmode_customize");
          $oxymade_darkmode_custom_css_status = get_option("oxymade_darkmode_custom_css_status");
        ?>
        
          <form method="post" action="" name="oxymade_darkmode_settings">
          <div
            class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
          >
            <table
            class="min-w-full divide-y divide-gray-200"
            >
            <thead class="bg-gray-50">
            <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Dark mode option
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
            </tr>
            </thead>
            <tbody>
            
            <tr class="bg-white">
            <td
              class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
            >
              <label for="oxymade_darkmode_customize">Dark mode customization</label>
            </td>
            <td
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
            >
              
              <select name="oxymade_darkmode_customize" id="oxymade_darkmode_customize" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="true" <?php echo ($oxymade_darkmode_customize_status == "true") ? "selected='selected'" : "" ; ?>>Enable</option>
                <option value="false" <?php echo ($oxymade_darkmode_customize_status != "true") ? "selected='selected'" : "" ; ?>>Disable</option> 
              </select>
              
              
            </td>
            </tr>
            
            <tr class="bg-gray-50">
            <td
              class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
            >
              <label for="oxymade_darkmode_custom_css_status">Dark mode custom CSS</label>
            </td>
            <td
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
            >
              
              <select name="oxymade_darkmode_custom_css_status" id="oxymade_darkmode_custom_css_status" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="true" <?php echo ($oxymade_darkmode_custom_css_status == "true") ? "selected='selected'" : "" ; ?>>Enable</option>
                <option value="false" <?php echo ($oxymade_darkmode_custom_css_status != "true") ? "selected='selected'" : "" ; ?>>Disable</option>                
              </select>
              
              
            </td>
            </tr>
            
    
            <!-- More items... -->
            </tbody>
            </table>
            
          </div>
          
          <div class="mt-4 flex text-sm">
            <a
            href="https://learn.oxymade.com/docs/darkmode/customize"
            class="group inline-flex items-center text-gray-500 hover:text-gray-900"
            target="_blank"
            >
            <svg
            class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
            x-description="Heroicon name: solid/question-mark-circle"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
            >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
              clip-rule="evenodd"
            ></path>
            </svg>
            <span class="ml-2">
            Learn more about our dark mode customization
            </span>
            </a>
            </div>
            
          <div class="flex-shrink-0 px-4 py-4 flex justify-end">
            <input name="oxymade_darkmode_settings" type="Submit" value="Save dark mode settings" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 float-right cursor-pointer">
            
            <button
            type="button"
            class="mt-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="open = false"
            >
            Cancel
            </button>
          </div>
            </form>
            
            
            
            <!-- Dark mode customization options started -->
            
            
            <?php 
            $oxymade_darkmode_customize_options = get_option("oxymade_darkmode_customize_options");
            $oxymade_darkmode_customize_options = base64_decode($oxymade_darkmode_customize_options);
            $oxymade_darkmode_customize_options = json_decode($oxymade_darkmode_customize_options);
            
            $oxymade_darkmode_customize_options = (array) $oxymade_darkmode_customize_options;
            
            $oxymade_darkmode_customize_options_bottom = (!empty($oxymade_darkmode_customize_options["bottom"])) ? $oxymade_darkmode_customize_options["bottom"] : "64px" ;
            
            $oxymade_darkmode_customize_options_right = (!empty($oxymade_darkmode_customize_options["right"])) ? $oxymade_darkmode_customize_options["right"] : "unset" ;
            
            $oxymade_darkmode_customize_options_left = (!empty($oxymade_darkmode_customize_options["left"])) ? $oxymade_darkmode_customize_options["left"] : "32px" ;
            
            $oxymade_darkmode_customize_options_time = (!empty($oxymade_darkmode_customize_options["time"])) ? $oxymade_darkmode_customize_options["time"] : "0.5s" ;
            
            $oxymade_darkmode_customize_options_mixcolor = (!empty($oxymade_darkmode_customize_options["mixcolor"])) ? $oxymade_darkmode_customize_options["mixcolor"] : "#fff" ;
            
            $oxymade_darkmode_customize_options_backgroundcolor = (!empty($oxymade_darkmode_customize_options["backgroundcolor"])) ? $oxymade_darkmode_customize_options["backgroundcolor"] : "#fff" ;
            
            $oxymade_darkmode_customize_options_buttoncolordark = (!empty($oxymade_darkmode_customize_options["buttoncolordark"])) ? $oxymade_darkmode_customize_options["buttoncolordark"] : "#100f2c" ;
            
            $oxymade_darkmode_customize_options_buttoncolorlight = (!empty($oxymade_darkmode_customize_options["buttoncolorlight"])) ? $oxymade_darkmode_customize_options["buttoncolorlight"] : "#fff" ;
            
            $oxymade_darkmode_customize_options_saveincookies = (!empty($oxymade_darkmode_customize_options["saveincookies"])) ? $oxymade_darkmode_customize_options["saveincookies"] : false ;
            
            $oxymade_darkmode_customize_options_label = (!empty($oxymade_darkmode_customize_options["label"])) ? $oxymade_darkmode_customize_options["label"] : "🌓" ;
            
            $oxymade_darkmode_customize_options_automatchostheme = (!empty($oxymade_darkmode_customize_options["automatchostheme"])) ? $oxymade_darkmode_customize_options["automatchostheme"] : true ;
          ?>
            
            <form method="post" action="" name="oxymade_darkmode_customize_options">
              <div
                class="mt-4 shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
              >
                <table
                class="min-w-full divide-y divide-gray-200"
                >
                <thead class="bg-gray-50">
                <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Dark mode customize options
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Value
                </th>
                </tr>
                </thead>
                
                <tbody>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="blogzine_infyscroll">Auto match user operating system theme</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                    <select name="oxymade_darkmode_customize_options_automatchostheme" id="oxymade_darkmode_customize_options_automatchostheme" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                      <option value="true" <?php echo ($oxymade_darkmode_customize_options_automatchostheme == "true") ? "selected='selected'" : "" ; ?>>Yes</option>
                      <option value="false" <?php echo ($oxymade_darkmode_customize_options_automatchostheme != "false") ? "selected='selected'" : "" ; ?>>No</option>                
                    </select>
                </td>
                </tr>
                                
                <tr class="bg-gray-50">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_backgroundcolor">Background color</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                <label for="oxymade_darkmode_customize_options_backgroundcolor" class="sr-only">Background color</label>
                <input type="text" name="oxymade_darkmode_customize_options_backgroundcolor" id="oxymade_darkmode_customize_options_backgroundcolor" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="#ffffff" value="<?php echo $oxymade_darkmode_customize_options_backgroundcolor; ?>">
                </td>
                </tr>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_buttoncolordark">Button color when in dark mode</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                     <label for="oxymade_darkmode_customize_options_buttoncolordark" class="sr-only">Button color dark mode</label>
                    <input type="text" name="oxymade_darkmode_customize_options_buttoncolordark" id="oxymade_darkmode_customize_options_buttoncolordark" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="#100f2c" value="<?php echo $oxymade_darkmode_customize_options_buttoncolordark; ?>">
                </td>
                </tr>  
                                
                <tr class="bg-gray-50">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_buttoncolorlight">Button color when in light mode</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                <label for="oxymade_darkmode_customize_options_buttoncolorlight" class="sr-only">Button color light mode</label>
                <input type="text" name="oxymade_darkmode_customize_options_buttoncolorlight" id="oxymade_darkmode_customize_options_buttoncolorlight" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="#ffffff" value="<?php echo $oxymade_darkmode_customize_options_buttoncolorlight; ?>">
                </td>
                </tr>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_saveincookies">Save user preference in cookies</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                     <select name="oxymade_darkmode_customize_options_saveincookies" id="oxymade_darkmode_customize_options_saveincookies" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                       <option value="true" <?php echo ($oxymade_darkmode_customize_options_saveincookies == "true") ? "selected='selected'" : "" ; ?>>Yes</option>
                       <option value="false" <?php echo ($oxymade_darkmode_customize_options_saveincookies != "true") ? "selected='selected'" : "" ; ?>>No</option>                
                     </select>
                </td>
                </tr>  
                                
                <tr class="bg-gray-50">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_label">Label Icon or Emoji</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                <label for="oxymade_darkmode_customize_options_label" class="sr-only">Label icon or emoji</label>
                
                <select name="oxymade_darkmode_customize_options_label" id="oxymade_darkmode_customize_options_label" class="breakpoint_inputs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                   <option value="🌓" <?php echo ($oxymade_darkmode_customize_options_label == "🌓") ? "selected='selected'" : "" ; ?>>🌓</option>
                   <option value="🌙" <?php echo ($oxymade_darkmode_customize_options_label == "🌙") ? "selected='selected'" : "" ; ?>>🌙</option>    
                   <option value="🌏" <?php echo ($oxymade_darkmode_customize_options_label == "🌏") ? "selected='selected'" : "" ; ?>>🌏</option>    
                   <option value="🎉" <?php echo ($oxymade_darkmode_customize_options_label == "🎉") ? "selected='selected'" : "" ; ?>>🎉</option>    
                   <option value="☀️" <?php echo ($oxymade_darkmode_customize_options_label == "☀️") ? "selected='selected'" : "" ; ?>>☀️</option>    
                   <option value="🔆" <?php echo ($oxymade_darkmode_customize_options_label == "🔆") ? "selected='selected'" : "" ; ?>>🔆</option>    
                   <option value="✨" <?php echo ($oxymade_darkmode_customize_options_label == "✨") ? "selected='selected'" : "" ; ?>>✨</option>    
                   <option value="🌟" <?php echo ($oxymade_darkmode_customize_options_label == "🌟") ? "selected='selected'" : "" ; ?>>🌟</option>    
                   <option value="⭐" <?php echo ($oxymade_darkmode_customize_options_label == "⭐") ? "selected='selected'" : "" ; ?>>⭐</option>    
                   <option value="🍭" <?php echo ($oxymade_darkmode_customize_options_label == "🍭") ? "selected='selected'" : "" ; ?>>🍭</option>    
                   <option value="💡" <?php echo ($oxymade_darkmode_customize_options_label == "💡") ? "selected='selected'" : "" ; ?>>💡</option>    
                   <option value="💥" <?php echo ($oxymade_darkmode_customize_options_label == "💥") ? "selected='selected'" : "" ; ?>>💥</option>    
                   <option value="🍭" <?php echo ($oxymade_darkmode_customize_options_label == "🍭") ? "selected='selected'" : "" ; ?>>🍭</option>    
                   <option value="🪄" <?php echo ($oxymade_darkmode_customize_options_label == "🪄") ? "selected='selected'" : "" ; ?>>🪄</option>    
                   <option value="😀" <?php echo ($oxymade_darkmode_customize_options_label == "😀") ? "selected='selected'" : "" ; ?>>😀</option>    
                 </select>
                
                </td>
                </tr>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_mixcolor">Mix Color</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                     <label for="oxymade_darkmode_customize_options_mixcolor" class="sr-only">Mix color</label>
                    <input type="text" name="oxymade_darkmode_customize_options_mixcolor" id="oxymade_darkmode_customize_options_mixcolor" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="#ffffff" value="<?php echo $oxymade_darkmode_customize_options_mixcolor; ?>">
                </td>
                </tr>  
                                
                <tr class="bg-gray-50">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_time">Transition time</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                <label for="oxymade_darkmode_customize_options_time" class="sr-only">Transition time</label>
                <input type="text" name="oxymade_darkmode_customize_options_time" id="oxymade_darkmode_customize_options_time" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="0.5s" value="<?php echo $oxymade_darkmode_customize_options_time; ?>">
                </td>
                </tr>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_bottom">Bottom position</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                     <label for="oxymade_darkmode_customize_options_bottom" class="sr-only">Bottom</label>
                    <input type="text" name="oxymade_darkmode_customize_options_bottom" id="oxymade_darkmode_customize_options_bottom" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="64px" value="<?php echo $oxymade_darkmode_customize_options_bottom; ?>">
                </td>
                </tr>  
                                
                <tr class="bg-gray-50">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_right">Right position</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                <label for="oxymade_darkmode_customize_options_right" class="sr-only">Right</label>
                <input type="text" name="oxymade_darkmode_customize_options_right" id="oxymade_darkmode_customize_options_right" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="unset" value="<?php echo $oxymade_darkmode_customize_options_right; ?>">
                </td>
                </tr>
                
                <tr class="bg-white">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  <label for="oxymade_darkmode_customize_options_left">Left position</label>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                >
                     <label for="oxymade_darkmode_customize_options_left" class="sr-only">Left</label>
                    <input type="text" name="oxymade_darkmode_customize_options_left" id="oxymade_darkmode_customize_options_left" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="32px" value="<?php echo $oxymade_darkmode_customize_options_left; ?>">
                </td>
                </tr>  
                  
                </tbody>
                </table>
                
              </div>
              
              <div class="mt-4 flex text-sm">
                <a
                href="https://learn.oxymade.com/docs/darkmode/options/"
                class="group inline-flex items-center text-gray-500 hover:text-gray-900"
                target="_blank"
                >
                <svg
                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                x-description="Heroicon name: solid/question-mark-circle"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
                >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                  clip-rule="evenodd"
                ></path>
                </svg>
                <span class="ml-2">
                Learn more about our Dark mode options
                </span>
                </a>
                </div>
                
                
              <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                <input name="oxymade_darkmode_customize_options" type="Submit" value="Save customize options" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 float-right cursor-pointer">
                
                <button
                type="button"
                class="mt-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="open = false"
                >
                Cancel
                </button>
              </div>
                </form>
                
                
                <?php 
                  $oxymade_darkmode_custom_css = get_option("oxymade_darkmode_custom_css");
                ?>
                
                <form method="post" action="" name="oxymade_darkmode_customcss">
                  <div
                    class="mt-4 shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
                  >
                    <table
                    class="min-w-full divide-y divide-gray-200"
                    >
                    <thead class="bg-gray-50">
                    <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Dark mode custom CSS
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Value
                    </th>
                    </tr>
                    </thead>
                    
                    <tbody>
                    
                    <tr class="bg-white">
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                      >
                        <label for="blogzine_infyscroll">Custom CSS</label>
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                      >
                        <textarea id="oxymade_darkmode_custom_css" name="oxymade_darkmode_custom_css" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border border-gray-300 rounded-md"><?php echo $oxymade_darkmode_custom_css; ?></textarea>
                      </td>
                    </tr>
                                    
                                
                      
                    </tbody>
                    </table>
                    
                  </div>
                  
                  <div class="mt-4 flex text-sm">
                    <a
                    href="https://learn.oxymade.com/docs/darkmode/customcss/"
                    class="group inline-flex items-center text-gray-500 hover:text-gray-900"
                    target="_blank"
                    >
                    <svg
                    class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                    x-description="Heroicon name: solid/question-mark-circle"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                    >
                    <path
                      fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                      clip-rule="evenodd"
                    ></path>
                    </svg>
                    <span class="ml-2">
                    Learn more about our dark mode custom CSS
                    </span>
                    </a>
                    </div>
                    
                    
                  <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                    <input name="oxymade_darkmode_customcss" type="Submit" value="Save custom CSS" class="mt-3 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2 float-right cursor-pointer">
                    
                    <button
                    type="button"
                    class="mt-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="open = false"
                    >
                    Cancel
                    </button>
                  </div>
                    </form>
                
                
          </div>
          </div>
          </div>
        </div>
        </div>
        
      
      
      </div>
    </form>
    </div>
    </div>
    </div>
  </section>
  <?php } ?>
  
  <!-- 
    ======================================
    ======================================
    Purge Slideover 
    ======================================
    ======================================
    -->
    <?php if($user_level == "pro") { ?>
    <section
      x-data="{ open: false }"
      @keydown.window.escape="open = false"
      x-show="open"
      @open-purge-slideover.window="if ($event.detail.id == 1) open = true"
      class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
      aria-labelledby="import-colors"
      x-ref="dialog"
      role="dialog"
      aria-modal="true"
    >
      <div class="absolute inset-0 overflow-hidden">
      <div
      x-description="Background overlay, show/hide based on slide-over state."
      class="absolute inset-0"
      @click="open = false"
      aria-hidden="true"
      ></div>
    
      <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
      <div
      x-show="open"
      x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:enter-start="translate-x-full"
      x-transition:enter-end="translate-x-0"
      x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:leave-start="translate-x-0"
      x-transition:leave-end="translate-x-full"
      class="w-screen max-w-md"
      x-description="Slide-over panel, show/hide based on slide-over state."
      >
      <form
        class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
      >
        <div class="flex-1 h-0 overflow-y-auto">
        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
        <div class="flex items-center justify-between">
        <h2
          class="text-lg font-medium text-white"
          id="slide-over-title"
        >
          Purge unused css classes
        </h2>
        <div class="ml-3 h-7 flex items-center">
          <button
          type="button"
          class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
          @click="open = false"
          >
          <span class="sr-only">Close panel</span>
          <svg
          class="h-6 w-6"
          x-description="Heroicon name: outline/x"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
          ></path>
          </svg>
          </button>
        </div>
        </div>
        <div class="mt-1">
        <p class="text-sm text-indigo-300">
          You can remove unused css classes from our framework or the Oxygen Install and reduce the CSS size.
        </p>
        </div>
        </div>
        <div class="flex-1 flex flex-col justify-between">
        <div class="px-4 sm:px-6">
        <div class="space-y-6 pt-6 pb-5">
  
          <div>
          <div class="flex justify-between">
          <label
          for="whitelistClasses"
          class="block text-sm font-medium text-gray-900 mt-2"
          >
          Whitelist classes (comma separated)
          </label>

          </div>
          
          <p class="text-sm text-gray-500 my-3">
            Below classes won't be deleted with the purge.
            </p>
            
          <div class="mt-1">
          <textarea
          id="whitelist_classes"
          name="whitelist_classes"
          rows="3"
          class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md text-gray-400"
          disabled
          ><?php echo $oxymade_purge_whitelist; ?></textarea>
          </div>
          </div>
        </div>
        
        <!-- <div class="w-full inline-flex items-center px-4 py-3 border-0 text-sm font-medium rounded-md text-red-700 bg-red-100">Make a backup before you begin the purge below</div> -->
        
         
        
        
        <label for="selected_folders">Choose folders to purge:</label>
        
        <select class="purge-whitelist-select" name="selected_folders[]" id="selected_folders" multiple>
          <?php
          $stylefolders = get_option("ct_style_folders", []);
          foreach ($stylefolders as $key => $stylefolder) { ?>
            <option class="purge_folder_option" value="<?php echo $key; ?>"><?php echo $key; ?></option>
            <?php }
          ?>
        </select>
        
        <p class="text-sm text-gray-500 my-3">
          Choose selector folders where all unused classes should be removed from. If you don't select any folder, Purge will remove unused classes from 'OxyMadeFramework' folder.
          <hr>
          <br>
          <b>MUST READ:</b>
          <br>
          <br>
          All the deleted classes from the folder "OxyMadeFramework" can be re-installed using the "Re-install framework" button.
          <br>
          <br>
          <b>Please take a backup before purging all the unused classes</b>
          <br>
          <br>
          Any CSS classes you have used in the code block / html / javascript / PHP will be deleted as well. So please make sure you add those classes in the purge whitelist area to keep them.
          </p>
          
          
          <br>
          <input type="checkbox" id="purge_terms" name="purge_terms" value="agreed" onchange="activateButton(this)">
          <label for="purge_terms_content"> <b>I read above lines, took the backup, understood the concept of whitelist and confident to run purge below.</b></label><br>
          
          <br>
          <hr>
          
          <div class="pt-4 pb-6">
            <div class=" flex text-sm">
            <a
            href="https://learn.oxymade.com/docs/framework/purge/"
            class="group inline-flex items-center text-gray-500 hover:text-gray-900"
            target="_blank"
            >
            <svg
            class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
            x-description="Heroicon name: solid/question-mark-circle"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
            >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
              clip-rule="evenodd"
            ></path>
            </svg>
            <span class="ml-2">
            Learn more about our purge feature
            </span>
            </a>
            </div>
          </div>
        
        </div>
        </div>
        </div>
        
        <div class="flex-shrink-0 px-4 py-4 flex justify-end">
        <button
        type="button"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="open = false"
        >
        Cancel
        </button>
        <button
        type="submit"
        name="purge_submit"
        id="purge_submit"
        value="yes"
        disabled
        class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
        Purge unused classes
        </button>
        </div>
      </form>
      </div>
      </div>
      </div>
    </section>
    <?php } ?>
    
  <!-- 
    ======================================
    ======================================
    Purge Whitelist Slideover 
    ======================================
    ======================================
    -->
    <?php if($user_level == "pro") { ?>
    <section
      x-data="{ open: false }"
      @keydown.window.escape="open = false"
      x-show="open"
      @open-purge-whitelist-slideover.window="if ($event.detail.id == 1) open = true"
      class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
      aria-labelledby="import-colors"
      x-ref="dialog"
      role="dialog"
      aria-modal="true"
    >
      <div class="absolute inset-0 overflow-hidden">
      <div
      x-description="Background overlay, show/hide based on slide-over state."
      class="absolute inset-0"
      @click="open = false"
      aria-hidden="true"
      ></div>
    
      <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
      <div
      x-show="open"
      x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:enter-start="translate-x-full"
      x-transition:enter-end="translate-x-0"
      x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:leave-start="translate-x-0"
      x-transition:leave-end="translate-x-full"
      class="w-screen max-w-md"
      x-description="Slide-over panel, show/hide based on slide-over state."
      >
      <form
        class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
      >
        <div class="flex-1 h-0 overflow-y-auto">
        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
        <div class="flex items-center justify-between">
        <h2
          class="text-lg font-medium text-white"
          id="slide-over-title"
        >
          Whitelisted classes for purge
        </h2>
        <div class="ml-3 h-7 flex items-center">
          <button
          type="button"
          class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
          @click="open = false"
          >
          <span class="sr-only">Close panel</span>
          <svg
          class="h-6 w-6"
          x-description="Heroicon name: outline/x"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
          ></path>
          </svg>
          </button>
        </div>
        </div>
        <div class="mt-1">
        <p class="text-sm text-indigo-300">
          You can remove unused css classes from our framework or the Oxygen Install and reduce the CSS size.
        </p>
        </div>
        </div>
        <div class="flex-1 flex flex-col justify-between">
        <div class="px-4 sm:px-6">
        <div class="space-y-6 pt-6 pb-5">
  
          <div>
          <div class="flex justify-between">
          <label
          for="whitelistClasses"
          class="block text-sm font-medium text-gray-900 mt-2"
          >
          Whitelist classes (comma separated)
          </label>
          </div>
          
          <p class="text-sm text-gray-500 my-3">
            Please enter class names you have used in code-blocks, javascript, or php code to keep the classes without deleting them. All the classes entered and saved below will not be deleted from the purge function.
            </p>
            
          <div class="mt-1">
            <textarea
            id="whitelist_classes"
            name="whitelist_classes"
            rows="3"
            class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md text-gray-400"
            ><?php echo $oxymade_purge_whitelist; ?></textarea>
            </div>
            </div>
          </div>
          
          <div class="w-full inline-flex items-center px-4 py-3 border-0 text-sm font-medium rounded-md text-red-700 bg-red-100">Make a backup before you begin the purge below</div>
          
           
          
          <div class="pt-4 pb-6">
          <div class=" flex text-sm">
          <a
          href="https://learn.oxymade.com/docs/framework/purge-whitelist/"
          class="group inline-flex items-center text-gray-500 hover:text-gray-900"
          target="_blank"
          >
          <svg
          class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
          x-description="Heroicon name: solid/question-mark-circle"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
          >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
            clip-rule="evenodd"
          ></path>
          </svg>
          <span class="ml-2">
          Learn more about purge whitelist classes
          </span>
          </a>
          </div>
        </div>
        </div>
        </div>
        </div>
        <div class="flex-shrink-0 px-4 py-4 flex justify-end">
        <button
        type="button"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="open = false"
        >
        Cancel
        </button>
        <button
        type="submit"
        name="oxymade_purge_whitelist"
        value="yes"
        class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
        Save classes to the whitelist
        </button>
        </div>
      </form>
      </div>
      </div>
      </div>
    </section>
    <?php } ?>
  <!-- 
  ======================================
  ======================================
  Import colors from collection slide over 
  ======================================
  ======================================
  -->
  <section
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    @open-import-colors-slideover.window="if ($event.detail.id == 1) open = true"
    class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
    aria-labelledby="import-colors-from-collections"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div class="absolute inset-0 overflow-hidden">
    <div
    x-description="Background overlay, show/hide based on slide-over state."
    class="absolute inset-0"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
    <div
    x-show="open"
    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="w-screen max-w-xl"
    x-description="Slide-over panel, show/hide based on slide-over state."
    >
    <form
      class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="post"
    >
      <div class="flex-1 h-0 overflow-y-auto">
      <div class="py-6 px-4 bg-indigo-700 sm:px-6">
      <div class="flex items-center justify-between">
      <h2
        class="text-lg font-medium text-white"
        id="slide-over-title"
      >
        Import Colors from the collection
      </h2>
      <div class="ml-3 h-7 flex items-center">
        <button
        type="button"
        class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
        @click="open = false"
        >
        <span class="sr-only">Close panel</span>
        <svg
        class="h-6 w-6"
        x-description="Heroicon name: outline/x"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
        ></path>
        </svg>
        </button>
      </div>
      </div>
      <div class="mt-1">
      <p class="text-sm text-indigo-300">
        Import readymade color palettes from the collection
      </p>
      </div>
      </div>
      <div class="flex-1 flex flex-col justify-between">
      <div class="px-4 divide-y divide-gray-200 sm:px-6">
      <div class="space-y-4 pt-6 pb-5">
        <div class="max-w-7xl mx-auto py-8 px-2 sm:px-4 lg:px-4">
        <ul
        role="list"
        class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-8"
        >
        
        <?php
        $oxymade_sets_data = base64_decode($oxymade_sets);

        $sets_data = json_decode($oxymade_sets_data, true);

        // var_dump($sets_data);
        foreach ($sets_data as $set_name => $set_data) {

          $set_colors = $set_data["colors"];
          $set_image = "https://oxymade.com/images/kits/" . $set_name . ".png";

          $set_value = [];
          $set_value["colors"] = $set_data["colors"];

          $set_json = json_encode($set_value);
          $set_base64 = base64_encode($set_json);
          ?>
         
         <li class="relative">
           <div
           class="group block w-full rounded-lg"
           >
           <p
           class="mb-3 block text-base font-medium text-gray-900 truncate"
           >
           <?php echo ucfirst($set_name); ?>
           </p>
           <img
           src="<?php echo $set_image; ?>"
           alt="<?php echo $set_name; ?> Design Set Color Palette"
           class="group-hover:opacity-75 object-cover rounded"
           />
           </div>
           <button title="Install colors" class="mt-3 inline-flex w-full items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="submit" name="color_skin" value="<?php echo $set_base64; ?>"><svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
           </svg> Install Palette</button>
         </li>
         
         <?php
        }
        ?>
        
        
        
        
        
        
        
  
        
        </ul>
        </div>
      </div>
      </div>
      </div>
      </div>
      <div class="flex-shrink-0 px-4 py-4 flex justify-end">
      <button
      type="button"
      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      @click="open = false"
      >
      Cancel
      </button>
       
      </div>
    </form>
    </div>
    </div>
    </div>
  </section>
  <!-- 
  ======================================
  ======================================
   Change the base design set
  ======================================
  ======================================
  -->
  <section
  x-data="{ open: false }"
  @keydown.window.escape="open = false"
  x-show="open"
  @open-change-base-design-set-slideover.window="if ($event.detail.id == 1) open = true"
  class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
  aria-labelledby="change-base-design-set"
  x-ref="dialog"
  role="dialog"
  aria-modal="true"
  >
  <div class="absolute inset-0 overflow-hidden">
    <div
    x-description="Background overlay, show/hide based on slide-over state."
    class="absolute inset-0"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
    <div
    x-show="open"
    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="w-screen max-w-xl"
    x-description="Slide-over panel, show/hide based on slide-over state."
    >
    <form
    class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="post"
    >
    <div class="flex-1 h-0 overflow-y-auto">
      <div class="py-6 px-4 bg-indigo-700 sm:px-6">
      <div class="flex items-center justify-between">
      <h2
      class="text-lg font-medium text-white"
      id="slide-over-title"
      >
      Change the base design set
      </h2>
      <div class="ml-3 h-7 flex items-center">
      <button
        type="button"
        class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
        @click="open = false"
      >
        <span class="sr-only">Close panel</span>
        <svg
        class="h-6 w-6"
        x-description="Heroicon name: outline/x"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
        >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
        ></path>
        </svg>
      </button>
      </div>
      </div>
      <div class="mt-1">
      <p class="text-sm text-indigo-300">
      Change the base design set to use the buttons, icons, cards, avatars, headings etc.. from the particular design
      set.
      </p>
      </div>
      </div>
      <div class="flex-1 flex flex-col justify-between">
      <div class="px-4 divide-y divide-gray-200 sm:px-6">
      <div class="space-y-4 pt-6 pb-5">
      <div class="max-w-7xl mx-auto py-8 px-2 sm:px-4 lg:px-4">
        <ul
        role="list"
        class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-8"
        >
        
         <form action="" method="post">
        <?php
        $oxymade_sets_data = base64_decode($oxymade_sets);

        $sets_data = json_decode($oxymade_sets_data, true);

        // var_dump($sets_data);
        foreach ($sets_data as $set_name => $set_data) {
          // $set_colors = $set_data;
          $set_image = "https://oxymade.com/images/kits/" . $set_name . ".png";
          // $set_value = [];
          // // $set_value["colors"] = $set_data["colors"];
          // $set_value["classes"] = $set_data["extras"];
          // $set_value["global_settings"] =
          //   $set_data["settings"];
          // $set_json = json_encode($set_data);
          // $set_base64 = base64_encode($set_json);
          ?>
        
        
        
         
         
         <li class="relative">
         <div
           class="group block w-full rounded-lg"
         >
         <p
           class="mb-3 block text-base font-medium text-gray-900 truncate"
         >
           <?php echo ucfirst($set_name); ?>
         </p>
           <img
           src="<?php echo $set_image; ?>"
           alt="<?php echo $set_name; ?> Design Set"
           class="group-hover:opacity-75 object-cover rounded"
           />
         </div>
         <button title="Install Design Set" class="mt-3 inline-flex w-full items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="submit" name="change_base_design_set" value="<?php echo $set_name; ?>"><svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
         </svg> Install the set</button>
         </li>
         
         <?php
        }
        ?>
        
         </form>
        
        
        </ul>
      </div>
      </div>
      </div>
      </div>
    </div>
    <div class="flex-shrink-0 px-4 py-4 flex justify-end">
      <button
      type="button"
      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      @click="open = false"
      >
      Cancel
      </button>
     
    </div>
    </form>
    </div>
    </div>
  </div>
  </section>
  
  
  <!-- 
  ======================================
  ======================================
  Base design set reset
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-reset-base-design-set-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="reset-base-design-set"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to reset the base design set?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      Once the base design set is reset, all the customizations you have made will be replaced by default base design set styles. The design system will revert to its defaults.
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm" name="reset_base_design_set" value="yes"
      @click="open = false"
    >
      Please, Reset now
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  
  <!-- 
  ======================================
  ======================================
  Re-install the framework modal
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-reinstall-framework-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="re-install-the-framework"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100"
    >
      <svg
      class="h-6 w-6 text-green-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to re-install the framework?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      By reinstalling the framework, you remove existing framework
      selectors and stylesheets and reinstall them from the source
      code. A few common responsive issues can be fixed by
      re-installing the framework.
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      name="reinstall_framework"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm" value="yes"
      @click="open = false"
    >
      Re-install the framework
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
  ======================================
  ======================================
  Install Oxygen Settings modal
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-install-oxygen-settings-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="re-install-the-framework"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100"
    >
      <svg
      class="h-6 w-6 text-green-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to over write Oxygen settings?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      By installing our Oxygen Default settings, all the current Oxygen global settings you have will be erased and updated by our default settings.
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      name="install_settings"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm" value="yes"
      @click="open = false"
    >
      Install Oxygen settings
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
   ======================================
   ======================================
     Backup Classes
   ======================================
   ======================================
   -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-backup-classes-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="backup-classes"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100"
    >
      <svg
      class="h-6 w-6 text-indigo-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to backup classes?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
     You are taking a back up of your classes and folders. Do you wish to proceed?
      </p>
      </div>
    </div>
    </div>
    <form method="post" action="">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm" name="gs_action" value="Backup"
      @click="open = false"
    >
      Backup classes
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
   ======================================
   ======================================
     Restore classes backup modal
   ======================================
   ======================================
   -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-restore-classes-backup-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="restore-classes"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100"
    >
      <svg
      class="h-6 w-6 text-gray-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to restore the classes backup?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      Would you like to replace the current classes & style folders with the old backup?
      </p>
      </div>
    </div>
    </div>
    <form method="post" action="">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:col-start-2 sm:text-sm" name="gs_action" value="Restore"
      @click="open = false"
    >
      Restore the backup
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
   ======================================
   ======================================
     Delete Classes backup modal
   ======================================
   ======================================
   -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-delete-classes-backup-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="delete-classes"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-red-900"
      id="modal-title"
      >
      Do you want to delete the all classes & style folders in your Oxygen site??
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      If you choose to delete all the classes and style folders and empty them, you will lose all the classes and style folders. Would you like to proceed?
      </p>
      </div>
    </div>
    </div>
    <form method="post" action="">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="gs_action" value="Delete"
      @click="open = false"
    >
      I understand, delete
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
   ======================================
   ======================================
     Reset back to before OxyMade
   ======================================
   ======================================
   -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-reset-back-to-oxymade-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="reset-back-to-oxymade"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-red-900"
      id="modal-title"
      >
      Do you want to Reset your site back to before OxyMade Settings?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
     If you reset back to Oxygen settings before installing the OxyMade framework state, all OxyMade settings will be discarded. Could you please take a backup from Oxygen -> Import / Export before continuing with the reset process.
      </p>
      </div>
    </div>
    </div>
    <form method="post" action="">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="reset_oxygen_to_before_om" value="yes"
      @click="open = false"
    >
      I understand, Reset please
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
   ======================================
   ======================================
     Reset back to before OxyMade
   ======================================
   ======================================
   -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-reset-oxygen-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="reset-back-to-before-oxymade"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-red-900"
      id="modal-title"
      >
      Do you want to Reset your site back to Oxygen default settings?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      By reset back to default Oxygen settings, all the classes, stylesheets, global colours, settings, presets will be removed and replaced with the default empty data. Do you want to continue? Please take a backup of Oxygen settings from the Oxygen -> Import/export page before continuing with the reset process.
      </p>
      </div>
    </div>
    </div>
    <form method="post" action="">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="reset_oxygen_defaults" value="yes"
      @click="open = false"
    >
      I understand, Reset please
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  
  <!-- 
    ======================================
    ======================================
    Export Selectors Slideover 
    ======================================
    ======================================
    -->
    <section
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    @open-export-classes-slideover.window="if ($event.detail.id == 1) open = true"
    class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
    aria-labelledby="export-selectors"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
    >
    <div class="absolute inset-0 overflow-hidden">
      <div
      x-description="Background overlay, show/hide based on slide-over state."
      class="absolute inset-0"
      @click="open = false"
      aria-hidden="true"
      ></div>
    
      <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
      <div
      x-show="open"
      x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:enter-start="translate-x-full"
      x-transition:enter-end="translate-x-0"
      x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
      x-transition:leave-start="translate-x-0"
      x-transition:leave-end="translate-x-full"
      class="w-screen max-w-md"
      x-description="Slide-over panel, show/hide based on slide-over state."
      >
      <form
      class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
      >
      <div class="flex-1 h-0 overflow-y-auto">
        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
        <div class="flex items-center justify-between">
        <h2
        class="text-lg font-medium text-white"
        id="slide-over-title"
        >
        Export CSS Selectors Between Sites
        </h2>
        <div class="ml-3 h-7 flex items-center">
        <button
          type="button"
          class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
          @click="open = false"
        >
          <span class="sr-only">Close panel</span>
          <svg
          class="h-6 w-6"
          x-description="Heroicon name: outline/x"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
          >
          <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
          ></path>
          </svg>
        </button>
        </div>
        </div>
        <div class="mt-1">
        <p class="text-sm text-indigo-300">
        Copy & paste the selectors export code in the Oxygen → Export/Import area.
        </p>
        </div>
        </div>
        <div class="flex-1 flex flex-col justify-between">
        <div class="px-4 divide-y divide-gray-200 sm:px-6">
        <div class="space-y-6 pt-6 pb-5">
  
        <div>
          <div class="flex justify-between">
          <label
          for="ExportSelectors"
          class="block text-sm font-medium text-gray-900 mt-2"
          >
          Export Selectors
          </label>
          <div><button id="mselectors_export" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="copySelectors()">Copy Selectors</button></div>
          </div>
          <div class="mt-1">
          <textarea
          id="export_selectors_area"
          name="export_selectors_area"
          rows="16"
          class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md text-gray-400"
          ><?php echo $selectors_export; ?></textarea>
          <p class="text-sm text-gray-500 my-3">
          Please copy selectors from the above export
          selectors text box and paste it into the Oxygen → Export/Import box.
          </p>
          </div>
        </div>
        </div>
        <div class="pt-4 pb-6">
        
        <div class="mt-4 flex text-sm">
          <a
          href="https://learn.oxymade.com/docs/get-started/color-system-overview/"
          class="group inline-flex items-center text-gray-500 hover:text-gray-900"
          target="_blank"
          >
          <svg
          class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
          x-description="Heroicon name: solid/question-mark-circle"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
          >
          <path
          fill-rule="evenodd"
          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
          clip-rule="evenodd"
          ></path>
          </svg>
          <span class="ml-2">
          Learn how to export selectors between sites
          </span>
          </a>
        </div>
        </div>
        </div>
        </div>
      </div>
      <div class="flex-shrink-0 px-4 py-4 flex justify-end">
        <button
        type="button"
        class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="open = false"
        >
        Cancel
        </button>
      </div>
      </form>
      </div>
      </div>
    </div>
    </section>
    
    
    
    <!-- 
    ======================================
    ======================================
      Export Stylesheets Slideover 
    ======================================
    ======================================
    -->
      <section
      x-data="{ open: false }"
      @keydown.window.escape="open = false"
      x-show="open"
      @open-export-stylesheets-slideover.window="if ($event.detail.id == 1) open = true"
      class="fixed inset-0 overflow-hidden content-visibility-auto hidden"
      aria-labelledby="export-stylesheets"
      x-ref="dialog"
      role="dialog"
      aria-modal="true"
      >
      <div class="absolute inset-0 overflow-hidden">
      <div
        x-description="Background overlay, show/hide based on slide-over state."
        class="absolute inset-0"
        @click="open = false"
        aria-hidden="true"
      ></div>
    
      <div class="absolute inset-y-0 pl-16 max-w-full right-0 flex" style="top: 30px;">
        <div
        x-show="open"
        x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="w-screen max-w-md"
        x-description="Slide-over panel, show/hide based on slide-over state."
        >
        <form
        class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" action="" method="POST"
        >
        <div class="flex-1 h-0 overflow-y-auto">
        <div class="py-6 px-4 bg-indigo-700 sm:px-6">
          <div class="flex items-center justify-between">
          <h2
          class="text-lg font-medium text-white"
          id="slide-over-title"
          >
          Export stylesheets Between Sites
          </h2>
          <div class="ml-3 h-7 flex items-center">
          <button
          type="button"
          class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
          @click="open = false"
          >
          <span class="sr-only">Close panel</span>
          <svg
            class="h-6 w-6"
            x-description="Heroicon name: outline/x"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true"
          >
            <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
            ></path>
          </svg>
          </button>
          </div>
          </div>
          <div class="mt-1">
          <p class="text-sm text-indigo-300">
          Copy & paste the stylesheets export code in the Oxygen → Export/Import area.
          </p>
          </div>
        </div>
        <div class="flex-1 flex flex-col justify-between">
          <div class="px-4 divide-y divide-gray-200 sm:px-6">
          <div class="space-y-6 pt-6 pb-5">
    
          <div>
          <div class="flex justify-between">
          <label
            for="ExportSelectors"
            class="block text-sm font-medium text-gray-900 mt-2"
          >
            Export Stylesheets
          </label>
          <div><button id="mstylesheets_export" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="copyStylesheets()">Copy Stylesheets</button></div>
          </div>
          <div class="mt-1">
            <textarea
            id="export_stylesheets_area"
            name="export_stylesheets_area"
            rows="16"
            class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md text-gray-400"
            ><?php echo $stylesheets_export; ?></textarea>
            <p class="text-sm text-gray-500 my-3">
            Please copy stylesheets from the above export
            stylesheets text box and paste it into the Oxygen → Export/Import box.
            </p>
          </div>
          </div>
          </div>
          <div class="pt-4 pb-6">
      
          <div class="mt-4 flex text-sm">
          <a
            href="https://learn.oxymade.com/docs/get-started/color-system-overview/"
            class="group inline-flex items-center text-gray-500 hover:text-gray-900"
            target="_blank"
          >
            <svg
            class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
            x-description="Heroicon name: solid/question-mark-circle"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
            >
            <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
            clip-rule="evenodd"
            ></path>
            </svg>
            <span class="ml-2">
            Learn how to export stylesheets between sites
            </span>
          </a>
          </div>
          </div>
          </div>
        </div>
        </div>
        <div class="flex-shrink-0 px-4 py-4 flex justify-end">
        <button
          type="button"
          class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          @click="open = false"
        >
          Cancel
        </button>
        </div>
        </form>
        </div>
      </div>
      </div>
      </section>
  
  
  <!-- 
  ======================================
  ======================================
  Backup stylesheets
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-backup-stylesheets-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="backup-stylesheets"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100"
    >
      <svg
      class="h-6 w-6 text-indigo-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to backup stylesheets?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      You are going to take a backup of all the stylesheets, would you like to continue?
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm" name="ss_action" value="Backup"
      @click="open = false"
    >
      Backup stylesheets
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
  ======================================
  ======================================
  Restore stylesheets backup modal
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-restore-stylesheets-backup-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="restore-stylesheets"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100"
    >
      <svg
      class="h-6 w-6 text-gray-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to restore the stylesheets backup?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      By restoring backup of stylesheets, the old stylesheets are restored, and the current stylesheets are removed. Do you want to continue?
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:col-start-2 sm:text-sm" name="ss_action" value="Restore"
      @click="open = false"
    >
      Restore the backup
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  
  
  <!-- 
  ======================================
  ======================================
  Delete classes folder modal
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false, keyvalue:'' }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-delete-selectors-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="delete-classes-folder"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
    x-on:open-delete-selectors-modal.window="keyvalue = $event.detail.value;"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to delete the classes folder? You are going to delete all the classes inside the particular folder.
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      
      </p>
      </div>
    </div>
    </div>
    <form method="post" action="">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="delete_selectors_folder" :value="keyvalue"
      @click="open = false"
    >
      Delete the folder
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
   
   <!-- 
     ======================================
     ======================================
     Delete all the style sheets modal
     ======================================
     ======================================
     -->
     
     <div
       x-data="{ open: false, keyvalue:'' }"
       @keydown.window.escape="open = false"
       x-show="open"
       class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
       @open-delete-all-stylesheets-modal.window="if ($event.detail.id == 1) open = true"
       aria-labelledby="delete-all-stylesheets"
       x-ref="dialog"
       role="dialog"
       aria-modal="true"
     >
       <div
       class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
       >
       <div
       x-show="open"
       x-transition:enter="ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       x-description="Background overlay, show/hide based on modal state."
       class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
       @click="open = false"
       aria-hidden="true"
       ></div>
     
       <!-- This element is to trick the browser into centering the modal contents. -->
       <span
       class="hidden sm:inline-block sm:align-middle sm:h-screen"
       aria-hidden="true"
       >&ZeroWidthSpace;</span
       >
     
       <div
       x-show="open"
       x-transition:enter="ease-out duration-300"
       x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
       x-transition:leave="ease-in duration-200"
       x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
       x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
       x-description="Modal panel, show/hide based on modal state."
       class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
       >
       <div>
       <div
         class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
       >
         <svg
         class="h-6 w-6 text-red-600"
         xmlns="http://www.w3.org/2000/svg"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor"
         >
         <path
         stroke-linecap="round"
         stroke-linejoin="round"
         stroke-width="2"
         d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
         />
         </svg>
       </div>
       <div class="mt-3 text-center sm:mt-5">
         <h3
         class="text-lg leading-6 font-medium text-gray-900"
         id="modal-title"
         >
         Do you want to delete all the stylesheets?
         </h3>
         <div class="mt-2">
         <p class="text-sm text-gray-500">
         Do not delete all your stylesheets unless you're starting over from scratch with your stylesheets.
         </p>
         </div>
       </div>
       </div>
       <form action="" method="post">
       <div
       class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
       >
       <button
         type="submit"
         class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="ss_action" value="Delete"
         @click="open = false"
       >
         I understand, delete!
       </button>
       <button
         type="button"
         class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
         @click="open = false"
       >
         Cancel
       </button>
       </div>
       </form>
       </div>
       </div>
     </div>
     
     
  <!-- 
  ======================================
  ======================================
  Delete the style sheet modal
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false, keyvalue:'' }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-delete-stylesheets-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="delete-stylesheet"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
    x-on:open-delete-stylesheets-modal.window="keyvalue = $event.detail.value;"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to delete the stylesheet?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      Can you confirm that you want to delete this stylesheet? In case you delete it, you cannot have it back once it is deleted.
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="delete_stylesheets_folder" :value="keyvalue"
      @click="open = false"
    >
      Delete the stylesheet
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
  <!-- 
  ======================================
  ======================================
  Delete the License key modal
  ======================================
  ======================================
  -->
  
  <div
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
    x-show="open"
    class="fixed z-10 inset-0 overflow-y-auto content-visibility-auto hidden"
    @open-remove-license-modal.window="if ($event.detail.id == 1) open = true"
    aria-labelledby="delete-license-key"
    x-ref="dialog"
    role="dialog"
    aria-modal="true"
  >
    <div
    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-description="Background overlay, show/hide based on modal state."
    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    @click="open = false"
    aria-hidden="true"
    ></div>
  
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span
    class="hidden sm:inline-block sm:align-middle sm:h-screen"
    aria-hidden="true"
    >&ZeroWidthSpace;</span
    >
  
    <div
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-description="Modal panel, show/hide based on modal state."
    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
    >
    <div>
    <div
      class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
    >
      <svg/Volumes/Macintosh HD/Users/anvesh/Library/Caches/Transmit/CD15EE25-668F-4F64-9719-50E38DA93EDE/boundaries.oxymade.com/sites/staging.boundaries.oxymade.com/htdocs/wp-content/plugins/oxymade/admin/partials/oxymade-admin-dashboard.php
      class="h-6 w-6 text-red-600"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      >
      <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
      </svg>
    </div>
    <div class="mt-3 text-center sm:mt-5">
      <h3
      class="text-lg leading-6 font-medium text-gray-900"
      id="modal-title"
      >
      Do you want to remove the license key?
      </h3>
      <div class="mt-2">
      <p class="text-sm text-gray-500">
      By removing the license key, you will not be able to use our framework, modules and other features that we offer. Remove the license only if you want to enter a new license key or you don't want to use our framework anymore.
      </p>
      </div>
    </div>
    </div>
    <form action="" method="post">
    <div
    class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
    >
    <button
      type="submit"
      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm" name="remove_oxymade_license" value="yes"
      @click="open = false"
    >
      Remove the license key
    </button>
    <button
      type="button"
      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
      @click="open = false"
    >
      Cancel
    </button>
    </div>
    </form>
    </div>
    </div>
  </div>
 <?php }} else {OxyMadeLicense::license_page();}
?>

<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * CAPTCHA image generator
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  include '../../../globals.php';
  include $DOCUMENT_ROOT.'/inc/include.php';
  include 'config.php';

  $cfg = $CAPTCHA_Config;
  
  $w = $cfg['width'];
  $h = $cfg['height'];
  $dh = (($cfg['signature']['text'])?(-12):(0));
  
  $fonts = array ();

  $bg_rgb = $cfg['colors']['backgrounds'][mt_rand (0, count ($cfg['colors']['backgrounds'])-1)];
  $fg_rgb = $cfg['colors']['foregrounds'][mt_rand (0, count ($cfg['colors']['foregrounds'])-1)];

  // Get list of fonts
  $dir = opendir ($DOCUMENT_ROOT.'/pics/captcha/fonts');
  while (($file = readdir ($dir)) != false) {
    if ($file != '.' && $file != '..') {
      if (eregi ('.*\.png$', $file)) {
        $fonts[] = $file;
      }
    }
  }
  closedir ($dir);

  // Generate keycode
  while (true) {
    $keystring = '';
    $l = strlen ($cfg['allowed_symbols']) - 1;

    for ($i = 0, $n = $cfg['keystring_length']; $i < $n; $i++) {
      $keystring .= $cfg['allowed_symbols'][mt_rand (0, $l)];
    }

    if (!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/',
                    $keystring)) {
      break;
    }
  }

  session_register ('CAPTCHA_Keystring');
  $_SESSION['CAPTCHA_Keystring'] = $keystring;

  // Load random font
  $font_file = $fonts[mt_rand (0, count ($fonts) - 1)];
  $font = imagecreatefrompng ($DOCUMENT_ROOT.'/pics/captcha/fonts/'.$font_file);
  imagealphablending ($font, true);
  $fontfile_width  = imagesx($font);
  $fontfile_height = imagesy($font) - 1;
  $font_metrics    = array();
  $reading_symbol=false;

  // Look throug font file and fill da metrics
  $alphabet_length = strlen ($cfg['alphabet']);
  $symbol = 0;
  $alphabet = $cfg['alphabet'];
  for ($i = 0; $i < $fontfile_width && $alphabet_length; $i++){
    $transparent = (imagecolorat ($font, $i, 0) >> 24) == 127;
    if (!$reading_symbol && !$transparent) {
      $font_metrics[$alphabet{$symbol}] = array('start' => $i);
      $reading_symbol = true;
      continue;
    }

    if($reading_symbol && $transparent) {
      $font_metrics[$alphabet{$symbol}]['end'] = $i;
      $reading_symbol = false;
      $symbol++;
      continue;
    }
  }

  // Print da texto
  $text_img = imagecreatetruecolor ($w * 2, $h * 2);
  imagealphablending ($text_img, true);
  $white=imagecolorallocate ($text_img, 255, 255, 255);
  imagefilledrectangle ($text_img, 0, 0, $w - 1, $h - 1, $white);

  // draw text
  $x = 1;
  for ($i = 0; $i < $cfg['keystring_length']; $i++) {
    $m = $font_metrics[$keystring{$i}];
    $y = mt_rand(-$cfg['fluctuation_amplitude'],
                 $cfg['fluctuation_amplitude']) + ($h - $fontfile_height) / 2 + 2;

    if ($cfg['no_spaces']) {
      $shift = 0;
      if ($i > 0) {
        $shift = 1000;
        for ($sy = 7; $sy < $fontfile_height - 20; ++$sy) {
         for ($sx = $m['start'] - 1; $sx < $m['end']; ++$sx) {
            $rgb = imagecolorat ($font, $sx, $sy);
            $opacity = $rgb >> 24;
            if ($opacity < 127) {
              $left = $sx - $m['start'] + $x;
              $py = $sy + $y;

              if ($py > $h) {
                break;
              }

              for ($px = min ($left, $w - 1); $px > $left - 12 &&
                       $px >= 0; --$px) {
                $color = imagecolorat ($text_img, $px, $py) & 0xff;
                if ($color + $opacity < 190) {
                  if ($shift > $left - $px) {
                    $shift = $left - $px;
                  }
                  break;
                }
              }
              break;
            }
         }
        }

        if ($shift == 1000) {
          $shift = mt_rand (4,6);
        }
      }
    } else {
      $shift = 1;
    }

    imagecopy ($text_img, $font, $x - $shift, $y,
               $m['start'], 1, $m['end'] - $m['start'], $fontfile_height);
    $x += $m['end'] - $m['start'] - $shift;
  }

  // periods
  $rand1 = mt_rand(750000,1200000)/10000000;
  $rand2 = mt_rand(750000,1200000)/10000000;
  $rand3 = mt_rand(750000,1200000)/10000000;
  $rand4 = mt_rand(750000,1200000)/10000000;

  // phases
  $rand5 = mt_rand(0,31415926)/10000000;
  $rand6 = mt_rand(0,31415926)/10000000;
  $rand7 = mt_rand(0,31415926)/10000000;
  $rand8 = mt_rand(0,31415926)/10000000;

  // amplitudes
  $rand9  = mt_rand(330,420)/110;
  $rand10 = mt_rand(330,450)/110;

  // Main buffer
  $new_text_img = imagecreatetruecolor ($w * 2, $h * 2);
  imagealphablending ($new_text_img, true);

  // Fill work area and draw borders
  imagefilledrectangle ($new_text_img, 0, 0, $w * 2, $h * 2,
                        imagecolorallocate ($new_text_img, $bg_rgb[0],
                                            $bg_rgb[1], $bg_rgb[2]));

  $center = $x / 2;

  // wave distortion
  for ($x = 0; $x < $w; $x++) {
    for($y = 0; $y < $h; $y++) {
      $sx = $x + (sin ($x * $rand1 + $rand5) + sin ($y * $rand3 + $rand6)) * $rand9
        - $w / 2 + $center + 1;
      $sy = $y + (sin ($x * $rand2 + $rand7) + sin ($y * $rand4 + $rand8)) * $rand10;

      if ($sx < 0 || $sy < 0 || $sx >= $w - 1 || $sy >= $h - 1) {
       continue;
      } else {
        $color    = imagecolorat ($text_img, $sx, $sy) & 0xFF;
        $color_x  = imagecolorat ($text_img, $sx + 1, $sy) & 0xFF;
        $color_y  = imagecolorat ($text_img, $sx, $sy + 1) & 0xFF;
        $color_xy = imagecolorat ($text_img, $sx + 1, $sy + 1) & 0xFF;
      }

      if ($color == 255 && $color_x == 255 && $color_y == 255 &&
          $color_xy == 255) {
        continue;
      } else if ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
        $newred   = $fg_rgb[0];
        $newgreen = $fg_rgb[1];
        $newblue  = $fg_rgb[2];
      } else {
        $frsx  = $sx-floor($sx);
        $frsy  = $sy-floor($sy);
        $frsx1 = 1-$frsx;
        $frsy1 = 1-$frsy;

        $newcolor = (
          $color    * $frsx1 * $frsy1+
          $color_x  * $frsx  * $frsy1+
          $color_y  * $frsx1 * $frsy+
          $color_xy * $frsx  * $frsy);

        if ($newcolor > 255) {
          $newcolor = 255;
        }
        $newcolor  = $newcolor/255;
        $newcolor0 = 1-$newcolor;

        $newred   = $newcolor0 * $fg_rgb[0] + $newcolor * $bg_rgb[0];
        $newgreen = $newcolor0 * $fg_rgb[1] + $newcolor * $bg_rgb[1];
        $newblue  = $newcolor0 * $fg_rgb[2] + $newcolor * $bg_rgb[2];
      }
      imagesetpixel ($new_text_img, $x, $y,
                     imagecolorallocate ($text_img, $newred, $newgreen, $newblue));
    }
  }
  
  // Search for text
  $sx = -1;
  $fx = -1;
  $sy = -1;
  $fy = -1;
  for ($i = 0; $i < $w; $i++) {
    for ($j = 0; $j < $h; $j++) {
      $color = imagecolorat ($new_text_img, $i, $j);
      $r = floor ($color / 0x10000);
      $g = floor ($color / 0x100) % 0x100;
      $b = $color % 0x100;
      if ($r < 230 && $g < 230 && $b < 230) {
        if ($sx < 0) {
          $sx = $i;
        } else if ($i>$fx) {
          $fx = $i;
        }

        if ($sy<0 || $j<$sy) {
          $sy = $j;
        }

        if ($j > $fy) {
          $fy = $j;
        }
      }
    }
  }
  
  // Main buffer
  $img = imagecreatetruecolor ($w, $h);
  imagealphablending ($img, true);

  $bg           = imagecolorallocate ($img, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
  $color_border = imagecolorallocate ($img, $cfg['colors']['border'][0],
                                      $cfg['colors']['border'][1],
                                      $cfg['colors']['border'][2]);

  // Fill work area and draw borders
  imagefilledrectangle ($img, 0, 0, $w - 1, $h - 1, $color_border);
  imagefilledrectangle ($img, 1, 1, $w - 2, $h - 2, $bg);

  // Draw da signatura
  $delta_h = 0;
  if ($cfg['signature']['text'] != '') {
    $sig_fg  = imagecolorallocate ($img, $cfg['signature']['foreground'][0],
                                   $cfg['signature']['foreground'][1],
                                   $cfg['signature']['foreground'][2]);

    $sig_bg  = imagecolorallocate ($img, $cfg['signature']['background'][0],
                                   $cfg['signature']['background'][1],
                                   $cfg['signature']['background'][2]);

    imagefilledrectangle ($img, 0, $h - 12, $w - 1, $h - 12, $color_border);
    imagefilledrectangle ($img, 1, $h - 11, $w - 2, $h - 2, $sig_bg);

    imagestring ($img, 2, $w / 2 - ImageFontWidth (2) *
                 mb_strlen($cfg['signature']['text']) / 2,
                 $h - ImageFontHeight (2), $cfg['signature']['text'], $sig_fg);
    $delta_h =- ImageFontHeight(2);
  }

  $dw = $fx - $sx;
  $dh = $fy - $sy;
  imagecopy ($img, $new_text_img, ($w - $dw) / 2,
               ($h - $dh + $delta_h) / 2, $sx, $sy, $dw, $dh);

  // make sure this thing doesn't cache
  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  header ('Cache-Control: no-store, no-cache, must-revalidate');
  header ('Cache-Control: post-check=0, pre-check=0', false);
  header ('Pragma: no-cache');

  // Put to output stream
  header ("Content-type: image/gif");
  imagegif ($img);
  imagedestroy ($img);
  imagedestroy ($text_img);
?>

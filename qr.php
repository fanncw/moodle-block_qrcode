<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Render and display a QRCode.
 *
 * @package    block_qrcode
 * @copyright  2016 Chi-Wen Fann (http://www.kipt.com.tw)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Endroid\QrCode\QrCode;
require_once("thirdparty/QrCode/src/QrCode.php");
require_once('../../config.php');
header('Content-Type: image/png');

function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);
   $r=0; $g=0; $b=0;

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array('r' => $r, 'g' => $g, 'b' => $b, 'a' => 0);
   return $rgb; // returns an array with the rgb values
}


$qrcodeData = required_param('data', PARAM_RAW);
$qrcodeRGB = required_param('color', PARAM_RAW);
$qrcodeSize = required_param('size', PARAM_INT);

$code = new QrCode();
$code->setText($qrcodeData);
$code->setSize($qrcodeSize);
$code->setPadding(6);
$code->setErrorCorrection('high');
$code->setForegroundColor(hex2rgb($qrcodeRGB));
$code->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0));
$code->setLabelFontSize(16);
$code->render();


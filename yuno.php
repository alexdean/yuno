<?php
require dirname(__FILE__).'/config.php';

if (!extension_loaded('imagick')) {
  header("HTTP/1.1 500 Server Error");
  echo "Image magick extension not loaded.";
  exit;
}

$token = isset($_GET['auth']) ? urldecode($_GET['auth']) : null;
if (!yuno_auth($token)) {
  header("HTTP/1.1 403 Forbidden");
  echo "Invalid auth token.";
  exit;
}

class YUNoText extends ImagickDraw {
  public function __construct() {
    parent::__construct();
    $this->setFillColor('white');
    $this->setStrokeColor('black');
    $this->setStrokeWidth(1.3);
    $this->setStrokeAntialias(true);
    $this->setTextAlignment(2);
    $this->setFont('impact.ttf');
  }
}

$image = new Imagick('yuno.jpg');
$line1 = strtoupper(trim(urldecode($_GET['name'])));
$line2 = 'Y U NO '.strtoupper(trim(urldecode($_GET['action'])));
$id = yuno_id($line1.' '.$line2);

if (file_exists(yuno_path($id))) {
  echo yuno_url($id);
  exit;
}

// 16 chars @60pt looks ok on 1 line
$top = new YUNoText();
$top->setFontSize(60);
$image->annotateImage($top, 246, 65, 0, $line1);

$len = strlen($line2);
// 24 chars @40pt looks ok on 1 line
if ($len <= 24) {

  $bottom = new YUNoText();
  $bottom->setFontSize(40);
  $image->annotateImage($bottom, 246, 360, 0, $line2);

} else if ($len <= 60) {

  for ($i=$len/2+5; $i>0; $i--) {
    if ($line2[$i] == ' ') {
      $line2_a = substr($line2, 0, $i);
      $line2_b = substr($line2, $i+1);
      //file_put_contents('php://stderr', $line2_a."\n".$line2_b);
      break;
    }
  }
  $bottom1 = new YUNoText();
  $bottom1->setFontSize(30);
  $image->annotateImage($bottom1, 246, 338, 0, $line2_a);

  $bottom2 = new YUNoText();
  $bottom2->setFontSize(30);
  $image->annotateImage($bottom2, 246, 368, 0, $line2_b);
}

file_put_contents(yuno_path($id), $image);
echo yuno_url($id);
?>

<?php
include "AudioToImage.php";
$wave2png = new AudioToImage("suara.wav");
$image = $wave2png->generate_png();
header("Content-Type: image/png");
imagepng($image);

// You can also save image to a file
// i.e.
// imagepng($image, "suara.png");

?>

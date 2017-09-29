# AudioToImage
This program will create image of audio file.

This program can only run under the Linux operating system. This program has been tested in Centos 7 Minimal.

Before using this program, you must first install "Lame" and "Sox". Lame is needed to convert the file from MP3 format to WAV format because SoX does not support MP3 format.

## How To Install

To install Lame, download RPM from the repository then install it on your server.

```bash
cd /opt 
curl --location http://downloads.sourceforge.net/project/lame/lame/3.99/lame-3.99.5.tar.gz > lame-3.99.5.tar.gz
tar zxvf lame-3.99.5.tar.gz 
cd lame-3.99.5
./configure 
make 
make install
cd /opt 
rm -rf lame-3.99.5.tar.gz 
rm -rf lame-3.99.5
```

To install SoX, download RPM from the repository then install it on your server.

```bash
cd /var/development
mkdir /var/development/sox
cd /var/development/sox
curl --location http://vault.centos.org/7.3.1611/os/Source/SPackages/sox-14.4.1-6.el7.src.rpm > sox.rpm
rpm -Uvh sox.rpm
yum install -y sox
rm -rf sox.rpm
```

## Using PHP

```php
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
```

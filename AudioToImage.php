<?php
class WaveToPNG{
	var $input_file = "";
	var $bit_depth = 32; // -b
	var $sample_rate = 200; // -r
	var $number_of_channel = 1; // -c
	var $image_width = 480;
	var $image_height = 140;
	function __construct($input = NULL)
	{
		if($input !== NULL)
		{
			$this->input_file = $input;
		}
	}
	function set_sample_rate($sample_rate)
	{
		$this->sample_rate = $sample_rate;
	}
	function set_bit_depth($bit_depth)
	{
		$this->bit_depth = $bit_depth;
	}
	function set_number_of_channel($number_of_channel)
	{
		$this->number_of_channel = $number_of_channel;
	}
	function set_image_width($image_width)
	{
		$this->image_width = $image_width;
	}
	function set_image_height($image_height)
	{
		$this->image_height = $image_height;
	}
	function normalization_data($data1)
	{
		$data2 = $data1;
		$ndata = count($data1);
		if($ndata >= 5)
		{
			for($i = 2; $i<$ndata-5; $i++)
			{
				$data2[$i] = round(($data2[$i-2] + ($data2[$i-1]*2) + ($data2[$i]*5) + ($data2[$i+1]*2) + $data2[$i+2]) / 11); 
			}
			$data2[0] = round((($data1[0]*7) + ($data1[1]*2) + $data1[2])/10);
			$data2[1] = round((($data1[0]*2) + ($data1[1]*7) + ($data1[2]*2) + $data1[3])/12);
			$data2[$ndata-1] = round((($data1[$ndata-1]*7) + ($data1[$ndata-2]*2) + $data1[$ndata-3])/10);
			$data2[$ndata-2] = round((($data1[$ndata-1]*2) + ($data1[$ndata-2]*7) + ($data1[$ndata-3]*2) + $data1[$ndata-4])/12);
		}
		return $data2;
	}
	function generate_html()
	{
		$command = "sox $this->input_file -b $this->bit_depth -c $this->number_of_channel -r $this->sample_rate -t raw - | od -t u1 -v - | cut -c 9- | sed -e 's/\ / /g' -e 's/ / /g' -e 's/ /,/g' | tr '\n' ','";
		$data = shell_exec($command);
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = trim($data, ",");
		$wave = explode(",", $data);
		$wave = $this->normalization_data($wave);
		$number_of_sample = count($wave);
		$factor = $number_of_sample/$this->image_width; // float
		$samples = array();

		$html = "";
		$html .= "<span style=\"padding:0px; display:inline-block; border:1px solid #DDDDDD; margin:10px 0; height:".$this->image_height."px;\">";
		$html .= "<span style=\"display:table-cell; height:".$this->image_height."px; line-height:".$this->image_height."px; vertical-align:middle;\">";
		for($i = 0; $i < $this->image_width; $i++)
		{
			$j = round($factor*$i);
			if($j < 0)
			{
				$j = 0;
			}
			if($j >= $number_of_sample)
			{
				$j = $number_of_sample - 1;
			}
			$samples[$i] = round($wave[$j] * $this->image_height / 256);
			
			$html .= "<span style=\"display:inline-block; vertical-align:middle; background-color:#069; width:1px; height:".$samples[$i]."px;\"></span>";
			
		}
		$html .= "</span>";
		$html .= "</span>";
		return $html;
	}
	function generate_png($width = NULL, $height = NULL)
	{
		if($width !== NULL && $width > 0)
		{
			$this->image_height = $width;
		}
		if($height !== NULL && $height > 0)
		{
			$this->image_height = $height;
		}
		$data = shell_exec("sox $this->input_file -b $this->bit_depth -c $this->number_of_channel -r $this->sample_rate -t raw - | od -t u1 -v - | cut -c 9- | sed -e 's/\ / /g' -e 's/ / /g' -e 's/ /,/g' | tr '\n' ','");
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = trim($data, ",");
		$wave = explode(",", $data);
		$wave = $this->normalization_data($wave);
		$number_of_sample = count($wave);
		$factor = $number_of_sample/$this->image_width; // float
		$samples = array();
		$image = imagecreatetruecolor($this->image_width, $this->image_height);
		
		$x1 = 0; $y1 = 0; $x2 = $this->image_width - 1; $y2 = $this->image_height - 1;
		$white = imagecolorallocate($image, 255, 255, 255);
		$black = imagecolorallocate($image, 0, 0, 0);
		ImageFilledRectangle($image , $x1 , $y1 , $x2 , $y2 , $white);
		for($i = 0; $i < $this->image_width; $i++)
		{
			$j = round($factor*$i);
			if($j < 0)
			{
				$j = 0;
			}
			if($j >= $number_of_sample)
			{
				$j = $number_of_sample - 1;
			}
			$samples[$i] = round($wave[$j] * $this->image_height / 256);
			$y1 = round(($this->image_height/2) - ($samples[$i]/2));
			$y2 = round(($this->image_height/2) + ($samples[$i]/2));
			$x1 = $x2 = $i;
			imageline($image, $x1, $y1, $x2, $y2, $black);
		}
		imagecolortransparent($image, $white);
		return $image;
	}
}


// example using
if(!file_exists("Number-7.wav"))
{
	shell_exec("lame --decode Number-7.mp3");
}
$wave2png = new WaveToPNG("Number-7.wav");
$mode = 1;
if($mode)
{
	header("Content-Type: image/png");
	$image = $wave2png->generate_png();
	imagepng($image);
}
else
{
	echo $wave2png->generate_html();
}
?>

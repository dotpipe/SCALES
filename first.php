<?php

    class ImageMaker {

        // arrest minority/majority
        public $asian;
        public $color_asian = 0xFAF;
        public $afro;
        public $color_afro = 0xFFFFFF;
        public $caucasian;
        public $color_caucasian = 0x0;
        public $latin;
        public $color_latin = 0xFFF;
        public $mid_eastern;
        public $color_mideast = 0xFFFF;
        public $european;
        public $color_euro = 0xFFFAA;
        public $ethnicity_total_pct;
        public $color;
        // Date of arrest
        public $date;
        // Image of arrests
        public $image;
        public $image_candle;
        // Candle height counter
        public $candle_cnt;
        // Days counted
        public $day_cnt = 7;
        // Image Scale
        public $image_scale = 100;
        public $image_width = 1000;
        public $image_height = 400;

        public function start_image ($race)
        {
            
            $this->image_candle = imagecreatetruecolor(25, 100);
            $this->candle_name = $race;
            $this->image = imagecreatetruecolor($this->image_width, $this->image_height);
            imagefilledrectangle($this->image, 0, 0, $this->image_width, $this->image_height, 0xDDDDDD);
            imagefilter($this->image, IMG_FILTER_GRAYSCALE);
            return $this;
        }

        public function draw_arrest_pct ($percent, $between = 0)
        {
            imagedashedline($this->image, 0, $percent%$this->image_scale, $this->image_width, $percent%$this->image_scale, 0x00);
            while ($percent < $this->image_height && $between > 0) {
                $percent += $between;
                imagedashedline($this->image, 0, $percent%$this->image_height, $this->image_width, $percent%$this->image_height, 0x00);
            }
            return $this;
        }

        public function set_ethnic_color(string $ethnicity)
        {
            $color = 'color_' . $ethnicity;
            $this->color = $this->$color;
            return $this;
        }

        public function draw_candlestick ($max, $min, $y) {

            $this->image = imagecreatetruecolor($this->image_width, $this->image_height);
            while ($this->day_cnt > 0) {
                imagefilledrectangle($this->image, $this->day_cnt*100, $max, $this->day_cnt*100 + 20, $min+$this->image_scale, 0xFFFF);
                $this->day_cnt--;
            }

            return $this;
        }

        public function draw_candlestick_indiv (string $ethnicity, float $ethnicity_pct, float $min, float $max) {

            $this->set_ethnic_color($ethnicity);
            imagefilledrectangle($this->image_candle, 0, ($this->ethnicity_total_pct)*100, 25, ($this->ethnicity_total_pct + $ethnicity_pct)*100, $this->color);
            $this->ethnicity_total_pct += $ethnicity_pct;
            return $this;
        }

        public function merge_candlestick (int $day_cnt, int $max)
        {
            imagecopymerge($this->image, $this->image_candle, $day_cnt*50, $max, 0, 0, 25, 100, 100);
        }

        public function export ()
        {
            imagewebp($this->image,$this->candle_name,100);
            imagedestroy($this->image);
        }
    }
    $x = 0;
    $img_mrk = new ImageMaker();
    $array = array("asian", "afro", "caucasian", "latin", "mideast", "euro");
    $img_mrk->start_image("null.webp")->draw_arrest_pct(50, 50);
    $y = 0;
    while ($x < 0.6) {
        $img_mrk->draw_candlestick_indiv($array[$y], $x, 1, $x, 10);
        $x += 0.08 + ($y/100);
        $y++;
    }
    $img_mrk->merge_candlestick(1, 100);
    $img_mrk->merge_candlestick(2, 75);
    $img_mrk->export();

    echo "<img src='null.webp'>";
?>
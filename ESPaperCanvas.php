<?php

class Font
{
    public $name;
    public $family;
    public $size;
    public $style;
}

class Canvas
{
    private $isFirstCall = true;
    private $isJson = true;
    private $font;
    private $color = 'black';
    private $alignment = 'start';
    private $width;
    private $height;

    private $fonts = array();

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function setJson($isJson)
    {
        $this->isJson = $isJson;
    }


    public function printJson($command, $params)
    {

        if ($this->isFirstCall) {
            $separator = '';
            $this->isFirstCall = false;
        } else {
            $separator = ",\n";
        }
        echo "$separator\n {\n \"command\": \"" . $command . "\",\n";
        //if (sizeof($params)>0) {
        echo "   \"params\": {\n";
        $i = 0;
        foreach ($params as $key => $value) {
            $comma = ',';
            if (++$i === count($params)) {
                $comma = '';
            }
            echo "     \"$key\": \"$value\"$comma\n";
        }
        echo "   }\n";
        //}
        echo ' }';
    }

    public function start()
    {
        if ($this->isJson) {
            echo "{ \"meta\": { \"height\": $this->width, \"width\": $this->height }, \"commands\": [";
        } else {
            echo '<svg width="' . $this->width . 'px" height="' . $this->height . 'px"  xmlns="http://www.w3.org/2000/svg" version="1.1">';
            echo "\t<rect x=\"0\" y=\"0\" width=\"$this->width\" height=\"$this->height\" fill=\"none\" stroke=\"grey\" stroke-width=\"0.5\"  />\n";
        }
    }

    public function end()
    {
        if ($this->isJson) {
            echo ']}';
        } else {
            echo '</svg>';
        }
    }

    public function drawLine($x1, $y1, $x2, $y2)
    {
        $x1 = round($x1);
        $y1 = round($y1);
        $x2 = round($x2);
        $y2 = round($y2);
        if ($this->isJson) {
            $params = ['x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2];
            $this->printJson('drawLine', $params);
        } else {
            echo "\t<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" stroke=\"$this->color\" stroke-width=\"1\" />\n";
        }
    }

    public function fillBuffer($color)
    {
        if ($this->isJson) {
            $params = array('color' => $color);
            $this->printJson('fillBuffer', $params);
        } else {
            // TODO
        }
    }

    public function setFont($fontName)
    {
        if ($this->isJson) {
            $params = array('fontName' => $fontName);
            $this->printJson('setFont', $params);
        } else {
            $this->font = $this->fonts[$fontName];
        }
    }

    public function registerFont($fontFolderUrl, $fontFamily, $style, $size, $fontName)
    {
        if ($this->isJson) {
            $url = $fontFolderUrl . $fontName;
            $params = array(
                'url' => $url,
                'fontFamily' => $fontFamily,
                'fontStyle' => $style,
                'fontSize' => $size,
                'fontName' => $fontName
            );
            $this->printJson('registerFont', $params);
        } else {
            $font = new Font();
            $font->name = $fontName;
            $font->family = $fontFamily;
            $font->size = $size;
            $font->style = $style;
            $this->fonts[$fontName] = $font;
            $this->font = $font;
            $url = $fontFolderUrl . $fontFamily . '.ttf';
            ?>
            <style type="text/css">
                @font-face {
                    font-family: '<?php echo $fontFamily; ?>';
                    src: url('<?php echo $url; ?>') format('truetype');
                    font-weight: normal;
                    font-style: normal;
                }
            </style>
            <?php

        }

    }

    public function setTextAlignment($textAlignment)
    {
        if ($this->isJson) {
            $params = ['textAlignment' => $textAlignment];
            $this->printJson('setTextAlignment', $params);
        } else {
            if ($textAlignment === 'LEFT') {
                $this->alignment = 'start';
            } else {
                if ($textAlignment === 'CENTER') {
                    $this->alignment = 'middle';
                } else {
                    if ($textAlignment === 'RIGHT') {
                        $this->alignment = 'end';
                    }
                }
            }
        }
    }


    public function drawString($x1, $y1, $text)
    {
        $x1 = round($x1);
        $y1 = round($y1);
        if ($this->isJson) {
            $params = ['x1' => $x1, 'y1' => $y1, 'text' => $text];
            $this->printJson('drawString', $params);
        } else {
            // compensate for different baseline
            $y1 += $this->font->size;
            echo "\t<text text-anchor=\"$this->alignment\" x=\"$x1\" y=\"$y1\" font-family=\"" . $this->font->family . '" font-size="' . $this->font->size . "\" fill=\"$this->color\" >\n";
            echo "\t\t$text\n";
            echo "\t</text>";
        }
    }


    public function drawRect($x1, $y1, $x2, $y2)
    {
        $x1 = round($x1);
        $y1 = round($y1);
        $x2 = round($x2);
        $y2 = round($y2);
        if ($this->isJson) {
            $params = ['x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2];
            $this->printJson('drawRect', $params);
        } else {
            //$x2 = max(0, $x2 - 1);
            //$y2--;
            echo "\t<rect x=\"$x1\" y=\"$y1\" width=\"$x2\" height=\"$y2\" fill=\"none\" stroke=\"$this->color\" stroke-width=\"1\"  />\n";
        }
    }

    public function fillRect($x1, $y1, $x2, $y2)
    {
        $x1 = round($x1);
        $y1 = round($y1);
        $x2 = round($x2);
        $y2 = round($y2);
        if ($this->isJson) {
            $params = ['x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2];
            $this->printJson('fillRect', $params);
        } else {
            //$x2 = max(0, $x2 - 1);
            //$y2--;
            echo "\t<rect x=\"$x1\" y=\"$y1\" width=\"$x2\" height=\"$y2\" fill=\"$this->color\" stroke=\"$this->color\" stroke-width=\"0\"  />\n";
        }
    }

    public function commit()
    {
        if ($this->isJson) {
            $params = array();
            $this->printJson('commit', $params);
        } else {
            // TODO
        }
    }

    public function setColor($color)
    {
        if ($this->isJson) {
            $params = array('color' => $color);
            $this->printJson('setColor', $params);
        } else {
            $this->color = 'white';
            if ($color === 0) {
                $this->color = 'black';
            }
        }
    }

    public function drawImage($x1, $y1, $fileName)
    {
        if ($this->isJson) {
            $params = ['x1' => $x1, 'y1' => $y1, 'fileName' => $fileName];
            $this->printJson('drawImage', $params);
        } else {
            // TODO
        }
    }

    public function drawBmpFromFile($x1, $y1, $fileName)
    {
        if ($this->isJson) {
            $params = ['x1' => $x1, 'y1' => $y1, 'fileName' => $fileName];
            $this->printJson('drawBmpFromFile', $params);
        } else {
            // TODO
        }
    }

    public function downloadFile($url, $fileName, $expires)
    {
        if ($this->isJson) {
            $params = ['url' => $url, 'fileName' => $fileName, 'expires' => $expires];
            $this->printJson('downloadFile', $params);
        } else {
            // TODO
        }
    }

    public function meteocons()
    {
        if ($this->isJson) {
            // TODO
        } else {
            ?>
            <style type="text/css">
                @font-face {
                    font-family: 'MeteoconsRegular';
                    src: url('fonts/meteocons.ttf') format('truetype');
                    font-weight: normal;
                    font-style: normal;
                }
            </style>
            <?php
        }
    }

    public function moonphases()
    {
        if ($this->isJson) {
            // TODO
        } else {
            ?>
            <style type="text/css">
                @font-face {
                    font-family: 'MoonphasesRegular';
                    src: url('fonts/moonphases.ttf') format('truetype');
                    font-weight: normal;
                    font-style: normal;
                }
            </style>
            <?php
        }
    }

}

?>

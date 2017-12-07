<?php
    include('ESPaperCanvas.php');

    date_default_timezone_set("Europe/Zurich");
    $WUNDERGROUND_API_KEY = "!!!API KEY!!!";

    $output = $_GET["output"];
    if (!isset($output) || $output === "json") {
      $isJson = true;
    } else {
      $isJson = false;
    }
    if ($isJson) {
      $baseUrl = "http://www.squix.org/espaper";
    	header('Content-Type: application/json');
    } else {
      $baseUrl = "";
      header('Content-Type: image/svg+xml');
    }
  	$battery = $_GET['battery'] / 1024.0;
  	$voltage = round(($battery + 0.083) * 13 / 3.0, 2);
  	if ($voltage > 4.2) {
  		$percentage = 100;
  	} else if ($voltage < 3) {
  		$percentage = 0;
  	} else {
  		$percentage = round(($voltage - 3.2) * 100 / (4.2 - 3.0));
  	}

  	$meteocons = array(
  		"chanceflurries" => "F",
  		"chancerain" => "Q",
  		"chancesleet" => "W",
  		"chancesnow" => "V",
  		"chancetstorms" => "S",
  		"clear" => "B",
  		"cloudy" => "Y",
  		"flurries" => "F",
  		"fog" => "M",
  		"hazy" => "E",
  		"mostlycloudy" => "Y",
  		"mostlysunny" => "H",
  		"partlycloudy" => "H",
  		"partlysunny" => "J",
  		"sleet" => "W",
  		"rain" => "R",
  		"snow" => "W",
  		"sunny" => "B",
  		"tstorms" => "0",
  		"nt_chanceflurries" => "F",
  		"nt_chancerain" => "7",
  		"nt_chancesleet" => "#",
  		"nt_chancesnow" => "#",
  		"nt_chancetstorms" => "&",
  		"nt_clear" => "2",
  		"nt_cloudy" => "Y",
  		"nt_flurries" => "9",
  		"nt_fog" => "M",
  		"nt_hazy" => "E",
  		"nt_mostlycloudy" => "5",
  		"nt_mostlysunny" => "3",
  		"nt_partlycloudy" => "4",
  		"nt_partlysunny" => "4",
  		"nt_sleet" => "9",
  		"nt_rain" => "7",
  		"nt_snow" => "#",
  		"nt_sunny" => "4",
  		"nt_tstorms" => "&"
  	);

  	$json_string = file_get_contents("http://api.wunderground.com/api/".$WUNDERGROUND_API_KEY."/conditions/hourly/astronomy/q/CH/Zurich.json");
  	$result = json_decode($json_string);
  	$condition = $result->{'current_observation'};
  	$hourly = $result->{'hourly_forecast'};
  	$astronomy = $result->{'moon_phase'};


  	$moon_age_char = chr(65 + 26 * (($astronomy->{'ageOfMoon'} % 30) / 30.0));

  	$min_temp = 999;
  	$max_temp = -999;
    $sum_temp = 0;
  	$temps = array();
  	for ($i = 0; $i < sizeof($hourly); $i++) {
  		$temp = $hourly[$i]->{'temp'}->{'metric'};
  		if ($temp < $min_temp) {
  			$min_temp = $temp;
  		}
  		if ($temp > $max_temp) {
  			$max_temp = $temp;
  		}
      $sum_temp += $temp;
  		array_push($temps, $temp);
  	}
    $avg_temp = $sum_temp / sizeof($hourly);

    $canvas = new Canvas(296, 128);
    $canvas->setJson($isJson);
    $canvas->start();
    $canvas->registerFont($baseUrl . "/fonts/", "Meteocons", "Plain", 42, "MeteoconsPlain42");
    $canvas->registerFont($baseUrl . "/fonts/", "Meteocons", "Plain", 21, "MeteoconsPlain21");
    $canvas->registerFont($baseUrl . "/fonts/", "Moonphases", "Plain", 36, "MoonphasesPlain36");

    $canvas->registerFont($baseUrl . "/fonts/", "Arial", "Plain", 10, "ArialPlain10");
    $canvas->registerFont($baseUrl . "/fonts/", "Arial", "Plain", 16, "ArialPlain16");
    $canvas->registerFont($baseUrl . "/fonts/", "Arial", "Plain", 24, "ArialPlain24");


		$canvas->fillBuffer(1);
		$canvas->setFont("ArialPlain10");
		$canvas->setTextAlignment("LEFT");
		$canvas->drawString(2, -2, "Updated: ".date("Y-m-d H:i:s"));
		$canvas->drawLine(0, 11, 296, 11);
		$canvas->setTextAlignment("RIGHT");
		$canvas->drawString(274, -1, $voltage. "V ".$percentage."%");
		$canvas->drawRect(274, 0, 18, 10);
		$canvas->drawRect(293, 2, 1, 6);
		$canvas->fillRect(276, 2, round(14 * $percentage / 100), 6);
		$canvas->setTextAlignment("LEFT");
		$canvas->setFont("MeteoconsPlain42");
		$canvas->drawString(5, 20, $meteocons[$condition->{'icon'}]);
		$canvas->setFont("ArialPlain10");
		$canvas->drawString(55, 15, $condition->{'display_location'}->{'city'});
		$canvas->drawString(55, 50, $condition->{'weather'});
		$canvas->setFont("ArialPlain24");
		$canvas->drawString(55, 25, $condition->{'temp_c'}."°C");
		$canvas->drawLine(0, 65, 296, 65);


		function DrawForecastDetail($x, $y, $hourForecast) {
				global $meteocons, $canvas;
				$canvas->setFont("ArialPlain10");
				$canvas->setTextAlignment("CENTER");
				$canvas->drawString($x + 25, $y -  2, $hourForecast->{'FCTTIME'}->{'hour_padded'}.":00");
				$canvas->drawString($x + 25, $y + 12, $hourForecast->{'temp'}->{'metric'} . "° " . $hourForecast->{'pop'} . "%");
				$canvas->setFont("MeteoconsPlain21");
				$canvas->drawString($x + 25, $y + 24, $meteocons[$hourForecast->{'icon'}]);
				$canvas->drawLine($x + 2, 12, $x + 2, 65);
				$canvas->drawLine($x + 2, 25, $x + 43, 25);

		}
		drawForecastDetail(296 / 2 -  20, 15, $hourly[0]);
		drawForecastDetail(296 / 2 +  22, 15, $hourly[3]);
		drawForecastDetail(296 / 2 +  64, 15, $hourly[6]);
		drawForecastDetail(296 / 2 + 106, 15, $hourly[9]);

    function tempToPixel($height, $max, $min, $temp) {
      return $temp * $height / ($max - $min);
    }

		for ($i = 0; $i < min(32, sizeof($temps)); $i++) {
      $height = tempToPixel(13, $max_temp, $min_temp, $temps[$i]);
			$canvas->fillRect(200 + $i * 3, 116 - $height, 2, $height);
	 	}
    $canvas->setTextAlignment("LEFT");
    $canvas->setFont("ArialPlain10");
    $canvas->drawString(200, 65, "Min:".$min_temp."°C   Max:".$max_temp."°C");
		$canvas->drawLine(196, 65, 196, 284);
		$canvas->setFont("MoonphasesPlain36");
		$canvas->setTextAlignment("LEFT");
		$canvas->drawString(5, 72, $moon_age_char);
		$canvas->setFont("ArialPlain10");
		$canvas->drawString(55, 72, "Sun:");
		$canvas->drawString(95, 72,
			$astronomy->{'sunrise'}->{'hour'} . ":". $astronomy->{'sunrise'}->{'minute'}." - "
		  .$astronomy->{'sunset'}->{'hour'} . ":". $astronomy->{'sunset'}->{'minute'});
		$canvas->drawString(55, 84, "Moon:");
		$canvas->drawString(95, 84,
			$astronomy->{'moonrise'}->{'hour'} . ":" . $astronomy->{'moonrise'}->{'minute'}." - "
			.$astronomy->{'moonset'}->{'hour'} . ":" . $astronomy->{'moonset'}->{'minute'});
		$canvas->drawString(55, 96, "Phase:");
		$canvas->drawString(95, 96, $astronomy->{'phaseofMoon'});
		$canvas->setTextAlignment("CENTER");
		$canvas->drawString(48, 116, "CONFIG+RST");
		$canvas->drawString(245, 116, "UPDATE");
		$canvas->drawLine(0, 116, 296, 116);
		$canvas->drawLine(98, 116, 98, 128);
		$canvas->drawLine(196, 116, 196, 128);
		$canvas->commit();
    $canvas->end();

		?>

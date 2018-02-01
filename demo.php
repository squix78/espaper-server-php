<?php
    include('ESPaperCanvas.php');
    $baseUrl = "http://www.squix.org/espaper";

    $feed = implode(file('http://twitrss.me/twitter_user_to_rss/?user=squix78'));
    $xml = simplexml_load_string($feed);
    $json = json_encode($xml);
    $rss = json_decode($json);

    const BLACK = 0;
    const WHITE = 1;
    date_default_timezone_set("Europe/Zurich");

    $output = $_GET["output"];
    if (!isset($output) || $output === "json") {
      $isJson = true;
    } else {
      $isJson = false;
    }
    if ($isJson) {
      header('Content-Type: application/json');
    } else {
      header('Content-Type: image/svg+xml');
      //header('Content-Type: application/json');
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

    $canvas = new Canvas(296, 128);
    $canvas->setJson($isJson);
    $canvas->start();

    $canvas->registerFont($baseUrl . "/fonts/", "Arial", "Plain", 10, "ArialPlain10");
    $canvas->registerFont($baseUrl . "/fonts/", "Arial", "Plain", 16, "ArialPlain16");
    $canvas->registerFont($baseUrl . "/fonts/", "Arial", "Plain", 24, "ArialPlain24");
    $canvas->setFont("ArialPlain10");
    $canvas->setTextAlignment("LEFT");
    $canvas->drawString(2, -2, "Updated: ".date("Y-m-d H:i:s"));
    $canvas->drawLine(0, 11, 296, 11);
    $canvas->setTextAlignment("RIGHT");
    $canvas->drawString(273, -1, $voltage. "V ".$percentage."%");
    $canvas->drawRect(274, 0, 18, 10);
    $canvas->drawRect(293, 2, 1, 6);
    $canvas->fillRect(276, 2, round(14 * $percentage / 100), 6);


    //$canvas->registerFont(NULL, "Arial", "plain", 20, "ArialPlain20");
    $canvas->fillBuffer(WHITE);
    $canvas->setColor(BLACK);
    $canvas->setTextAlignment("LEFT");
    $canvas->setFont("ArialPlain10");
    for ($i = 0; $i < 4; $i++) {
      $canvas->drawString(0, 20 + $i * 14, $rss->{'channel'}->{'item'}[$i]->title);
    }


    $canvas->commit();
    $canvas->end();

?>

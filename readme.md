# espaper-server-php

This repository can be used in conjunction with the  espaper-client repository to render content for the ESPaper modules server side. ESPaper modules can be ordered here:

 * <https://blog.squix.org/product/2-9-espaper-lite-kit>
 * <https://blog.squix.org/product/2-9-espaper-plus-kit>

 You are supporting the creator of this library.

## Introduction

This repository can render JSON objects consumed by ESPaper modules to display information on (e-paper) displays. It also has a built in emulator mode which allows you to preview the results locally before making it available to the ESPaper modules. In this case the code renders SVG instructions instead of espaper-json. This emulation is not 100% adequate but is helpful to quickly see errors before uploading the file to the server.

The scripts in this repository use PHP to render the JSON objects. The parser at <https://github.com/squix78/espaper-client> just consumes the espaper-json object. As long as it contains valid commands it doesn't care what software created it. This means you could also write a server based on NodeJS, Python, Java or any other language processing an HTTP request and rendering some kind of JSON.

## Getting started

The library contains the following files:

* ESPaperCanvas.php is the library you can use to create espaper-json objects
* weatherstation.php is a server version of the espaper-weatherstaton. They produce nearly identical results on the espaper-json
* index.php is called by espaper. You can switch between applications by activating the appropriate include
* demo.php is some kind of playground

Before we even make the code available to the ESPaper we're going to start a local server to test the scripts. Make sure you have PHP installed and available on your command line tool. Then navigate to the folder of this repository and execute:
```
php -S localhost:8080
```
This starts a web server. Then open the following URL in your browser:

<http://localhost:8080/index.php?battery=123&output=svg>

Do you see the output=svg at the end of the URL? This tells the script to render SVG instead of JSON. By replacing svg with json you can also look at the generated JSON object:

<http://localhost:8080/index.php?battery=123&output=json>

You can also remove the &output=json completely. Default is to render JSON. If you are happy with the results you can upload this to your php server. Then follow instructions on <https://github.com/squix78/espaper-client> to get the ESPaper ready.

## Font commands

I will add here all the commands later on. For now just let me explain a bit about the font commands. For now all fonts have to be "backed" into the firmware. I am working on a feature which allows you to define new fonts on the server and then download them to the ESPaper. There is a bug in the SDK which causes failure for bigger font files.   

<!DOCTYPE html>
<html>
  <head>
    <meta charset='utf-8'/>
    <meta content='width=device-width, initial-scale=1.0' name='viewport'/>
    <meta content='IE=edge' http-equiv='X-UA-Compatible'/>
    <title><?php print $head_title; ?></title>
    <?php /* Remove */ ?>
    <?php // print $head; ?>
    <?php // print $styles; ?>
    <!--[if lte IE 8]>
    <script src='//assets.malmo.se/internal/3.0/html5shiv-printshiv.js' type='text/javascript'></script><![endif]-->
    <link href='//assets.malmo.se/internal/3.0/malmo.css' media='all' rel='stylesheet' type='text/css'/>
    <!--[if lte IE 7]>
    <link href='//assets.malmo.se/internal/3.0/legacy/ie7.css' media='all' rel='stylesheet' type='text/css'/>
    <![endif]-->
    <link rel='stylesheet' href='<?php print $komin_style; ?>'>
    <link rel='icon' type='image/x-icon' href='//assets.malmo.se/internal/3.0/favicon.ico'/>
  </head>
  <body class="<?php print $classes; ?>" <?php print $attributes; ?>>
    <div class='app-title'><a href='<?php print url('<front>')  ?>'><?php print $head_title_array['name'];  ?></a></div>

    <?php print $page_top; ?>
    <?php print $page; ?>
    <?php print $page_bottom; ?>
    <script src='//assets.malmo.se/internal/3.0/malmo.js'></script>
    <script src='<?php print $komin_script; ?>'></script>
    <?php print $scripts; ?>

  </body>
</html>

<!DOCTYPE html>
<html>
	<head>
		<title>X-Wing League 2016-2017</title>
    <meta charset="utf-8">

    <link href='css/bootstrap.min.css' rel='stylesheet' type='text/css'>
    <link href='css/style.css' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/css?family=Roboto+Mono:,100,300,700,900|Roboto:100,300,700" rel="stylesheet">
    <script type="text/javascript" src="js/points.js"></script>
	</head>
  <body>
    <?php
       $matchs = file_get_contents('data/matchs.json');
       $matchs = (json_decode($matchs));
       $players = file_get_contents('data/players.json');
       $players = (json_decode($players));

       //var_dump($players);
     ?>
  </body>
</html>

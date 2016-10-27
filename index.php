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
  <body class="container">
    <?php
       $matchs = file_get_contents('data/matchs.json');
       $matchs = (json_decode($matchs));
       $players = file_get_contents('data/players.json');
       $players = (json_decode($players));

     //var_dump($players);
    ?>

    <header></header>
    <section class="history col-sm-4">
      <h2>Historique des matchs</h2>
     <?php
        foreach ($matchs as $match){
          $content= '';
          $content.='<article class="match">';
          $content.='  <strong class="winner">'.getCallsign($match->{'winner'}).'</strong>';
          $content.='  <span>a gagné '.$match->{'win_points'}.' à '.$match->{'lose_points'}.' face à</span>';
          $content.='  <strong class="loser">'.getCallsign($match->{'loser'}).'</strong>';
          $content.='</article>';
        }
        echo($content);
     ?>
    </section>


    <?php
      function getCallsign($id){
        global $players;
        foreach ($players as $e) {
					if($e->{'id'}==$id)return $e->{'callsign'};
				}
        return "not found";
      }
     ?>
  </body>
</html>

<!DOCTYPE html>
<html>
	<head>
		<title>X-Wing League 2016-2017</title>
		<meta charset="utf-8">

		<!-- Bootstrap CDN -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<link href='css/style.css' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/css?family=Roboto+Mono:,100,300,700,900|Roboto:100,300,700" rel="stylesheet">
	</head>

	<body class="container">
    <?php
       // CHARGER LES .JSON ET INITIALISER LES PRINCIPAUX TABLEAUX DE DONNEES
       $matchs = json_decode(file_get_contents('data/matchs.json'));
       $players = json_decode(file_get_contents('data/players.json'));
       $pairings=[];

       // CALCULER LE SCORE ET LA MARGE DES JOUEURS
        $i=0;
        //pour chaque match
        foreach ($matchs as $match) {
          // on normalise et sauvegarde le pairing
          $pairing = [min($match->winner,$match->loser), max($match->winner,$match->loser)];
          // on compte de nombre de pré-occurence du pairing
          $nbEncounter=0;
          foreach ($pairings as $k => $v) {if($v==$pairing){$nbEncounter++;}}
          // si c'est la première rencontre
          if($nbEncounter==0){
            // si c'est une égalité
            if($match->win_points==$match->lose_points){
              // les deux joueurs gagne 1 point
              getPlayer($match->winner)->score+=1;
              getPlayer($match->loser)->score+=1;
            }
            // sinon
            else{
              //le gagnant prend 3 points
              getPlayer($match->winner)->score+=3;
            }
            // on donne les marges de victoire
            $delta=$match->win_points-$match->lose_points;
            getPlayer($match->winner)->margin+=(100+$delta);
            getPlayer($match->loser)->margin+=(100-$delta);
          }
          // si c'est la seconde rencontre
          else if($nbEncounter==1){
            // si le match n'est pas une égalité
            if($match->win_points!=$match->lose_points){
              //le gagnant prend 1 point
              getPlayer($match->winner)->score+=1;
            }
          }
          // on ajoute le pairing à la liste des pairings pour les matchs suivants
          $pairings[$i] = $pairing;
          $i++;
        }

        // ORDONNER L'ARRAY DE JOUEURS SELON LE SCORE ET LA MARGE
				$leaderboard = $players;
        usort($leaderboard, "cmp");
        function cmp($a, $b){
          if ($a->score == $b->score) {
              if($a->margin == $b->margin){
                return 0;
              }
              return ($a->margin > $b->margin) ? -1 : 1;
          }
          return ($a->score > $b->score) ? -1 : 1;
        }

    ?>

    <header class="row" style="position:relative">
      <div style="padding: 0 30px;">
        <h1 title="X-Wing Miniature Game">
					<img src="img/swx-logo.png" id="swx" />
				</h1>
				<section class="title">
					<h2>Les Immortels du Cercle</h2>
	        <h3>Championnat de la ligue 2016-2017</h3>
		 		</section>
        <button type="button" class="btn btn-default" style="position:absolute;right:30px;top:20px" onclick="document.getElementById('reportmatch').classList.add('open');">Ajouter un match</button>
      </div>
    </header>
		<?php if(isset($_GET['wrongPassword']))echo('<p class="bg-danger feedback">Mot de passe incorrect, votre requête n\'a pas été prise en compte. <button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>'); ?>
		<?php if(isset($_GET['addMatchSuccess']))echo('<p class="bg-success feedback">Succès, le rapport de votre match à bien été pris en comtpe. <button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>'); ?>



    <section id="reportmatch" class="col-xs-12" style="padding: 0 30px;">
      <section class="wrapper row">
				<button type="button" class="close" onclick="this.parentNode.parentNode.classList.remove('open')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3>Ajouter un match</h3>
        <form action="form/addMatch.php">
          <section class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="winnerselect">Vainqueur</label>
              <select id="winnerselect" class="form-control" name="winner" required>
                <option disabled selected value>Selectionner un joueur</option>
                <?php
                foreach ($players as $player){
                  $content= '<option value="'.$player->id.'">'.$player->callsign.'</option>';
                  echo($content);
                }
                 ?>
              </select>
            </div>
            <div class="form-group">
              <label for="winnerscoreinput">Score du vainqueur</label>
              <input id="winnerscoreinput" name="win_points" class="form-control" type="number" min="0" max="100" required></input>
            </div>
          </section>
          <section class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="loserselect">Vaincu</label>
              <select id="loserselect" class="form-control" name="loser" required>
                <option disabled selected value>Selectionner un joueur</option>
                <?php
                foreach ($players as $player){
                  $content= '<option value="'.$player->id.'">'.$player->callsign.'</option>';
                  echo($content);
                }
                 ?>
              </select>
            </div>
            <div class="form-group">
              <label for="loserscoreinput">Score du vaincu</label>
              <input id="loserscoreinput" name="lose_points" class="form-control" type="number" min="0" max="100" required></input>
            </div>
          </section>
          <section class="col-sm-6 col-md-3">
            <p style="color:#AAA;font-size:15px;margin-top:26px;"><strong style="color:#444">Avant de valider le match</strong>, assurez vous que ce ne soit pas un doublon et que toutes les informations sont correctes.</p>
          </section>
          <section class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="inputmatchpassword">Mot de passe</label>
              <input id="inputmatchpassword" name="password" class="form-control" type="password" required></input>
            </div>
            <button type="submit" name="submit" class="btn btn-primary btn-block">Envoyer</button>
					</section>

        </form>
      </section>
    </section>


    <section id="leaderboard" class="col-xs-12 col-md-4">
      <section class="wrapper">
        <h3>Classement</h3>
        <?php
        $i=1;
        foreach ($leaderboard as $player){
          $content= '';
          $content.='<article class="player" onclick="document.getElementById(\'players\').dataset.page='.$player->id.'">';
          if($i<=4){$content.='  <h3>'.$i.'.</h3>';}
          else{$content.='  <h4>'.$i.'.</h4>';}
          $content.='  <span class="points">'.$player->score.'<span class="margin">('.$player->margin.')</span></span>';
          $content.='  <strong>'.$player->callsign.'</strong>';
          $content.='</article>';
          echo($content);
          $i++;
        }
         ?>
       </section>
    </section>
    <section id="players" class="col-xs-12 col-md-8" data-page="null">
      <section class="wrapper row">
				<button type="button" class="close" onclick="this.parentNode.parentNode.dataset.page=null" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php
           foreach ($players as $player){
             $content= '';
             $content.='<article class="player" data-page="'.$player->id.'">';
						 $content.=' 	<section class="col-md-6">';
             $content.='  	<h3>'.$player->callsign.'</h3>';
             $content.='  	<h4>Score : <strong>'.$player->score.'</strong></h4>';
             $content.='  	<h4>Marge de vicoire : <strong>'.$player->margin.'</strong></h4>';
						 $content.='	</section>';
						 $content.=' 	<section class="col-md-6" style="margin-top:46px">';
             $content.='  	<h4>Matchs :</h4>';

						 foreach ($matchs as $match){
							 if($match->winner==$player->id && $match->win_points==$match->lose_points){
								 $content.='<article class="match bg-warning">';
	 						 	 $content.='  <time>'.date('j\/n',$match->timestamp).'</time><br />';
	 					 		 $content.='  <span>A fait égalité ('.$match->win_points.'pts) face à</span>';
	 							 $content.='  <strong class="loser" onclick="document.getElementById(\'players\').dataset.page='.$match->loser.'">'.getPlayer($match->loser)->callsign.'</strong>';
	 							 $content.='</article>';
							 }
							 else if($match->loser==$player->id && $match->win_points==$match->lose_points){
								 $content.='<article class="match bg-warning">';
	 						 	 $content.='  <time>'.date('j\/n',$match->timestamp).'</time><br />';
	 					 		 $content.='  <span>A fait égalité ('.$match->win_points.'pts) face à</span>';
	 							 $content.='  <strong class="winner" onclick="document.getElementById(\'players\').dataset.page='.$match->winner.'">'.getPlayer($match->winner)->callsign.'</strong>';
	 							 $content.='</article>';
							 }
							 else if($match->winner==$player->id){
								 $content.='<article class="match bg-success">';
	 						 	 $content.='  <time>'.date('j\/n',$match->timestamp).'</time><br />';
	 					 		 $content.='  <span>A gagné '.$match->win_points.' à '.$match->lose_points.' face à</span>';
	 							 $content.='  <strong class="loser" onclick="document.getElementById(\'players\').dataset.page='.$match->loser.'">'.getPlayer($match->loser)->callsign.'</strong>';
	 							 $content.='</article>';
							 }
							 else if($match->loser==$player->id){
								 $content.='<article class="match bg-danger">';
	 						 	 $content.='  <time>'.date('j\/n',$match->timestamp).'</time><br />';
	 					 		 $content.='  <span>A perdu '.$match->win_points.' à '.$match->lose_points.' face à</span>';
	 							 $content.='  <strong class="winner" onclick="document.getElementById(\'players\').dataset.page='.$match->winner.'">'.getPlayer($match->winner)->callsign.'</strong>';
	 							 $content.='</article>';
							 }
						 }

						 $content.='	</section>';
             $content.='</article>';
             echo($content);
           }
        ?>
      </section>
    </section>


    <section id="pairings" class="col-md-8 hidden-xs">
      <section class="wrapper">
	      <h3>Pairings</h3>
        <table>
          <?php
             $i=0;
             $content= '';
             foreach ($players as $player){
               $content.='<tr><th>'.$player->callsign.'</th>';
                  for ($j=0; $j <$i+1 ; $j++) {
                    if ($i==$j) {$content.='<td data="null"></td>';}
                    else{
                      $pairing=[$j,$i];
											$nbEncounter=0;
                      foreach ($pairings as $k => $v) {if($v==$pairing){$nbEncounter++;}}
                      $content.='<td data="'.$nbEncounter.'"></td>';
                    }
                  }
                $content.='</tr>';
								$i++;
             }
             $content.='<tr><th></th>';
             foreach ($players as $player){
               $content.='<th>'.$player->callsign.'</th>';
             }
             $content.='</tr>';
             echo($content);
          ?>
        </table>
				<p class="legend">
					<span class="match-done">Match aller effectué</span><br />
					<span class="match-redone">Match retour effectué</span>
				</p>
      </section>
    </section>
		<div class="col-xs-12 row">
			<section id="history" class="col-xs-12 col-md-6 col-md-offset-3">
				<section class="wrapper">
					<h3>Historique des matchs</h3>
					 <?php
							foreach (array_reverse($matchs) as $match){
								$content= '';
								$content.='<article class="match">';
								$content.='  <time>'.date('j\/n',$match->timestamp).'</time><br />';
								$content.='  <strong class="winner" onclick="document.getElementById(\'players\').dataset.page='.$match->winner.'">'.getPlayer($match->winner)->callsign.'</strong>';
								$content.='  <span>a gagné '.$match->win_points.' à '.$match->lose_points.' face à</span>';
								$content.='  <strong class="loser" onclick="document.getElementById(\'players\').dataset.page='.$match->loser.'">'.getPlayer($match->loser)->callsign.'</strong>';
								$content.='</article>';
								echo($content);
							}
					 ?>
				</section>
			</section>
		</div>


    <?php
      function getPlayer($id){
        global $players;
        foreach ($players as $e) {
					if($e->id==$id)return $e;
				}
        return null;
      }
     ?>
		 <script type="text/javascript" src="signature.js"></script>
  </body>
</html>

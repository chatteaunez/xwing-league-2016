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
    <script type="text/javascript" src="js/points.js"></script>
	</head>
  <body class="container">
    <?php
       // CHARGER LES .JSON ET INITIALISER LES PRINCIPAUX TABLEAUX DE DONNEES
       $matchs = file_get_contents('data/matchs.json');
       $matchs = (json_decode($matchs));
       $players = file_get_contents('data/players.json');
       $players = (json_decode($players));
       $pairings=[];
       //var_dump($players);

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
        usort($players, "cmp");
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
        <h2>Les Immortels du Cercle</h2>
        <h1>X-Wing Miniature</h1>
        <h3>Championnat de la ligue 2016-2017</h3>
        <button type="button" class="btn btn-default" style="position:absolute;right:30px;top:0" onclick="document.getElementById('reportmatch').classList.add('open');">Ajouter un match</button>
      </div>
    </header>

    <section id="reportmatch" class="col-sm-12" style="padding: 0 30px;">
      <section class="wrapper row">
        <h3>Ajouter un match</h3>
        <form>
          <section class="col-md-3">
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
              <label for="winnerscoreinput">Score</label>
              <input id="winnerscoreinput" class="form-control" type="number" min="0" max="100" required></input>
            </div>
          </section>
          <section class="col-md-3">
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
              <label for="loserscoreinput">Score</label>
              <input id="loserscoreinput" class="form-control" type="number" min="0" max="100" required></input>
            </div>
          </section>
          <section class="col-md-3">
            <p style="color:#AAA;font-size:15px;margin-top:26px;"><strong style="color:#444">Avant de valider le match</strong>, assurez vous que ce ne soit pas un doublon et que toutes les informations sont correctes.</p>
          </section>
          <section class="col-md-3">
            <div class="form-group">
              <label for="inputmatchpassword">Password</label>
              <input id="inputmatchpassword" class="form-control" type="password" required></input>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Envoyer</button>
          </section>

        </form>
      </section>
    </section>

    <section id="leaderboard" class="col-sm-4">
      <section class="wrapper">
        <h3>Classement</h3>
        <?php
        $i=1;
        foreach ($players as $player){
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
    <section id="history" class="col-sm-4">
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
    <section id="players" class="col-sm-4" data-page="">

      <section class="wrapper">
        <?php
           foreach ($players as $player){
             $content= '';
             $content.='<article class="player" data-page="'.$player->id.'">';
             $content.='  <h3>'.$player->callsign.'</h3>';
             $content.='  <h4>Score : <strong>'.$player->score.'</strong></h4>';
             $content.='  <h4>Marge de vicoire : <strong>'.$player->margin.'</strong></h4>';
             $content.='  <h4>Matchs :</h4>';
             $content.='</article>';
             echo($content);
           }
        ?>
      </section>
    </section>


    <?php



      function getPlayer($id){
        global $players;
        foreach ($players as $e) {
					if($e->id==$id)return $e;
				}
        return null;
      }
     ?>
  </body>
</html>

<?php
  if(md5($_GET['password'])=="4ab769ac32200905be1d9fbcbbf4f69f"){
    $matchs = json_decode(file_get_contents('../data/matchs.json'));
    $matchinsert = new match();
    $matchinsert->winner=$_GET['winner'];
    $matchinsert->loser=$_GET['loser'];
    $matchinsert->win_points=$_GET['win_points'];
    $matchinsert->lose_points=$_GET['lose_points'];
    $matchinsert->timestamp=time();
    array_push($matchs,$matchinsert);
    file_put_contents('../data/matchs.json', json_encode($matchs,TRUE));
    header('Location:../index.php?addMatchSuccess');
  }
  else{header('Location:../index.php?wrongPassword');;}

  class match {
      public function __construct(array $arguments = array()) {
          if (!empty($arguments)) {
              foreach ($arguments as $property => $argument) {
                  $this->{$property} = $argument;
              }
          }
      }

      public function __call($method, $arguments) {
          $arguments = array_merge(array("stdObject" => $this), $arguments); // Note: method argument 0 will always referred to the main class ($this).
          if (isset($this->{$method}) && is_callable($this->{$method})) {
              return call_user_func_array($this->{$method}, $arguments);
          } else {
              throw new Exception("Fatal error: Call to undefined method match::{$method}()");
          }
      }
  }
?>

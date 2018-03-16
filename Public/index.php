<?php
require_once('../vendor/autoload.php'); // Composer autoloader
/**
 * Battleships gameplay
 */

use BattleShips\Ship;
use BattleShips\Battlefield;

if (php_sapi_name() == "cli") {
    // In cli-mode
    cliUI();
} else {
    // Not in cli-mode
    GUI();
}

function cliUI(){
    $Table = new Battlefield(1,2);

    while(!$Table->allDead()){
        $Table->showGrid();
        echo("Enter coordinates (row, col), e.g. A5 =".PHP_EOL);
        $line = trim(fgets(STDIN));

        $res= $Table->makeHit($line);
        if($res == Battlefield::MISS){
            echo "**** Miss *****".PHP_EOL;
        }elseif ($res == Battlefield::HIT ){
            echo "**** Hit ****".PHP_EOL;
        }

        //$Table->showShipsView();
    }

}

?>


<?php

/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 3/8/2018
 * Time: 10:20 AM
 */
namespace BattleShips;

use BattleShips\Ship;

class Battlefield
{
    CONST SIZE_VERTICAL = 10;
    CONST SIZE_HORIZONTAL = 10;


    CONST HIT = "X";
    CONST MISS = "-";
    CONST NO_SHOT = ".";

    CONST DISPLAY_BATTLESHIP = "B";
    CONST DISPLAY_DESTROYER = "D";


    private $field_view = array();

    /**
     * @var array
     */
    private $ships = array();

    /**
     * @var array coordinates of hits
     */
    private $hits = array();


    /**
     * @param $hit_coordinates
     * @return bool|int
     */
    public function makeHit($hit_coordinates)
    {
        $res = self::MISS ;
        if(! self::checkCoordinates($hit_coordinates)) {
            echo("WRONG coordinates for hit: ".$hit_coordinates);
            return false;
        }


        // Already existing
        if(in_array($hit_coordinates, $this->hits ) ) {
            echo("Duplicated Hit" . $hit_coordinates ) ;

            return $this->getCoordinatesView($hit_coordinates);
        }else{
            array_push($this->hits, $hit_coordinates );
            // check ships coordinates for hit
            foreach ($this->getShips() as $Ship ){
                foreach ($Ship->getCoordinatesArray() as $ship_coordinate ){
//                    echo("CMP : ".$ship_coordinate[0]. " | " .$hit_coordinates.PHP_EOL);
                    if($ship_coordinate[0] == $hit_coordinates){
                        $res = self::HIT ;
                        $Ship->increaseHits();
                    }
                }
            }
        }

        // Change View
        $this->hitView($hit_coordinates, $res);

        return $res;
    }

    /**
     * @param $hit_coordinates
     * @param $value
     * @return bool
     */
    private function hitView($hit_coordinates, $value){
        if(!self::checkCoordinates($hit_coordinates)){
            echo ("Incorrect Coordinates Can't hit view.");
            return false;
        }

        // Change View
        $Coordinates = self::splitCoordinates($hit_coordinates);
        $this->field_view[$Coordinates[1]][$Coordinates[2]] = $value;
        return true;
    }
    /**
     * @param $coordinates A5, G6, C7 ...
     * @return mixed
     */
    public function getCoordinatesView($coordinates ){

        $Coordinates = self::splitCoordinates($coordinates);

        if(isset($this->field_view[$Coordinates[1]][$Coordinates[2]])) {
            return $this->field_view[$Coordinates[1]][$Coordinates[2]];
        }else{
            return null;
        }
    }

    /**
     * Battlefield constructor.
     * @param $destroyers_count
     * @param $battleships_count
     */
    public function __construct($destroyers_count, $battleships_count)
    {
        // init Empty batle field
        $this->field_view = $this->initEmptyBattlefield();

        for ($iterator = 0 ; $iterator< $destroyers_count; $iterator++ ){
            $this->initRandomDestroyer();
        }
        for ($iterator = 0 ; $iterator< $battleships_count; $iterator++ ){
            $this->initRandomBattleship();
        }

    }

    private function initEmptyBattlefield( ){
        $line = array_fill('1', self::SIZE_HORIZONTAL, self::NO_SHOT );
        $arr = array();
        for($iterator = 0 ; $iterator < self::SIZE_VERTICAL ; $iterator++ ){
            $arr[self::increaseLetters('A', $iterator)] = $line ;
        }

        return $arr;
    }

    /**
     * @return string
     */
    public static function getRandomCoordinates(){
        $vertical =  rand(0, self::SIZE_VERTICAL-1);
        $horizontal = rand(0, self::SIZE_HORIZONTAL-1);

        $vertical = self::increaseLetters( 'A' , $vertical ) ;

        return $vertical.$horizontal;
    }

    /**
     * @param $coordinates J6, A7, ....
     * @return bool
     */
    public static function checkCoordinates( $coordinates ){
        $coordinates = self::splitCoordinates($coordinates);
        $max_coordinates = self::splitCoordinates(self::getMaxCoordinates()[0]);

        $msg = "Incorrect coordinates: ".$coordinates[0].PHP_EOL;
        if($coordinates[1] > $max_coordinates[1] || $coordinates[2] > $max_coordinates[2] ){
//           echo($msg);
           return false;
       }else{
            return true;
        }
    }

    /**
     * @return mixed
     */
    public static function getMaxCoordinates(){
        return self::splitCoordinates(self::increaseLetters('A',self::SIZE_VERTICAL-1) . (self::SIZE_HORIZONTAL-1) );
    }

    /**
     * @param $coordinates G10, A7, C5 ...
     * @return mixed
     */

    public static function splitCoordinates($coordinates){
        $coordinates = strtoupper($coordinates);

        if(!preg_match('/([A-Z]+)(\d+)/',$coordinates, $res_coordinates )){
//            echo ("Incorect coordinates: ". $coordinates);
            return false;
        }
        return $res_coordinates;
    }
    /**
     * @param $base
     * @param $num
     * @return string
     */
    public static function increaseLetters( $base , $num ){
        return chr(ord( $base )+ $num );
    }

    /**
     * Add Destroyer to queue
     */
    private function initRandomDestroyer(){

        $Ship = new Ship($this->getRandomCoordinates(), Ship::getRandomOrientation(), Ship::DSTROYER_LENGTH  ) ;
        if($this->verifyShipOnBattleField($Ship)) {
            array_push($this->ships, $Ship);
        }else{
            $this->initRandomDestroyer();
        }
    }

    /**
     * Add Battleship to queue
     */
    private function initRandomBattleship(){

        $Ship = new Ship($this->getRandomCoordinates(), Ship::getRandomOrientation(), Ship::BATTLESHIP_LENGTH  ) ;
        if($this->verifyShipOnBattleField($Ship)) {
            array_push($this->ships, $Ship);
        }else{
            $this->initRandomBattleship();
        }
    }

    /**
     * @param \BattleShips\Ship $Ship
     * @return bool
     */
    private function verifyShipOnBattleField(Ship $Ship){

        // Check sides
        foreach ($Ship->getCoordinatesArray() as $item ) {

            if(!self::checkCoordinates($item[0])){
                //echo( ( ($Ship->getLength()==4 ) ? "Battleship": "Destroyer" ).   "get out field: ". $item[0].PHP_EOL);
                return false;
            }
        }


        // Check existing battleships for crossing
        $cross = true;
        // Existing ships
        foreach ( $this->getShips() as $current_ship ){
            // Existing Ships Coordinates
            $ecs_coordinates = $current_ship->getCoordinatesArray();
            // New Ship Coordinates
            foreach ($Ship->getCoordinatesArray() as $new_ship_coordinates ){
                // Check Positions
                foreach($ecs_coordinates as $ecs_coordinate ){
                    if($ecs_coordinate[0] == $new_ship_coordinates[0] ){
//                        echo("Cross Check [Failed]".PHP_EOL);
                        $cross = false;
                        break 3;
                    }
                }
            }
        }

        return $cross;
    }

    /**
     * @return bool
     */
    public function allDead(){

        $destroyed_count = 0 ;
        foreach ($this->ships as $Ship ){
            if($Ship->isDestroyed()){ $destroyed_count++; }
        }

        return count($this->ships) == $destroyed_count;
    }

    /**
     * @param null $grid
     * @param bool $html
     */
    public function showGrid( $grid = null , $html = false){

        if(!$grid){
            $grid = $this->field_view;
        }
        echo "  ".implode(" ", range(1 ,self::SIZE_HORIZONTAL ) ) . PHP_EOL;
        for ( $iterator = 0; $iterator < self::SIZE_VERTICAL ; $iterator++ ){
            $letter= self::increaseLetters('A', $iterator);
            echo $letter . " " . implode(" ", $grid[$letter]) . PHP_EOL;
        }
        echo PHP_EOL;

    }

    /**
     * @return array
     */
    private function getShips()
    {
        return $this->ships;
    }

    /**
     * @return array
     */
    public function getHits()
    {
        return $this->hits;
    }

    public function showShipsView(){
        $grid = $this->initEmptyBattlefield();

        foreach ($this->getShips() as $Ship) {
            $Coordinates = $Ship->getCoordinatesArray();
            foreach($Coordinates as $coordinate ){
                $letter = "" ;
                if(Ship::DSTROYER_LENGTH == $Ship->getLength() ){
                    $letter = "D";
                }elseif (Ship::BATTLESHIP_LENGTH == $Ship->getLength() ){
                    $letter = "B";
                }else{
                    $letter = "X";
                }

                $grid[$coordinate[1]][$coordinate[2]] = $letter;
            }
        }

        $this->showGrid($grid);
    }

    public function getHitsCount(){
        return count($this->hits);
    }
}
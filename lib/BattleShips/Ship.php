<?php
/**
 * Created by PhpStorm.
 * User: teyka
 * Date: 3/8/2018
 * Time: 1:35 AM
 */
namespace BattleShips;

class Ship
{
    CONST DSTROYER_LENGTH = 5;
    CONST BATTLESHIP_LENGTH = 4;
    CONST HORIZONTAL = true;
    CONST VERTICAL = false;

    /**
     * @var int
     */
    private $length  ;

    /**
     * @var int
     */
    private $hits ;

    /**
     * @var bool
     */
    private $destroyed ;

    /**
     * @var bool
     */
    private $direction ;

    /**
     * @var String A5, B6, starting position
     */
    private $coordinates;

    public function __construct( $coordinates, $direction = true ,  $length = null )
    {
        $this->setCoordinates($coordinates);
        $this->setDirection($direction);
        $this->setLength($length);
        $this->setDestroyed(false);
        $this->setHits(0);

        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */

    public static function getRandomOrientation(){
        $res =  (bool) rand(0,1);

        if($res === self::HORIZONTAL){
            return self::HORIZONTAL;
        }elseif($res === self::VERTICAL){
            return self::VERTICAL;
        }else{
            throw new Exception("Unknown orientation type: $res" . PHP_EOL);
        }

    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return bool
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param bool $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }


    /**
     * @return String
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @param String $coordinates
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getCoordinatesArray(){

        $start_coordinates = Battlefield::splitCoordinates($this->coordinates);

        $array_coordinates = array( );

        // Generate coordinates
        for ( $iterator = 1 ; $iterator<=$this->getLength(); $iterator++){
            if(($this->getDirection() == self::HORIZONTAL )){
                $hor = $start_coordinates[2] +$iterator ;
                $c_coordinates = array($start_coordinates[1].$hor, $start_coordinates[1], $hor );
            }else{
                $ver = Battlefield::increaseLetters( $start_coordinates[1],$iterator ) ;
                $c_coordinates = array( $ver.$start_coordinates[2], $ver, $start_coordinates[2]);
            }
            array_push($array_coordinates,$c_coordinates);
        }

        return $array_coordinates;
    }

    /**
     * Increase Hits and detect Destruction
     */
    public function increaseHits(){
        $this->hits++;

        if($this->hits == $this->length){
            $this->destroyed = true;
        }
    }
    /**
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     */
    private function setHits($hits)
    {
        $this->hits = $hits;
    }
    /**
     * @return bool
     */
    public function isDestroyed()
    {
        return $this->destroyed;
    }

    /**
     * @param bool $destroyed
     */
    private function setDestroyed($destroyed)
    {
        $this->destroyed = $destroyed;
    }

}
<?php

/**
 * Created by IntelliJ IDEA.
 * User: Aaron Crosbie: B00082567
 * Date: 06/12/2016
 * Time: 11:47
 */

require_once('Motherboard.php');
require_once('Cpu.php');
require_once('Gpu.php');
require_once('Hdd.php');
require_once('Ram.php');
require_once('Ssd.php');
require_once('Psu.php');

use itb\DbManager;
require_once('DbManager.php');

class ComponentController
{
    //***************DEFAULT VARIABLES FOR EACH CLASS******************
    protected $price;
    protected $motherboard;
    protected $cpu;
    protected $gpu;
    protected $ram;
    protected $psu;
    protected $hdd;
    protected $ssd;
    protected $dbMgr;

    //*****************CHOICES FROM FORM INPUT*******************
    private $motherboardPref;
    private $cpuPref;
    private $gpuPref;
    private $ramPref;
    private $hddSize;
    private $ssdSize;

    /**
     * Controller constructor.
     * @param $price
     * @param $motherboardPref
     * @param $cpuPref
     * @param $gpuPref
     * @param $ramPref
     * @param $hddSize
     * @param $ssdSize
     * Initialises all component classes & takes in form data for later use
     */
    function __construct($price, $motherboardPref, $cpuPref, $gpuPref, $ramPref, $hddSize, $ssdSize)
    {
        //Instantiates all component classes
        $this->motherboard = new Motherboard();
        $this->cpu = new Cpu();
        $this->gpu = new Gpu();
        $this->ram = new Ram();
        $this->psu = new Psu();
        $this->hdd = new Hdd();
        $this->ssd = new Ssd();
        $this->dbMgr = new DbManager();

        //Instantiates all preferences
        $this->price = $price;
        $this->motherboardPref = $motherboardPref;
        $this->cpuPref = $cpuPref;
        $this->gpuPref = $gpuPref;
        $this->ramPref = $ramPref;
        $this->hddSize = $hddSize;
        $this->ssdSize = $ssdSize;
    }

    /**
     * @param $motherboard
     */
    function setMotherboard($motherboard){
        $this->motherboard = $motherboard;
        echo "<b>Motherboard:</b><br>Name: " . $this->motherboard->getName() . "<br>Manufacturer: " . $this->motherboard->getManufacturer()
            . "<br>Form Factor: " . $this->motherboard->getFormat() . "<br>Price: " . $this->motherboard->getPrice() . "<hr>";
    }


    function setCpu($cpu){
        $this->cpu = $cpu;
        echo "<b>CPU:</b><br>Name: " . $this->cpu->getName() . "<br>Manufacturer: " . $this->cpu->getManufacturer()
            . "<br>Cores: " . $this->cpu->getCores() . "<br>Price: " . $this->cpu->getPrice();
    }

    function setGpu($gpu){
        $this->gpu = $gpu;
        echo "<br><b>GPU:</b><br>Name: " . $this->gpu->getName() . "<br>Manufacturer: " . $this->gpu->getManufacturer()
            . "<br>Form Factor: " . $this->gpu->getFormFactor() . "<br>Price: " . $this->gpu->getPrice();
    }

    function setRam($ram){
        $this->ram = $ram;
        echo "<br><b>Ram:</b><br>Name: " . $this->ram->getName() . "<br>Manufacturer: " . $this->ram->getManufacturer()
            . "<br>Ram Type: " . $this->ram->getType() . "<br>Price: " . $this->ram->getPrice();
    }

    function setHdd($hdd){
        $this->hdd = $hdd;
        echo "<br><b>HDD:</b><br>Name: " . $this->hdd->getName() . "<br>Storage: " . $this->hdd->getStorage()
           . "<br>Price: " . $this->hdd->getPrice();
    }

    function setSsd($ssd){
        $this->ssd = $ssd;
        echo "<br><b>SSD:</b><br>Name: " . $this->ssd->getName() . "<br>Storage: " . $this->ssd->getStorage()
            . "<br>Price: " . $this->ssd->getPrice();
    }

    function setPsu($psu){

    }

    /**
     * @return Motherboard name
     */
    function getMotherboardName(){
        return $this->motherboard->getName();
    }

    /**
     * @return Motherboard price
     */
    function getMotherboardPrice(){
        return $this->motherboard->getPrice();
    }

    /**
     * @return CPU name
     */
    function getCpuName(){
        return $this->cpu->getName();
    }

    /**
     * @return CPU price
     */
    function getCpuPrice(){
        return $this->cpu->getPrice();
    }

    /**
     * @return GPU name
     */
    function getGpuName(){
        return $this->gpu->getName();
    }

    /**
     * @return GPU price
     */
    function getGpuPrice(){
        return $this->gpu->getPrice();
    }

    /**
     * @return Ram name
     */
    function getRamName(){
        return $this->ram->getName();
    }

    /**
     * @return Ram price
     */
    function getRamPrice(){
        return $this->ram->getPrice();
    }

    /**
     * @return HDD name
     */
    function getHddName(){
        return $this->hdd->getName();
    }

    /**
     * @return HDD price
     */
    function getHddPrice(){
        return $this->hdd->getPrice();
    }

    /**
     * @return SSD name
     */
    function getSsdName(){
        return $this->ssd->getName();
    }

    /**
     * @return SSD price
     */
    function getSsdPrice(){
        return $this->ssd->getPrice();
    }

    /**
     * @return string
     * Calls method to assemble components & returns price
     */
    function surveyAnalysis(){
        $price = (int)$this->price;
        $this->buildComputer($price);
    }

    function buildComputer($price){
        $tempPrice = 0;
        $maxPrice = $price;
        $currentWattage = 0;

        $this->setMotherboard($this->dbMgr->chooseMotherboard($this->motherboardPref, $maxPrice));
        $tempPrice += $this->motherboard->getPrice();
        $this->setCpu($this->dbMgr->chooseCpu($this->cpuPref, $maxPrice));
        $tempPrice += $this->cpu->getPrice();
        $currentWattage += $this->cpu->getWatts();
        $this->setGpu($this->dbMgr->chooseGpu($this->gpuPref, $maxPrice));
        $tempPrice += $this->gpu->getPrice();
        $currentWattage += $this->gpu->getWatts();
        $this->setRam($this->dbMgr->chooseRam($this->motherboard->getRamType(), $this->ramPref));
        $tempPrice += $this->ram->getPrice();
        $this->setHdd($this->dbMgr->chooseHdd($this->hddSize));
        $tempPrice += $this->hdd->getPrice();
        $currentWattage += $this->hdd->getWatts();
        $this->setSsd($this->dbMgr->chooseSsd($this->ssdSize));
        $tempPrice += $this->ssd->getPrice();
        $currentWattage += $this->ssd->getWatts();
        $this->setPsu($this->dbMgr->choosePsu($currentWattage, $maxPrice));
        echo "<hr><b>" . $tempPrice . "</b><hr>";
        //$tempPrice += $this->psu->getPrice();
    }
}
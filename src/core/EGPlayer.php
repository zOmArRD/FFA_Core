<?php
namespace core;

use pocketmine\network\mcpe\RakLibInterface;
use pocketmine\Player;
use pocketmine\utils\Config;

/**
 * Class YGCPlayer
 * @package zOmArRD\core
 */
class EGPlayer extends Player {

    private $data = null;
    private $plugin;
    private $kills = false;
    private $deaths = false;


    public function __construct(RakLibInterface $interface, string $ip, int $port) {
        parent::__construct($interface, $ip, $port);
        if(($plugin = $this->getServer()->getPluginManager()->getPlugin("FFA_Core")) instanceof Main && $plugin->isEnabled()) {
            $this->setPlugin($plugin);
        }else {
            //Todo: implements
        }
    }


    public function int() {
        @mkdir($this->getPlugin()->getDataFolder()."players");
        $this->data = new Config($this->getPlugin()->getDataFolder()."players/".strtolower($this->getName()).".json", Config::JSON, ["Kills" => 0, "Deaths" => 0]);
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin($plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return array
     */
    public function getData() : array {
        return $this->getDataFile()->getAll();
    }

    /**
     * @param string $data
     * @param        $value
     */
    public function setData(string $data, $value) {
        $this->getDataFile()->set($data, $value);
        $this->getDataFile()->save();
    }

    /**
     * @return Config
     */
    public function getDataFile() : Config {
        return $this->data;
    }

    /**
     * @param string $data
     * @param int $amount
     */
    public function addData(string $data, int $amount) {
        $this->getDataFile()->set($data, $this->getData()[$data] + $amount);
        $this->getDataFile()->save();
    }

    /**
     * @param int $deaths
     */
    public function addDeaths(int $deaths) {
        $this->getDataFile()->set("Deaths", $this->getDeaths() + $deaths);
        $this->getDataFile()->save();
    }

    /**
     * @param int $kills
     */
    public function addKills(int $kills) {
        $this->getDataFile()->set("Kills", $this->getKills() + $kills);
        $this->getDataFile()->save();
    }

    public function isKills() : bool {
        return $this->kills;
    }

    /**
     * @return int
     */
    public function getKills() : int {
        return $this->getData()["Kills"];
    }

    /**
     * @param bool $kills
     */
    public function setKills(bool $kills) {
        $this->kills = $kills;
    }

    public function isDeaths() : bool {
        return $this->deaths;
    }

    /**
     * @return mixed
     */
    public function getDeaths() : int {
        return $this->getData()["Deaths"];
    }

    /**
     * @param bool $deaths
     */
    public function setDeaths(bool $deaths) {
        $this->deaths = $deaths;

    }
}
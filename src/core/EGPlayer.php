<?php
namespace core;

use core\utils\PlayerUtils;
use pocketmine\entity\Attribute;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\RakLibInterface;
use pocketmine\Player;
use pocketmine\utils\Config;

/**
 * Class YGCPlayer
 * @package zOmArRD\core
 */
class EGPlayer extends Player
{

    private $data = null;
    private $plugin;
    private $kills = false;
    private $deaths = false;

    protected $enderpearlcooldown = false;

    public const MAX_ENDERPEARL_SEC = 10;

    private $maxEnderpearlTicks;
    private $enderpearlTick;

    public function __construct(RakLibInterface $interface, string $ip, int $port)
    {
        parent::__construct($interface, $ip, $port);
        if (($plugin = $this->getServer()->getPluginManager()->getPlugin("FFA_Core")) instanceof Main && $plugin->isEnabled()) {
            $this->setPlugin($plugin);
        } else {
            //Todo: implements
        }
        $this->maxEnderpearlTicks = Utils::secondsToTicks(self::MAX_ENDERPEARL_SEC);
        $this->enderpearlTick = 0;
    }


    public function int()
    {
        @mkdir($this->getPlugin()->getDataFolder() . "players");
        $this->data = new Config($this->getPlugin()->getDataFolder() . "players/" . strtolower($this->getName()) . ".json", Config::JSON, ["Kills" => 0, "Deaths" => 0]);
    }

    /**
     * @return Main
     */
    public function getPlugin(): Main
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->getDataFile()->getAll();
    }

    /**
     * @param string $data
     * @param        $value
     */
    public function setData(string $data, $value)
    {
        $this->getDataFile()->set($data, $value);
        $this->getDataFile()->save();
    }

    /**
     * @return Config
     */
    public function getDataFile(): Config
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @param int $amount
     */
    public function addData(string $data, int $amount)
    {
        $this->getDataFile()->set($data, $this->getData()[$data] + $amount);
        $this->getDataFile()->save();
    }

    /**
     * @param int $deaths
     */
    public function addDeaths(int $deaths)
    {
        $this->getDataFile()->set("Deaths", $this->getDeaths() + $deaths);
        $this->getDataFile()->save();
    }

    /**
     * @param int $kills
     */
    public function addKills(int $kills)
    {
        $this->getDataFile()->set("Kills", $this->getKills() + $kills);
        $this->getDataFile()->save();
    }

    public function isKills(): bool
    {
        return $this->kills;
    }

    /**
     * @return int
     */
    public function getKills(): int
    {
        return $this->getData()["Kills"];
    }

    /**
     * @param bool $kills
     */
    public function setKills(bool $kills)
    {
        $this->kills = $kills;
    }

    public function isDeaths(): bool
    {
        return $this->deaths;
    }

    /**
     * @return mixed
     */
    public function getDeaths(): int
    {
        return $this->getData()["Deaths"];
    }

    /**
     * @param bool $deaths
     */
    public function setDeaths(bool $deaths)
    {
        $this->deaths = $deaths;
    }

    public function setEnderPearlCooldown(bool $value){
        $this->enderpearlcooldown = $value;
    }

    public function isEnderPearlCooldown():bool{
        return $this->enderpearlcooldown!==false;
    }

    public function handleLevelSoundEvent(LevelSoundEventPacket $packet): bool
    {
        if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_STRONG or $packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE){
            return false;
        }
        PlayerUtils::broadcastPacketToViewers($this, $packet, function (Player $player, DataPacket $packet){
            if ($player instanceof EGPlayer and $packet instanceof LevelSoundEventPacket){
                if (!isset(PlayerUtils::SWISH_SOUNDS[$packet->sound])){
                    return true;
                }
                return false;
            }
            return true;
        });
        return true;
    }

    public function attack(EntityDamageEvent $source):void
    {
        parent::attack($source);
        if ($source->isCancelled()) {
            return;
        }
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                switch ($this->getLevel()) {
                    case $this->plugin->getServer()->getLevelByName("nodebuff");
                        $this->attackTime = 10;
                        break;
                    case $this->plugin->getServer()->getLevelByName("gapple");
                        $this->attackTime = 10;
                        break;
                    case $this->plugin->getServer()->getLevelByName("buildffa");
                        $this->attackTime = 10;
                        break;
                    case $this->plugin->getServer()->getLevelByName("combo");
                        $this->attackTime = 3;
                        break;
                    case $this->plugin->getServer()->getLevelByName("fist");
                        $this->attackTime = 7;
                        break;
                    case $this->plugin->getServer()->getLevelByName("resistance");
                        $this->attackTime = 7;
                        break;
                }
            }
        }
    }

    public function knockBack($damager, float $damage, float $x, float $z, float $base=0.4):void
    {
        $xzKB = 0.388;
        $yKb = 0.390;
        if ($damager instanceof Player) {
            switch ($this->getLevel()) {
                /*case $this->plugin->getServer()->getLevelByName("nodebuff");
                    $xzKB=0.388;
                    $yKb=0.390;
                    break;
                case $this->plugin->getServer()->getLevelByName("nodebuff-low");
                    $xzKB=0.385;
                    $yKb=0.380;
                    break;*/
                case $this->plugin->getServer()->getLevelByName("nodebuff");
                    $xzKB = 0.390;
                    $yKb = 0.366;
                    break;
                case $this->plugin->getServer()->getLevelByName("gapple");
                    $xzKB = 0.393;
                    $yKb = 0.392;
                    break;
                case $this->plugin->getServer()->getLevelByName("buildffa");
                    $xzKB = 0.391;
                    $yKb = 0.391;
                    break;
                case $this->plugin->getServer()->getLevelByName("combo");
                    $xzKB = 0.330;
                    $yKb = 0.300;
                    break;
                case $this->plugin->getServer()->getLevelByName("fist");
                    $xzKB = 0.405;
                    $yKb = 0.408;
                    break;
                case $this->plugin->getServer()->getLevelByName("resistance");
                    $xzKB = 0.405;
                    $yKb = 0.408;
                    break;
            }
        }

        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > $this->getAttributeMap()->getAttribute(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;
            $motion = clone $this->motion;
            $motion->x /= 2;
            $motion->y /= 2;
            $motion->z /= 2;
            $motion->x += $x * $f * $xzKB;
            $motion->y += $yKb;
            $motion->z += $z * $f * $xzKB;
            if ($motion->y > $yKb) {
                $motion->y = $yKb;
            }
            $this->setMotion($motion);
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: zOmArRD
 * Date: 2020-08-21
 *       ___               _         ____  ____
 *  ____/ _ \ _ __ ___    / \   _ __|  _ \|  _ \
 * |_  / | | | '_ ` _ \  / _ \ | '__| |_) | | | |
 *  / /| |_| | | | | | |/ ___ \| |  |  _ <| |_| |
 * /___|\___/|_| |_| |_/_/   \_\_|  |_| \_\____/
 *
 */
declare(strict_types=1);
namespace core;

/** Pocketmine */

use core\events\PlayerListener;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\PluginBase;
use pocketmine\network\mcpe\protocol\types\{DeviceOS, SkinAdapterSingleton};
use pocketmine\Player;
use pocketmine\utils\TextFormat as TE;

/** System */
use core\events\SpamListener;
use core\providers\BungeeCord;
use core\utils\ParsonaSkinAdapter;
use core\events\DataPacketListener;
use core\providers\Scoreboards;

/** EndGames Ranks */
use EndGamesRanks\instances\User;

/**
 * Class Main
 * @package core
 */
class Main extends PluginBase
{
    /** @var $instance */
    public static $instance;

    private $data = null;

    private $kills = false;

    /** @var null $originalAdaptor */
    private $originalAdaptor = null;

    /** @var string $PREFIX */
    public const PREFIX = "§7[§4Dragon§7] §r";

    /**
     * Returns an instance of the plugin
     * @return mixed
     */
    public static function getInstance() : Main
    {
        return self::$instance;
    }

    public function getPrefix(){
        return self::PREFIX;
    }

    public function onLoad() : void
    {
        self::$instance = $this;
    }

    public function onEnable() : void
    {
        /** @var  $logger */
        $logger = $this->getLogger();

        $this->registerEvents();

        $this->originalAdaptor = SkinAdapterSingleton::get();
        SkinAdapterSingleton::set(new ParsonaSkinAdapter());

        if (!in_array(ProtocolInfo::CURRENT_PROTOCOL, [407, 408, 419])) {
            $this->getServer()->shutdown();
        } else {
            $logger->info("§cEndGames §fNetwork §6System enabled");
        }

        $lobby = $this->getServer()->getDefaultLevel();
        $lobby->setTime(16000);
        $lobby->stopTime();
        foreach (['soccer'] as $ffa){
            $this->getServer()->loadLevel($ffa);
        }
    }

    public function onDisable() : void
    {
        foreach($this->getServer()->getOnlinePlayers() as $player){
            BungeeCord::transferPlayer($player, "lobby1");
        }

        if($this->originalAdaptor !== null){
            SkinAdapterSingleton::set($this->originalAdaptor);
        }
        sleep(1);
    }

    /** @param Register events */
    private function registerEvents() : void {
        $pluginManager = $this->getServer()->getPluginManager();

        $pluginManager->registerEvents(new SpamListener(), $this);
        $pluginManager->registerEvents(new DataPacketListener(), $this);
        $pluginManager->registerEvents(new PlayerListener(), $this);


    }

    /**
     * @param Player $player
     * @param int $opcion
     */
    public function createScoreboard(Player $player, int $opcion) : void {
        $api = new Scoreboards();
        switch($opcion) {
            case 0:
                $api->new($player, $player->getName(), "§l§cEndGames §fNetwork");
                $api->setLine($player, 8, TE::RED. "§7────────────────");
                $api->setLine($player, 7, TE::RESET. " §cNick: §7".$player->getName());
                $api->setLine($player, 6, TE::GRAY. "§4");
                $rank = "";
                $user = new User($player);
                if($user->getUserMainGroup()->getId() !== null){
                    $rank = $user->getUserMainGroup()->getName();
                } else {
                    $rank = "Get it!";
                }
                $api->setLine($player, 5, TE::RESET. " §cRank: §7".$rank);
                $api->setLine($player, 4, TE::YELLOW. "§8§1§8");
                $api->setLine($player, 3, TE::RESET. " §cLobby: §7#1");
                $api->setLine($player, 2, TE::YELLOW. "§8§1§8");
                $api->setLine($player, 1, TE::RESET. " §7play.endgames.cf");
                $api->setLine($player, 0, TE::RESET. "§7────────────────");
                $api->getObjectiveName($player);
                break;
        }
    }


}

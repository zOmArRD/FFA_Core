<?php
declare(strict_types=1);
namespace core\tasks;

use core\Main;
use core\providers\BossAPI;
use core\providers\SelectorForm;
use core\utils\PlayerUtils;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

/**
 * Class VerifyTask
 * @package system\tasks
 */
class PlayerJoin extends Task
{

    private $time = 3;

    /**
     * VerifyTask constructor.
     * @param EGDuels $plugin
     * @param Player $player
     */
    public function __construct(Main $plugin, Player $player)
    {
        $this->plugin = $plugin;
        $this->pl = $player;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        $player = $this->pl;
        $time = $this->time;
        if ($player->isOnline()){
            if ($time == 3) {
                $player->setInvisible(true);
                $player->sendMessage("§l§a» §r§fWe are registering you in the database, please wait...");
            }
            if ($time == 2){

            }
            if ($time == 1){
                $player->sendMessage("§l§a» §r§fFinishing process...");
                $player->removeAllEffects();
            }
            if ($time == 0){
                PlayerUtils::selectorItem($player);
                SelectorForm::selectorFFA($player);
                BossAPI::sendBossBarText($player, "§c§lEndGames §fNetwork");
            }
        }
        $this->time--;
    }
}
<?php
declare(strict_types=1);

namespace core\events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use core\Main;

class SpamListener implements Listener{

    public $ips = [".es", ".net", ".ddns", ".eu", ".us", ".club", ".sytes", ".cf", ".tk", ".ml", ".pro", ".com", ".1", ".2", ".3", ".4", ".5", ".6", ".7", ".8", ".9", ".10", ",net", ",pro", ",com", ",ml", ",tk", ",cf", "cubecraft", "versai"];
    public $allowed = ["play.endgames.cf", "shop.endgames.cf", "endgames.cf"];

    /**
     * @param PlayerChatEvent $ev
     */
    public function PlayerChatEvent(PlayerChatEvent $ev)
    {
        $p = $ev->getPlayer();
        $pn = $p->getName();
        $msg = $ev->getMessage();
        $pref = Main::PREFIX;
        foreach ($this->ips as $ips) {
            if (strpos($msg, $ips)) {
                if ($p->hasPermission("endgames.staff")) {
                    $ev->setCancelled(false);
                } else {
                    $p->sendMessage($pref . "§cWe have seen that you tried to pass ip from another server, the staff will take serious measures if this happens again");
                    $ev->setCancelled(true);
                }
                foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    if ($player->hasPermission("endgames.staff")) {
                        $player->sendMessage($pref . "§cAttention, the player §6$pn has tried to pass an IP\n§bMessage: §c$msg");
                    }
                }
                foreach ($this->allowed as $allow) {
                    if (strpos($msg, $allow)) {
                        $ev->setCancelled(false);
                    }
                }
            }
        }
    }
}
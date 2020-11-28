<?php
/**
 * Created by PhpStorm.
 * User: zOmArRD
 *       ___               _         ____  ____
 *  ____/ _ \ _ __ ___    / \   _ __|  _ \|  _ \
 * |_  / | | | '_ ` _ \  / _ \ | '__| |_) | | | |
 *  / /| |_| | | | | | |/ ___ \| |  |  _ <| |_| |
 * /___|\___/|_| |_| |_/_/   \_\_|  |_| \_\____/
 *
 */
namespace core\utils;

use core\EGPlayer;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class PlayerUtils
{
    const SWISH_SOUNDS=[LevelSoundEventPacket::SOUND_ATTACK => true, LevelSoundEventPacket::SOUND_ATTACK_STRONG => true];

    public static function onServerJoin(Player $p)
    {
        $p->setMaxHealth(1);
        $p->setHealth(1);
        $p->setFood(20);
        $p->setGamemode(2);
    }

    public static function onClearPlayer(Player $p)
    {
        $p->getArmorInventory()->clearAll();
        $p->getInventory()->clearAll();
        $p->removeAllEffects();
    }

    public static function healPlayer(Player $p)
    {
        $p->setMaxHealth(20);
        $p->setHealth(20);
        $p->setFood(20);
    }

    public static function selectorItem(Player $p)
    {
        $compass = Item::get(Item::COMPASS);
        $compass->setCustomName("§aSelector");
        $p->getInventory()->setItem(4, $compass);
    }

    public static function broadcastPacketToViewers(EGPlayer $inPlayer, DataPacket $packet, ?callable $callable=null, ?array $viewers=null):void
    {
        $viewers = $viewers ?? $inPlayer->getLevelNonNull()->getViewersForPosition($inPlayer->asVector3());
        foreach ($viewers as $viewer) {
            if ($viewer->isOnline()) {
                if ($callable !== null and !$callable($viewer, $packet)) {
                    continue;
                }
                $viewer->batchDataPacket($packet);
            }
        }
    }

}
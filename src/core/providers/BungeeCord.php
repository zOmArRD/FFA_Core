<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * @author zOmArRD
 *       ___               _         ____  ____
 *  ____/ _ \ _ __ ___    / \   _ __|  _ \|  _ \
 * |_  / | | | '_ ` _ \  / _ \ | '__| |_) | | | |
 *  / /| |_| | | | | | |/ ___ \| |  |  _ <| |_| |
 * /___|\___/|_| |_| |_/_/   \_\_|  |_| \_\____/
 *
 */
namespace core\providers;
use pocketmine\network\mcpe\protocol\ScriptCustomEventPacket;
use pocketmine\Player;
use pocketmine\utils\Binary;

/**
 * Class BungeeCord
 * @package system\providers
 */
class BungeeCord
{
    public static function transferPlayer(Player $player, String $server): bool
    {
        $pk = new ScriptCustomEventPacket();
        $pk->eventName = "bungeecord:main";
        $pk->eventData = Binary::writeShort(strlen("Connect")) . "Connect" . Binary::writeShort(strlen($server)) . $server;
        $player->sendDataPacket($pk);
        return true;
    }
}
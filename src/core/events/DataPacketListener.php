<?php
namespace core\events;

use core\utils\DeviceData;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Server;

class DataPacketListener implements Listener
{
    public $commandList = [];

    public function onDataReceive(DataPacketReceiveEvent $event)
    {
        $packet = $event->getPacket();

        if ($packet instanceof EmotePacket) {
            $emoteId = $packet->getEmoteId();
            Server::getInstance()->broadcastPacket($event->getPlayer()->getViewers(), EmotePacket::create($event->getPlayer()->getId(), $emoteId, 1 << 0));
        }
        if ($packet instanceof LoginPacket) {
            DeviceData::saveDevice($packet->username, $packet->clientData["DeviceOS"]);
            DeviceData::saveController($packet->username, $packet->clientData["CurrentInputMode"]);
            if ($packet->protocol != ProtocolInfo::CURRENT_PROTOCOL and in_array($packet->protocol, [407, 408, 419])) {
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL;
            }
        }
    }

    public function onDataPacketSend(DataPacketSendEvent $event) {
        $packet = $event->getPacket();
        foreach (["mw", "multiworld", "query", "nick", "tag", "gamerule", "checkperm"] as $command){
            $this->commandList[strtolower($command)] = null;
        }
        if ($packet instanceof AvailableCommandsPacket) {
            if ($event->getPlayer()->hasPermission("endgames.op")) return;
                $packet->commandData = array_diff_key($packet->commandData, $this->commandList);
            }
    }
}

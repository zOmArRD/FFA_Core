<?php


namespace core\providers;


use core\Main;
use core\providers\FormAPI\SimpleForm;
use core\utils\Kits;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class SelectorForm
{
    public static function selectorFFA(Player $p){
        $form = new SimpleForm(function (Player $player, ?int $data){
            if (!is_null($data)){
                switch ($data){
                    case 0:
                        $player->sendMessage(Main::PREFIX . "transfering...");
                        Kits::setKit($player, 0);
                        $player->teleport(new Position(1, 134, 21000, Server::getInstance()->getLevelByName("soccer")));
                        $player->setInvisible(false);
                        $player->setImmobile(false);
                        ScoreboardTypes::sendScoreboard($player, 0);
                        break;
                    case 6:
                        BungeeCord::transferPlayer($player, "lobby1");
                        break;
                    default:
                        $player->sendMessage(Main::PREFIX . "§cServer Error.");
                        return;
                }
            }
        });
        $br = "\n";
        $click = "§r§5Click to join";
        $nodebuff = "§r§a" . count(Server::getInstance()->getLevelByName("soccer")->getPlayers()) . "/20";
        $images = [
            "nodebuff" => "textures/items/potion_bottle_splash_heal",
            "gapple" => "textures/items/apple_golden",
            "build" => "textures/items/fishing_rod_uncast",
            "combo" => "textures/items/fish_pufferfish_raw",
            "resistance" => "textures/items/bread",
            "fist" => "textures/items/beef_cooked",
            "return" => "textures/ui/refresh_light"
        ];
        $form->setTitle("§7FFA Type Selector");
        $form->setContent("§r§7Select here.");
        $form->addButton("§l§9NoDebuff $nodebuff" . $br . $click, 0, $images["nodebuff"]);
        $form->addButton("§l§6Gapple $nodebuff" . $br . $click, 0, $images["gapple"]);
        $form->addButton("§l§5BuildFFA $nodebuff" . $br . $click, 0, $images["build"]);
        $form->addButton("§l§eCombofly $nodebuff" . $br . $click, 0, $images["combo"]);
        $form->addButton("§l§aResistance $nodebuff" . $br . $click, 0, $images["resistance"]);
        $form->addButton("§l§aFist $nodebuff" . $br . $click, 0, $images["fist"]);
        $form->addButton("§l§aReturn to the Lobby" . $br . $click, 0, $images["return"]);


        $p->sendForm($form);
    }
}
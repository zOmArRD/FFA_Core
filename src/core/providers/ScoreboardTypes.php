<?php
namespace core\providers;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TE;
use EndGamesRanks\instances\User;

class ScoreboardTypes
{
    public static function sendScoreboard(Player $player, int $type)
    {
        $api = new Scoreboards();
        $api->new($player, $player, "§l§cEndGames §r§7| FFA #1");
        $rank = "";
        $user = new User($player);
        if($user->getUserMainGroup()->getId() !== null){
            $rank = $user->getUserMainGroup()->getName();
        } else {
            $rank = "Get it!";
        }
        switch ($type){
            case 0:
                $api->setLine($player, 5, TE::YELLOW . "§7────────────");
                $api->setLine($player, 4, TE::RESET . "§c Rank: §7" . $rank);
                $api->setLine($player, 3, TE::RESET . "§c Type: §7NoDebuff");
                $api->setLine($player, 2, TE::RESET . "§7 ");
                $api->setLine($player, 1, TE::RESET . "§7 @EndGamesNetwork");
                $api->setLine($player, 0, TE::RESET . "§7────────────");
                break;
        }
        $api->getObjectiveName($player);
    }
}
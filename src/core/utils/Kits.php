<?php
declare(strict_types=1);

namespace core\utils;


use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;

class Kits {

    public static function setKit(Player $player, int $kit){

        switch ($kit){
            case 0:
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->setAllowFlight(false);
                $player->setFlying(false);
                $player->setMaxHealth(20);
                $player->setHealth(20);
                $helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
                $chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
                $leggings = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
                $boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
                $sword = Item::get(Item::DIAMOND_SWORD, 0, 1);
                $potis = Item::get(438, 22, 30);
                $unbreakable = Enchantment::getEnchantment(17);
                $irrompible = new EnchantmentInstance($unbreakable, 3);
                $helmet->addEnchantment($irrompible);
                $chestplate->addEnchantment($irrompible);
                $leggings->addEnchantment($irrompible);
                $boots->addEnchantment($irrompible);
                $sword->addEnchantment($irrompible);
                $sharpness = Enchantment::getEnchantment(9);
                $filo = new EnchantmentInstance($sharpness, 3);
                $sword->addEnchantment($filo);
                $protection = Enchantment::getEnchantment(0);
                $protecc = new EnchantmentInstance($protection, 2);
                $helmet->addEnchantment($protecc);
                $chestplate->addEnchantment($protecc);
                $leggings->addEnchantment($protecc);
                $boots->addEnchantment($protecc);
                $player->getArmorInventory()->setHelmet($helmet);
                $player->getArmorInventory()->setChestplate($chestplate);
                $player->getArmorInventory()->setLeggings($leggings);
                $player->getArmorInventory()->setBoots($boots);
                $player->getArmorInventory()->sendContents($player);
                $player->getInventory()->addItem($sword);
                $player->getInventory()->setItem(1, Item::get(368, 0, 16));
                $player->getInventory()->setItem(2, Item::get(373, 15, 1));
                $player->getInventory()->setItem(3, Item::get(438, 22, 1));
                $player->getInventory()->setItem(4, Item::get(438, 22, 1));
                $player->getInventory()->setItem(5, Item::get(438, 22, 1));
                $player->getInventory()->setItem(6, Item::get(438, 22, 1));
                $player->getInventory()->setItem(8, Item::get(438, 22, 1));
                $boots->addEnchantment($irrompible);
                $player->getInventory()->setItem(17, Item::get(373, 15, 1));
                $player->getInventory()->setItem(26, Item::get(373, 15, 1));
                $player->getInventory()->setItem(35, Item::get(373, 15, 1));
                $player->getInventory()->addItem($potis);
                break;
            /*case 0: //gapple
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->setInvisible(false);
                $player->setImmobile(false);
                $player->setAllowFlight(false);
                $player->setFlying(false);
                $helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
                $chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
                $leggings = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
                $boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
                $sword = Item::get(Item::DIAMOND_SWORD, 0, 1);
                $unbreakable = Enchantment::getEnchantment(17);

                $protection = Enchantment::getEnchantment(0);
                $protecc = new EnchantmentInstance($protection, 1);

                $sharpness = Enchantment::getEnchantment(9);
                $filo = new EnchantmentInstance($sharpness, 1);

                $enchantmentInstance = new EnchantmentInstance($unbreakable, 2);
                $helmet->addEnchantment($enchantmentInstance);
                $chestplate->addEnchantment($enchantmentInstance);
                $leggings->addEnchantment($enchantmentInstance);
                $boots->addEnchantment($enchantmentInstance);

                $helmet->addEnchantment($protecc);
                $chestplate->addEnchantment($protecc);
                $leggings->addEnchantment($protecc);
                $boots->addEnchantment($protecc);

                $sword->addEnchantment($enchantmentInstance);
                $sword->addEnchantment($filo);
                $player->getArmorInventory()->setHelmet($helmet);
                $player->getArmorInventory()->setChestplate($chestplate);
                $player->getArmorInventory()->setLeggings($leggings);
                $player->getArmorInventory()->setBoots($boots);
                $player->getInventory()->addItem($sword);
                $player->getInventory()->setItem(1, Item::get(Item::GOLDEN_APPLE, 0, 12));
                break;
            case 1:
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->setAllowFlight(false);
                $player->setFlying(false);
                $scrim = Item::get(320, 0, 32);

                $player->getInventory()->addItem($scrim);

                break;
            case 3:
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->setAllowFlight(false);
                $player->setFlying(false);
                $scrim = Item::get(320, 0, 32);

                $player->getInventory()->addItem($scrim);
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 600000, 5, false));

                break;
            case 4:
                //
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->setAllowFlight(false);
                $player->setFlying(false);
                $scrim = Item::get(320, 0, 32);

                $player->getInventory()->addItem($scrim);
                break;*/
            default:
                break;
        }
    }
}
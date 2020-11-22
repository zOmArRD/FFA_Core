<?php
declare(strict_types=1);

namespace core\events;
use core\Main;
use core\providers\SelectorForm;
use core\tasks\PlayerJoin;
use core\utils\PlayerUtils;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\GlassBottle;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class PlayerListener implements Listener
{

    public function onServerJoin(PlayerJoinEvent $ev)
    {
        $pl = $ev->getPlayer();
        $pl->setInvisible(true);
        $pl->setImmobile(true);
        PlayerUtils::onClearPlayer($pl);
        PlayerUtils::onServerJoin($pl);
        $pl->teleport(new Position(575, 69, 191, Server::getInstance()->getLevelByName("lobby")));
        $pl->getLevel()->addSound(new EndermanTeleportSound(new Vector3($pl->getX(), $pl->getY(), $pl->getZ())));
        $pl->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 999999999, 1, false));
        $pl->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 999999999, 1, false));
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PlayerJoin(Main::getInstance(), $pl), 20);
        $ev->setJoinMessage(null);
    }

    public function onServerLeave(PlayerQuitEvent $ev)
    {
        $ev->setQuitMessage(null);
        $player = $ev->getPlayer();
        PlayerUtils::onClearPlayer($player);
        $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
    }

    public function PlayerInteractEvent(PlayerInteractEvent $event) : void
    {
        $action = $event->getAction();
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();


        if ($action == PlayerInteractEvent::RIGHT_CLICK_AIR) {
            $id = $event->getItem()->getId();
            $custom = $item->getCustomName();
            if ($id == Item::COMPASS) {
                SelectorForm::selectorFFA($player);
            }
        }

    }

    public function noHunguer(PlayerExhaustEvent $ev) : void
    {
        $ev->setCancelled(true);

    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            $event->setCancelled(true);
        }
        if($entity->getLevel()->getName() === "lobby"){
            $event->setCancelled();
        }
    }

    public function onCraft(CraftItemEvent $event){
        $event->setCancelled();
    }

    public function onDecay(LeavesDecayEvent $ev) : void
    {
        $ev->setCancelled(true);
    }

    public function onDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getLevel()->getName();
        $world = Server::getInstance()->getDefaultLevel()->getName();
        $item = $event->getItem();
        if ($level == $world && !$player->isOp()) {
            $event->setCancelled(true);
            if ($player->isOp() && $player instanceof Player) {
                $event->setCancelled(false);
            }
        }
        if ($item instanceof GlassBottle){
            $event->setCancelled();
            foreach ($player->getInventory()->getContents() as $index => $item){
                if ($item instanceof GlassBottle){
                    $player->getInventory()->clear($index);
                    break;
                }
            }
        }

    }
}
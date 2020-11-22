<?php
declare(strict_types=1);

namespace core\events;
use core\EGPlayer;
use core\Main;
use core\providers\SelectorForm;
use core\tasks\PlayerJoin;
use core\utils\DeviceData;
use core\utils\PlayerUtils;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\GlassBottle;
use pocketmine\item\Item;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;

class PlayerListener implements Listener
{

    public function onCreation(PlayerCreationEvent $event) {

        $event->setPlayerClass(EGPlayer::class);

    }

    public function onServerJoin(PlayerJoinEvent $ev)
    {
        $pl = $ev->getPlayer();
        $pn = $pl->getName();
        $device = DeviceData::getDeviceName($pl);
        $controller = DeviceData::getController($pl);
        if ($pl instanceof EGPlayer){
            $pl->int();
            $pl->setScoreTag("§l§★§a" . $device . "§7 | §f$controller");
            $pl->setInvisible(true);
            $pl->setImmobile(true);
            PlayerUtils::onClearPlayer($pl);
            PlayerUtils::onServerJoin($pl);
            $pl->teleport(new Position(575, 69, 191, Server::getInstance()->getLevelByName("lobby")));
            $pl->getLevel()->addSound(new EndermanTeleportSound(new Vector3($pl->getX(), $pl->getY(), $pl->getZ())));
            $pl->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 999999999, 1, false));
            $pl->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 999999999, 1, false));
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PlayerJoin(Main::getInstance(), $pl), 20);

        }
        $ev->setJoinMessage("§7[§a+§7] §b$pn");
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
        $player = $event->getEntity();
        if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            $event->setCancelled(true);
        }
        if($player->getLevel()->getName() === "lobby"){
            $event->setCancelled();
        }

        if ($player instanceof EGPlayer){
            $cause = $player->getLastDamageCause();
            $dead = "§b".$player->getName() . "§4[§c" . $player->getKills() . "§4]";
            if ($cause instanceof EntityDamageByEntityEvent) {
                if ($event->getFinalDamage() > $player->getHealth()) {
                    $this->kill($player);
                    $player->addDeaths(1);

                    $killer = $cause->getDamager();
                    $kill = $killer->getName();

                    if ($killer instanceof EGPlayer) {
                        $killer->addKills(1);
                        $kill = "§b". $killer->getName() . "§4[§c" . $killer->getKills() . "§4]";

                    }
                    Main::getInstance()->getServer()->broadcastMessage($kill . " §6ha asesinado a " . $dead);
                }
            }
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

    public function onDeaht(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $event->setDrops([]);
        if ($player instanceof EGPlayer) {
            $player->addDeaths(1);
            $dead = "§b". $player->getName() . "§4[§c" . $player->getKills() . "§4]";
            $cause = $player->getLastDamageCause();
            if ($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                $kill = $killer->getName();
                if ($killer instanceof EGPlayer) {
                    $killer->addKills(1);
                    $kill = "§b". $killer->getName() . "§4[§c" . $killer->getKills() . "§4]";

                }
                $this->Lightning($player);

                $event->setDeathMessage($kill . " §6ha asesinado a " . $dead);
            }
        }
    }

    public function kill(Player $player)
    {
        $this->Lightning($player);
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setGamemode(1);
        $player->setMaxHealth(1);
        $player->setHealth(1);
        $player->setInvisible(true);
        PlayerUtils::selectorItem($player);
    }

    public function onRespawn(PlayerRespawnEvent $ev)
    {
        $player = $ev->getPlayer();
        $player->setImmobile(true);
        $player->setInvisible(true);
        $ev->setRespawnPosition(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
        PlayerUtils::onClearPlayer($player);
        PlayerUtils::onServerJoin($player);
        PlayerUtils::selectorItem($player);
    }

    public function Lightning(Player $player) :void
    {
        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = [];
        $light->motion = null;
        $light->yaw = $player->getYaw();
        $light->pitch = $player->getPitch();
        $light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
        Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $light);
        $block = $player->getLevel()->getBlock($player->getPosition()->floor()->down());
        $particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), $block);
        $player->getLevel()->addParticle($particle);
        $sound = new PlaySoundPacket();
        $sound->soundName = "ambient.weather.thunder";
        $sound->x = $player->getX();
        $sound->y = $player->getY();
        $sound->z = $player->getZ();
        $sound->volume = 3;
        $sound->pitch = 1;
        Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $sound);
    }

    /**
     * @param InventoryTransactionEvent $event
     */
    public function noChangeItemSlot(InventoryTransactionEvent $event): void
    {
        $entity = $event->getTransaction()->getSource();
        if ($entity->getLevel()->getName() === "lobby") {
            $event->setCancelled(true);
            if ($entity->isOp()) {
                $event->setCancelled(false);
            }
        }
    }
}
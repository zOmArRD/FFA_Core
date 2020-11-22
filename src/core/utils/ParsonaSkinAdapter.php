<?php
/**
 * Created by PhpStorm.
 * User: zOmArRD
 * Date: 2020-08-25
 *       ___               _         ____  ____
 *  ____/ _ \ _ __ ___    / \   _ __|  _ \|  _ \
 * |_  / | | | '_ ` _ \  / _ \ | '__| |_) | | | |
 *  / /| |_| | | | | | |/ ___ \| |  |  _ <| |_| |
 * /___|\___/|_| |_| |_/_/   \_\_|  |_| \_\____/
 *
 */
declare(strict_types=1);

namespace core\utils;

use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\types\LegacySkinAdapter;
use pocketmine\network\mcpe\protocol\types\SkinData;

class ParsonaSkinAdapter extends LegacySkinAdapter{

    /** @var SkinData[] */
    private $personaSkins = [];

    public function fromSkinData(SkinData $data) : Skin{
        if($data->isPersona()){
            $id = $data->getSkinId();
            $this->personaSkins[$id] = $data;
            return new Skin($id, str_repeat(random_bytes(3) . "\xff", 2048));
        }
        return parent::fromSkinData($data);
    }

    public function toSkinData(Skin $skin) : SkinData{
        return $this->personaSkins[$skin->getSkinId()] ?? parent::toSkinData($skin);
    }
}
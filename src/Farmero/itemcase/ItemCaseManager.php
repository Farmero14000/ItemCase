<?php

declare(strict_types=1);

namespace Farmero\itemcase;

use pocketmine\entity\object\ItemEntity;
use pocketmine\nbt\tag\StringTag;
use pocketmine\world\World;
use pocketmine\utils\Config;

use Farmero\itemcase\ItemCase;

class ItemCaseManager {

    private array $itemCases = [];
    private array $usedIds = [];

    public function __construct() {
        $this->loadItemCases();
    }

    public function addItemCase(int $id, ItemEntity $itemEntity): void {
        $this->itemCases[$id] = $itemEntity;
        $this->usedIds[] = $id;
        $this->saveItemCases();
    }

    public function removeItemCase(int $id): bool {
        if (!isset($this->itemCases[$id])) {
            return false;
        }

        $itemEntity = $this->itemCases[$id];
        $itemEntity->flagForDespawn();
        unset($this->itemCases[$id]);
        $this->usedIds = array_diff($this->usedIds, [$id]);

        $this->saveItemCases();
        return true;
    }

    public function generateUniqueId(): int {
        $id = mt_rand(1, 9999);
        while (in_array($id, $this->usedIds)) {
            $id = mt_rand(1, 9999);
        }
        return $id;
    }

    public function saveItemCases(): void {
        $data = [];
        foreach ($this->itemCases as $id => $itemEntity) {
            $data[] = [
                'id' => $id,
                'item' => $itemEntity->getItem()->jsonSerialize(),
                'position' => [
                    'x' => $itemEntity->getPosition()->getX(),
                    'y' => $itemEntity->getPosition()->getY(),
                    'z' => $itemEntity->getPosition()->getZ(),
                    'level' => $itemEntity->getWorld()->getFolderName()
                ]
            ];
        }

        $config = new Config(ItemCase::getInstance()->getDataFolder() . "itemcases.json", Config::JSON);
        $config->set('item_cases', $data);
        $config->save();
    }

    public function loadItemCases(): void {
        $config = new Config($this->plugin->getDataFolder() . "itemcases.json", Config::JSON);
        $data = $config->get('item_cases', []);
        foreach ($data as $entry) {
            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($entry['position']['level']);
            if ($world instanceof World) {
                $item = Item::legacyJsonDeserialize($entry['item']);
                $position = new Vector3($entry['position']['x'], $entry['position']['y'], $entry['position']['z']);
                $itemEntity = $world->dropItem($position, $item);
                $itemEntity->setPickupDelay(0);
                $itemEntity->setNameTagVisible(true);
                $itemEntity->setNameTagAlwaysVisible(true);

                $this->itemCases[$entry['id']] = $itemEntity;
                $this->usedIds[] = $entry['id'];
            }
        }
    }
}
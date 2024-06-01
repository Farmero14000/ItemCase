<?php

declare(strict_types=1);

namespace Farmero\itemcase\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\StringTag;

use Farmero\itemcase\ItemCase;

class ItemCaseCommand extends Command {

    public function __construct() {
        parent::__construct("itemcase", "Spawn an item case", "/itemcase <id> <itemName>");
        $this->setPermission("itemcase.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game");
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage: /itemcase <id> <itemName>");
            return false;
        }

        $itemId = (int)$args[0];
        $itemName = $args[1] ?? "ItemCase";

        $item = StringToItemParser::getInstance()->parse($itemId);
        if ($item === null) {
            $sender->sendMessage(TextFormat::RED . "Invalid item ID");
            return false;
        }

        $itemCaseManager = ItemCase::getInstance()->getItemCaseManager();
        $uniqueId = $itemCaseManager->generateUniqueId();
        
        $item->setCustomName($itemName);
        $item->setNamedTag(new StringTag("itemcase_tag", (string)$uniqueId));

        $itemEntity = $sender->getWorld()->dropItem($sender->getPosition(), $item);
        $itemEntity->setPickupDelay(0);
        $itemEntity->setNameTagVisible(true);
        $itemEntity->setNameTagAlwaysVisible(true);

        $itemCaseManager->addItemCase($uniqueId, $itemEntity);

        $sender->sendMessage(TextFormat::GREEN . "Item case spawned with tag: " . $uniqueId);
        return true;
    }
}

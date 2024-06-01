<?php

declare(strict_types=1);

namespace Farmero\itemcase\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use Farmero\itemcase\ItemCase;

class RItemCaseCommand extends Command {

    public function __construct() {
        parent::__construct("ritemcase", "Remove an item case", "/ritemcase <special tag>", []);
        $this->setPermission("itemcase.cmd.ritemcase");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage: /ritemcase <special tag>");
            return false;
        }

        $id = (int)$args[0];
        $itemCaseManager = ItemCase::getInstance()->getItemCaseManager();

        if (!$itemCaseManager->removeItemCase($id)) {
            $sender->sendMessage(TextFormat::RED . "No item case found with tag: " . $id);
            return false;
        }

        $sender->sendMessage(TextFormat::GREEN . "Item case with tag " . $id . " removed");
        return true;
    }
}
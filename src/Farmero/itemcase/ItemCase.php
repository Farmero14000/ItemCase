<?php

declare(strict_types=1);

namespace Farmero\itemcase;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

use Farmero\itemcase\ItemCaseManager;

use Farmero\itemcase\commands\ItemCaseCommand;
use Farmero\itemcase\commands\RItemCaseCommand;

class ItemCase extends PluginBase {

    public static ItemCase $instance;
    private ItemCaseManager $itemCaseManager;

    protected function onEnable(): void {
        self::$instance = $this;
        $this->itemCaseManager = new ItemCaseManager();
        $this->getServer()->getCommandMap()->registerAll("ItemCase", [
            new ItemCaseCommand(),
            new RItemCaseCommand()
        ]);
    }

    protected function onDisable(): void {
        $this->itemCaseManager->saveItemCases();
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function getItemCaseManager(): ItemCaseManager {
        return $this->itemCaseManager;
    }
}
<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\undo\Revert;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class UndoCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "undo", (new Translation(Translation::COMMANDS_UNDO_DESCRIPTION))->getMessage(), "/undo [amount]", ["u"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return false;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return false;
		}

		if(!SessionManager::getPlayerSession($sender)->getRevertStorer()->undoStorageExists()) {
			$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_UNDO_NO_UNDO))->getMessage());
			return false;
		}

		$undoAmount = 1;
		if(isset($args[0])) {
			$undoAmount = (int) $args[0];
			if($undoAmount > ($totalUndo = SessionManager::getPlayerSession($sender)->getRevertStorer()->getTotalStores(Revert::TYPE_UNDO))) {
				$undoAmount = $totalUndo;
			}
		}

		SessionManager::getPlayerSession($sender)->getRevertStorer()->restoreLatestRevert(Revert::TYPE_UNDO, $undoAmount);
		$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_UNDO_SUCCESS))->getMessage() . TF::AQUA . " (" . $undoAmount . ")");
		return true;
	}
}

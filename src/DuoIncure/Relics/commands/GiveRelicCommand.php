<?php

namespace DuoIncure\Relics\commands;

use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use DuoIncure\Relics\Main;
use function in_array;
use function is_numeric;
use function strtolower;

class GiveRelicCommand extends PluginCommand {

	/** @var Main */
	private $plugin;

	/**
	 * GiveRelicCommand constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("giverelic", $plugin);
		$this->setPermission("relics.command.giverelic");
		$this->setDescription("Give someone relics!");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$typesArray = ["common", "rare", "mythic", "legendary"];
		if(!$sender->hasPermission("relics.command.giverelic")){
			$sender->sendMessage("§l§8(§a!§8)§r§c You do not have permission to use this command.");
			return;
		}
		if(!isset($args[0])){
			$sender->sendMessage("§l§8(§a!§8)§r§c Usage: /giverelic [player] [type] [amount]");
			return;
		} elseif(!$this->plugin->getServer()->getPlayerExact($args[0])){
			$sender->sendMessage("§l§8(§a!§8)§r§c You must provide a valid player name.");
			return;
		} elseif(!isset($args[1]) || !in_array(strtolower($args[1]), $typesArray)){
			$sender->sendMessage("§l§8(§a!§8)§r§c You must provide a valid relic type." . TF::EOL . "§l§8(§a!§8)§r§7 Types: Common§f, §6Rare§f, §dMythic§f, §bLegendary§f, §4Godly§f, §cSacred");
			return;
		} elseif(!isset($args[2]) || !is_numeric($args[2])){
			$sender->sendMessage("§l§8(§a!§8)§r§c You must provide a valid amount!");
			return;
		}

		$player = $this->plugin->getServer()->getPlayerExact($args[0]);
		$type = strtolower($args[1]);
		$amount = $args[2];

		$this->plugin->getRelicFunctions()->giveRelic($player, $type, $amount);
	}
}
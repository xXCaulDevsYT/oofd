<?php

namespace DuoIncure\Relics;

use pocketmine\event\Listener;
use pocketmine\nbt\tag\StringTag;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use function in_array;
use function rand;

class RelicsListener implements Listener {

	/** @var Main */
	private $plugin;

	/**
	 * RelicsListener constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerInteractEvent $ev
	 */
	public function onInteract(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		if($ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK || $ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
			$item = $ev->getItem();
			$nbt = $item->getNamedTag();
			if($nbt->hasTag(RelicFunctions::RELIC_TAG)){
				$relicType = $nbt->getTagValue(RelicFunctions::RELIC_TAG, StringTag::class);
				switch($relicType){
					case "common":
						$this->plugin->getRelicFunctions()->giveRelicReward($player, $item, "common");
						break;
					case "rare":
						$this->plugin->getRelicFunctions()->giveRelicReward($player, $item, "rare");
						break;
					case "mythic":
						$this->plugin->getRelicFunctions()->giveRelicReward($player, $item, "mythic");
						break;
					case "legendary":
						$this->plugin->getRelicFunctions()->giveRelicReward($player, $item, "legendary");
						break;
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $ev
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onBreak(BlockBreakEvent $ev){
		$player = $ev->getPlayer();
		$config = $this->plugin->getConfig()->getAll();
		$blockID = $ev->getBlock()->getId();
		$configBlocks = $config["block-ids"];
		$configWorlds = $config["worlds"];
		$levelName = $player->getLevel()->getName();
		if(in_array($blockID, $configBlocks) && ($configWorlds[0] == "*" OR in_array($levelName, $configWorlds))){
			$commonChance = $config["common"]["chance"] ?? 10;
			$rareChance = $config["rare"]["chance"] ?? 5;
			$mythicChance = $config["mythic"]["chance"] ?? 3;
			$legendaryChance = $config["legendary"]["chance"] ?? 1;
			//Credit to SOFe and benda95280 for this chance system.
			$chance = rand(1, 200);
			if ($chance <= $commonChance) {
				$this->plugin->getRelicFunctions()->giveRelic($player, "common", 1);
				} else {
				$chance -= $commonChance;
				if ($chance <= $rareChance) {
					$this->plugin->getRelicFunctions()->giveRelic($player, "rare", 1);
				} else {
					$chance -= $rareChance;
					if ($chance <= $mythicChance) {
						$this->plugin->getRelicFunctions()->giveRelic($player, "mythic", 1);
					} else {
						$chance -= $mythicChance;
						if ($chance <= $legendaryChance) {
							$this->plugin->getRelicFunctions()->giveRelic($player, "legendary", 1);
						}
					}
				}
			}
		}
	}
}
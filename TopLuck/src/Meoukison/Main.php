<?php

namespace Meoukison;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\VanillaBlocks;
use Meoukison\commands\Pourcentage;
use Meoukison\commands\TopPourcentage;

class Main extends PluginBase implements Listener {

    private array $stats = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("TopLuck", new Pourcentage($this));
        $this->getServer()->getCommandMap()->register("TopLuck", new TopPourcentage($this));
    }

    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $playerName = $player->getName();

        if(!isset($this->stats[$playerName])){
            $this->stats[$playerName] = ["diamond" => 0, "other" => 0];
        }

        if($block->getTypeId() === VanillaBlocks::DIAMOND_ORE()->getTypeId()){ //Remplacez par le bloc que vous souhaitez
            $this->stats[$playerName]["diamond"]++;
        }
        elseif ($block->getTypeId() === VanillaBlocks::DEEPSLATE_DIAMOND_ORE()->getTypeId()){
            $this->stats[$playerName]["diamond"]++;
        }
        else {
            $this->stats[$playerName]["other"]++;
        }

        $pourcentage = $this->calcul(
            $this->stats[$playerName]["diamond"],
            $this->stats[$playerName]["other"]
        );

        if($pourcentage > 10) //Configurez le pourcentage que vous souhaitez ici
        {
            $this->getServer()->getLogger()->info("Attention : Le joueur {$playerName} a un pourcentage de diamants de " . round($pourcentage, 2) . "% !");
        }
    }

    public function getStats(string $playerName): ?array {
        return $this->stats[$playerName] ?? null;
    }

    public function getAllStats(): array {
        return $this->stats;
    }

    public function calcul(int $diamond, int $other): float {
        $total = $diamond + $other;
        if($total === 0) return 0;
        return ($diamond / $total) * 100;
    }
}
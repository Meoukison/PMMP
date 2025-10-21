<?php

namespace Meoukison\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use Meoukison\Main;

class TopPourcentage extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("toppourcentage", "Affiche le classement des joueurs par pourcentage de diamants");
        $this->setPermission(DefaultPermissions::ROOT_USER);
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        $allStats = $this->plugin->getAllStats();

        if(empty($allStats)){
            $sender->sendMessage("§cAucun joueur n'a encore miné de blocs !");
            return;
        }
        $classement = [];
        foreach($allStats as $playerName => $stats){
            $diamond = $stats["diamond"];
            $other = $stats["other"];
            $pourcentage = $this->plugin->calcul($diamond, $other);

            $classement[] = [
                "name" => $playerName,
                "diamond" => $diamond,
                "percentage" => $pourcentage
            ];
        }
        usort($classement, function($a, $b) {
            return $b["percentage"] <=> $a["percentage"];
        });


        $sender->sendMessage("§6§l===== TOP POURCENTAGE DIAMANTS =====");
        $sender->sendMessage("");

        $limit = min(10, count($classement));
        for($i = 0; $i < $limit; $i++){
            $pos = $i + 1;
            $player = $classement[$i];
            $name = $player["name"];
            $diamond = $player["diamond"];
            $percentage = round($player["percentage"], 2);


            if($pos === 1){
                $color = "§6";
            } elseif($pos === 2){
                $color = "§7";
            } elseif($pos === 3){
                $color = "§c";
            } else {
                $color = "§e";
            }

            $sender->sendMessage("{$color}#{$pos} §b{$name} §f- §e{$percentage}% §f(§a{$diamond} diamants§f)");
        }

        $sender->sendMessage("");
        $sender->sendMessage("§6§l==================================");
    }
}
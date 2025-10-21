<?php

namespace Meoukison\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use Meoukison\Main;

class Pourcentage extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("pourcentage", "Affiche le pourcentage de diamants minés");
        $this->setPermission(DefaultPermissions::ROOT_USER);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {

        if(empty($args)){
            if(!$sender instanceof Player){
                $sender->sendMessage("§cVous devez spécifier un nom de joueur depuis la console !");
                return;
            }
            $targetName = $sender->getName();
        } else {
            $targetName = $args[0];
        }


        $stats = $this->plugin->getStats($targetName);

        if($stats === null){
            $sender->sendMessage("§cLe joueur §e{$targetName}§c n'a pas encore miné de blocs !");
            return;
        }

        $diamond = $stats["diamond"];
        $other = $stats["other"];
        $total = $diamond + $other;
        $pourcentage = $this->plugin->calcul($diamond, $other);

        $sender->sendMessage("§a===== Statistiques de §e{$targetName}§a =====");
        $sender->sendMessage("§bDiamants minés : §e{$diamond}");
        $sender->sendMessage("§bAutres blocs minés : §e{$other}");
        $sender->sendMessage("§bTotal de blocs : §e{$total}");
        $sender->sendMessage("§6Pourcentage de diamants : §e" . round($pourcentage, 2) . "%");
    }
}
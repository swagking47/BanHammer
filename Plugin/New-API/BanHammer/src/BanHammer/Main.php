<?php

namespace BanHammer;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;

class Main extends PluginBase{
    public function onEnable(){
        $this->getLogger()->log("[BanHammer] BanHammer Loaded!");
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "banhammer":
				if($args[0] == "edit"){
				    //TODO BanHammer Editing
				}elseif($args[0] == "get"){
					//TODO Give BanHammer
				}else{
					$sender->sendMessage("Usage: /banhammer <edit|get>");
				}
			break;
		}
	}
    
   /**
    * @param PlayerInteractEvent $event
    *
    * @priority NORMAL
    * @ignoreCanceled false
    */
    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        
    }
    
    public function onDisable(){
        $this->getLogger()->log("[BanHammer] BanHammer Unloaded!");
    }
}
?>

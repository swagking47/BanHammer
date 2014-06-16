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
				    if($args[1] == "type"){
				    	if($args[2] == "banip"){
				    	    
				    	}elseif($args[2] == "ban"){
				    	    
				    	}elseif($args[2] == "kick"){
				    	    
				    	}else{
				    	    $sender->sendMessage("Usage: /banhammer edit <type> <banip|ban|kick>");
				    	}
				    }
				}elseif($args[0] == "get"){
				    if($sender->isOP){ //I'll figure our the real way to do that later
				    	//TODO Give BanHammer
				    	return true;
				    }else{
				    	$sender->sendMessage("[BanHammer] You do not have permission to obtain the BanHammer!");
				    	return false;
				    }
				}elseif($args[0] == "allow"){
					//TODO Add $args[1] to a config
				}else{
				    $sender->sendMessage("Usage: /banhammer <get|edit|allow> [player]");
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
        $target = $event->getPlayer($target); //Is this how we get the target?
    }
    
    public function onDisable(){
        $this->getLogger()->log("[BanHammer] BanHammer Unloaded!");
    }
}
?>

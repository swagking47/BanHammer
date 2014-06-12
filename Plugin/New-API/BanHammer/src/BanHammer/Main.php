<?php

namespace BanHammer;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;

class Main extends PluginBase{
    public function onEnable(){
        $this->getLogger()->log("[BanHammer] BanHammer Loaded!");
    }
    
    public function onCommand(){
        
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

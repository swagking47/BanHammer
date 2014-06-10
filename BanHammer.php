<?php

/*
__PocketMine Plugin__
name=BanHammer
description=The BanHammer Has Spoken!
version=1.7.1
author=Comedyman937
class=BanHammer
apiversion=11,12
*/

/*
NOTE: You will see an E_WARNING ERROR when adding people to the BanHammer whitelist.  This is a bug that I am working on fixing.  The player will still be added to the BanHammer Whitelist correctly!
*/

/*
===============
   Changelog
===============

1.7.1
- Security Improvements
- Added BanHammer Whitelist For Allowing Non-OP Support
- New Config Folder Now Generates ("./plugins/BanHammer/Allowed/")
- Added BanHammerAllow Command
- Added Alias For BanHammerAllow Command

1.6.8
- Security Improvements
- New Config Option For TimerBan Support ("TimerBanTime")
- Better Config Updating With Commands (Hopefully)
- Added BanHammerTime Command
- Added Alias For BanHammerTime Command
- TimerBan Compatibility (Please Report Issues As There May Be Bugs)
- Small Bug Fixes (Most Were Rare)
- Better Code Organization (For Future Updates)

1.4.0
- Added kick command support

1.0.0
- Initial Release

===============
 End Changelog
===============
*/

class BanHammer implements Plugin
{
    private $api;

    public function __construct(ServerAPI $api, $server = false)
    {
        $this->api = $api;
    }

    public function init()
    {
        if(!file_exists($this->api->plugin->configPath($this) . "config.yml"))
        {
            $this->CONFIG = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML, array(
                "BanHammer" => '286',
                "BanType" => 'ban',
                "TimerBanTime" => '6',
            ));
        }

        @mkdir($this->api->plugin->configPath($this) . "Allowed/");

        $this->CONFIG = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . "config.yml");

        $this->api->console->register("banhammer", "Get the BanHammer!", array($this, "CommandHandler"));
        $this->api->console->register("banhammeritem", "Change the BanHammer Item!", array($this, "Item"));
        $this->api->console->register("banhammertype", "Change the BanHammer Type!", array($this, "Type"));
        $this->api->console->register("banhammertime", "Change the BanHammer TimerBan Time!", array($this, "Time"));
        $this->api->console->register("banhammerallow", "Change the BanHammer TimerBan Time!", array($this, "Allow"));

        $this->api->addHandler("player.equipment.change", array($this, "EventHandler"));
        $this->api->addHandler("player.interact", array($this, "EventHandler"));

        $this->api->console->alias("bha","banhammerallow");
        $this->api->console->alias("bhtime","banhammertime");
        $this->api->console->alias("bht","banhammertype");
        $this->api->console->alias("bhi","banhammeritem");
        $this->api->console->alias("bh","banhammer");

        $this->api->ban->cmdWhitelist("banhammer");

        if($this->CONFIG["BanHammer"] <= 0 || $this->CONFIG["BanHammer"] > 500){
                    console("[ERROR] BanHammer Has detected an Invalid item in the Config.yml file!");
                    $this->api->console->run("stop");
                        }elseif(!($this->api->block->getItem($this->CONFIG["BanHammer"]) instanceof Item)){
                            console("[ERROR] BanHammer Has detected an Invalid item in the Config.yml file!");
                            $this->api->console->run("stop");
                                }else{

                                    console("[INFO] BanHammer Loaded!");
        }
    }

    public function CommandHandler($cmd, $params, $issuer, $alias)
    {
        switch(strtolower($cmd))
        {
            case "banhammer":
                if(!($issuer instanceof Player)){
                     return "[ERROR] [BanHammer] Please run this command in-game!";
                         }elseif($this->api->ban->isOP($issuer) || file_exists($this->api->plugin->configPath($this) . "Allowed/" . $issuer . ".yml")){

                             if($this->CONFIG["BanHammer"] <= 0 || $this->CONFIG["BanHammer"] > 500){
                                 return "[BanHammer] An invalid item is set in the config.yml\nBanHammer will not work until this has been fixed!";
                                     }elseif(!($this->api->block->getItem($this->CONFIG["BanHammer"]) instanceof Item)){
                                         return "[BanHammer] An invalid item is set in the config.yml\nBanHammer will not work until this has been fixed!";
                                             }else{
                                                 $this->api->console->run("give " . $issuer . " " . $this->CONFIG["BanHammer"] . " 1", "console", false);

                                                 return "[BanHammer] The BanHammer has been added to your inventory!\nInteract with a player to ban/kick them!";
                        }
                                                     }else{
                                                         return "[BanHammer] You have insufficient permissions!";
                                                     }
                break;
          }
     }

     public function Item($cmd, $params, $issuer, $alias)
     {
        switch(strtolower($cmd))
        {
            case "banhammeritem":
                if(!($issuer instanceof Player)){
                    return "[ERROR] [BanHammer] Please run this command in-game!";
                        }elseif($this->api->ban->isOP($issuer)){
                            if(count($params) > 2){
                                return "Usage: /banhammeritem <ITEM ID>";
                                    }if($params[0] <= 0 || $params[0] > 500){
                                        return "[BanHammer] Invalid Item ID!";
                                            }if(!($this->api->block->getItem($params[0]) instanceof Item)){
                                                return "[BanHammer] Invalid Item ID!";
                                                    }if(isset($this->CONFIG["BanHammer"])){
                                                        $this->CONFIG["BanHammer"] = $params[0];
                                            }
                                                            $this->api->plugin->writeYAML($this->api->plugin->configPath($this) . "config.yml", $this->CONFIG);
                                                            $this->CONFIG = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . "config.yml");

                                                            return "[BanHammer] The BanHammer Item has been updated!";

                                                                }else{

                                                                    return "[BanHammer] You have insufficient permissions!";

                         }

                break;
          }
     }

     public function Type($cmd, $params, $issuer, $alias)
     {
        switch(strtolower($cmd))
        {
            case "banhammertype":
                if(!($issuer instanceof Player)){
                    return "[ERROR] [BanHammer] Please run this command in-game!";
                        }elseif($this->api->ban->isOP($issuer)){
                            if(count($params) > 2){
                                return "Usage: /banhammertype <BAN|BANIP|KICK|TIMERBAN|TIMERBANIP>";
                                    }if($params[0] == "ban" || $params[0] == "banip" || $params[0] == "kick" || $params[0] == "timerban" || $params[0] == "timerbanip"){
                                        if(isset($this->CONFIG["BanType"])){
                                            $this->CONFIG["BanType"] = $params[0];
                                        }
                                                }else{
                                                    return "Usage: /banhammertype <BAN|BANIP|KICK|TIMERBAN|TIMERBANIP>";
                                                }
                                                        $this->api->plugin->writeYAML($this->api->plugin->configPath($this) . "config.yml", $this->CONFIG);
                                                        $this->CONFIG = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . "config.yml");

                                                        return "[BanHammer] The BanHammer Type has been updated!";

                                                            }else{

                                                                return "[BanHammer] You have insufficient permissions!";

                         }

                break;
        }
    }

     public function Time($cmd, $params, $issuer, $alias)
     {
        switch(strtolower($cmd))
        {
            case "banhammertime":
                if(!($issuer instanceof Player)){
                    return "[ERROR] [BanHammer] Please run this command in-game!";
                        }elseif($this->api->ban->isOP($issuer)){
                            if(count($params) > 2){
                                return "Usage: /banhammertime <HOURS>";
                                    }if(isset($this->CONFIG["TimerBanTime"])){
                                        if($params[0] >= 0.01){
                                            $this->CONFIG["TimerBanTime"] = $params[0];
                                                }else{
                                                    return "[BanHammer] The TimerBan Time MUST be greater than 0.01 Hours!";
                                                }
                                                        }else{
                                                            return "Usage: /banhammertime <HOURS>";
                                             }
                                                                $this->api->plugin->writeYAML($this->api->plugin->configPath($this) . "config.yml", $this->CONFIG);
                                                                $this->CONFIG = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . "config.yml");

                                                                return "[BanHammer] The BanHammer TimerBan Time has been updated!\nNOTE: This will only be used if the BanType is set to \"timerban\" or \"timerbanip\" in the Config.yml file!";

                                                                    }else{

                                                                        return "[BanHammer] You have insufficient permissions!";

                         }

                break;
        }
    }

     public function Allow($cmd, $params, $issuer, $alias)
     {
        switch(strtolower($cmd))
        {
            case "banhammerallow":
                if(!($issuer instanceof Player)){
                    if(count($params) > 2){
                        return "Usage: /banhammerallow <USERNAME>";
                    }
                            if(isset($params[0])){
                                if(!(file_exists($this->api->plugin->configPath($this) . "Allowed/" . $params[0] . ".yml"))){
                                    console("[BanHammer] User: " . $params[0] . " Is now permitted to use the BanHammer!");
                                    return new Config($this->api->plugin->configPath($this) . "Allowed/" . $params[0] . ".yml", CONFIG_YAML, array(
                                        "isAllowed" => true,
                                    ));
                                        }else{
                                            return "[BanHammer] User: " . $params[0] . " Is already allowed to use the BanHammer!";
                                        }
                                                }else{
                                                    return "Usage: /banhammerallow <USERNAME>";
                                                }$this->
                                                        }elseif($this->api->ban->isOP($issuer)){
                                                            if(count($params) > 2){
                                                                return "Usage: /banhammerallow <USERNAME>";
                                                            }
                                                                    if(isset($params[0])){
                                                                        if(!(file_exists($this->api->plugin->configPath($this) . "Allowed/" . $params[0] . ".yml"))){
                                                                            return "[BanHammer] User: " . $params[0] . " Is now permitted to use the BanHammer!";
                                                                            return new Config($this->api->plugin->configPath($this) . "Allowed/" . $params[0] . ".yml", CONFIG_YAML, array(
                                                                                "isAllowed" => true,
                                          ));
                                                  }else{
                                                      return "[BanHammer] User: " . $params[0] . " Is already allowed to use the BanHammer!";
                                                  }
                                                          }else{
                                                              return "Usage: /banhammerallow <USERNAME>";
                                                          }
                                                                  }else{

                                                                      return "[BanHammer] You have insufficient permissions!";

                         }

                break;
        }
    }

    public function EventHandler($data, $event)
    {
        switch($event)
        {
            case "player.equipment.change":

                if($data["item"]->getID() == $this->CONFIG["BanHammer"]){
                    $this->TEMP[$data["player"]->username] = true;
                        }else{
                            $this->TEMP[$data["player"]->username] = false;
                        }
                break;

            case "player.interact":

                $this->ALLOW = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . $data["entity"]);

                if($this->api->ban->isOP($data["entity"]->player->username) || file_exists($this->api->plugin->configPath($this) . "Allowed/" . $data["entity"]->player->username . ".yml") || $this->ALLOW["isAllowed"] == true){
                    if(isset($this->TEMP[$data["entity"]->player->username]) and $this->TEMP[$data["entity"]->player->username] == true){                
                        if($this->CONFIG["BanType"] == "ban" || $this->CONFIG["BanType"] == "banip"){
                            $this->api->console->run($this->CONFIG["BanType"] . " add " . $data["targetentity"]->player->username);
                      
                            $this->api->chat->broadcast("[BanHammer] The BanHammer has spoken against " . $data["targetentity"]->player->username . "!");
                                }elseif($this->CONFIG["BanType"] == "timerban"){

                                    $this->api->console->run($this->CONFIG["BanType"] . " add " . $data["targetentity"]->player->username . " " . $this->CONFIG["TimerBanTime"]);

                                    $this->api->chat->broadcast("[BanHammer] The BanHammer has spoken against " . $data["targetentity"]->player->username . "!");
                                        }elseif($this->CONFIG["BanType"] == "timerbanip"){

                                            $this->api->console->run($this->CONFIG["BanType"] . " add " . $data["targetentity"]->player->ip . " " . $this->CONFIG["TimerBanTime"]);

                                            $this->api->chat->broadcast("[BanHammer] The BanHammer has spoken against " . $data["targetentity"]->player->username . "!");
                                                }elseif($this->CONFIG["BanType"] == "kick"){

                                                    $this->api->console->run($this->CONFIG["BanType"] . " " . $data["targetentity"]->player->username . " The BanHammer Has Spoken!");

                                                    $this->api->chat->broadcast("[BanHammer] The BanHammer has spoken against " . $data["targetentity"]->player->username . "!");
                                                        }else{
                                                            $this->api->chat->broadcast("[BanHammer] The BanHammer could not speak as it does not know how to use its set BanType!");
                                                        }
                        break;
                    }
                }
                break;
        }
    }

    public function __destruct()
    {
        console("[INFO] BanHammer Unloaded!");
    }
}

?>

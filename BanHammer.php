<?php

/*
__PocketMine Plugin__
name=BanHammer
description=The BanHammer Has Spoken!
version=1.0.0
author=Comedyman937
class=BanHammer
apiversion=11,12,13
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
                "BanHammer" => 286,
                "BanType" => add,
            ));
        }

        $this->CONFIG = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . "config.yml");

        $this->api->console->register("banhammer", "Get the BanHammer!", array($this, "CommandHandler"));
        $this->api->console->register("banhammeritem", "Change the BanHammer!", array($this, "CommandHandler"));

        $this->api->addHandler("player.equipment.change", array($this, "EventHandler"));
        $this->api->addHandler("player.interact", array($this, "EventHandler"));

        $this->api->console->alias("bhi","banhammeritem");
        $this->api->console->alias("bh","banhammer");

        console("[INFO] BanHammer Loaded!");
    }

    public function CommandHandler($cmd, $params, $issuer, $alias)
    {
        switch(strtolower($cmd))
        {
            case "banhammer":

                $this->api->console->run("give " . $issuer->username . " " . $this->CONFIG["BanHammer"] . " 1", "console", false);

                return "The BanHammer has been added to your inventory!\nTap on a player to ban them!";

            case "banhammeritem":

                if(count($params) > 2)
                {
                    return "Usage: /banhammeritem <ID>";
                }
                if($params[0] <= 0 || $params[0] > 500)
                {
                    return "Error: Invalid Item ID!";
                }
                if(!($this->api->block->getItem($params[0]) instanceof Item))
                {
                    return "Error: Invalid Item!";
                }
                if(isset($this->CONFIG["BanHammer"]))
                {
                    $this->CONFIG["BanHammer"] = $params[0];
                }
                $this->api->plugin->writeYAML($this->api->plugin->configPath($this) . "config.yml", $this->CONFIG);

                return "The BanHammer Item has been changed!";
        }

            case "banhammertype":

                if(count($params) > 2)
                {
                    return "Usage: /banhammertype <ADD|IP>";
                }
                if($params[0] = add || $params[0] = ip)
                {
                                    if(isset($this->CONFIG["BanType"]))
                                    {
                                         $this->CONFIG["BanType"] = $params[0];
                                    }
                }
                else
                {
                    return "Usage: /banhammertype <ADD|IP>";
                }
                $this->api->plugin->writeYAML($this->api->plugin->configPath($this) . "config.yml", $this->CONFIG);

                return "The BanHammer Type has been changed!";
        }
    }

    public function EventHandler($data, $event)
    {
        switch($event)
        {
            case "player.equipment.change":

                if($data["item"]->getID() == $this->CONFIG["BanHammer"])
                {
                    $this->TEMP[$data["player"]->username] = true;
                }
                else
                {
                    $this->TEMP[$data["player"]->username] = false;
                }

                break;

            case "player.interact":

                if($this->api->ban->isOP($data["entity"]->player->username))
                {
                    if(isset($this->TEMP[$data["entity"]->player->username]) and $this->TEMP[$data["entity"]->player->username] == true)
                    {                
                        $this->api->console->run("ban " . $this->CONFIG["BanType"] . " " . $data["targetentity"]->player->username);
                      
                        $this->api->chat->broadcast("[BanHammer] The BanHammer has spoken against " . $data["targetentity"]->player->username . "!");
                        
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

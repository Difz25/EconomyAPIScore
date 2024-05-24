<?php

namespace Difz25\EconomyAPIScore;


use {
    Difz25\EconomyAPIScore\Listener\TagResolveListener,
    pocketmine\plugin\PluginBase,
    Ifera\ScoreHud\scoreboard\ScoreTag,
    pocketmine\event\Listener,
    pocketmine\scheduler\ClosureTask,
    Ifera\ScoreHud\event\PlayerTagsUpdateEvent,
    Ifera\ScoreHud\ScoreHud,
    pocketmine\plugin\Plugin
};

/**
 * @property Plugin|null $eco
 * @property $EconomyAPI
 */
class Main extends PluginBase implements Listener
{

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if ($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") == null) {
            $this->getLogger()->alert("EconomyAPI not found!");
        }
        if (class_exists(ScoreHud::class)) {
            $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(
                closure: function (): void {
                    foreach ($this->getServer()->getOnlinePlayers() as $player) {
                        if (!$player->isOnline()) {
                            continue;
                        }

                        (new PlayerTagsUpdateEvent($player, [
                            new ScoreTag("moneys.count", $this->Format($this->EconomyAPI::getInstance()->myMoney($player))),
                        ]))->call();
                    }
                }
            ), 1);
            $this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);
        }
    }

        public function Format($num): string
        {
            if(!is_numeric($num)) return  'IDR 0';
        $format = number_format((int) $num, 0, ',', '.');
        return 'IDR' . $format;
    }
}
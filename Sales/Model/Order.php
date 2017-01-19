<?php
namespace Df\Sales\Model;
/**
 * 2016-03-26
 * @method setIsInProcess(bool $value)
 *
 * 2017-01-19
 * В настоящее время я этот флаг не использую
 * Дважды пытался использовать его в методе
 * @see \Df\Sales\Plugin\Model\ResourceModel\Order\Handler\State::aroundCheck(),
 * однако оба раза это решение не оказывалось лучшим, и я от него отказывался.
 * Однако решил не удалять это объявление отсюда, и оставить и его, и комментарий на будущее:
 * вдруг пригодится.
 * Обратите внимание, что этот флаг не сохраняется в базе данных.
 * @method setForcedCanCreditmemo(bool $value)
 */
class Order {}
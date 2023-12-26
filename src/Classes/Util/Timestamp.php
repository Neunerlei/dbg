<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


class Timestamp
{
    /**
     * @var \DateTime
     */
    protected $dateTime;
    
    public function __construct(\DateTime $dateTime) {
        $this->dateTime = $dateTime;
    }
    
    public function getDateTime(): \DateTime{
        return $this->dateTime;
    }
    
    public function __toString(): string {
        return '[' . $this->dateTime->format('Y-m-d H:i:s e') . ']';
    }
}
<?php


namespace Domain;


class TabClosed
{

    /**
     * @var string
     */
    private $tabId;
    /**
     * @var float
     */
    private $amountPaid;
    /**
     * @var float
     */
    private $orderValue;
    /**
     * @var float
     */
    private $tipValue;

    public function __construct(
        string $tabId,
        float $amountPaid,
        float $orderValue,
        float $tipValue
    )
    {
        $this->tabId = $tabId;
        $this->amountPaid = $amountPaid;
        $this->orderValue = $orderValue;
        $this->tipValue = $tipValue;
    }

    /**
     * @return string
     */
    public function getTabId(): string
    {
        return $this->tabId;
    }

    /**
     * @return float
     */
    public function getAmountPaid(): float
    {
        return $this->amountPaid;
    }

    /**
     * @return float
     */
    public function getOrderValue(): float
    {
        return $this->orderValue;
    }

    /**
     * @return float
     */
    public function getTipValue(): float
    {
        return $this->tipValue;
    }


}
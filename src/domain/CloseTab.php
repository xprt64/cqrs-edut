<?php


namespace Domain;


class CloseTab
{

    /**
     * @var string
     */
    private $tabId;
    /**
     * @var float
     */
    private $amountPaid;

    public function __construct(
        string $tabId,
        float $amountPaid
    )
    {
        $this->tabId = $tabId;
        $this->amountPaid = $amountPaid;
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


}
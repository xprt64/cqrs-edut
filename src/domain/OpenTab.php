<?php


namespace Domain;


class OpenTab
{
    /**
     * @var string
     */
    private $tabId;
    /**
     * @var int
     */
    private $tableNumber;
    /**
     * @var string
     */
    private $waiter;

    public function __construct(
        string $guid,
        int $tableNumber,
        string $waiter
    )
    {
        $this->tabId = $guid;
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
    }

    /**
     * @return string
     */
    public function getTabId(): string
    {
        return $this->tabId;
    }

    /**
     * @return int
     */
    public function getTableNumber(): int
    {
        return $this->tableNumber;
    }

    /**
     * @return string
     */
    public function getWaiter(): string
    {
        return $this->waiter;
    }

}
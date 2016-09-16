<?php


namespace Domain;


class DrinksOrdered
{

    /**
     * @var string
     */
    private $tabId;

    /**
     * @var OrderedItem[]
     */
    private $items;

    public function __construct(
        string $tabId,
        array $items
    )
    {
        $this->tabId = $tabId;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getTabId(): string
    {
        return $this->tabId;
    }

    /**
     * @return OrderedItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
<?php


namespace Domain;


class OrderedItem
{

    /**
     * @var int
     */
    private $menuNumber;
    /**
     * @var string
     */
    private $description;
    /**
     * @var bool
     */
    private $isDrink;
    /**
     * @var float
     */
    private $price;

    public function __construct(
        int $menuNumber,
        string $description,
        bool $isDrink,
        float $price
    )
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
        $this->isDrink = $isDrink;
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getMenuNumber(): int
    {
        return $this->menuNumber;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function isDrink(): bool
    {
        return $this->isDrink;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    public function equals(self $item)
    {
        return $this->description == $item->description &&
        $this->isDrink == $this->isDrink &&
        $this->price == $item->price &&
        $this->menuNumber == $item->menuNumber;
    }

}
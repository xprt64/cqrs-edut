<?php


namespace Domain;


class Aggregate
{
    private $open = false;

    /** @var OrderedItem[] */
    private $outstandingDrinks = [];

    /** @var OrderedItem[] */
    private $outstandingFood = [];

    /** @var OrderedItem[] */
    private $servedDrinks = [];

    /** @var OrderedItem[] */
    private $preparedFood = [];

    private $servedItemsCost = 0;

    public function handleOpenTab(OpenTab $command)
    {
        yield new TabOpened($command->getTabId(), $command->getTableNumber(), $command->getWaiter());
    }

    public function handlePlaceOrder(PlaceOrder $command)
    {
        $drinksOrdered = [];
        $foodOrdered = [];

        if (!$this->open) {
            throw new TabNotOpenException();
        }

        foreach ($command->getItems() as $orderedItem) {
            if ($orderedItem->isDrink()) {
                $drinksOrdered[] = $orderedItem;
            } else {
                $foodOrdered[] = $orderedItem;
            }
        }

        if ($drinksOrdered) {
            yield new DrinksOrdered($command->getTabId(), $drinksOrdered);
        }

        if ($foodOrdered) {
            yield new FoodOrdered($command->getTabId(), $foodOrdered);
        }
    }

    public function handleMarkDrinksServed(MarkDrinksServed $command)
    {
        foreach ($command->getItems() as $servedItem) {
            $found = $this->findOrderItem($this->outstandingDrinks, $servedItem);

            if (false === $found) {
                throw new DrinksNotOutstandingException(sprintf("%s item was not ordered", $servedItem->getDescription()));
            }
        }

        yield new DrinksServed($command->getTabId(), $command->getItems());
    }

    public function handleMarkFoodPrepared(MarkFoodPrepared $command)
    {
        foreach ($command->getItems() as $preparedItem) {
            $found = $this->findOrderItem($this->outstandingFood, $preparedItem);

            if (false === $found) {
                throw new DrinksNotOutstandingException(sprintf("%s item was not ordered", $preparedItem->getDescription()));
            }
        }

        yield new FoodPrepared($command->getTabId(), $command->getItems());
    }

    public function handleMarkFoodServed(MarkFoodServed $command)
    {
        foreach ($command->getItems() as $servedItem) {
            $found = $this->findOrderItem($this->preparedFood, $servedItem);

            if (false === $found) {
                throw new FoodNotPrepared(sprintf("%s item was not prepared", $servedItem->getDescription()));
            }
        }

        yield new FoodServed($command->getTabId(), $command->getItems());
    }

    public function handleCloseTab(CloseTab $command)
    {
        if (!$this->open) {
            throw new TabNotOpenException();
        }

        if ($this->servedItemsCost > $command->getAmountPaid()) {
            throw new MustPayEnoughException();
        }

        yield new TabClosed(
            $command->getTabId(),
            $command->getAmountPaid(),
            $this->servedItemsCost,
            $command->getAmountPaid() - $this->servedItemsCost);
    }

    private function findOrderItem(array $haystack, OrderedItem $needle)
    {
        return (new \Cqrs\SearchItemInArray)->findOrderItemInArray($haystack, $needle);
    }

    public function applyTabOpened(TabOpened $event)
    {
        $this->open = true;
    }

    public function applyDrinksOrdered(DrinksOrdered $event)
    {
        $this->outstandingDrinks = $event->getItems();
    }

    public function applyFoodOrdered(FoodOrdered $event)
    {
        $this->outstandingFood = $event->getItems();
    }

    public function applyDrinksServed(DrinksServed $event)
    {
        foreach ($event->getItems() as $servedItem) {
            $found = $this->findOrderItem($this->outstandingDrinks, $servedItem);

            if (false === $found) {
                continue;
            }

            $this->servedDrinks[] = $servedItem;

            unset($this->outstandingDrinks[$found]);

            $this->servedItemsCost += $servedItem->getPrice();
        }
    }

    public function applyFoodPrepared(FoodPrepared $event)
    {
        foreach ($event->getItems() as $preparedItem) {
            $found = $this->findOrderItem($this->outstandingFood, $preparedItem);

            if (false === $found) {
                continue;
            }

            $this->preparedFood[] = $preparedItem;

            unset($this->outstandingFood[$found]);
        }
    }

    public function applyFoodServed(DrinksServed $event)
    {
        foreach ($event->getItems() as $servedItem) {
            $found = $this->findOrderItem($this->preparedFood, $servedItem);

            if (false === $found) {
                continue;
            }

            unset($this->preparedFood[$found]);

            $this->servedItemsCost += $servedItem->getPrice();
        }
    }
}
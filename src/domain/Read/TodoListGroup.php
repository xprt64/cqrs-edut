<?php
/**
 * @copyright  Copyright (c) Constantin Galbenu xprt64@gmail.com
 * All rights reserved.
 */

namespace Domain\Read;


use Domain\OrderedItem;

class TodoListGroup
{

    /**
     * @var OrderedItem[]
     */
    private $items;

    /**
     * @param OrderedItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function findItem(OrderedItem $item)
    {
        return (new \Cqrs\SearchItemInArray())->findOrderItemInArray($this->items, $item);
    }

    public function withItemRemoved(OrderedItem $item)
    {
        $items = $this->items;

        $found = $this->findItem($item);
        if (false !== $found) {
            unset($items[$found]);
        }

        return new self($items);
    }

    public function equals(self $operand)
    {
        if (count($this->items) != count($operand->items)) {
            return false;
        }

        foreach ($operand->items as $item) {
            if (false === $this->findItem($item)) {
                return false;
            }
        }

        foreach ($this->items as $item) {
            if (false === $operand->findItem($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return \Domain\OrderedItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function countItems()
    {
        return count($this->items);
    }
}
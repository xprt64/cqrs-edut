<?php
/**
 * @copyright  Copyright (c) Constantin Galbenu xprt64@gmail.com
 * All rights reserved.
 */

namespace Domain\Read;


class ChefTodoList
{
    /** @var TodoListGroup[] */
    private $todoList = [];

    /**
     * @return TodoListGroup[]
     */
    public function getTodoList()
    {
        return array_values($this->todoList);
    }

    public function handleFoodOrdered(\Domain\FoodOrdered $event)
    {
        $group = new TodoListGroup($event->getItems());

        $this->todoList[$event->getTabId()] = $group;
    }

    public function handleFoodServed(\Domain\FoodServed $event)
    {
        $group = $this->todoList[$event->getTabId()];

        foreach ($event->getItems() as $item) {
            if (false !== $group->findItem($item)) {
                $group = $group->withItemRemoved($item);
            }
        }

        if ($group->countItems() == 0) {
            unset($this->todoList[$event->getTabId()]);
        } else {
            $this->todoList[$event->getTabId()] = $group;
        }
    }
}
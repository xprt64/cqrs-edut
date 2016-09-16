<?php
/**
 * @copyright  Copyright (c) Constantin Galbenu xprt64@gmail.com
 * All rights reserved.
 */

namespace tests;


use Domain\Read\TodoListGroup;

class Read1Test extends \Cqrs\BddReadModelTestHelper
{
    private $tabId;
    private $tableNumber;
    private $waiter;
    private $testDrink1;
    private $testDrink2;
    private $testFood1;
    private $testFood2;

    /** @var  \Domain\Read\ChefTodoList */
    private $readModel;

    public function setUp()
    {
        parent::setUp();

        $this->tabId = new \Cqrs\Guid();
        $this->tableNumber = 42;
        $this->waiter = 'Derek';

        $this->testDrink1 = new \Domain\OrderedItem(1, 'test drink 1', true, 100);
        $this->testDrink2 = new \Domain\OrderedItem(2, 'test drink 2', true, 200);
        $this->testFood1 = new \Domain\OrderedItem(3, 'test food 1', false, 10);
        $this->testFood2 = new \Domain\OrderedItem(4, 'test food 2', false, 20);

        $this->readModel = new \Domain\Read\ChefTodoList();

        $this->subscribe($this->readModel);
    }

    public function testHandleFoodOrdered()
    {
        $this->given(
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $expectedGroup = new TodoListGroup([$this->testFood1, $this->testFood2]);


        $this->assertCount(1, $this->readModel->getTodoList());

        $actualGroups = $this->readModel->getTodoList();
        $actualGroup = reset($actualGroups);

        $this->assertTrue($expectedGroup->equals($actualGroup));
    }

    public function testHandleFoodServed()
    {
        $this->given(
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            ),
            new \Domain\FoodServed(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->assertCount(0, $this->readModel->getTodoList(), 'all food should be served');
    }

    public function testHandleSomeFoodServed()
    {
        $this->given(
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            ),
            new \Domain\FoodServed(
                $this->tabId,
                [$this->testFood2]
            )
        );

        $this->assertEqualObjectsArray([new TodoListGroup([$this->testFood1])], $this->readModel->getTodoList(), 'some food should still be unserved');
    }

}
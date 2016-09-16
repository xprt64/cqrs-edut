<?php


namespace tests;


use Domain\FoodNotPrepared;

class Command1AggregateTest extends \Cqrs\BddAggregateTestHelper
{
    private $tabId;
    private $tableNumber;
    private $waiter;
    private $testDrink1;
    private $testDrink2;
    private $testFood1;
    private $testFood2;

    protected function setUp()
    {
        $this->tabId = new \Cqrs\Guid();
        $this->tableNumber = 42;
        $this->waiter = 'Derek';

        $this->testDrink1 = new \Domain\OrderedItem(1, 'test drink 1', true, 100);
        $this->testDrink2 = new \Domain\OrderedItem(2, 'test drink 2', true, 200);
        $this->testFood1 = new \Domain\OrderedItem(3, 'test food 1', false, 10);
        $this->testFood2 = new \Domain\OrderedItem(4, 'test food 2', false, 20);

        $aggregate = new \Domain\Aggregate();

        $this->onAggregate($aggregate);

        parent::setUp();
    }

    public function testShouldDispatchCommandAndGenerateEvents()
    {
        $this->given();

        $this->when(new \Domain\OpenTab(
            $this->tabId,
            $this->tableNumber,
            $this->waiter));

        $this->then(new \Domain\TabOpened(
            $this->tabId,
            $this->tableNumber,
            $this->waiter
        ));
    }

    public function testShouldFailIfTabNotOpen()
    {
        $this->given();

        $this->when(
            new \Domain\PlaceOrder(
                $this->tabId,
                [$this->testDrink1]
            )
        );

        $this->thenShouldFailWith(\Domain\TabNotOpenException::class);
    }

    public function testCanPlaceDrinksOrder()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter)
        );

        $this->when(
            new \Domain\PlaceOrder(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );

        $this->then(
            new \Domain\DrinksOrdered(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );
    }

    public function testCanPlaceFoodOrder()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter)
        );

        $this->when(
            new \Domain\PlaceOrder(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->then(
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );
    }

    public function testCanPlaceFoodAndDrinksOrder()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter)
        );

        $this->when(
            new \Domain\PlaceOrder(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2, $this->testFood1, $this->testFood2]
            )
        );

        $this->then(
            new \Domain\DrinksOrdered(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            ),
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );
    }

    public function testOrderedDrinksWhereServed()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter),
            new \Domain\DrinksOrdered(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );

        $this->when(
            new \Domain\MarkDrinksServed(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );

        $this->then(
            new \Domain\DrinksServed(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );
    }

    public function testOrderedFoodIsPrepared()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter
            ),
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->when(
            new \Domain\MarkFoodPrepared(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->then(
            new \Domain\FoodPrepared(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );
    }

    public function testPreparedFoodIsServed()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter
            ),
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            ),
            new \Domain\FoodPrepared(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->when(
            new \Domain\MarkFoodServed(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->then(
            new \Domain\FoodServed(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );
    }

    public function testUnPreparedFoodShouldntBeServed()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter
            ),
            new \Domain\FoodOrdered(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->when(
            new \Domain\MarkFoodServed(
                $this->tabId,
                [$this->testFood1, $this->testFood2]
            )
        );

        $this->thenShouldFailWith(FoodNotPrepared::class);
    }

    public function testNotOrderedDrinksCanNotBeServed()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter)
        );

        $this->when(
            new \Domain\MarkDrinksServed(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );

        $this->thenShouldFailWith(\Domain\DrinksNotOutstandingException::class);
    }

    public function testNotOrderedDrinksCanNotBeServed2()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter),
            new \Domain\DrinksOrdered(
                $this->tabId,
                [$this->testDrink1]
            )
        );

        $this->when(
            new \Domain\MarkDrinksServed(
                $this->tabId,
                [$this->testDrink2]
            )
        );

        $this->thenShouldFailWith(\Domain\DrinksNotOutstandingException::class);
    }

    public function testAlreadyServedDrinksCanNotBeServedAgain()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter),
            new \Domain\DrinksOrdered(
                $this->tabId,
                [$this->testDrink1]
            ),
            new \Domain\DrinksServed(
                $this->tabId,
                [$this->testDrink1]
            )
        );

        $this->when(
            new \Domain\MarkDrinksServed(
                $this->tabId,
                [$this->testDrink1]
            )
        );

        $this->thenShouldFailWith(\Domain\DrinksNotOutstandingException::class);
    }

    public function testCanCloseTabWithTip()
    {
        $this->given(
            new \Domain\TabOpened(
                $this->tabId,
                $this->tableNumber,
                $this->waiter),
            new \Domain\DrinksOrdered(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            ),
            new \Domain\DrinksServed(
                $this->tabId,
                [$this->testDrink1, $this->testDrink2]
            )
        );

        $this->when(
            new \Domain\CloseTab(
                $this->tabId,
                350
            )
        );

        $this->then(
            new \Domain\TabClosed(
                $this->tabId,
                350,
                300,
                50
            )
        );
    }
}

<?php
/**
 * @copyright  Copyright (c) Constantin Galbenu xprt64@gmail.com
 * All rights reserved.
 */

namespace Cqrs;


class SearchItemInArray
{
    public function findOrderItemInArray(array $haystack, \Domain\OrderedItem $needle)
    {
        foreach ($haystack as $i => $item) {
            if ($needle->equals($item)) {
                return $i;
            }
        }

        return false;
    }
}
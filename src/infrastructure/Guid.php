<?php


namespace Cqrs;


class Guid
{
    /**
     * @var string
     */
    private $guid;

    public function __construct(string $guid = null)
    {
        if (!$guid) {
            $guid = $this->newGuid();
        }

        $this->guid = $guid;
    }

    public function __toString()
    {
        return $this->guid;
    }

    public function newGuid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535));
    }
}
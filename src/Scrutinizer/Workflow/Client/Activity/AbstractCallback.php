<?php

namespace Scrutinizer\Workflow\Client\Activity;

use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class AbstractCallback implements CallbackInterface
{
    private $serializer;

    public function __construct(Serializer $serializer = null)
    {
        $this->serializer = $serializer ?: SerializerBuilder::create()->build();
    }

    public function initialize()
    {
    }

    public function cleanUp()
    {
    }

    protected function exec($cmd, $cwd = null)
    {
        $proc = new Process($cmd, $cwd);
        if (0 !== $proc->run()) {
            throw new ProcessFailedException($proc);
        }

        return $proc;
    }

    protected function serialize($data, array $groups = array())
    {
        $this->serializer->setExclusionStrategy(empty($groups) ? null : new GroupsExclusionStrategy($groups));

        return $this->serializer->serialize($data, 'json');
    }

    protected function deserialize($data, $type, array $groups = array())
    {
        $this->serializer->setExclusionStrategy(empty($groups) ? null : new GroupsExclusionStrategy($groups));

        return $this->serializer->deserialize($data, $type, 'json');
    }
}
<?php

declare(strict_types=1);

namespace Training\DependencyExample\Model;

class Main
{
    private array $data;
    private Injectable $injectable;
    private NonInjectableFactory $nonInjectableFactory;

    public function __construct(
        Injectable           $injectable,
        NonInjectableFactory $nonInjectableFactory,
        array                $data = []
    ) {
        $this->data = $data;
        $this->injectable = $injectable;
        $this->nonInjectableFactory = $nonInjectableFactory;
    }

    public function getId():string
    {
        return $this->data['id'];
    }

    public function getInjectable(): Injectable
    {
        return $this->injectable;
    }

    public function getNonInjectable()
    {
        return $this->nonInjectableFactory->create();
    }
}

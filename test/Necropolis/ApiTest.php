<?php

namespace Necropolis\Test;

class ApiTest extends TestCase
{
    public function testDeleteItem()
    {
        $item = $this->api()->create('items')->getContent();
        $this->api()->delete('items', $item->id());

        $necropolisResource = $this->api()->read('necropolis_resources', $item->id())->getContent();

        $this->assertEquals($item->id(), $necropolisResource->id());
        $this->assertNotNull($necropolisResource->deleted());
    }
}

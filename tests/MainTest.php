<?php

namespace gamringer\JSONPatch\Test;

use \gamringer\JSONPatch\Patch;
use \gamringer\JSONPatch\Operation;
use \gamringer\JSONPatch;
use \gamringer\JSONPointer\Pointer;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that an empty patch is succesfully rendered to string
     */
    public function testEmptyPatch()
    {
        $patch = new Patch();

        $this->assertEquals('[]', (string)$patch);
    }

    /**
     * Tests that an operation can be successfully be added
     */
    public function testAddOperation()
    {
        $patch = new Patch();
        $op = new Resources\MockAtomic();
        $patch->addOperation($op);

        $this->assertNotEmpty(json_decode($patch));
    }

    /**
     * Tests that a patch can be created from JSON
     */
    public function testFromJSON()
    {
        $patch = new Patch();
        $patch->addOperation(new Operation\Add('/foo', 'bar'));
        $patch->addOperation(new Operation\Copy('/foo', '/bar'));
        $patch->addOperation(new Operation\Move('/foo', '/bar'));
        $patch->addOperation(new Operation\Remove('/foo'));
        $patch->addOperation(new Operation\Replace('/foo', 'bar'));
        $patch->addOperation(new Operation\Test('/foo', 'bar'));

        $this->assertEquals($patch, Patch::fromJSON($patch));
    }

    /**
     * Tests that a patch applies properly
     */
    public function testApplyPatch()
    {
        $patch = new Patch();
        $op = new Resources\MockAtomic(function(Pointer $pointer){
            $value = 'baz';
            $pointer->set('/foo', $value);
        });
        $patch->addOperation($op);

        $target = ['foo'=>'bar'];
        $patch->apply($target);
        $this->assertEquals($target['foo'], 'baz');
    }

    /**
     * Tests that a patch reverts properly
     */
    public function testRevertPatch()
    {
        $patch = new Patch();
        $op = new Resources\MockAtomic(function(Pointer $pointer){
            $value = 'baz';
            $pointer->set('/foo', $value);
        }, function(Pointer $pointer){
            $value = 'reverted';
            $pointer->set('/foo', $value);
        });
        $patch->addOperation($op);
        $op = new Resources\MockAtomic(function(Pointer $pointer){
            throw new Operation\Exception('Forced Reversion');
        });
        $patch->addOperation($op);

        $target = ['foo'=>'bar'];
        try {
            $patch->apply($target);
        } catch (JSONPatch\Exception $e) {

        }
        $this->assertEquals($target['foo'], 'reverted');
    }

    /**
     * Tests that a patch fails to revert
     *
     * @expectedException \gamringer\JSONPatch\Exception
     */
    public function testRevertFailPatch()
    {
        $patch = new Patch();
        $op = new Resources\MockAtomic(function(Pointer $pointer){
            $value = 'baz';
            $pointer->set('/foo', $value);
        }, function(Pointer $pointer){
            throw new Operation\Exception('Reversion failed');
        });
        $patch->addOperation($op);
        $op = new Resources\MockAtomic(function(Pointer $pointer){
            throw new Operation\Exception('Forced Reversion');
        });
        $patch->addOperation($op);

        $target = ['foo'=>'bar'];
        $patch->apply($target);
    }

    /**
     * Tests that invalid patch is
     *
     * @dataProvider invalidPatchProvider
     * @expectedException \gamringer\JSONPatch\Exception
     * @group wip
     */
    public function testInvalidPatch($patchContent)
    {
        Patch::fromJSON($patchContent);        
    }

    public function invalidPatchProvider()
    {
        return [
            ['null'],
            ['foo'],
            ['['],
            ['{'],
            ['{}'],
            ['"foo"'],
            ['{"op":"test","path":"/foo","value":"bar"}'],
        ];
    }
}

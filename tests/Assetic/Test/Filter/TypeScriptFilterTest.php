<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\TypeScriptFilter;

/**
 * @group integration
 */
class TypeScriptFilterTest extends \PHPUnit_Framework_TestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        if (!isset($_SERVER['TYPESCRIPT_BIN']) || !isset($_SERVER['NODE_BIN'])) {
            $this->markTestSkipped('There is no TYPESCRIPT_BIN or NODE_BIN environment variable.');
        }

        $this->filter = new TypeScriptFilter($_SERVER['TYPESCRIPT_BIN'], $_SERVER['NODE_BIN']);

        $this->asset = new FileAsset(__DIR__.'/fixtures/typescript/greater.ts');
        $this->asset->load();
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testFilter()
    {
        $this->filter->filterDump($this->asset);
        $expected = file_get_contents(__DIR__.'/fixtures/typescript/greater.js');
        $this->assertSame($expected, $this->asset->getContent());
    }
}

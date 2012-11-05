<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * TypeScript filter.
 *
 * @link http://www.tslang.org
 * @author Bruno Galeotti <bgaleotti@gmail.com>
 */
class TypeScriptFilter implements FilterInterface
{
    private $tsPath;
    private $nodeJsPath;

    /**
     * @param string $tsPath       Absolute path to the tsc executable
     * @param string $nodeJsPath   Absolute path to the folder containg node.js executable
     */
    public function __construct($tsPath, $nodeJsPath = null)
    {
        $this->tsPath = $tsPath;
        $this->nodeJsPath = $nodeJsPath;
    }

    /**
     * {@inheritDoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function filterDump(AssetInterface $asset)
    {

        $input = tempnam(sys_get_temp_dir(), 'assetic_typescript_input').'.ts';
        $output = tempnam(sys_get_temp_dir(), 'assetic_typescript_output');

        file_put_contents($input, $asset->getContent());

        $executables = array();
        if (null !== $this->nodeJsPath) {
            $executables[] = $this->nodeJsPath;
        }
        $executables[] = $this->tsPath;

        $pb = new ProcessBuilder($executables);
        $pb->add($input)->add('--out')->add($output);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            if (127 === $code) {
                throw new \RuntimeException('Path to node executable could not be resolved.');
            }

            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $content = file_get_contents($output);
        unlink($output);

        $asset->setContent($content);
    }
}

<?php

/**
 * LESS precompiler
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Precomp\Css;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;

class Less extends AbstractPrecomp
{
    /**
     * Returns the compiled sources
     *
     * @return string
     */
    public function getCompiled()
    {
        $css = '';
        foreach ($this->files as $file) {
            $css .= $this->compileOne($file);
        }

        return $css;
    }

    /**
     * Compiles one LESS file
     *
     * @param string $filename
     *   The file name
     */
    protected function compileOne(string $filename)
    {
        $css = '';

        $cmd = '/usr/bin/env lessc --no-color "' . $filename . '"';
        $descriptorSpec = [
            0 => [ 'pipe', 'r' ],
            1 => [ 'pipe', 'w' ],
            2 => [ 'pipe', 'w' ]
        ];
        $proc = proc_open($cmd, $descriptorSpec, $pipes);

        if ($proc !== false) {
            fclose($pipes[0]);
            $css = stream_get_contents($pipes[1]);
            $err = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            if ($err) {
                $this->compilerErrors[] = $err;
            }
        }

        return $css;
    }
}

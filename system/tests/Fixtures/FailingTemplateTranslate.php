<?php

namespace Tests\Fixtures;

use Translate\Translate;

class FailingTemplateTranslate extends Translate
{
    private int $moves = 0;

    protected function moveTemplateFile(string $source, string $target): void
    {
        $this->moves++;
        if ($this->moves === 2) {
            throw new \RuntimeException('Simulated commit failure');
        }

        parent::moveTemplateFile($source, $target);
    }
}

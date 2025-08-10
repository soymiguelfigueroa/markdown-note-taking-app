<?php

declare(strict_types=1);

namespace App\MisspellingHandler;

use PhpSpellcheck\MisspellingHandler\MisspellingHandlerInterface;
use PhpSpellcheck\MisspellingInterface;

class VoidHandler implements MisspellingHandlerInterface
{
    /**
     * @param MisspellingInterface[] $misspellings
     */
    public function handle(iterable $misspellings): void
    {
        //
    }
}

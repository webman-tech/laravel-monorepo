<?php

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

return [
    'enable' => true,
    'translation' => function (): ?Translator {
        return null;
    },
    'extends' => function (ValidationFactory $validator): void {
    },
];

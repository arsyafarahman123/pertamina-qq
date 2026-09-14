<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Anthropic (Claude) — dipakai oleh FuelMaosService sebagai fallback AI
    | untuk menjawab pertanyaan bebas yang tidak cocok rule/knowledge base.
    |--------------------------------------------------------------------------
    | Isi ANTHROPIC_API_KEY di file .env untuk mengaktifkan mode ini.
    | Kalau dikosongkan, asisten tetap jalan normal pakai rule-based saja.
    */
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5-20250929'),
    ],

];

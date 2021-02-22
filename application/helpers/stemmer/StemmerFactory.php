<?php

//namespace Wamania\Snowball;

use voku\helper\UTF8;

class StemmerFactory
{
    const LANGS = [
        Catalan::class    => ['ca', 'cat', 'catalan'],
        Danish::class     => ['da', 'dan', 'danish'],
        Dutch::class      => ['nl', 'dut', 'nld', 'dutch'],
        English::class    => ['en', 'eng', 'english'],
        Finnish::class    => ['fi', 'fin', 'finnish'],
        French::class     => ['fr', 'fre', 'fra', 'french'],
        German::class     => ['de', 'deu', 'ger', 'german'],
        Italian::class    => ['it', 'ita', 'italian'],
        Norwegian::class  => ['no', 'nor', 'norwegian'],
        Portuguese::class => ['pt', 'por', 'portuguese'],
        Romanian::class   => ['ro', 'rum', 'ron', 'romanian'],
        Russian::class    => ['ru', 'rus', 'russian'],
        Spanish::class    => ['es', 'spa', 'spanish'],
        Swedish::class    => ['sv', 'swe', 'swedish']
    ];

    /**
     * @throws NotFoundException
     */
    public static function create(string $code): Stemmer
    {
        global $libs;
        if (!isset($libs)) { $libs = array(); }
        $code = UTF8::strtolower($code);

        foreach (self::LANGS as $classname => $isoCodes) {
            if (in_array($code, $isoCodes)) {
                $file = $classname.'.php';
                if (!isset($libs[$classname]))
                    {
                        $libs[$classname] = 1;
                        require($file);
                    }
                return new $classname;
            }
        }

        throw new NotFoundException(sprintf('Stemmer not found for %s', $code));
    }
}

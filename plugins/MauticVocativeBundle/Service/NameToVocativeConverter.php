<?php

namespace MauticPlugin\MauticVocativeBundle\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;
use MauticPlugin\MauticVocativeBundle\Service\Helpers\NameToVocativeOptions;
use MauticPlugin\MauticVocativeBundle\Service\Helpers\RecursiveImplode;

class NameToVocativeConverter
{
    /**
     * @var CzechName
     */
    private $name;

    public function __construct(CzechName $name)
    {
        $this->name = $name;
    }

    /**
     * @param string                     $name
     * @param NameToVocativeOptions|null $options
     *
     * @return string
     */
    public function toVocative($name, NameToVocativeOptions $options = null)
    {
        if ($options !== null) {
            if ($name === '') {
                if ($options->hasEmptyNameAlias()) {
                    $name = $options->getEmptyNameAlias();
                }
            } elseif ($options->hasMaleAlias() && $this->name->isMale($name)) {
                $name = $options->getMaleAlias();
            } elseif ($options->hasFemaleAlias() && !$this->name->isMale($name)) {
                $name = $options->getFemaleAlias();
            }
        }
        if ($name === '') {
            return '';
        }

        $decodedName = html_entity_decode($name);
        if ($decodedName === $name) {
            return $this->name->vocative($name);
        }

        return htmlentities($this->name->vocative($decodedName));
    }

    /**
     * Searching for [name|vocative] (enclosed by native or URL encoded square brackets,
     * with name optionally enclosed by [square brackets] as well to match email preview.
     *
     * @param string $value
     *
     * @return string
     */
    public function findAndReplace($value)
    {
        $tokens = $this->vocalizeByShortCodes($value);
        // this lines is not necessary now, hope :)
      //  $value = $this->removeEmptyShortCodes($value);
        return $tokens;
    }

    /**
     * Used regexp is split to parts only because of logical grouping to easier read.
     *
     * @param string $value
     *
     * @return string
     */
    private function vocalizeByShortCodes($value)
    {
        $regexpParts = [
            '(?<toReplace>',
            [
                '(?:\[|%5B)', // opening bracket, native or URL encoded
                [
                    '\s*', // leading white characters are trimmed
                    '(', // follows two possible enclosing formats
                    [
                        [ // first
                            '[\[]\s*', // enclosed by brackets
                            '(?<toVocative1>', $toVocativeRegexp = '(?:[^\[\]]*[^\s\[\]]|)', ')', // without any bracket, ending by non-bracket and non-white-character, or emptiness
                            '\s*[\]]'// enclosed by brackets
                        ],
                        '|', // or
                        [ // second
                            '(?<toVocative2>', $toVocativeRegexp, ')' // without enclosing brackets
                        ],
                    ],
                    ')', // end of combinations group
                    '\s*', // trailing white characters are trimmed
                    '\|\s*vocative\s*', // with trailing (relatively to name) pipe and keyword "vocative"
                    '(?:\(+', // options are enclosed in parenthesis
                    [
                        '\s*',
                        '(?<options>[^\)\]]*[^\)\]\s])?', // optional values of options with fail-save by excluded right bracket in case of missing closing parenthesis
                        '\s*',
                    ],
                    '\)*)?', // last parenthesis can be (but should not be) omitted, whole options are optional
                    '\s*', // trailing white characters are trimmed
                ],
                '(?:\]|%5D)', // closing bracket, native or URL encoded
            ],
            ')',
        ];
        $regexp = '~'.RecursiveImplode::implode($regexpParts).'~u'; // u = UTF-8
        $tokens = [];
        if (preg_match_all($regexp, $value, $matches) > 0) {
            foreach ($matches['toReplace'] as $index => $toReplace) {
                $toVocative = '';
                if ($matches['toVocative1'][$index] !== '') {
                    $toVocative = $matches['toVocative1'][$index];
                } elseif ($matches['toVocative2'][$index] !== '') {
                    $toVocative = $matches['toVocative2'][$index];
                }
                $stringOptions      = $matches['options'][$index];
                $tokens[$toReplace] = $this->toVocative($toVocative, NameToVocativeOptions::createFromString($stringOptions));
//                $value = str_replace(
//                    $toReplace,
//                    $this->toVocative($toVocative, NameToVocativeOptions::createFromString($stringOptions)),
//                    $value
//                );
            }
        }

        return $tokens;
    }

    private function removeEmptyShortCodes($value)
    {
        $regexpParts = [
            '(?<toReplace>',
            [
                '(?:\[|%5B)', // opening bracket, native or URL encoded
                '\s*', // optional leading white character(s)
                '(?<toKeep>[^|\]]*[^\s|\]])?', // do not delete string before pipe or closing bracket (but trim it)
                '\s*\|\s*vocative\s*', // pipe and "vocative" keyword, optionally surrounded by white characters
                '(?:\s*\([^)]*\))?', // optional options
                '\s*', // optional trailing white character(s)
                '(?:\]|%5D)', // closing bracket, native or URL encoded
            ],
            ')',
        ];
        $regexp = '~'.RecursiveImplode::implode($regexpParts).'~u'; // u = UTF-8
        $tokens = [];
        if (preg_match_all($regexp, $value, $matches) > 0) {
            foreach ($matches['toReplace'] as $index => $toReplace) {
                $toKeep             = $matches['toKeep'][$index];
                $tokens[$toReplace] = $toKeep;
               // $value = str_replace($toReplace, $toKeep, $value);
            }
        }

        return $tokens;
    }
}

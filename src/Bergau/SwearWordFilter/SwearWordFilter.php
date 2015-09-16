<?php

namespace Bergau\SwearWordFilter;

class SwearWordFilter
{
    /**
     * @type string
     */
    private $replaceChar = 'x';

    /**
     * @type array
     */
    private $charsInBetweenBadWords = array(' ', '.', '_');

    /**
     * @type array
     */
    private $wordsToFilter = array();

    /**
     * SwearWordFilter constructor.
     *
     * @param array  $wordsToFilter Array of words to filter
     * @param string $replaceChar   The Character that replaces the bad word
     */
    public function __construct(array $wordsToFilter, $replaceChar = 'x')
    {
        $this->wordsToFilter = $wordsToFilter;
        $this->replaceChar = $replaceChar;
    }

    /**
     * @param string $unfiltered
     *
     * @return string
     */
    public function filter($unfiltered)
    {
        $f = $unfiltered;

        while ($this->containsBadWord($f)) {
            $f = $this->filterByWord($f);
        }

        return $f;
    }

    private function containsBadWord($input)
    {
        $u = str_replace($this->charsInBetweenBadWords, '', $input);

        foreach ($this->wordsToFilter as $wordToFilter) {
            if (false !== strpos($u, $wordToFilter)) {
                return true;
            }
        }

        return false;
    }

    private function filterByWord($unfiltered)
    {
        foreach ($this->wordsToFilter as $wordToFilter) {
            $firstCharacterFromWordToFilter = substr($wordToFilter, 0, 1);

            // We search from the beginning of the unfiltered string
            for ($i = 0; $i < strlen($unfiltered); $i++) {
                $nextCharacterFromUnfiltered = substr($unfiltered, $i, 1);

                // We potentially found a bad word, a string began with the same char as the bad words first char
                if ($nextCharacterFromUnfiltered === $firstCharacterFromWordToFilter) {
                    $lengthOfRestOfUnfilteredString = strlen($unfiltered) - $i;

                    // We search from the begin until the string ends "This is <start>b.a.d.w.o.r.d<end>"
                    for ($d = $i; $d <= $lengthOfRestOfUnfilteredString + $i; $d++) {
                        $u = str_replace($this->charsInBetweenBadWords, '', $unfiltered);

                        if (false !== strpos($u, $wordToFilter)) {
                            // ok we found one
                            $positionOfUnfilteredStringThatBeginsWithBadWord = $i;
                            // we need to find the exact length in the unfiltered that contains the bad word
                            // "This is a >>>ba d.wo r d<<<"
                            $stack = '';
                            $rest = $lengthOfRestOfUnfilteredString + $i;
                            for ($x = $positionOfUnfilteredStringThatBeginsWithBadWord; $x <= $rest; $x++) {
                                $stack .= substr($unfiltered, $x, 1);

                                $u = str_replace($this->charsInBetweenBadWords, '', $stack);

                                if (false !== strpos($u, $wordToFilter)) {
                                    // we now know the position of the badword
                                    $startPos = $i;
                                    $endPos = $x;

                                    $stringBeforeBadWord = substr($unfiltered, 0, $startPos);
                                    $stringAfterBadWord = substr($unfiltered, $endPos + 1); // this can be false

                                    $replaceBadWordWith = str_repeat($this->replaceChar, $endPos - $startPos + 1);
                                    $filtered = $stringBeforeBadWord . $replaceBadWordWith . $stringAfterBadWord;

                                    return $filtered;
                                }
                            }
                        }
                    }
                }
            }
        }

        return '';
    }
}

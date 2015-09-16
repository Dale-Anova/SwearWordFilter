<?php

namespace Bergau\SwearWordFilter;

class SwearWordFilter
{
    private $wordsToFilter = array();

    /**
     * SwearWordFilter constructor.
     *
     * @param array $wordsToFilter
     */
    public function __construct(array $wordsToFilter)
    {
        $this->wordsToFilter = $wordsToFilter;
    }

    public function filter($unfiltered)
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
                    for ($d = $i; $d <= $lengthOfRestOfUnfilteredString; $d++) {
                        $u = str_replace(array(' ', '.', '_'), '', $unfiltered);
                        if (false !== strpos($u, $wordToFilter)) {
                            // ok we found one
                            $positionOfUnfilteredStringThatBeginsWithBadWord = $i;
                            $lengthOfBadWordFound = strlen($wordToFilter);

                            // we need to find the exact length in the unfiltered that contains the bad word
                            // "This is a >>>ba d.wo r d<<<"
                            $stack = '';
                            $rest = $lengthOfRestOfUnfilteredString + $i;
                            for ($x = $positionOfUnfilteredStringThatBeginsWithBadWord; $x <= $rest; $x++) {
                                $stack .= substr($unfiltered, $x, 1);

                                $u = str_replace(array(' ', '.', '_'), '', $stack);

                                if (false !== strpos($u, $wordToFilter)) {
                                    // we now know the position of the badword
                                    $startPos = $i;
                                    $endPos = $x;

                                    $stringBeforeBadWord = substr($unfiltered, 0, $startPos);
                                    $stringAfterBadWord = substr($unfiltered, $endPos + 1); // this can be false

                                    $filtered = $stringBeforeBadWord . '' . $stringAfterBadWord;

                                    return $filtered;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $unfiltered;
    }
}

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
            $f = $this->innerFilter($f);
        }

        return $f;
    }

    /**
     * @param string $input
     *
     * @return bool
     */
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

    private function innerFilter($input)
    {
        foreach ($this->wordsToFilter as $wordToFilter) {
            $realPositionAfterBugfix = $this->getPositionFromAndToOfBadword($input, $wordToFilter);

            $startPos = $realPositionAfterBugfix['from'];
            $endPos   = $realPositionAfterBugfix['to'];

            $stringBeforeBadWord = substr($input, 0, $startPos);
            $stringAfterBadWord  = substr($input, $endPos + 1);

            $replaceBadWordWith  = str_repeat($this->replaceChar, $endPos - $startPos + 1);
            $filtered = $stringBeforeBadWord . $replaceBadWordWith . $stringAfterBadWord;

            return $filtered;
        }

        return '';
    }

    private function getPositionFromAndToOfBadword($input, $wordToFilter)
    {
        $stack = '';
        $foundFromPos = false;
        $posOfBadWord = 0;
        $fromPosition = 0;
        $endPosition = 0;

        for ($posOfInput = 0; $posOfInput < strlen($input); $posOfInput++) {
            $aCharFromUnfiltered = substr($input, $posOfInput, 1);
            $aCharFromWordToFilter = substr($wordToFilter, $posOfBadWord, 1);

            // "Ba dword"
            if (in_array($aCharFromUnfiltered, $this->charsInBetweenBadWords)) {
                continue;
            } else {
                if ($aCharFromUnfiltered != $aCharFromWordToFilter) {
                    $posOfBadWord = 0;
                    $stack = '';
                    $foundFromPos = false;
                    $aCharFromWordToFilter = substr($wordToFilter, $posOfBadWord, 1);
                }
            }

            if ($aCharFromUnfiltered === $aCharFromWordToFilter) {
                if (!$foundFromPos) {
                    $fromPosition = $posOfInput;
                    $foundFromPos = true;
                }
                $stack .= $aCharFromUnfiltered;
                $posOfBadWord++;
            } else {
                // Ok it was something like "ba.badword" where we just analyzed "ba." and that does not match, rerun.
                $fromPosition = 0;
                $posOfBadWord = 0;
                $stack = '';
            }

            if ($stack === $wordToFilter) {
                $endPosition = $posOfInput;
                break;
            }
        }

        return array('from' => $fromPosition, 'to' => $endPosition);
    }
}

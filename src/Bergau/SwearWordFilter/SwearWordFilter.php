<?php

namespace Bergau\SwearWordFilter;

/**
 * SwearWordFilter
 *
 * @author      Christian Bergau <cbergau86@gmail.com>
 * @copyright   2015, Christian Bergau
 * @license     MIT
 */
class SwearWordFilter
{
    /**
     * @type string
     */
    private $replaceChar = 'x';

    /**
     * @type array
     */
    private $charsInBetweenSwearWords = array(' ', '.', '_');

    /**
     * @type array
     */
    private $wordsToFilter = array();

    /**
     * @type array
     */
    private $wordsThatWereAlreadyFiltered = array();

    /**
     * SwearWordFilter constructor.
     *
     * @param array  $wordsToFilter Array of words to filter
     * @param string $replaceChar   The Character that replaces the swear word
     */
    public function __construct(array $wordsToFilter, $replaceChar = 'x')
    {
        $this->wordsToFilter = $wordsToFilter;
        $this->replaceChar = $replaceChar;
    }

    /**
     * @api
     *
     * @param string $input
     *
     * @return string
     */
    public function filter($input)
    {
        $this->wordsThatWereAlreadyFiltered = array();
        $filtered = $input;

        while ($this->containsSwearWord($filtered)) {
            $filtered = $this->doFilter($filtered);
        }

        return $filtered;
    }

    /**
     * @param string $input
     *
     * @return bool
     */
    private function containsSwearWord($input)
    {
        $inputSpecialCharsStrippedOut = str_replace($this->charsInBetweenSwearWords, '', $input);

        foreach ($this->wordsToFilter as $wordToFilter) {
            if (false !== strpos($inputSpecialCharsStrippedOut, $wordToFilter)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $input
     * @param string $swearWord
     *
     * @return bool
     */
    private function contains($input, $swearWord)
    {
        $inputSpecialCharsStrippedOut = str_replace($this->charsInBetweenSwearWords, '', $input);

        if (false !== strpos($inputSpecialCharsStrippedOut, $swearWord)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function doFilter($input)
    {
        foreach ($this->wordsToFilter as $wordToFilter) {

            if ($this->wordWasAlreadyFiltered($wordToFilter)) {
                continue;
            }

            if (!$this->contains($input, $wordToFilter)) {
                continue;
            }

            $positionOfSwearWord = $this->getPositionFromAndToOfSwearWord($input, $wordToFilter);

            $startPos = $positionOfSwearWord['from'];
            $endPos = $positionOfSwearWord['to'];

            $stringBeforeSwearWord = substr($input, 0, $startPos);
            $stringAfterSwearWord = substr($input, $endPos + 1);

            $swearWordReplaced = str_repeat($this->replaceChar, $endPos - $startPos + 1);
            $filtered = $stringBeforeSwearWord . $swearWordReplaced . $stringAfterSwearWord;

            if (!$this->contains($filtered, $wordToFilter)) {
                $this->setWordIsFiltered($wordToFilter);
            }

            return $filtered;
        }

        return '';
    }

    /**
     * @param string $input
     * @param string $wordToFilter
     *
     * @return array
     */
    private function getPositionFromAndToOfSwearWord($input, $wordToFilter)
    {
        $stack = '';
        $foundFromPos = false;
        $posOfSwearWord = 0;
        $fromPosition = 0;
        $endPosition = 0;

        for ($positionOfInput = 0; $positionOfInput < strlen($input); $positionOfInput++) {
            $aCharFromUnfiltered = substr($input, $positionOfInput, 1);
            $aCharFromWordToFilter = substr($wordToFilter, $posOfSwearWord, 1);

            if (in_array($aCharFromUnfiltered, $this->charsInBetweenSwearWords)) {
                continue;
            } else {
                if ($aCharFromUnfiltered != $aCharFromWordToFilter) {
                    $posOfSwearWord = 0;
                    $stack = '';
                    $foundFromPos = false;
                    $aCharFromWordToFilter = substr($wordToFilter, $posOfSwearWord, 1);
                }
            }

            if ($aCharFromUnfiltered === $aCharFromWordToFilter) {
                if (!$foundFromPos) {
                    $fromPosition = $positionOfInput;
                    $foundFromPos = true;
                }
                $stack .= $aCharFromUnfiltered;
                $posOfSwearWord++;
            } else {
                $fromPosition = 0;
                $posOfSwearWord = 0;
                $stack = '';
            }

            if ($stack === $wordToFilter) {
                $endPosition = $positionOfInput;
                break;
            }
        }

        return array('from' => $fromPosition, 'to' => $endPosition);
    }

    /**
     * @param string $wordToFilter
     *
     * @return bool
     */
    private function wordWasAlreadyFiltered($wordToFilter)
    {
        return in_array($wordToFilter, $this->wordsThatWereAlreadyFiltered);
    }

    /**
     * @param string $wordToFilter
     */
    private function setWordIsFiltered($wordToFilter)
    {
        $this->wordsThatWereAlreadyFiltered[] = $wordToFilter;
    }
}

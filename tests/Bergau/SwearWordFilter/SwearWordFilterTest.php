<?php

namespace Bergau\SwearWordFilter;

use PHPUnit_Framework_TestCase;

class SwearWordFilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array  $wordsToFilter
     * @param string $input
     * @param string $filtered
     */
    private function assertFilter(array $wordsToFilter, $input, $filtered)
    {
        $filter = new SwearWordFilter($wordsToFilter);
        $this->assertSame($filtered, $filter->filter($input));
    }

    public function provider()
    {
        return array(
            array('', ''),
            array('badword', 'xxxxxxx'),
            array('b adword', 'xxxxxxxx'),
            array('b a d w o r d', 'xxxxxxxxxxxxx'),
            array('b_a d w o r d', 'xxxxxxxxxxxxx'),
            array('b_a_d_w_o_r_d', 'xxxxxxxxxxxxx'),
            array('b a_d_w_o_r_d', 'xxxxxxxxxxxxx'),
            array('b a.d_w_o_r_d', 'xxxxxxxxxxxxx'),
            array('ba badword', 'ba xxxxxxx'),
            array('ba ba badword', 'ba ba xxxxxxx'),
            array('ba bad badword', 'ba bad xxxxxxx'),
            array('ba badwor badword', 'ba badwor xxxxxxx'),
            array('This is a b a.d_w_o_r_d', 'This is a xxxxxxxxxxxxx'),
            array('This is a b a.d_w_o_r_d you see', 'This is a xxxxxxxxxxxxx you see'),
            array('This is a b a.d_w_o_r_d you see a badword', 'This is a xxxxxxxxxxxxx you see a xxxxxxx'),
            array('This is a b a.d_w_o_r_d you see a b.a dword', 'This is a xxxxxxxxxxxxx you see a xxxxxxxxx'),
            array(' badword bad word     badword', ' xxxxxxx xxxxxxxx     xxxxxxx'),
        );
    }

    /**
     * @dataProvider provider
     *
     * @param string $input
     * @param string $filtered
     */
    public function testFilter($input, $filtered)
    {
        $this->assertFilter(array('badword'), $input, $filtered);
    }

    public function multipleWordProvider()
    {
        return array(
            array('', ''),
            array('badword', 'xxxxxxx'),
            array('badwordanotherone', 'xxxxxxxxxxxxxxxxx'),
            array('anotherone', 'xxxxxxxxxx'),
            array('A badword and anotherone', 'A xxxxxxx and xxxxxxxxxx'),
            array('A badword and anotherone and anotherone', 'A xxxxxxx and xxxxxxxxxx and xxxxxxxxxx'),
        );
    }

    /**
     * @dataProvider multipleWordProvider
     *
     * @param string $input
     * @param string $filtered
     */
    public function testFilterWithMultipleWords($input, $filtered)
    {
        $this->assertFilter(array('badword', 'anotherone'), $input, $filtered);
    }

    public function testFilterWithMultipleCrossingWords()
    {
        $this->assertFilter(array('bad', 'anotherbadword'), 'bad and anotherbadword', 'xxx and xxxxxxxxxxxxxx');
        $this->assertFilter(array('bad', 'anotherbadword'), 'bad bad and anotherbadword', 'xxx xxx and xxxxxxxxxxxxxx');
    }
}

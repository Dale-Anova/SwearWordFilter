<?php

namespace Bergau\SwearWordFilter;

use PHPUnit_Framework_TestCase;

class SwearWordFilterTest extends PHPUnit_Framework_TestCase
{
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
     * @param string $unfiltered
     * @param string $expectedValueFromFilter
     */
    public function testFilter($unfiltered, $expectedValueFromFilter)
    {
        $wordsToFilter = array('badword');

        $filter = new SwearWordFilter($wordsToFilter);

        $this->assertSame($expectedValueFromFilter, $filter->filter($unfiltered));
    }

    public function multipleWordProvider()
    {
        return array(
            array('A badword and anotherone', 'A xxxxxxx and xxxxxxxxxx'),
            array('A badword and anotherone and anotherone', 'A xxxxxxx and xxxxxxxxxx and xxxxxxxxxx'),
        );
    }

    /**
     * @dataProvider multipleWordProvider
     *
     * @param $unfiltered
     * @param $expectedValueFromFilter
     */
    public function testFilterWithMultipleWords($unfiltered, $expectedValueFromFilter)
    {
        $wordsToFilter = array('badword', 'anotherone');

        $filter = new SwearWordFilter($wordsToFilter);

        $this->assertSame($expectedValueFromFilter, $filter->filter($unfiltered));
    }
}

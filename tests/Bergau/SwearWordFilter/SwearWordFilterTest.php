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
            array('This is a b a.d_w_o_r_d', 'This is a xxxxxxxxxxxxx'),
            array('This is a b a.d_w_o_r_d you see', 'This is a xxxxxxxxxxxxx you see'),
            array('This is a b a.d_w_o_r_d you see a badword', 'This is a xxxxxxxxxxxxx you see a xxxxxxx'),
            array('This is a b a.d_w_o_r_d you see a b.a dword', 'This is a xxxxxxxxxxxxx you see a xxxxxxxxx'),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testFilter($unfiltered, $expected)
    {
        $wordsToFilter = array('badword');

        $filter = new SwearWordFilter($wordsToFilter);

        $this->assertSame($expected, $filter->filter($unfiltered));
    }
}

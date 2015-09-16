<?php

namespace Bergau\SwearWordFilter;

use PHPUnit_Framework_TestCase;

class SwearWordFilterTest extends PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return array(
            array('', ''),
            array('badword', ''),
            array('b adword', ''),
            array('b a d w o r d', ''),
            array('b_a d w o r d', ''),
            array('b_a_d_w_o_r_d', ''),
            array('b a_d_w_o_r_d', ''),
            array('b a.d_w_o_r_d', ''),
            array('This is a b a.d_w_o_r_d', 'This is a '),
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

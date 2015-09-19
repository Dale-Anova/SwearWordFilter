SwearWordFilter
===

[![Build Status](https://travis-ci.org/cbergau/SwearWordFilter.svg)](https://travis-ci.org/cbergau/SwearWordFilter)

Cool Swear Word Filter, not like the cheap ones which u can trick with adding a "." in between characters :P

Installation with Composer
===

    composer require cbergau/swearwordfilter

Examples
===

Look at the tests: https://github.com/cbergau/SwearWordFilter/blob/master/tests/Bergau/SwearWordFilter/SwearWordFilterTest.php

Example Code
===

    <?php
    
    use Bergau\SwearWordFilter;
    
    // This is the string you want to filter
    $myInputIWantToFilter = '....';
    
    $replaceWith = 'x';
    $allBadWords = array('badword');
    
    $filter = new SwearWordFilter($allBadWords, $replaceWith);
    
    $cleanInput = $filter->filter($myInputIWantToFilter);

Todos
===

 - "badword and anotherbadword" is tricky yet because it now returns "anotherxxxxxxx"

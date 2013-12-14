<?php

require __DIR__ . "/../../bootstrap.php";


$date = new \Ale\DateInterval("PT1S");
\Tester\Assert::equal(1, $date->getTotalSeconds());

$date = new \Ale\DateInterval("PT1M");
\Tester\Assert::equal(1 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("PT1H");
\Tester\Assert::equal(1 * 60 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("P1D");
\Tester\Assert::equal(24 * 60 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("P1W");
\Tester\Assert::equal(7 * 24 * 60 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("P1M");
\Tester\Assert::equal(30 * 24 * 60 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("P1Y");
\Tester\Assert::equal(365 * 24 * 60 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("P2Y2M");
\Tester\Assert::equal((2 * 365 * 24 * 60 * 60) + (2 * 30 * 24 * 60 * 60), $date->getTotalSeconds());

$date = new \Ale\DateInterval("P1DT2H");
\Tester\Assert::equal(24 * 60 * 60 + 2 * 60 * 60, $date->getTotalSeconds());

$date = new \Ale\DateInterval("PT1M2S");
\Tester\Assert::equal(1 * 60 + 2, $date->getTotalSeconds());

$date = new \Ale\DateInterval("PT2H5S");
\Tester\Assert::equal(2 * 60 * 60 + 5, $date->getTotalSeconds());



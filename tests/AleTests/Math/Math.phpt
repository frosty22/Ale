<?php

require __DIR__ . "/../../bootstrap.php";


Tester\Assert::equal(1.0, Ale\Math::getOnePercent(100));
Tester\Assert::equal(0.5, Ale\Math::getOnePercent(50));
Tester\Assert::equal(2.0, Ale\Math::getOnePercent(200));

Tester\Assert::equal(10.0, Ale\Math::getPercentFromPart(10, 100));
Tester\Assert::equal(35.0, Ale\Math::getPercentFromPart(35, 100));
Tester\Assert::equal(20.0, Ale\Math::getPercentFromPart(10, 50));
Tester\Assert::equal(100.0, Ale\Math::getPercentFromPart(100, 100));
Tester\Assert::equal(0.0, Ale\Math::getPercentFromPart(0, 100));

Tester\Assert::equal(100, Ale\Math::sum(20, 20, 10, 50));
Tester\Assert::equal(10, Ale\Math::sum(10));
Tester\Assert::equal(0, Ale\Math::sum());


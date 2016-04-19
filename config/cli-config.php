<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require "../app/bootstrap.php";

return ConsoleRunner::createHelperSet($em);

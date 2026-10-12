<?php
declare(strict_types=1);
require __DIR__."/../src/Layout.php";
App\Layout::error(404,"signpost","This page doesn't exist","The link might be broken, or the page may have been moved or removed.");

<?php
declare(strict_types=1);
require __DIR__."/../src/Layout.php";
App\Layout::error(403,"lock","Access denied","You don't have permission to view this page.");

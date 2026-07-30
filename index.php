<?php

// The legacy bot-blocker includes below were written for older PHP syntax and crash on modern PHP 8+.
// They are not required for the basic redirect flow, so skip them to restore the site entry page.
header('Location: servr.php');
exit;
<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$GM_V2_LOCATIONS = gm_v2_locations();
$GM_V2_LOCATION_IDS = gm_v2_location_ids();
$GM_V2_CURRENT_LOCATION_ID = gm_v2_requested_location_id();
$GM_V2_CURRENT_LOCATION = gm_v2_current_location();

<?php

// Don't run this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// don't load page if not demo mode.
// demo mode just for developers.
// default: not defined.
if ( defined('YP_DEMO_MODE') == false ) {
	die( '-1' );
}

// Get frame.
wyp_frame_output();

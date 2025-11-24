<?php
/**
 * Product Animation Content Block - Helper Functions
 * 
 * Block registration: handled by block.json
 * Field definitions: handled by fields.json (auto-loaded by ACF)
 * 
 * This file contains custom helper functions and logic for the Product Animation Content block.
 */

// Any custom functions or logic for this block can go here
// For example: custom validation, data processing, etc.

wp_enqueue_script('gsap');
wp_enqueue_script('gsap-scrolltrigger', '', array('gsap'));

<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFilter
 */
/**
 * Smarty time modifier
 *
 * @param string                    $source input string
 * @param \Smarty_Internal_Template $template
 *
 * @return string filtered output
 */
function smarty_modifier_time($timeInSeconds)
{

    $hours = floor($timeInSeconds / 3600);
    $minutes = floor(($timeInSeconds % 3600) / 60);
    $seconds = $timeInSeconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

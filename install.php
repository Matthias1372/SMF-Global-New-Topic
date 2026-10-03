<?php
/**
 * Global New Topic
 *
 * @package GlobalNewTopic
 * @author Matthias
 * @copyright 2026
 * @license BSD-3-Clause
 * @version 1.0.0
 */

if (!defined('SMF'))
	die('No direct access...');

global $smcFunc;

$defaults = [
	'mcp_gnt_enabled' => '1',
	'mcp_gnt_groups' => '1',
	'mcp_gnt_collapsed_default' => '1',
];

foreach ($defaults as $variable => $value)
{
	$smcFunc['db_insert'](
		'ignore',
		'{db_prefix}settings',
		['variable' => 'string-255', 'value' => 'string-65534'],
		[[$variable, $value]],
		['variable']
	);
}

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

$smcFunc['db_query']('', '
	DELETE FROM {db_prefix}settings
	WHERE variable IN ({array_string:variables})',
	[
		'variables' => [
			'mcp_gnt_enabled',
			'mcp_gnt_groups',
			'mcp_gnt_collapsed_default',
		],
	]
);

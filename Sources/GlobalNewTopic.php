<?php
/**
 * Global New Topic
 *
 * Displays a collapsible native-style new-topic composer on normal forum pages.
 *
 * @package GlobalNewTopic
 * @author Matthias
 * @copyright 2026
 * @license BSD-3-Clause
 * @version 1.0.1
 */

if (!defined('SMF'))
	die('No direct access...');

function mcp_gnt_admin_areas(&$admin_areas)
{
	global $txt;

	loadLanguage('GlobalNewTopic');

	// Add Global New Topic as a subpage of SMF's native Modification Settings.
	if (!isset($admin_areas['config']['areas']['modsettings']['subsections']))
		$admin_areas['config']['areas']['modsettings']['subsections'] = [];

	$admin_areas['config']['areas']['modsettings']['subsections']['globalnewtopic'] = [
		$txt['mcp_gnt_admin_title'],
		'admin_forum',
	];
}

function mcp_gnt_modify_modifications(&$subActions)
{
	$subActions['globalnewtopic'] = 'mcp_gnt_admin';
}

function mcp_gnt_admin()
{
	global $context, $txt, $modSettings;

	isAllowedTo('admin_forum');
	loadLanguage('GlobalNewTopic');
	loadTemplate('GlobalNewTopic');

	$context['page_title'] = $txt['mcp_gnt_admin_title'];
	$context['sub_template'] = 'mcp_gnt_admin';
	$context['mcp_gnt_groups'] = mcp_gnt_get_membergroups();

	if (isset($_POST['save']))
	{
		checkSession();

		$valid_ids = array_map('strval', array_keys($context['mcp_gnt_groups']));
		$selected = isset($_POST['groups']) && is_array($_POST['groups'])
			? array_values(array_intersect($valid_ids, array_map('strval', $_POST['groups'])))
			: [];

		updateSettings([
			'mcp_gnt_enabled' => !empty($_POST['enabled']) ? '1' : '0',
			'mcp_gnt_groups' => implode(',', $selected),
			'mcp_gnt_collapsed_default' => !empty($_POST['collapsed_default']) ? '1' : '0',
		]);

		redirectexit('action=admin;area=modsettings;sa=globalnewtopic;saved');
	}

	$context['mcp_gnt_enabled'] = !empty($modSettings['mcp_gnt_enabled']);
	$context['mcp_gnt_collapsed_default'] = !empty($modSettings['mcp_gnt_collapsed_default']);
	$context['mcp_gnt_selected_groups'] = mcp_gnt_setting_groups();

	if (isset($_GET['saved']))
		$context['settings_saved'] = true;
}

function mcp_gnt_load_theme()
{
	global $context, $modSettings, $user_info, $sourcedir, $txt, $options, $smcFunc;

	if (empty($modSettings['mcp_gnt_enabled']) || empty($user_info))
		return;

	$excluded_actions = [
		'admin', 'post', 'post2', 'pm', 'profile', 'xmlhttp', 'helpadmin',
		'printpage', 'quotefast', 'quickmod', 'quickmod2', 'verificationcode',
		// SMF uses action=suggest for member-name autocomplete, including
		// the "To:" field when composing private messages. The global
		// composer must never initialize on this AJAX endpoint.
		'suggest',
	];

	$current_action = isset($_REQUEST['action']) ? (string) $_REQUEST['action'] : '';

	if (!empty($context['xml']) || in_array($current_action, $excluded_actions, true))
		return;

	// Do not show the global composer on board/message-index pages (index.php?board=x.x).
	if ($current_action === '' && isset($_REQUEST['board']))
		return;

	if (!mcp_gnt_group_allowed())
		return;

	$boards = mcp_gnt_get_postable_boards();

	if (empty($boards))
		return;

	loadLanguage('Post');
	loadLanguage('GlobalNewTopic');
	loadTemplate('GlobalNewTopic');

	require_once($sourcedir . '/Subs-Editor.php');

	// Native new-topic posting state, matching Post().
	// Native SMF 2.1 draft setup from Post.php.
	$context['drafts_save'] = !empty($modSettings['drafts_post_enabled']) && allowedTo('post_draft');
	$context['drafts_autosave'] = !empty($context['drafts_save'])
		&& !empty($modSettings['drafts_autosave_enabled'])
		&& !empty($options['drafts_autosave_enabled']);
	$context['id_draft'] = 0;

	// Build the same saved-draft list that the native post screen displays.
	if (!empty($context['drafts_save']))
	{
		require_once($sourcedir . '/Drafts.php');
		ShowDrafts($user_info['id'], false);
	}
	$context['show_spellchecking'] = false;
	$context['submit_label'] = $txt['post'];
	$context['message'] = '';
	$context['subject'] = '';
	$context['icon'] = 'xx';
	$context['is_new_topic'] = true;
	$context['is_new_post'] = true;
	$context['is_first_post'] = true;
	$context['can_notify'] = !$context['user']['is_guest'];
	$context['notify'] = false;
	$context['auto_notify'] = false;
	$context['back_to_topic'] = false;
	$context['use_smileys'] = true;
	$context['show_additional_options'] = false;

	// Native SMF 2.1 guest identity fields from Post().
	if (!empty($user_info['is_guest']))
	{
		$context['name'] = isset($_SESSION['guest_name']) ? $_SESSION['guest_name'] : '';
		$context['email'] = isset($_SESSION['guest_email']) ? $_SESSION['guest_email'] : '';
	}

	// Native SMF 2.1 posting verification from Post(). Post2() validates the
	// control using this same "post" identifier.
	$context['require_verification'] = empty($user_info['is_mod'])
		&& empty($user_info['is_admin'])
		&& !empty($modSettings['posts_require_captcha'])
		&& ($user_info['posts'] < $modSettings['posts_require_captcha']
			|| (!empty($user_info['is_guest']) && $modSettings['posts_require_captcha'] == -1));

	if ($context['require_verification'])
	{
		$verificationOptions = [
			'id' => 'post',
		];
		$context['require_verification'] = create_control_verification($verificationOptions);
		$context['visual_verification_id'] = $verificationOptions['id'];
	}

	// Native SMF 2.1 new-topic option permissions.
	// This follows Post.php for a new topic with a board selection list.
	$mcp_gnt_board_ids = array_map(
		static function ($board_data) {
			return (int) $board_data['id'];
		},
		$boards
	);

	$context['becomes_approved'] = true;
	$context['already_locked'] = 0;
	$context['already_sticky'] = 0;
	$context['sticky'] = !empty($_REQUEST['sticky']);

	$context['can_lock'] = allowedTo(array('lock_any', 'lock_own'), $mcp_gnt_board_ids, true);
	$context['can_sticky'] = allowedTo('make_sticky', $mcp_gnt_board_ids, true);
	$context['can_move'] = allowedTo('move_any', $mcp_gnt_board_ids, true);
	$context['can_announce'] = allowedTo('announce_topic', $mcp_gnt_board_ids, true) && $context['becomes_approved'];
	$context['show_approval'] = !allowedTo('approve_posts', $mcp_gnt_board_ids, true)
		? 0
		: ($context['becomes_approved'] ? 2 : 1);

	$context['move'] = !empty($_REQUEST['move']);
	$context['announce'] = !empty($_REQUEST['announce']);
	$context['locked'] = !empty($_REQUEST['lock']);

	$editorOptions = [
		'id' => 'mcp_gnt_message',
		'value' => '',
		'labels' => [
			'post_button' => $context['submit_label'],
		],
		'height' => '175px',
		'width' => '100%',
		'preview_type' => 2,
		'required' => true,
		'form' => 'mcp_gnt_postmodify',
	];

	create_control_richedit($editorOptions);
	$context['mcp_gnt_post_box_name'] = $editorOptions['id'];

	// Use SMF's native message icon helper.
	$context['icons'] = getMessageIcons(0);
	if (!empty($context['icons']))
		$context['icons'][count($context['icons']) - 1]['is_last'] = true;

	$context['icon_url'] = '';
	foreach ($context['icons'] as $key => $icon)
	{
		$context['icons'][$key]['selected'] = $icon['value'] === $context['icon'];

		if ($context['icons'][$key]['selected'])
			$context['icon_url'] = $icon['url'];
	}

	if (empty($context['icon_url']) && !empty($context['icons']))
	{
		$context['icon'] = $context['icons'][0]['value'];
		$context['icons'][0]['selected'] = true;
		$context['icon_url'] = $context['icons'][0]['url'];
	}

	// Build the native posting_fields array so template_post_header() can be used.
	$context['posting_fields'] = [];

	if (isset($context['name'], $context['email']))
	{
		$context['posting_fields']['guestname'] = [
			'label' => [
				'text' => $txt['name'],
			],
			'input' => [
				'type' => 'text',
				'attributes' => [
					'size' => 25,
					'maxlength' => 25,
					'value' => $context['name'],
					'required' => true,
				],
			],
		];

		if (empty($modSettings['guest_post_no_email']))
		{
			$context['posting_fields']['email'] = [
				'label' => [
					'text' => $txt['email'],
				],
				'input' => [
					'type' => 'email',
					'attributes' => [
						'size' => 25,
						'value' => $context['email'],
						'required' => true,
					],
				],
			];
		}
	}

	$context['mcp_gnt_default_board'] = 0;

	$context['posting_fields']['board'] = [
		'label' => [
			'text' => $txt['calendar_post_in'],
		],
		'input' => [
			'type' => 'select',
			'attributes' => [
				'id' => 'mcp_gnt_board',
				'name' => 'mcp_gnt_board',
				'onchange' => 'mcpGntBoardChanged();',
				'required' => true,
			],
			'options' => [
				$txt['mcp_gnt_choose_board'] => [
					'value' => '',
					'selected' => true,
					'disabled' => true,
				],
			],
		],
	];

	foreach ($boards as $board_data)
	{
		if (!isset($context['posting_fields']['board']['input']['options'][$board_data['category']]))
			$context['posting_fields']['board']['input']['options'][$board_data['category']] = ['options' => []];

		$context['posting_fields']['board']['input']['options'][$board_data['category']]['options'][$board_data['name']] = [
			'value' => $board_data['id'],
			'selected' => false,
			'label' => ($board_data['child_level'] > 0 ? str_repeat('==', $board_data['child_level'] - 1) . '=&gt;' : '') . ' ' . $board_data['name'],
		];
	}

	$context['posting_fields']['subject'] = [
		'label' => [
			'text' => $txt['subject'],
			'class' => '',
		],
		'input' => [
			'type' => 'text',
			'attributes' => [
				'size' => 80,
				'maxlength' => 80,
				'value' => '',
				'required' => true,
			],
		],
	];

	$context['posting_fields']['icon'] = [
		'label' => [
			'text' => $txt['message_icon'],
		],
		'input' => [
			'type' => 'select',
			'attributes' => [
				'id' => 'icon',
				'onchange' => 'showimage();',
			],
			'options' => [],
			'after' => ' <img id="icons" src="' . $context['icon_url'] . '">',
		],
	];

	foreach ($context['icons'] as $icon)
	{
		$context['posting_fields']['icon']['input']['options'][$icon['name']] = [
			'value' => $icon['value'],
			'selected' => $icon['value'] == $context['icon'],
		];
	}

	// Native attachment state from Post().
	$post_attachment_boards = mcp_gnt_get_permission_boards('post_attachment');
	$unapproved_attachment_boards = !empty($modSettings['postmod_active'])
		? mcp_gnt_get_permission_boards('post_unapproved_attachments')
		: [];

	$context['can_post_attachment'] = !empty($modSettings['attachmentEnable'])
		&& $modSettings['attachmentEnable'] == 1
		&& (!empty($post_attachment_boards) || !empty($unapproved_attachment_boards));

	$context['current_attachments'] = [];
	$context['attachments'] = [
		'total_size' => 0,
		'quantity' => 0,
	];

	if ($context['can_post_attachment'])
	{
		$context['num_allowed_attachments'] = empty($modSettings['attachmentNumPerPostLimit'])
			? PHP_INT_MAX
			: $modSettings['attachmentNumPerPostLimit'];

		$context['attachment_restrictions'] = [];
		$context['allowed_extensions'] = !empty($modSettings['attachmentCheckExtensions'])
			? strtr(strtolower($modSettings['attachmentExtensions']), [',' => ', '])
			: '';

		$attachmentRestrictionTypes = ['attachmentNumPerPostLimit', 'attachmentPostLimit', 'attachmentSizeLimit'];

		foreach ($attachmentRestrictionTypes as $type)
		{
			if (empty($modSettings[$type]))
				continue;

			$context['attachment_restrictions'][$type] = sprintf(
				$txt['attach_restrict_' . $type . ($modSettings[$type] >= 1024 ? '_MB' : '')],
				comma_format($modSettings[$type] >= 1024 ? $modSettings[$type] / 1024 : $modSettings[$type], 2)
			);

			if ($type == 'attachmentNumPerPostLimit')
				$context['attachment_restrictions'][$type] .= ' (' . sprintf($txt['attach_remaining'], max($modSettings['attachmentNumPerPostLimit'], 0)) . ')';
		}

		// These are exactly the native SMF 2.1 attachment assets.
		loadCSSFile('attachments.css', ['minimize' => true, 'order_pos' => 450], 'smf_attachments');
		loadJavaScriptFile('dropzone.min.js', ['defer' => true], 'smf_dropzone');
		loadJavaScriptFile('smf_fileUpload.js', ['defer' => true, 'minimize' => true], 'smf_fileUpload');

		$acceptedFiles = empty($context['allowed_extensions']) ? '' : implode(',', array_map(
			function ($val) use ($smcFunc)
			{
				return !empty($val) ? ('.' . $smcFunc['htmltrim']($val)) : '';
			},
			explode(',', $context['allowed_extensions'])
		));

		addInlineJavaScript('
	var current_attachments = [];
	$(function() {
		smf_fileUpload({
			dictDefaultMessage : ' . JavaScriptEscape($txt['attach_drop_zone']) . ',
			dictFallbackMessage : ' . JavaScriptEscape($txt['attach_drop_zone_no']) . ',
			dictCancelUpload : ' . JavaScriptEscape($txt['modify_cancel']) . ',
			genericError: ' . JavaScriptEscape($txt['attach_php_error']) . ',
			text_attachDropzoneLabel: ' . JavaScriptEscape($txt['attach_drop_zone']) . ',
			text_attachLimitNag: ' . JavaScriptEscape($txt['attach_limit_nag']) . ',
			text_attachLeft: ' . JavaScriptEscape($txt['attachments_left']) . ',
			text_deleteAttach: ' . JavaScriptEscape($txt['attached_file_delete']) . ',
			text_attachDeleted: ' . JavaScriptEscape($txt['attached_file_deleted']) . ',
			text_insertBBC: ' . JavaScriptEscape($txt['attached_insert_bbc']) . ',
			text_attachUploaded: ' . JavaScriptEscape($txt['attached_file_uploaded']) . ',
			text_attach_unlimited: ' . JavaScriptEscape($txt['attach_drop_unlimited']) . ',
			text_totalMaxSize: ' . JavaScriptEscape($txt['attach_max_total_file_size_current']) . ',
			text_max_size_progress: ' . JavaScriptEscape('{currentRemain} ' . ($modSettings['attachmentPostLimit'] >= 1024 ? $txt['megabyte'] : $txt['kilobyte']) . ' / {currentTotal} ' . ($modSettings['attachmentPostLimit'] >= 1024 ? $txt['megabyte'] : $txt['kilobyte'])) . ',
			dictMaxFilesExceeded: ' . JavaScriptEscape($txt['more_attachments_error']) . ',
			dictInvalidFileType: ' . JavaScriptEscape(sprintf($txt['cant_upload_type'], $context['allowed_extensions'])) . ',
			dictFileTooBig: ' . JavaScriptEscape(sprintf($txt['file_too_big'], comma_format($modSettings['attachmentSizeLimit'], 0))) . ',
			acceptedFiles: ' . JavaScriptEscape($acceptedFiles) . ',
			thumbnailWidth: ' . (!empty($modSettings['attachmentThumbWidth']) ? $modSettings['attachmentThumbWidth'] : 'null') . ',
			thumbnailHeight: ' . (!empty($modSettings['attachmentThumbHeight']) ? $modSettings['attachmentThumbHeight'] : 'null') . ',
			limitMultiFileUploadSize:' . (!empty($modSettings['attachmentPostLimit']) ? (int) $modSettings['attachmentPostLimit'] * 1024 : 0) . ',
			maxFileAmount: ' . (!empty($context['num_allowed_attachments']) ? $context['num_allowed_attachments'] : 'null') . ',
			maxTotalSize: ' . (!empty($modSettings['attachmentPostLimit']) ? $modSettings['attachmentPostLimit'] : '0') . ',
			maxFilesize: ' . (!empty($modSettings['attachmentSizeLimit']) ? $modSettings['attachmentSizeLimit'] : '0') . '
		});
	});', true);

		// Native Post.php removes the non-JavaScript attachment UI once JS is available.
		addInlineJavaScript("\n\t" . '$("#post_attachments_area #postAttachment").remove();', true);
	}

	// Native submit-once protection used by action=post/action=post2.
	checkSubmitOnce('register');

	$context['mcp_gnt_boards'] = $boards;
	$context['mcp_gnt_collapsed_default'] = !empty($modSettings['mcp_gnt_collapsed_default']);

	if (!isset($context['template_layers']) || !is_array($context['template_layers']))
		$context['template_layers'] = [];

	$context['template_layers'][] = 'mcp_globalnewtopic';
}

function mcp_gnt_group_allowed()
{
	global $user_info, $smcFunc;

	$allowed = mcp_gnt_setting_groups();

	if (empty($allowed))
		return false;

	// Guests are SMF's special pseudo group -1.
	if (!empty($user_info['is_guest']))
		return in_array('-1', $allowed, true);

	// SMF's administrator group has the fixed ID 1.
	// If it is enabled in the mod settings, a real administrator must always match.
	if (!empty($user_info['is_admin']) && in_array('1', $allowed, true))
		return true;

	if (empty($user_info['id']))
		return false;

	// Read the actual member group assignments. This deliberately keeps group 0,
	// which is SMF's pseudo group for regular members with no primary group.
	$request = $smcFunc['db_query']('', '
		SELECT id_group, id_post_group, additional_groups
		FROM {db_prefix}members
		WHERE id_member = {int:id_member}
		LIMIT 1',
		[
			'id_member' => (int) $user_info['id'],
		]
	);

	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);

	if (empty($row))
		return false;

	$member_groups = [
		(string) (int) $row['id_group'],
		(string) (int) $row['id_post_group'],
	];

	if (!empty($row['additional_groups']))
	{
		foreach (explode(',', $row['additional_groups']) as $group)
		{
			$group = trim($group);

			if ($group !== '' && ctype_digit($group))
				$member_groups[] = (string) (int) $group;
		}
	}

	return (bool) array_intersect($allowed, array_unique($member_groups));
}

function mcp_gnt_setting_groups()
{
	global $modSettings;

	if (!isset($modSettings['mcp_gnt_groups']) || $modSettings['mcp_gnt_groups'] === '')
		return [];

	return array_values(array_unique(array_filter(
		array_map('trim', explode(',', (string) $modSettings['mcp_gnt_groups'])),
		static function ($value) {
			return $value === '-1' || ctype_digit($value);
		}
	)));
}

function mcp_gnt_get_membergroups()
{
	global $smcFunc, $txt;

	$groups = [
		'-1' => $txt['mcp_gnt_guests'],
		'0' => $txt['mcp_gnt_regular_members'],
	];

	$request = $smcFunc['db_query']('', '
		SELECT id_group, group_name
		FROM {db_prefix}membergroups
		WHERE id_group > {int:regular}
		ORDER BY id_group',
		['regular' => 0]
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
		$groups[(string) $row['id_group']] = $row['group_name'];

	$smcFunc['db_free_result']($request);

	return $groups;
}

function mcp_gnt_get_postable_boards()
{
	global $modSettings, $smcFunc;

	// Match SMF's native new-topic permission lookup. When post moderation is
	// active, a user (especially a guest) may be allowed to submit an
	// unapproved topic without having the regular post_new permission.
	$post_permissions = ['post_new'];

	if (!empty($modSettings['postmod_active']))
		$post_permissions[] = 'post_unapproved_topics';

	$allowed_boards = array_values(array_unique(array_map('intval', boardsAllowedTo($post_permissions))));

	if (empty($allowed_boards))
		return [];

	$params = [];
	$where_allowed = '';

	// Board 0 is SMF's special value meaning "all boards".
	if (!in_array(0, $allowed_boards, true))
	{
		$where_allowed = 'AND b.id_board IN ({array_int:allowed_boards})';
		$params['allowed_boards'] = $allowed_boards;
	}

	$request = $smcFunc['db_query']('', '
		SELECT b.id_board, b.name AS board_name, b.child_level,
			c.name AS category_name, c.cat_order, b.board_order
		FROM {db_prefix}boards AS b
			INNER JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
		WHERE b.redirect = {string:empty_redirect}
			' . $where_allowed . '
		ORDER BY c.cat_order, b.board_order',
		$params + ['empty_redirect' => '']
	);

	$boards = [];

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$boards[] = [
			'id' => (int) $row['id_board'],
			'name' => $row['board_name'],
			'category' => $row['category_name'],
			'child_level' => (int) $row['child_level'],
		];
	}

	$smcFunc['db_free_result']($request);

	return $boards;
}

function mcp_gnt_get_permission_boards($permission)
{
	$boards = array_values(array_unique(array_map('intval', boardsAllowedTo($permission))));

	if (empty($boards))
		return [];

	if (in_array(0, $boards, true))
		return [0];

	return $boards;
}

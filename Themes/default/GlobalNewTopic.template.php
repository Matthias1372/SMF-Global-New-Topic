<?php
/**
 * Global New Topic template
 *
 * Uses SMF 2.1 native post markup and controls.
 *
 * @package GlobalNewTopic
 * @author Matthias
 * @copyright 2026
 * @license BSD-3-Clause
 * @version 1.0.1
 */

if (!defined('SMF'))
	die('No direct access...');

function template_mcp_globalnewtopic_above()
{
	global $context, $txt, $scripturl, $modSettings, $options, $settings;

	$collapsed = !empty($context['mcp_gnt_collapsed_default']);

	echo '
	<form action="', $scripturl, '?action=post2" method="post" accept-charset="', $context['character_set'], '" name="mcp_gnt_postmodify" id="mcp_gnt_postmodify" class="flow_hidden" onsubmit="mcpGntSetBoardAction(this); submitonce(this);" enctype="multipart/form-data">
		<div id="mcp_gnt_preview_section" style="display: none;">
			<div class="cat_bar">
				<h3 class="catbg">
					<span id="mcp_gnt_preview_subject">&nbsp;</span>
				</h3>
			</div>
			<div id="mcp_gnt_preview_body" class="windowbg">
				<br>
			</div>
			<br>
		</div>
		<div class="cat_bar">
			<h3 class="catbg">
				<a href="#" id="mcp_gnt_expand_link">', $txt['mcp_gnt_title'], '</a>
			</h3>
		</div>
		<div id="mcp_gnt_collapsed_hint" class="roundframe noup', $collapsed ? '' : ' hidden', '">
			<a href="#" id="mcp_gnt_collapsed_hint_link">', $txt['mcp_gnt_click_to_start'], '</a>
		</div>
		<div id="mcp_gnt_container">
			<div id="post_area">
				<div class="roundframe noup">
					<div class="noticebox" style="display: none" id="mcp_gnt_errors">
						<dl>
							<dt>
								<strong id="mcp_gnt_error_serious">', $txt['error_while_submitting'], '</strong>
							</dt>
							<dd class="error" id="mcp_gnt_error_list"></dd>
						</dl>
					</div>';

	// This is SMF's native Post.template.php header renderer.
	mcp_gnt_template_post_header();

	echo '
					', template_control_richedit($context['mcp_gnt_post_box_name'], 'smileyBox_message', 'bbcBox_message');

	// Native SMF 2.1 attachment markup from Post.template.php.
	if ($context['can_post_attachment'])
	{
		echo '
					<div id="post_attachments_area" class="roundframe noup">
						<div id="postAttachment">
							<div class="padding">
								<div>
									<strong>', $txt['attachments'], '</strong>:
									<input type="file" multiple="multiple" name="attachment[]" id="attachment1">
									<a href="javascript:void(0);" onclick="cleanFileInput(\'attachment1\');">(', $txt['clean_attach'], ')</a>';

		if (!empty($modSettings['attachmentSizeLimit']))
			echo '
									<input type="hidden" name="MAX_FILE_SIZE" value="', (int) $modSettings['attachmentSizeLimit'] * 1024, '">';

		echo '
								</div>';

		if (!empty($context['attachment_restrictions']))
			echo '
								<div class="smalltext">', $txt['attach_restrictions'], ' ', implode(', ', $context['attachment_restrictions']), '</div>';

		echo '
								<div class="smalltext">
									<input type="hidden" name="attach_del[]" value="0">
									', $txt['uncheck_unwatchd_attach'], '
								</div>
							</div>
							<div class="attachments"></div>
						</div>
						<div id="attachment_upload">
							<div id="drop_zone_ui" class="centertext">
								<div class="attach_drop_zone_label">
									', $txt['attach_drop_zone'], '
								</div>
							</div>
							<div class="files" id="attachment_previews">
								<div id="au-template">
									<div class="attachment_preview_wrapper">
										<div class="attach-ui roundframe">
											<a data-dz-remove class="main_icons delete floatright cancel"></a>
											<div class="attached_BBC_width_height">
												<div class="attached_BBC_width">
													<label for="attached_BBC_width">', $txt['attached_insert_width'], '</label>
													<input type="number" name="attached_BBC_width" min="0" value="" placeholder="', $txt['attached_insert_placeholder'], '">
												</div>
												<div class="attached_BBC_height">
													<label for="attached_BBC_height">', $txt['attached_insert_height'], '</label>
													<input type="number" name="attached_BBC_height" min="0" value="" placeholder="', $txt['attached_insert_placeholder'], '">
												</div>
											</div>
										</div>
										<div class="attach-preview">
											<img data-dz-thumbnail>
										</div>
										<div class="attachment_info">
											<span class="name" data-dz-name></span>
											<span class="error" data-dz-errormessage></span>
											<span class="size" data-dz-size></span>
											<span class="message" data-dz-message></span>
											<div class="progress_bar" role="progressBar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
												<div class="bar"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="attachment_spacer">
									<div class="fallback">
										<input type="file" multiple="multiple" name="attachment[]" id="attachment1" class="fallback"> (<a href="javascript:void(0);" onclick="cleanFileInput(\'attachment1\');">', $txt['clean_attach'], '</a>)';

		if (!empty($modSettings['attachmentSizeLimit']))
			echo '
										<input type="hidden" name="MAX_FILE_SIZE" value="', (int) $modSettings['attachmentSizeLimit'] * 1024, '">';

		echo '
									</div>
								</div>
							</div>
						</div>
						<div id="max_files_progress" class="max_files_progress progress_bar" role="progressBar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
							<div class="bar"></div>
							<div id="max_files_progress_text"></div>
						</div>
					</div>';
	}

	// Native SMF 2.1 "Other options" markup.
	if (!empty($modSettings['additional_options_collapsable']))
		echo '
					<div id="post_additional_options_header">
						<strong><a href="#" id="postMoreExpandLink"> ', $txt['post_additionalopt'], '</a></strong>
					</div>';

	echo '
					<div id="post_additional_options">
						<div id="post_settings" class="smalltext">
							<ul class="post_options">
								', $context['can_notify'] ? '<li><input type="hidden" name="notify" value="0"><label for="check_notify"><input type="checkbox" name="notify" id="check_notify"' . ($context['notify'] || !empty($options['auto_notify']) || $context['auto_notify'] ? ' checked' : '') . ' value="1"> ' . $txt['notify_replies'] . '</label></li>' : '', '
								', $context['can_lock'] ? '<li><input type="hidden" name="already_locked" value="' . $context['already_locked'] . '"><input type="hidden" name="lock" value="0"><label for="check_lock"><input type="checkbox" name="lock" id="check_lock"' . ($context['locked'] ? ' checked' : '') . ' value="1"> ' . $txt['lock_topic'] . '</label></li>' : '', '
								<li><label for="check_back"><input type="checkbox" name="goback" id="check_back"' . ($context['back_to_topic'] || !empty($options['return_to_post']) ? ' checked' : '') . ' value="1"> ', $txt['back_to_topic'], '</label></li>
								', $context['can_sticky'] ? '<li><input type="hidden" name="already_sticky" value="' . $context['already_sticky'] . '"><input type="hidden" name="sticky" value="0"><label for="check_sticky"><input type="checkbox" name="sticky" id="check_sticky"' . ($context['sticky'] ? ' checked' : '') . ' value="1"> ' . $txt['sticky_after_posting'] . '</label></li>' : '', '
								<li><label for="check_smileys"><input type="checkbox" name="ns" id="check_smileys"', $context['use_smileys'] ? '' : ' checked', ' value="NS"> ', $txt['dont_use_smileys'], '</label></li>', '
								', $context['can_move'] ? '<li><input type="hidden" name="move" value="0"><label for="check_move"><input type="checkbox" name="move" id="check_move" value="1"' . (!empty($context['move']) ? ' checked' : '') . '> ' . $txt['move_after_posting'] . '</label></li>' : '', '
								', $context['can_announce'] && $context['is_first_post'] ? '<li><label for="check_announce"><input type="checkbox" name="announce_topic" id="check_announce" value="1"' . (!empty($context['announce']) ? ' checked' : '') . '> ' . $txt['announce_topic'] . '</label></li>' : '', '
								', $context['show_approval'] ? '<li><label for="approve"><input type="checkbox" name="approve" id="approve" value="2"' . ($context['show_approval'] === 2 ? ' checked' : '') . '> ' . $txt['approve_this_post'] . '</label></li>' : '', '
							</ul>
						</div>
					</div>';

	// Native SMF 2.1 saved-drafts list from Post.template.php.
	if (!empty($modSettings['drafts_post_enabled']) && !empty($context['drafts']) && !empty($modSettings['drafts_show_saved_enabled']) && !empty($options['drafts_show_saved_enabled']))
	{
		echo '
					<div id="post_draft_options_header" class="title_bar">
						<h4 class="titlebg">
							<span id="postDraftExpand" class="toggle_up floatright" style="display: none;"></span> <strong><a href="#" id="postDraftExpandLink">', $txt['drafts_show'], '</a></strong>
						</h4>
					</div>
					<div id="post_draft_options">
						<dl class="settings">
							<dt><strong>', $txt['subject'], '</strong></dt>
							<dd><strong>', $txt['draft_saved_on'], '</strong></dd>';

		foreach ($context['drafts'] as $draft)
			echo '
							<dt>', $draft['link'], '</dt>
							<dd>', $draft['poster_time'], '</dd>';

		echo '
						</dl>
					</div>';
	}

	// Native SMF 2.1 posting verification markup from Post.template.php.
	if (!empty($context['require_verification']))
		echo '
					<div class="post_verification">
						<span>
							<strong>', $txt['verification'], ':</strong>
						</span>
						', template_control_verification($context['visual_verification_id'], 'all'), '
					</div>';

	echo '
					<span id="post_confirm_buttons">
						', template_control_richedit_buttons($context['mcp_gnt_post_box_name']), '
					</span>
				</div>
			</div>
			<br class="clear">

			<input type="hidden" name="additional_options" id="additional_options" value="0">
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '">
		</div>
	</form>

	<script>
		var current_board = null;
		var mcpGntToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: false,
			aSwappableContainers: [
				\'mcp_gnt_container\'
			],
			aSwapLinks: [
				{
					sId: \'mcp_gnt_expand_link\',
					msgExpanded: ', JavaScriptEscape($txt['mcp_gnt_title']), ',
					msgCollapsed: ', JavaScriptEscape($txt['mcp_gnt_title']), '
				}
			]
		});';

	// Keep the native collapsed hint in sync with the main SMF toggle.
	echo '
		function mcpGntUpdateCollapsedHint()
		{
			var hint = document.getElementById("mcp_gnt_collapsed_hint");

			if (!hint || typeof mcpGntToggle === "undefined")
				return;

			if (mcpGntToggle.bCollapsed)
				hint.classList.remove("hidden");
			else
				hint.classList.add("hidden");
		}

		var mcpGntExpandLink = document.getElementById("mcp_gnt_expand_link");

		if (mcpGntExpandLink)
		{
			mcpGntExpandLink.onclick = function () {
				mcpGntToggle.toggle();
				mcpGntUpdateCollapsedHint();
				this.blur();
				return false;
			};
		}

		var mcpGntHintLink = document.getElementById("mcp_gnt_collapsed_hint_link");

		if (mcpGntHintLink)
		{
			mcpGntHintLink.onclick = function () {
				mcpGntToggle.changeState(false);
				mcpGntUpdateCollapsedHint();
				this.blur();
				return false;
			};
		}
	';

	// Native SMF toggle code for Other options.
	if (!empty($modSettings['additional_options_collapsable']))
		echo '
		var oSwapAdditionalOptions = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: true,
			funcOnBeforeCollapse: function () {
				document.getElementById(\'additional_options\').value = \'0\';
			},
			funcOnBeforeExpand: function () {
				document.getElementById(\'additional_options\').value = \'1\';
			},
			aSwappableContainers: [
				\'post_additional_options\',
			],
			aSwapImages: [
				{
					sId: \'postMoreExpandLink\',
					altExpanded: \'-\',
					altCollapsed: \'+\'
				}
			],
			aSwapLinks: [
				{
					sId: \'postMoreExpandLink\',
					msgExpanded: ', JavaScriptEscape($txt['post_additionalopt']), ',
					msgCollapsed: ', JavaScriptEscape($txt['post_additionalopt']), '
				}
			]
		});';

	// Native SMF 2.1 code for showing and hiding saved drafts.
	if (!empty($context['drafts']))
		echo '
		var oSwapDraftOptions = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: true,
			aSwappableContainers: [
				\'post_draft_options\',
			],
			aSwapImages: [
				{
					sId: \'postDraftExpand\',
					altExpanded: \'-\',
					altCollapsed: \'+\'
				}
			],
			aSwapLinks: [
				{
					sId: \'postDraftExpandLink\',
					msgExpanded: ', JavaScriptEscape($txt['draft_hide']), ',
					msgCollapsed: ', JavaScriptEscape($txt['drafts_show']), '
				}
			]
		});';

	// SMF 2.1 preview handler, scoped to this mod's form.
	// Based on smc_preview_post in Themes/default/scripts/script.js.
	echo '
		function mcp_gnt_preview_post(oOptions)
		{
			this.opts = oOptions;
			this.previewXMLSupported = true;
			this.form = document.getElementById(this.opts.sFormContainerID);
			this.init();
		}

		mcp_gnt_preview_post.prototype.init = function ()
		{
			if (!this.form)
				return;

			$(this.form).find("input[name=preview]").on("click", this.doPreviewPost.bind(this));
		};

		mcp_gnt_preview_post.prototype.doPreviewPost = function (event)
		{
			event.preventDefault();

			var form = this.form;
			var editorElement = document.getElementById(this.opts.sPostBoxContainerID);
			var x = [];

			if (!form || !editorElement)
				return false;

			if (!this.previewXMLSupported || !window.XMLHttpRequest)
				return false;

			if ("subject" in form)
				x[x.length] = "subject=" + form.elements["subject"].value.php_to8bit().php_urlencode();

			if ("guestname" in form)
				x[x.length] = "guestname=" + form.elements["guestname"].value.php_to8bit().php_urlencode();

			if ("email" in form)
				x[x.length] = "email=" + form.elements["email"].value.php_to8bit().php_urlencode();

			var editor = sceditor.instance(editorElement);
			var message = "";

			if (editor != undefined && typeof editor.getText().html !== "undefined")
				message = editor.getText().html();
			else if (editor != undefined)
				message = editor.getText();
			else
				message = editorElement.value;

			x[x.length] = "message=" + message.php_to8bit().php_urlencode();

			if (this.opts.sSessionVar in form)
				x[x.length] = this.opts.sSessionVar + "=" + form.elements[this.opts.sSessionVar].value.php_to8bit().php_urlencode();

			if ("icon" in form)
				x[x.length] = "icon=" + form.elements["icon"].value.php_to8bit().php_urlencode();

			if ("ns" in form && form.elements["ns"].checked)
				x[x.length] = "ns=" + form.elements["ns"].value;

			sendXMLDocument(
				smf_prepareScriptUrl(smf_scripturl)
					+ "action=post2;board=" + parseInt(this.opts.iCurrentBoard)
					+ ";preview;xml",
				x.join("&"),
				this.onDocSent.bind(this)
			);

			document.getElementById(this.opts.sPreviewSectionContainerID).style.display = "";
			setInnerHTML(document.getElementById(this.opts.sPreviewSubjectContainerID), this.opts.sTxtPreviewTitle);
			setInnerHTML(document.getElementById(this.opts.sPreviewBodyContainerID), this.opts.sTxtPreviewFetch);

			return false;
		};

		mcp_gnt_preview_post.prototype.onDocSent = function (XMLDoc)
		{
			var form = this.form;

			if (!XMLDoc || !XMLDoc.getElementsByTagName("smf")[0])
			{
				this.previewXMLSupported = false;
				return false;
			}

			var preview = XMLDoc.getElementsByTagName("smf")[0].getElementsByTagName("preview")[0];

			if (!preview)
				return false;

			var subjectNode = preview.getElementsByTagName("subject")[0];
			var bodyNode = preview.getElementsByTagName("body")[0];

			setInnerHTML(
				document.getElementById(this.opts.sPreviewSubjectContainerID),
				subjectNode && subjectNode.firstChild ? subjectNode.firstChild.nodeValue : ""
			);

			var bodyText = "";

			if (bodyNode)
				for (var i = 0, n = bodyNode.childNodes.length; i < n; i++)
					if (bodyNode.childNodes[i].nodeValue != null)
						bodyText += bodyNode.childNodes[i].nodeValue;

			setInnerHTML(document.getElementById(this.opts.sPreviewBodyContainerID), bodyText);

			$("#" + this.opts.sPreviewBodyContainerID + " .smf_select_text").on("click", function(e) {
				e.preventDefault();
				var actOnElement = $(this).attr("data-actonelement");
				return typeof actOnElement !== "undefined" ? smfSelectText(actOnElement, true) : smfSelectText(this);
			});

			document.getElementById(this.opts.sPreviewBodyContainerID).className = "windowbg";

			var errors = XMLDoc.getElementsByTagName("smf")[0].getElementsByTagName("errors")[0];
			var errorList = [];

			if (errors)
			{
				var errorNodes = errors.getElementsByTagName("error");

				for (var i = 0; i < errorNodes.length; i++)
					if (errorNodes[i].firstChild)
						errorList[errorList.length] = errorNodes[i].firstChild.nodeValue;

				document.getElementById(this.opts.sErrorsContainerID).style.display = errorNodes.length == 0 ? "none" : "";
				document.getElementById(this.opts.sErrorsContainerID).className = errors.getAttribute("serious") == 1 ? "errorbox" : "noticebox";
				document.getElementById(this.opts.sErrorsSeriousContainerID).style.display = errorNodes.length == 0 ? "none" : "";
				setInnerHTML(document.getElementById(this.opts.sErrorsListContainerID), errorNodes.length == 0 ? "" : errorList.join("<br>"));

				var captions = errors.getElementsByTagName("caption");

				for (var i = 0; i < captions.length; i++)
				{
					var caption = document.getElementById(this.opts.sCaptionContainerID.replace("%ID%", captions[i].getAttribute("name")));

					if (caption)
						caption.className = captions[i].getAttribute("class");
				}

				var editorElement = document.getElementById(this.opts.sPostBoxContainerID);

				if (errors.getElementsByTagName("post_error").length == 1)
					editorElement.style.border = "1px solid red";
				else if (editorElement.style.borderColor == "red" || editorElement.style.borderColor == "red red red red")
					editorElement.style.border = "";
			}

			location.hash = "#" + this.opts.sPreviewSectionContainerID;

			if (typeof(smf_codeFix) != "undefined")
				smf_codeFix();

			return false;
		};

		var oPreviewPost = new mcp_gnt_preview_post({
			sFormContainerID: "mcp_gnt_postmodify",
			sPreviewSectionContainerID: "mcp_gnt_preview_section",
			sPreviewSubjectContainerID: "mcp_gnt_preview_subject",
			sPreviewBodyContainerID: "mcp_gnt_preview_body",
			sErrorsContainerID: "mcp_gnt_errors",
			sErrorsSeriousContainerID: "mcp_gnt_error_serious",
			sErrorsListContainerID: "mcp_gnt_error_list",
			sCaptionContainerID: "caption_%ID%",
			sPostBoxContainerID: ', JavaScriptEscape($context['mcp_gnt_post_box_name']), ',
			sTxtPreviewTitle: ', JavaScriptEscape($txt['preview_title']), ',
			sTxtPreviewFetch: ', JavaScriptEscape($txt['preview_fetch']), ',
			sSessionVar: ', JavaScriptEscape($context['session_var']), ',
			iCurrentBoard: ', (int) $context['mcp_gnt_default_board'], '
		});

		var oEditorID = "', $context['mcp_gnt_post_box_name'], '";
		var oEditorObject = oEditorHandle_', $context['mcp_gnt_post_box_name'], ';

		function mcpGntBoardChanged()
		{
			var form = document.getElementById("mcp_gnt_postmodify");
			if (form)
				mcpGntSetBoardAction(form);
		}

		function mcpGntSetBoardAction(form)
		{
			var board = document.getElementById("mcp_gnt_board");

			current_board = board && board.value ? parseInt(board.value, 10) : null;

			if (current_board === null)
				return false;

			form.action = ', JavaScriptEscape($scripturl . '?action=post2;board='), ' + current_board + ".0";

			if (typeof oPreviewPost !== "undefined")
				oPreviewPost.opts.iCurrentBoard = current_board;

			return true;
		}

		mcpGntBoardChanged();

		$(document).ready(function () {
			// Keep the unique editor ID, but submit under SMF\'s native field name.
			var mcpGntMessage = document.getElementById("mcp_gnt_message");
			var mcpGntMessageMode = document.getElementById("mcp_gnt_message_mode");

			if (mcpGntMessage)
				mcpGntMessage.name = "message";

			if (mcpGntMessageMode)
				mcpGntMessageMode.name = "message_mode";

			// Keep the viewport width stable when expanding/collapsing the composer.
			document.documentElement.style.overflowY = "scroll";

			// SCEditor has now been initialized by SMF while its container was visible.
			// Apply the configured initial collapsed state only afterwards.
			', $collapsed ? 'mcpGntToggle.changeState(true, true); mcpGntUpdateCollapsedHint();' : 'mcpGntUpdateCollapsedHint();', '
		});

	</script>';
}

function template_mcp_globalnewtopic_below()
{
}


/**
 * Native SMF 2.1 post header renderer, renamed to avoid template_main() collisions.
 */
function mcp_gnt_template_post_header()
{
	global $context, $txt;

	if (empty($context['posting_fields']['subject']) || !is_array($context['posting_fields']['subject']))
	{
		$context['posting_fields']['subject'] = array(
			'label' => array('html' => '<label for="subject" id="caption_subject">' . $txt['subject'] . '</label>'),
			'input' => array('html' => '<input type="text" id="subject" name="subject" value="' . $context['subject'] . '" size="80" maxlength="80" required>')
		);
	}

	echo '
					<dl id="post_header">';

	foreach ($context['posting_fields'] as $pfid => $pf)
	{
		if (empty($pf['label']) || empty($pf['input']))
			continue;

		echo '
						<dt class="clear pf_', $pfid, '">';

		if (!empty($pf['label']['before']))
			echo '
							', $pf['label']['before'];

		if (!empty($pf['label']['html']))
			echo $pf['label']['html'];
		else
			echo '
							<label', ($pf['input']['type'] === 'radio_select' ? '' : ' for="' . (!empty($pf['input']['attributes']['id']) ? $pf['input']['attributes']['id'] : $pfid) . '"'), ' id="caption_', $pfid, '"', !empty($pf['label']['class']) ? ' class="' . $pf['label']['class'] . '"' : '', '>', $pf['label']['text'], '</label>';

		if (!empty($pf['label']['after']))
			echo '
							', $pf['label']['after'];

		echo '
						</dt>
						<dd class="pf_', $pfid, '">';

		if (!empty($pf['input']['before']))
			echo '
							', $pf['input']['before'];

		if (!empty($pf['input']['html']))
		{
			echo $pf['input']['html'];
		}
		elseif (in_array($pf['input']['type'], array('text', 'password', 'color', 'date', 'datetime-local', 'email', 'month', 'number', 'range', 'tel', 'time', 'url', 'week', 'checkbox')))
		{
			echo '
							<input type="', $pf['input']['type'], '"';

			if (empty($pf['input']['attributes']['id']))
				echo ' id="', $pfid, '"';
			if (empty($pf['input']['attributes']['name']))
				echo ' name="', $pfid, '"';

			if (!empty($pf['input']['attributes']) && is_array($pf['input']['attributes']))
			{
				foreach ($pf['input']['attributes'] as $attribute => $value)
				{
					if (is_bool($value))
						echo $value ? ' ' . $attribute : '';
					else
						echo ' ', $attribute, '="', $value, '"';
				}
			}

			echo ' tabindex="', $context['tabindex']++, '">';
		}
		elseif ($pf['input']['type'] === 'textarea')
		{
			echo '
							<textarea';

			if (empty($pf['input']['attributes']['id']))
				echo ' id="', $pfid, '"';
			if (empty($pf['input']['attributes']['name']))
				echo ' name="', $pfid, '"';

			if (!empty($pf['input']['attributes']) && is_array($pf['input']['attributes']))
			{
				foreach ($pf['input']['attributes'] as $attribute => $value)
				{
					if ($attribute === 'value')
						continue;
					elseif (is_bool($value))
						echo $value ? ' ' . $attribute : '';
					else
						echo ' ', $attribute, '="', $value, '"';
				}
			}

			echo ' tabindex="', $context['tabindex']++, '">', !empty($pf['input']['attributes']['value']) ? $pf['input']['attributes']['value'] : '', '</textarea>';
		}
		elseif ($pf['input']['type'] === 'select' && is_array($pf['input']['options']))
		{
			echo '
							<select';

			if (empty($pf['input']['attributes']['id']))
				echo ' id="', $pfid, '"';
			if (empty($pf['input']['attributes']['name']))
				echo ' name="', $pfid, !empty($pf['input']['attributes']['multiple']) ? '[]' : '', '"';

			if (!empty($pf['input']['attributes']) && is_array($pf['input']['attributes']))
			{
				foreach ($pf['input']['attributes'] as $attribute => $value)
				{
					if (is_bool($value))
						echo $value ? ' ' . $attribute : '';
					else
						echo ' ', $attribute, '="', $value, '"';
				}
			}

			echo ' tabindex="', $context['tabindex']++, '">';

			foreach ($pf['input']['options'] as $optlabel => $option)
			{
				if (!empty($option['options']) && is_array($option['options']))
				{
					echo '
								<optgroup';

					if (empty($option['label']))
						echo ' label="', $optlabel, '"';

					foreach ($option as $attribute => $value)
					{
						if ($attribute === 'options')
							continue;
						elseif (is_bool($value))
							echo $value ? ' ' . $attribute : '';
						else
							echo ' ', $attribute, '="', $value, '"';
					}

					echo '>';

					foreach ($option['options'] as $grouped_option)
					{
						echo '
									<option';

						foreach ($grouped_option as $attribute => $value)
						{
							if (is_bool($value))
								echo $value ? ' ' . $attribute : '';
							else
								echo ' ', $attribute, '="', $value, '"';
						}

						echo '>', $grouped_option['label'], '</option>';
					}

					echo '
								</optgroup>';
				}
				else
				{
					echo '
								<option';

					foreach ($option as $attribute => $value)
					{
						if (is_bool($value))
							echo $value ? ' ' . $attribute : '';
						else
							echo ' ', $attribute, '="', $value, '"';
					}

					echo '>', $optlabel, '</option>';
				}
			}

			echo '
							</select>';
		}

		if (!empty($pf['input']['after']))
			echo '
							', $pf['input']['after'];

		echo '
						</dd>';
	}

	echo '
					</dl>';
}

function template_mcp_gnt_admin()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['mcp_gnt_admin_title'], '</h3>
	</div>';

	if (!empty($context['settings_saved']))
		echo '<div class="infobox">', $txt['mcp_gnt_saved'], '</div>';

	echo '
	<div class="windowbg">
		<form action="', $scripturl, '?action=admin;area=modsettings;sa=globalnewtopic" method="post">
			<dl class="settings">
				<dt><strong>', $txt['mcp_gnt_enabled'], '</strong><br><span class="smalltext">', $txt['mcp_gnt_enabled_desc'], '</span></dt>
				<dd><input type="checkbox" name="enabled" value="1"', !empty($context['mcp_gnt_enabled']) ? ' checked' : '', '></dd>
				<dt><strong>', $txt['mcp_gnt_collapsed_default'], '</strong><br><span class="smalltext">', $txt['mcp_gnt_collapsed_default_desc'], '</span></dt>
				<dd><input type="checkbox" name="collapsed_default" value="1"', !empty($context['mcp_gnt_collapsed_default']) ? ' checked' : '', '></dd>
				<dt><strong>', $txt['mcp_gnt_groups'], '</strong><br><span class="smalltext">', $txt['mcp_gnt_groups_desc'], '</span></dt>
				<dd>';

	foreach ($context['mcp_gnt_groups'] as $id => $name)
		echo '<label><input type="checkbox" name="groups[]" value="', htmlspecialchars((string) $id, ENT_QUOTES), '"', in_array((string) $id, $context['mcp_gnt_selected_groups'], true) ? ' checked' : '', '> ', htmlspecialchars($name, ENT_QUOTES), '</label><br>';

	echo '
				</dd>
			</dl>
			<div class="righttext"><input type="submit" name="save" value="', $txt['save'], '" class="button"></div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';
}

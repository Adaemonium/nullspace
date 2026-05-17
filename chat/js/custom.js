/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license Modified MIT License
 * @link https://blueimp.net/ajax/
 */

// Overriding client side functionality:

/*
// Example - Overriding the replaceCustomCommands method:
ajaxChat.replaceCustomCommands = function(text, textParts) {
	return text;
}
 */

ajaxChat.replaceCustomText = function(text) {
	text=text.replace(/fuck/gi, '****');
	text=text.replace(/shit/gi, '****');
	return text;
}

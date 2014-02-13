/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

        config.toolbar = [
        ['Source', '-', 'NewPage', 'Preview', '-', 'Templates' ],
        [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
        ['Find','Replace', 'SelectAll'],
        ['Bold','Italic','Underline','Strike','Subscript','Superscript','RemoveFormat'],
        ['Maximize', 'ShowBlocks'],
        '/',
        ['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Anchor'],
        ['Image','Table','HorizontalRule','SpecialChar','PageBreak'],
//        ['Styles','Format','Font'],
        ['Format','Font'],
        ['TextColor','BackColor']
    ];
};

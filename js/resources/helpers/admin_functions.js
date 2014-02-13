function show_success(message) {
    jSuccess(message, notify_param());
}
function show_error(message) {
    if (typeof(message) == 'undefined') {
        message = 'Unknown error occured'
    }
    jError(message, notify_param());
}
function notify_param() {
    return {
        autoHide: true,
        TimeShown: 3000,
        HorizontalPosition: 'left',
        VerticalPosition: 'bottom',
        ShowOverlay: false
    };
}

function get_current_date() {
    var date = new Date();
    return date.getFullYear() + '-' +
            (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1) + '-' +
            (date.getDate() < 10 ? '0' : '') + date.getDate();

}
function get_current_time() {
    var date = new Date();
    var minutes = date.getMinutes();
    if (minutes < 10)
        minutes = "0" + minutes
    var hours = date.getUTCHours();
    if (hours < 10)
        hours = "0" + hours
    return hours + ':' + minutes
}

function loadWindow(id, windowOption, title, content) {

    var modalHtml = $.View(base_url + 'js/resources/helpers/views/modal.tmpl', {
        id: id,
        title: title,
        content: content
    });

    $('body').append(modalHtml);

    if (windowOption.width) {
        $('#' + id + '_window').find('.modal-dialog').css('width', windowOption.width);
    }

    $('#' + id + '_window').modal();

    $('#' + id + '_window').on('hidden.bs.modal', function() {
        $('#' + id + '_window').remove();
    });

}

function date_picker(selector) {
    $(selector).datetimepicker({
        pickTime: false,
        format: 'dd.MM.yyyy hh:mm:ss'
    });
}
function time_picker(selector) {
    $(selector).datetimepicker({
        pickDate: false
    });
}
function date_time_picker(selector) {
    $(selector).datetimepicker({
         format: 'dd.MM.yyyy hh:mm:ss'
    });
}


function imperavi(selector) {
    selector.ckeditor({
        //        filebrowserBrowseUrl :'js/ckeditor/filemanager/browser/default/browser.html?Connector='+base_url+'filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
        filebrowserImageBrowseUrl: 'js/ckeditor/filemanager/browser/default/browser.html?Type=Image&Connector=' + base_url + 'filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
        //        filebrowserFlashBrowseUrl :'js/ckeditor/filemanager/browser/default/browser.html?Type=Flash&Connector='+base_url+'filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
        //        filebrowserUploadUrl  :base_url+'filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=File',
        filebrowserImageUploadUrl: base_url + 'js/resources/plugins/ckeditor/server/upload.php'
                //        filebrowserFlashUploadUrl : base_url+'filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=Flash'
    });
}

function showPreload() {
    if (!$('#preLoad').is(':visible'))
        $('#preLoad').stop(true, true).fadeIn(200);
}
function showCompPreload() {
    if (!$('#preLoadComp').is(':visible'))
        $('#preLoadComp').stop(true, true).fadeIn(200);
}
function hidePreload() {
    if ($('#preLoad').is(':visible'))
        $('#preLoad').stop(true, true).fadeOut(200);
}
function hideCompPreload() {
    if ($('#preLoadComp').is(':visible'))
        $('#preLoadComp').stop(true, true).fadeOut(200);
}
function componentLoaded(element) {
    if (element.data("display") == 'block') {
        OpenAjax.hub.publish('page.loaded');
        OpenAjax.hub.publish('component.loaded');
    }
}

function findWindowWidth() {
    var window_width = (window.innerWidth ? window.innerWidth : (document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.offsetWidth));

    return parseInt((window_width - 60) * 2 / 3);
}

$.processTimestamp = function(timestamp) {
    var date = new Date(timestamp * 1000);
    return (date.getDate() < 10 ? '0' : '') + date.getDate()+'.'+
           (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1) + '.' +
           date.getFullYear();
};

jQuery.validator.addMethod('regexp',
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input.");

jQuery.validator.addMethod('unique',
        function(value, element) {
            if (value.length !== 0) {
                var result = $.ajax({
                    async: false,
                    url: base_url + "admin/links/check_link",
                    data: 'link=' + value + '&link_id=' + $(element).prev().val() + '&language_id=' + $(element).parents('form').find('.language_id').val(),
                    type: 'post',
                    dataType: "text"
                });

                if (parseInt(result.responseText) === 1) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }

        }, "This url has already been  registered");

$.validator.addMethod('unique_email',
    function(value, element) {
        if (value.length !== 0){
            var result = $.ajax({
                async:false,
                url: base_url+"admin/users/check_email",
                data:'email='+$(element).val()+'&user_id='+$(element).parents('form').find('.user_id').val(),
                type: 'post',
                dataType: "text"
            });

            if(parseInt(result.responseText) === 1){
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }

}, "This email has already been  registered");

function tab_navigation(el, btn_class, active_class, wrap, current, block, mega_wrapper, href) {

    if (href === undefined)
        href = $(el).find('a').attr('href');

    if (!$(el).hasClass(active_class)) {

        $(mega_wrapper + ' .' + btn_class).removeClass(active_class);
        $(el).addClass(active_class);

        if ($(mega_wrapper + ' ' + wrap).find('.' + current).length) {
            $(mega_wrapper + ' ' + wrap).find('.' + current).fadeOut(200, function() {
                $(mega_wrapper + ' .' + block).removeClass(current);

                $(mega_wrapper + ' #' + href).fadeIn(200, function() {

                    $(mega_wrapper + ' #' + href).addClass(current);
                });
            });
        } else {
            $(mega_wrapper + ' #' + href).fadeIn(200, function() {
                $(mega_wrapper + ' #' + href).addClass(current);
            });
        }
    }
}

function getPref() {
    return pref;
}

function dataTableBootstrap() {


/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          oSettings._iDisplayLength === -1 ?
			0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    oSettings._iDisplayLength === -1 ?
			0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
};

/* Bootstrap style pagination control */
    $.extend($.fn.dataTableExt.oPagination, {
        "bootstrap": {
            "fnInit": function(oSettings, nPaging, fnDraw) {
                var oLang = oSettings.oLanguage.oPaginate;
                var fnClickHandler = function(e) {
                    e.preventDefault();
                    if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                        fnDraw(oSettings);
                    }
                };

                $(nPaging).addClass('pagination').append(
                        '<ul class="pagination">' +
                        '<li class="prev disabled"><a href="#">&larr; ' + oLang.sPrevious + '</a></li>' +
                        '<li class="next disabled"><a href="#">' + oLang.sNext + ' &rarr; </a></li>' +
                        '</ul>'
                        );
                var els = $('a', nPaging);
                $(els[0]).bind('click.DT', {action: "previous"}, fnClickHandler);
                $(els[1]).bind('click.DT', {action: "next"}, fnClickHandler);
            },
            "fnUpdate": function(oSettings, fnDraw) {
                var iListLength = 5;
                var oPaging = oSettings.oInstance.fnPagingInfo();
                var an = oSettings.aanFeatures.p;
                var i, ien, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

                if (oPaging.iTotalPages < iListLength) {
                    iStart = 1;
                    iEnd = oPaging.iTotalPages;
                }
                else if (oPaging.iPage <= iHalf) {
                    iStart = 1;
                    iEnd = iListLength;
                } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                    iStart = oPaging.iTotalPages - iListLength + 1;
                    iEnd = oPaging.iTotalPages;
                } else {
                    iStart = oPaging.iPage - iHalf + 1;
                    iEnd = iStart + iListLength - 1;
                }

                for (i = 0, ien = an.length; i < ien; i++) {
                    // Remove the middle elements
                    $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                    // Add the new list items and their event handlers
                    for (j = iStart; j <= iEnd; j++) {
                        sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                        $('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                                .insertBefore($('li:last', an[i])[0])
                                .bind('click', function(e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
                            fnDraw(oSettings);
                        });
                    }

                    // Add / remove disabled classes from the static elements
                    if (oPaging.iPage === 0) {
                        $('li:first', an[i]).addClass('disabled');
                    } else {
                        $('li:first', an[i]).removeClass('disabled');
                    }

                    if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                        $('li:last', an[i]).addClass('disabled');
                    } else {
                        $('li:last', an[i]).removeClass('disabled');
                    }
                }
            }
        }
    });

/*
 * TableTools Bootstrap compatibility
 * Required TableTools 2.1+
 */
if ( $.fn.DataTable.TableTools ) {
	// Set the classes that TableTools uses to something suitable for Bootstrap
	$.extend( true, $.fn.DataTable.TableTools.classes, {
		"container": "DTTT btn-group",
		"buttons": {
			"normal": "btn",
			"disabled": "disabled"
		},
		"collection": {
			"container": "DTTT_dropdown dropdown-menu",
			"buttons": {
				"normal": "",
				"disabled": "disabled"
			}
		},
		"print": {
			"info": "DTTT_print_info modal"
		},
		"select": {
			"row": "active"
		}
	} );

	// Have the collection use a bootstrap compatible dropdown
	$.extend( true, $.fn.DataTable.TableTools.DEFAULTS.oTags, {
		"collection": {
			"container": "ul",
			"button": "li",
			"liner": "a"
		}
	} );
}

}
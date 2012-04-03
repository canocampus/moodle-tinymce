<?php

require_once('../../../config.php');

global $CFG; 

$lastmodified = filemtime(__FILE__);
$lifetime = 1800;

header("Content-type: application/x-javascript; charset: utf-8");  // Correct MIME type
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
header("Cache-control: max_age = $lifetime");
header("Pragma: ");

define('MENU_SEPARATOR', '|');

$courseid = optional_param('id', 0, PARAM_INT);

$theme_advanced_buttons = array(1 => array('bold', 'italic', 'underline', 'strikethrough', MENU_SEPARATOR, 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', MENU_SEPARATOR, 'formatselect', 'fontselect', 'fontsizeselect', MENU_SEPARATOR, 'undo', 'redo'),
                                2 => array('cut', 'copy', 'paste', 'pastetext', 'pasteword', MENU_SEPARATOR, 'search', 'replace', MENU_SEPARATOR, 'bullist', 'numlist', MENU_SEPARATOR, 'outdent', 'indent', 'blockquote', MENU_SEPARATOR, 'link', 'unlink', 'anchor', 'image', 'cleanup', 'help', 'code', MENU_SEPARATOR, 'insertdate', 'inserttime', 'preview', MENU_SEPARATOR, 'forecolor', 'backcolor'),
                                3 => array('tablecontrols', MENU_SEPARATOR, 'hr', 'removeformat', 'visualaid', MENU_SEPARATOR, 'sub', 'sup', MENU_SEPARATOR, 'charmap', 'moodlesmileys', 'media', MENU_SEPARATOR, 'ltr', 'rtl', MENU_SEPARATOR, 'fullscreen'));

$tinymceplugins = array('safari', 
                        'pagebreak', 
                        'layer', 
                        'table', 
                        'moodlesmileys', 
                        'insertdatetime', 
                        'preview', 
                        'media', 
                        'searchreplace', 
                        'print', 
                        'contextmenu', 
                        'paste', 
                        'directionality', 
                        'fullscreen', 
                        'noneditable', 
                        'visualchars', 
                        'nonbreaking', 
                        'xhtmlxtras', 
                        'template', 
                        'inlinepopups');

// Enable use of the dragmath plugin if it has been included in this installation.
if (array_search('dragmath', $tinymceplugins) === false && 
    file_exists("{$CFG->dirroot}/lib/editor/tinymce/jscripts/tiny_mce/plugins/dragmath/editor_plugin.js")) {
    $theme_advanced_buttons[1][] = MENU_SEPARATOR;
    $theme_advanced_buttons[1][] = 'dragmath';
    $tinymceplugins[] = 'dragmath';
}

// setup coversion table for swapping HTMLarea controls to TinyMCE controls
$tinymcebuttonmap = array('fontname'             => 'fontselect', 
                          'fontsize'             => 'fontsizeselect', 
                          'formatblock'          => 'blockquote', 
                          'subscript'            => 'sub', 
                          'superscript'          => 'sup', 
                          'clean'                => 'cleanup', 
                          'lefttoright'          => 'ltr', 
                          'righttoleft'          => 'rtl', 
                          'insertorderedlist'    => 'numlist', 
                          'insertunorderedlist'  => 'bullist', 
//                          'hilitecolor'         => '', 
                          'inserthorizontalrule' => 'hr', 
                          'createanchor'         => 'anchor', 
                          'createlink'           => 'link', 
                          'insertimage'          => 'image', 
                          'inserttable'          => 'tablecontrols', 
                          'insertsmile'          => 'moodlesmileys', 
                          'insertchar'           => 'charmap', 
                          'spellcheck'           => 'spellchecker', 
                          'htmlmode'             => 'code', 
                          'popupeditor'          => 'fullscreen', 
                          'search_replace'       => 'search,replace');

//setup array of buttons to hide and convert them to TinyMCE if needed.
$hidebuttons =  array();
if(!empty($CFG->editorhidebuttons)) {
    foreach (explode(' ', $CFG->editorhidebuttons) as $index => $button) {
        $hidebuttons[] = array_key_exists($button, $tinymcebuttonmap) ? $tinymcebuttonmap[$button] : $button;
    }
}

// remove blocked controls
if(count($hidebuttons) > 0) {
    foreach($theme_advanced_buttons as $themeindex => $array) {
        $new_array = array();

        //prevent first menu item from being separator also prevent to separators in a row
        $last = MENU_SEPARATOR;
        foreach($array as $index => $icon) {
            if(!in_array($icon, $hidebuttons) && $last != $icon) {
                $new_array[] = $icon;
                $last = $icon;
            }
        }
        // if the last entry is a spacer remove it
        if($last == MENU_SEPARATOR) {
            array_pop($new_array);
        }

        $theme_advanced_buttons[$themeindex] = implode(',', $new_array);
    }
} else {
    foreach($theme_advanced_buttons as $themeindex => $array) {
        $theme_advanced_buttons[$themeindex] = implode(',', $array);
    }
}

// this doesn't seem to work
$editorbackgroundcolor = '#FFFFFF';
if(!empty($CFG->editorbackgroundcolor)) {
    $editorbackgroundcolor = $CFG->editorbackgroundcolor;
}

?>

/**
    Head JS     The only script in your <HEAD>
    Copyright   Tero Piirainen (tipiirai)
    License     MIT / http://bit.ly/mit-license
    Version     0.96

    http://headjs.com
*/
(function(a){function z(){d||(d=!0,s(e,function(a){p(a)}))}function y(c,d){var e=a.createElement("script");e.type="text/"+(c.type||"javascript"),e.src=c.src||c,e.async=!1,e.onreadystatechange=e.onload=function(){var a=e.readyState;!d.done&&(!a||/loaded|complete/.test(a))&&(d.done=!0,d())},(a.body||b).appendChild(e)}function x(a,b){if(a.state==o)return b&&b();if(a.state==n)return k.ready(a.name,b);if(a.state==m)return a.onpreload.push(function(){x(a,b)});a.state=n,y(a.url,function(){a.state=o,b&&b(),s(g[a.name],function(a){p(a)}),u()&&d&&s(g.ALL,function(a){p(a)})})}function w(a,b){a.state===undefined&&(a.state=m,a.onpreload=[],y({src:a.url,type:"cache"},function(){v(a)}))}function v(a){a.state=l,s(a.onpreload,function(a){a.call()})}function u(a){a=a||h;var b;for(var c in a){if(a.hasOwnProperty(c)&&a[c].state!=o)return!1;b=!0}return b}function t(a){return Object.prototype.toString.call(a)=="[object Function]"}function s(a,b){if(!!a){typeof a=="object"&&(a=[].slice.call(a));for(var c=0;c<a.length;c++)b.call(a,a[c],c)}}function r(a){var b;if(typeof a=="object")for(var c in a)a[c]&&(b={name:c,url:a[c]});else b={name:q(a),url:a};var d=h[b.name];if(d&&d.url===b.url)return d;h[b.name]=b;return b}function q(a){var b=a.split("/"),c=b[b.length-1],d=c.indexOf("?");return d!=-1?c.substring(0,d):c}function p(a){a._done||(a(),a._done=1)}var b=a.documentElement,c,d,e=[],f=[],g={},h={},i=a.createElement("script").async===!0||"MozAppearance"in a.documentElement.style||window.opera,j=window.head_conf&&head_conf.head||"head",k=window[j]=window[j]||function(){k.ready.apply(null,arguments)},l=1,m=2,n=3,o=4;i?k.js=function(){var a=arguments,b=a[a.length-1],c={};t(b)||(b=null),s(a,function(d,e){d!=b&&(d=r(d),c[d.name]=d,x(d,b&&e==a.length-2?function(){u(c)&&p(b)}:null))});return k}:k.js=function(){var a=arguments,b=[].slice.call(a,1),d=b[0];if(!c){f.push(function(){k.js.apply(null,a)});return k}d?(s(b,function(a){t(a)||w(r(a))}),x(r(a[0]),t(d)?d:function(){k.js.apply(null,b)})):x(r(a[0]));return k},k.ready=function(b,c){if(b==a){d?p(c):e.push(c);return k}t(b)&&(c=b,b="ALL");if(typeof b!="string"||!t(c))return k;var f=h[b];if(f&&f.state==o||b=="ALL"&&u()&&d){p(c);return k}var i=g[b];i?i.push(c):i=g[b]=[c];return k},k.ready(a,function(){u()&&s(g.ALL,function(a){p(a)}),k.feature&&k.feature("domloaded",!0)});if(window.addEventListener)a.addEventListener("DOMContentLoaded",z,!1),window.addEventListener("load",z,!1);else if(window.attachEvent){a.attachEvent("onreadystatechange",function(){a.readyState==="complete"&&z()});var A=1;try{A=window.frameElement}catch(B){}!A&&b.doScroll&&function(){try{b.doScroll("left"),z()}catch(a){setTimeout(arguments.callee,1);return}}(),window.attachEvent("onload",z)}!a.readyState&&a.addEventListener&&(a.readyState="loading",a.addEventListener("DOMContentLoaded",handler=function(){a.removeEventListener("DOMContentLoaded",handler,!1),a.readyState="complete"},!1)),setTimeout(function(){c=!0,s(f,function(a){a()})},300)})(document)

HTMLArea = function() {
    if (arguments.length == 0) {
        return;
    }

    this.id = arguments[0];

    // Default config (a replica of the HTMLArea config object).
    this.config = {
        pageStyle : 'body { background-color: #ffffff; }', 
        killWordOnPaste : true, 
        fontname : {
            // This is the default font list from HtmlArea.Config.
            'Arial'           : 'arial,helvetica,sans-serif',
            'Courier New'     : 'courier new,courier,monospace',
            'Georgia'         : 'georgia,times new roman,times,serif',
            'Tahoma'          : 'tahoma,arial,helvetica,sans-serif',
            'Times New Roman' : 'times new roman,times,serif',
            'Verdana'         : 'verdana,arial,helvetica,sans-serif',
            'Impact'          : 'impact',
            'WingDings'       : 'wingdings'
        }, 
        fontsize : {
            // This is the default font size list from HtmlArea.Config.
            '1 (8 pt)'  : '8pt', 
            '2 (10 pt)' : '10pt', 
            '3 (12 pt)' : '12pt', 
            '4 (14 pt)' : '14pt', 
            '5 (18 pt)' : '18pt', 
            '6 (24 pt)' : '24pt', 
            '7 (36 pt)' : '36pt'
        }
    };

    // Merge in supplied config if provided with one through the constructor.
    // Otherwise the config will be set by manipulating this.config.
    if (arguments.length > 1) {
        config = this._mergeObjects(config, arguments[1]);
    }
};

// A really really basic merge op to merge one object into another.
HTMLArea._mergeObjects = function(dest, source) {
    for (var key in source) {
        dest[key] = source[key];
    }

    return dest;
};

HTMLArea.tinymceloaded = false;
HTMLArea._loadTinyMCE = function(cb) {
	if (this.tinymceloaded) {
		cb();
	} else {
		head.js('<?php echo "{$CFG->httpswwwroot}/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js"; ?>', function() {
			this.tinymceloaded = true;
			cb();
		});
	}
}

// Compile a list of items into a string that may be passed to theme_avanced_fonts or theme_advanced_font_sizes.
HTMLArea._compileFontList = function(list) {
    // http://tinymce.moxiecode.com/wiki.php/Configuration:theme_advanced_fonts
    // http://tinymce.moxiecode.com/wiki.php/Configuration:theme_advanced_font_sizes
    var results = [];

    for (var item in list) {
        results.push(item + '=' + list[item]);
    }

    return results.join(';');
};

// Construct a configuration object for TinyMCE.
HTMLArea._buildTinymceConfig = function(areaConfig, overrides) {
	areaConfig = areaConfig || config;
    var editorConfig = {
        // Plugins
        plugins : "<?php echo implode(',', $tinymceplugins); ?>", 
        // General config
        convert_urls : false, 
        add_form_submit_trigger : true, 
        add_unload_trigger : true, 
        // HTMLArea did not encode many entites.  Default to 'raw' entity encoding.
        // 'raw' entity encoding will try to include the additional apos entity, no nice way to stop that however.
        entity_encoding: '<?php echo !empty($CFG->tinymce_entity_encoding) ? addslashes($CFG->tinymce_entity_encoding) : 'raw'; ?>',
		// No inline styles
		apply_source_formatting : false,
		cleanup : false, 
		verify_html : false,
		verify_css_classes : false,
		fix_list_elements : true,
<?php if (!empty($courseid)) { ?>
        // Browser
        file_browser_callback : 'moodleFileBrowser',
<?php } ?>
        // Theme
        theme : "advanced", 
        theme_advanced_buttons1 : "<?php echo $theme_advanced_buttons[1] ?>", 
        theme_advanced_buttons2 : "<?php echo $theme_advanced_buttons[2] ?>", 
        theme_advanced_buttons3 : "<?php echo $theme_advanced_buttons[3] ?>", 
        theme_advanced_toolbar_location : "top", 
        theme_advanced_toolbar_align : "left", 
        theme_advanced_statusbar_location : "bottom", 
        theme_advanced_resizing : true,
        theme_advanced_fonts : this._compileFontList(areaConfig.fontname),
        theme_advanced_font_sizes : this._compileFontList(areaConfig.fontsize)
    };

    // Merge with an object that may have been passed as an argument.
    // This should be used to set mode and elements.
    if (overrides) {
        editorConfig = this._mergeObjects(editorConfig, overrides);
    }

    if (areaConfig.killWordOnPaste) {
        editorConfig = this._mergeObjects(editorConfig, {
            // Be extremely aggressive when stripping out the word formatting.
            paste_create_paragraphs : true, 
            paste_auto_cleanup_on_paste : true, 
            paste_convert_middot_lists : true, 
            paste_convert_headers_to_strong : true, 
            paste_strip_class_attributes : 'all', 
            paste_retain_style_properties : 'none', 
            paste_postprocess : function(pl, o) { 
                tinymce.each(tinyMCE.activeEditor.dom.select('b', o.node), function(node) { 
                    tinyMCE.activeEditor.dom.rename(node, 'strong'); 
                }); 
            }
        });
    }

    return editorConfig;
};

// Initialize an instance of TinyMCE given a config.
HTMLArea._tinymceinit = function(editorConfig) {
	HTMLArea._loadTinyMCE(function() {
		tinyMCE.init(editorConfig);
		HTMLArea._hookSubmit();
	});
};

HTMLArea.prototype.generate = function() {
    var editorConfig = HTMLArea._buildTinymceConfig(this.config, { 
        mode     : 'exact', 
        elements : this.id 
    });

    HTMLArea._tinymceinit(editorConfig);
};

HTMLArea.replaceAll = function(areaConfig) {
    var editorConfig = this._buildTinymceConfig(areaConfig, { 
        mode : 'textareas'
    });

    this._tinymceinit(editorConfig);
};

HTMLArea._hasHookedSubmit = false;
HTMLArea._hookSubmit = function() {
	if (HTMLArea._hasHookedSubmit) {
		return;
	}
	
	tinymce.dom.Event.add(window, 'load', function() {
		var domutils = new tinymce.dom.DOMUtils(document);
		tinymce.each(domutils.select('input[type=\'submit\']'), function(button) {
			tinymce.dom.Event.add(button, 'click', function() {
				tinyMCE.triggerSave();
			});
		});
    });

	HTMLArea._hasHookedSubmit = true;
};
 
function moodleFileBrowser (field_name, url, type, win) {
    var cmsURL = '', 
        width = 0, 
        height = 0;

    switch(type) {
        default:
        case 'file':
        case 'media':
            cmsURL = '<?php echo "{$CFG->httpswwwroot}/lib/editor/tinymce/link.php?id={$courseid}"; ?>';
            width = 480;
            height = 400;
            break;
        case 'image':
            cmsURL = '<?php echo "{$CFG->httpswwwroot}/lib/editor/tinymce/insert_image.php?id={$courseid}"; ?>';
            width = 736;
            height = 430;
            break;
    }

    tinyMCE.activeEditor.windowManager.open({
        file           : cmsURL,
        width          : width,  
        height         : height,
        resizable      : 'yes',
        inline         : 'yes',  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : 'no'
    }, {
        window         : win,
        input          : field_name
    });

    return false;
}
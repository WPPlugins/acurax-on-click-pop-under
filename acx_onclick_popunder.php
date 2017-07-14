<?php
/* 
Plugin Name: Acurax On Click PopUnder
Plugin URI: http://www.acurax.com/Products/acurax-click-pop-plugin-wordpress/
Description: The Best Pop Under Plugin which helps you to show pop under on visitors browser on click.Plugin helps you to configure multiple URL'S. Plugin will set cookie on visitors browser when popunder appear and so it will show only once. You can also configure the cookie timeout in plugin settings.
Author: Acurax 
Version: 2.2.1
Author URI: http://www.acurax.com 
License: GPLv2 or later
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/ 
?>
<?php
define("ACURAX_POPUNDER_VERSION_P","2.2.1");
$url_array=get_option('acurax_popunder_array');
$url=get_option('acurax_popunder_url');
if($url_array != "")
{	if(is_serialized( $url_array))
	{ 
		$url_array = unserialize($url_array); 
	}
} else
{
	$url_array = array();
}
if(is_array($url_array))
{	$url_array = array_filter($url_array);
	if (empty($url_array)) 
	{
		if($url != "")
		{
			if(preg_match("/^(http)s?:\/\//", $url))
			{
				$url_array = array(esc_url_raw($url));
				if(!is_serialized( $url_array))
				{ 
					$url_array = serialize($url_array); 
				}
				update_option('acurax_popunder_array',$url_array);
			}
		}
	}
}
//*************** Include JS in Header ********
function enqueue_acx_popunder_script()
{	global $options;
	$url_array=get_option('acurax_popunder_array');
	
	if(is_serialized( $url_array))
	{ 
		$url_array = unserialize($url_array); 
	}
	if($url_array != "" && !is_array($url_array))
	{
		$url_array = array();
	}
	if(is_array($url_array))
	{
		$url_array = array_filter($url_array);
		if (!empty($url_array))
		{
			$url2 = array_rand($url_array,1);
			$url_array=$url_array[$url2];
		}
		else
		{
		$url_array='';
		}
	}
	else
	{
		$url_array='';
	}
	$acurax_time_out=get_option('acurax_popunder_timeout');
	if($acurax_time_out == "" || !is_numeric($acurax_time_out))
	{
		$acurax_time_out = 60;
	}
	
	if ($url_array != "") 
	{
	?>
	<script type="text/javascript">
	(function (jQuery) {
    jQuery.popunder = function (sUrl, width, height) {
        var bSimple = jQuery.browser.msie,
                run = function () {
                    jQuery.popunderHelper.open(sUrl, bSimple, width, height);
                };
        (bSimple) ? run() : window.setTimeout(run, 1);
        return jQuery;
    };
    jQuery.popunderHelper = {
        open: function (sUrl, bSimple, width, height) {
         var _parent = (top != self && typeof (top.document.location.toString()) === 'string') ? top : self,
                     sToolbar = (!jQuery.browser.webkit && (!jQuery.browser.mozilla || parseInt(jQuery.browser.version, 10) < 12)) ? 'yes' : 'no',
                    sOptions,
                    popunder; 
            if (top != self) {
                try {
                    if (top.document.location.toString()) {
                        _parent = top;
                    }
                }
                catch (err) { }
            }
            /* popunder options */
			sOptions = 'toolbar=' + sToolbar + ',scrollbars=1,location=1,menubar=0,resizable=1,width='+width;
            sOptions += ',height=' +height+ ',screenX=0';
            sOptions += ',screenY=0';
            sOptions += ',left=0,top=0';
            /* create pop-up from parent context */
            popunder = _parent.window.open(sUrl, '_blank', sOptions);
            if (popunder) {

                popunder.blur();
                if (bSimple) {
                    /* classic popunder, used for ie*/
                    window.focus();
                    try { opener.window.focus(); }
                    catch (err) { }

                }
                else {
					   /* window.setTimeout(function () {
							popunder.init(popunder);
													}, 0); */

                    /* popunder for e.g. ff4+, chrome */
                    popunder.init = function (e) {
                        with (e) {
                            (function () {
                                if (typeof window.mozPaintCount != 'undefined' || typeof navigator.webkitGetUserMedia === "function") {
                                    var x = window.open('about:blank');
                                    x.close();
                                    //opener.alert(opener.location);
                                }
                                //_parent.focus();
                                try { opener.window.focus(); }
                                catch (err) { }
                            })();
                        }
                    };
                    popunder.params = {
                        url: sUrl
                    };
                    popunder.init(popunder);
                }
            }
            return true;
        }
    };
})(jQuery);
	var acx_today = new Date();
	expires_date = new Date(acx_today.getTime() + (<?php echo $acurax_time_out;?> * 60 * 1000));
	if (navigator.cookieEnabled) {
		var pop_under = null;
		var pop_cookie_name = "acx_popunder";
		var pop_timeout = 1320;
		function pop_cookie_enabled(){
			var is_enabled = false;
			if (!window.opera && !navigator.cookieEnabled)return is_enabled;
			if (typeof document.cookie == 'string')if (document.cookie.length == 0){document.cookie = "test";is_enabled = document.cookie == 'test';
			document.cookie = '';}
			else{
			is_enabled = true;}
			return is_enabled;
		}
		function pop_getCookie(name){
			var cookie = " " + document.cookie;var search = " " + name + "=";
			var setStr = null;
			var offset = 0;
			var end = 0;
			if (cookie.length > 0){
			offset = cookie.indexOf(search);
			if (offset != -1){
			offset += search.length;
			end = cookie.indexOf(";", offset);
			if (end == -1){end = cookie.length;}
			setStr = unescape(cookie.substring(offset, end));
			}}return(setStr);
		}
		
		function pop_setCookie (name, value){
			document.cookie = name + "=" + escape(value) + "; expires=" + expires_date.toGMTString() + "; path=/;";
		}
		
		function show_pop(){
			var pop_wnd = "<?php echo esc_url($url_array);; ?>";
			var need_open = true;
			if (document.onclick_copy != null)document.onclick_copy();
			if (document.body.onbeforeunload_copy != null)document.body.onbeforeunload_copy();
			if (pop_under != null){
				if (!pop_under.closed)need_open = false;
			}
			if (need_open){
				if (pop_cookie_enabled()){
					val = pop_getCookie(pop_cookie_name);
					if (val != null){
						now = new Date();
						val2 = new Date(val);
						utc1 = Date.UTC(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), now.getMinutes(), now.getSeconds());
						utc2 = Date.UTC(val2.getFullYear(), val2.getMonth(), val2.getDate(), val2.getHours(), val2.getMinutes(), val2.getSeconds());
						if ((utc1 - utc2)/1000 < pop_timeout*60)
						{
						need_open = false;
						}
					}
				}
			}
			
			if (need_open){
				jQuery.popunder(pop_wnd,700,700);  
				 
				if (pop_cookie_enabled()){
					now = new Date();
					pop_setCookie(pop_cookie_name, now);
				}
			}
		}
		
		function pop_init(){
			var ver = parseFloat(navigator.appVersion);
			var ver2 = (navigator.userAgent.indexOf("Windows 95")>=0 || navigator.userAgent.indexOf("Windows 98")>=0 || navigator.userAgent.indexOf("Windows NT")>=0 )&&(navigator.userAgent.indexOf('Opera') == -1)&&(navigator.appName != 'Netscape') &&(navigator.userAgent.indexOf('MSIE') > -1) &&(navigator.userAgent.indexOf('SV1') > -1) &&(ver >= 4);
			if (ver2){
				if (document.links){
					for (var i=0; i < document.links.length; i++){
						if (document.links[i].target != "_blank"){
							document.links[i].onclick_copy = document.links[i].onclick;document.links[i].onclick = show_pop;
						}
					}
				}
			}
			document.onclick_copy = document.onclick;document.onmouseup = show_pop;
			return true;
			}
		
		pop_init();
	}
	</script>
<?php }
} add_action( 'get_footer', 'enqueue_acx_popunder_script' );
//*************** Include JS in Header Ends Here ********
//*************** Admin function ***************
include("functions.php");
function acx_onclick_popunder_admin() {
	include('acx_onclick_popunder_admin.php');
}
function acx_onclick_popunder_misc() 
{
	include('acx_onclick_popunder_misc.php');
}
function acx_onclick_popunder_admin_actions()
{
	add_menu_page(  'PopUnder', 'PopUnder','manage_options', 'Acurax-onclick-popunder-Settings','acx_onclick_popunder_admin',plugin_dir_url( __FILE__ ).'/images/admin.png' ); // manage_options for admin
	add_submenu_page('Acurax-onclick-popunder-Settings', 'Acurax Onclick Popunder Misc Settings', 'Misc', 'manage_options', 'Acurax-Onclick-Popunder-Misc' ,'acx_onclick_popunder_misc');
}
if ( is_admin() )
{
	add_action('admin_menu', 'acx_onclick_popunder_admin_actions');
}
?>
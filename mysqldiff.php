<?php
/*
*	Mysqldiff — Identify Differences Among Database Objects
*   Version 1.0
*	Copyright (c) 2018 http://www.ampnmp.com All rights reserved.
*	Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
*	Author: ampnmp.com <admin@ampnmp.com>, https://github.com/ampnmp/mysqldiff
*/
error_reporting(E_ERROR);  //E_ALL & ~E_NOTICE
array_map('addslashes', $_POST);
$account_history = $_COOKIE["account_history"];
$arr_account = json_decode($account_history, true);
if( !is_array($arr_account) ){
    $arr_account['db1'] = array();
    $arr_account['db2'] = array();
    //array_push($arr_account['db1'], array('host'=>'','username'=>'','password'=>''));
    //array_push($arr_account['db2'], array('host'=>'','username'=>'','password'=>''));
}
$act = $_REQUEST['act'];
if($act=='getDatabases'){      
    getDatabases();
} else if($act=='dbCompare'){
    dbCompare();
} else if($act=='getAccounts'){
    getAccounts();
} else if($act=='clearAccounts'){
    clearAccounts();
}
$arr1 = array_pop($arr_account['db1']);
$arr2 = array_pop($arr_account['db2']);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mysqldiff</title>
<style type="text/css">
    html,body{ margin: 0px; padding: 0px; height: 100%; -webkit-text-size-adjust:none; font-size:100%; font-family: segoe-ui_normal,Segoe UI,Segoe,Segoe WP,Helvetica Neue,Helvetica,sans-serif; }
    #header{ height: 50px; border-bottom:#6392B5 solid 1px; background-color:#4479A1; box-shadow: 0px 1px 1px #888888; color: #F3EADB; font-size: 24px; }
	#header h1{
		margin: 0px; padding: 6px 0px 0px 60px; font-size: 26px;
		background-repeat: no-repeat;
		background-position: 15px 10px;
		background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAALFSURBVFhH7Zbvh2JhFMf3jxsiEhERY1hiiBXDmiiJTVummdRGulR2GkZMZbedfkyabrMlsiQyJBKJK/oTvnvu3Wambk+3u80082ZefLjPPafu9znnOec8H/aOk3hL3gWsCjhpwZLqwhKrQuuU2XaATEAT9toMweIIAX4G7rcA9wUPzZLPyyIT0IarPoWDS9NzFsaLEUKNGaL8FIHSCI5MF/un2QX/57OSAt35CJH6CBbf/N2XEvSBCoyxHhw3U3AkKPirC8MLpYdxCAuwXE8Rue5AJ62z0AVKj+dBE+7AUaH08GN8Cj8/GgwBhPOPlAp7VFynYcoKiPxsL/gUsJ+bUDQEuBKFhff/D1sAffRjfoZQrvlv7bmDKSiei2UfU3qCSGMK9/n2ItYIIBJDRGsDmFm2R0hERpAicfSNZd/MegHzknQl5DuXU4C1TGei2oeRaVdGQUAS2u+yiljH2T0CVB2BdJVtV0BRwN5xC3ZqSK44y7aMOUclujFlqygLCPURrA9xwLLJmUdhc8qWURaQGoMrdtm2FfKwlugsVO5hYNrZKAowiiecGpK09jVhXilFGbEBtW7qHzH1UVApIE/dkWYCPWsZfk9UYRO7ZLk376KbUU4BVQF3S+Xl6cFL+eUaE9jOGH6LxIdSc/Je3qka58oCPF36sIDPqT6FVoC3PFURhTQMqQG8VfJVMc6VBRDGK7HT0e7FanBSWd4KqnOsOW3BViQhlQEOTti/2ShA3JHWX4H+63zy+Tpw0x+aVY/jNPTJIZXzBHZudWaoEMDA18bR1cO4VsBdgCnexeFlH66ygChjZmwnQMTNwxT+wbZJtOGoU+r4CVyZHt0x+acoLrC9ABVItyuqCH+mDf2alO1UgIiO68MvXnD5EQ4ZB3HnAiSceZjoghusjWGVddPXEfCAu0llSSW5kI7XFcDgXcAbC0jiL1fOz39XN9W+AAAAAElFTkSuQmCC);
	}
	#header h1 span{ font-weight: normal; font-size: 9px; margin: 0px 0px 0px 8px; }
	#footer{ height: 60px; width: 100%; border-top:#ccc solid 1px; background-color: #f3f3f3; }
	#container{ padding:0px; }
    #db_connect{ width: 760px; min-height: 150px; background-color:#f3f3f3; border:1px solid #ccc; border-radius:3px 3px 3px; margin:10px auto 0px auto; }
    #db_connect table{ width: 100%; }
    #db_connect table th{ text-align: left; }
/*    #db_connect input{ padding: 5px; border: 1px solid #ccc; }*/
    #db_tables{ width: 760px; min-height: 400px; border:1px solid #ccc; border-radius:3px 3px 3px; margin: 8px auto 10px auto; }
    #db_tables table{ width: 100%; } 
    #db_tables table th{ background-color: #f3f3f3; text-align: left; padding: 3px 0px 3px 10px; }
	#db_tables table th a{ float: right; color: #999; text-decoration: none; }
    #db_tables table td{ text-align: left; padding: 3px 0px 3px 20px; }
    .diff_field { font-size:12px; color:#EE5931; overflow:hidden; text-overflow:ellipsis; max-width:340px; }
	.diff_field_input { width:300px; border:none; color:#EE5931; }
	.diff_repeat { color:#BB5EB2; overflow:hidden; text-overflow:ellipsis; max-width:340px; }
    .diff_table { padding:0px 0px 0px 5px; font-weight:normal; font-size:10px; color:#E8A042; }
	.table_comment { padding:0px 0px 0px 5px; font-weight:normal; font-size:12px; color:#d0d0d0; }
	.field-comment { margin: 0px 0px 0px 5px; color:#d0d0d0; font-size: 12px; }
    #connHistoryes { display:none; font-size:12px; border:1px solid #ccc; background-color:white; position:absolute; box-shadow: 3px 3px 3px #ccc;  }
    #connHistoryes ul { margin: 4px 0px; padding: 0px; list-style: none; }
    #connHistoryes ul li { padding: 4px 15px; }
    #connHistoryes ul li:hover { cursor: pointer; background-color: #eee; }
    #connHistoryes a{ text-decoration: none; }    
	.ajaxloading{ display:none; vertical-align:middle; width:16px; height:16px; margin:0px 0px 0px 5px; background-image:url("data:image/gif;base64,R0lGODlhEAAQAKIGAMLY8YSx5HOm4Mjc88/g9Ofw+v///wAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAGACwAAAAAEAAQAAADMGi6RbUwGjKIXCAA016PgRBElAVlG/RdLOO0X9nK61W39qvqiwz5Ls/rRqrggsdkAgAh+QQFCgAGACwCAAAABwAFAAADD2hqELAmiFBIYY4MAutdCQAh+QQFCgAGACwGAAAABwAFAAADD1hU1kaDOKMYCGAGEeYFCQAh+QQFCgAGACwKAAIABQAHAAADEFhUZjSkKdZqBQG0IELDQAIAIfkEBQoABgAsCgAGAAUABwAAAxBoVlRKgyjmlAIBqCDCzUoCACH5BAUKAAYALAYACgAHAAUAAAMPaGpFtYYMAgJgLogA610JACH5BAUKAAYALAIACgAHAAUAAAMPCAHWFiI4o1ghZZJB5i0JACH5BAUKAAYALAAABgAFAAcAAAMQCAFmIaEp1motpDQySMNFAgA7"); }
</style>
<!--<script type="text/javascript" src="static/js/JSLite.min.js"></script>-->
<script type="text/javascript">!function(t,n){"function"==typeof define&&define.amd?define([],n):"object"==typeof exports?module.exports=n():t.JSLite=n()}(this,function(){function t(t){return t?"number"==typeof t.length:null}function n(n,e){var i,r;if(t(n)){for(i=0;i<n.length;i++)if(e.call(n[i],i,n[i])===!1)return n}else for(r in n)if(e.call(n[r],r,n[r])===!1)return n;return n}function e(t){return null==t?t+"":"object"==typeof t||"function"==typeof t?v[S.call(t)]||"object":typeof t}function i(t){return"function"==e(t)}function r(t){return"object"==e(t)}function o(t){return Array.isArray?Array.isArray(t):"array"===e(t)}function s(t){return"string"==typeof t}function a(t){function n(t){return t.hasOwnProperty}return"object"!==g.type(t)||t.nodeType||g.isWindow(t)?!1:!t.constructor||n.call(t.constructor.prototype,"isPrototypeOf")}function c(t){var n="object"==typeof t&&"[object object]"==S.call(t).toLowerCase()&&!t.length;return n}function u(t){return t&&t==t.window}function l(t){return t&&t.nodeType==t.DOCUMENT_NODE}function f(t,e){var i,r;return y.singleTagRE.test(t)&&(i=g(document.createElement(RegExp.$1))),i||(t.replace&&(t=t.replace(y.tagExpanderRE,"<$1></$2>")),void 0===e&&(e=y.fragmentRE.test(t)&&RegExp.$1),e in y.containers||(e="*"),r=y.containers[e],r.innerHTML=""+t,i=n(x.call(r.childNodes),function(){r.removeChild(this)})),i}function h(t,n,e,r){return i(n)?n.call(t,e,r):n}function p(t){return t.replace(/^-ms-/,"ms-").replace(/-([a-z])/g,function(t,n){return n.toUpperCase()})}function d(t){return t.replace(/::/g,"/").replace(/([A-Z]+)([A-Z][a-z])/g,"$1_$2").replace(/([a-z\d])([A-Z])/g,"$1_$2").replace(/_/g,"-").toLowerCase()}function m(t,n,e){for(var i=[];t.length>0;)t=$.map(t,function(t){return(t=t[e])&&!l(t)&&i.indexOf(t)<0?(i.push(t),t):void 0});return n&&s(n)?$(i).filter(n):$(i)}window&&!window.getComputedStyle&&(window.getComputedStyle=function(t,n){return this.el=t,this.getPropertyValue=function(n){return"float"==n&&(n="styleFloat"),n=p(n),t.currentStyle[n]||null},this}),Array.prototype.filter||(Array.prototype.filter=function(t){"use strict";if(void 0===this||null===this)throw new TypeError;var n=Object(this),e=n.length>>>0;if("function"!=typeof t)throw new TypeError;for(var i=[],r=arguments.length>=2?arguments[1]:void 0,o=0;e>o;o++)if(o in n){var s=n[o];t.call(r,s,o,n)&&i.push(s)}return i}),Array.indexOf||(Array.prototype.indexOf=function(t){for(var n=0;n<this.length;n++)if(this[n]==t)return n;return-1}),Array.prototype.forEach||(Array.prototype.forEach=function(t){var n=this.length;if("function"!=typeof t)throw new TypeError;for(var e=arguments[1],i=0;n>i;i++)i in this&&t.call(e,this[i],i,this)}),Array.prototype.remove||(Array.prototype.remove=function(t){var n=this.indexOf(t);return n>-1&&this.splice(n,1),this}),String.prototype.trim||(String.prototype.trim=function(){return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")});var v={};n("Boolean Number String Function Array Date RegExp Object Error".split(" "),function(t,n){v["[object "+n+"]"]=n.toLowerCase()});var y={};y={singleTagRE:/^<(\w+)\s*\/?>(?:<\/\1>|)$/,fragmentRE:/^\s*<(\w+|!)[^>]*>/,tagExpanderRE:/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,table:document.createElement("table"),tableRow:document.createElement("tr"),containers:{"*":document.createElement("div"),tr:document.createElement("tbody"),tbody:y.table,thead:y.table,tfoot:y.table,td:y.tableRow,th:y.tableRow}};var g,w=[],x=w.slice,E=w.filter,b=(w.some,{}),S=b.toString,j=[1,9,11],O={tabindex:"tabIndex",readonly:"readOnly","for":"htmlFor","class":"className",maxlength:"maxLength",cellspacing:"cellSpacing",cellpadding:"cellPadding",rowspan:"rowSpan",colspan:"colSpan",usemap:"useMap",frameborder:"frameBorder",contenteditable:"contentEditable"};g=function(){var t=function(n){return new t.fn.init(n)};return t.fn=t.prototype={init:function(n){var e;if(n)if("string"==typeof n&&(n=n.trim())&&"<"==n[0]&&/^\s*<(\w+|!)[^>]*>/.test(n))e=f(n),n=null;else{if(i(n))return t(document).ready(n);o(n)?e=n:r(n)?(e=[n],n=null):j.indexOf(n.nodeType)>=0||n===window?(e=[n],n=null):e=function(){var t;return document&&/^#([\w-]+)$/.test(n)?(t=document.getElementById(RegExp.$1))?[t]:[]:x.call(/^\.([\w-]+)$/.test(n)?document.getElementsByClassName(RegExp.$1):/^[\w-]+$/.test(n)?document.getElementsByTagName(n):document.querySelectorAll(n))}()}else e=w,e.selector=n||"",e.__proto__=t.fn.init.prototype;return e=e||w,t.extend(e,t.fn),e.selector=n||"",e}},t.fn.init.prototype=t.fn,t}(),g.extend=g.fn.extend=function(){var t,n,e,r,o=arguments[0],s=1,a=arguments.length,c=!1;for("boolean"==typeof o&&(c=o,o=arguments[1]||{},s=2),"object"==typeof o||i(o)||(o={}),a===s&&(o=this,--s);a>s;s++)if(null!=(t=arguments[s]))for(n in t)e=o[n],r=t[n],o!==r&&void 0!==r&&(o[n]=r);return o},g.extend({isDocument:l,isFunction:i,isObject:r,isArray:o,isString:s,isWindow:u,isPlainObject:a,isJson:c,parseJSON:JSON.parse,type:e,likeArray:t,trim:function(t){return null==t?"":String.prototype.trim.call(t)},intersect:function(t,n){var e=[];return t.forEach(function(t){n.indexOf(t)>-1&&e.push(t)}),e},error:function(t){throw t},getUrlParam:function(t,n){var e=n||location.search,i={};if(-1!=e.indexOf("?"))for(var r=e.substr(1).split("&"),o=0,s=r.length;s>o;o++){var a=r[o].split("=");i[a[0]]=a[1]&&decodeURIComponent(a[1])}return t?i[t]:i},each:function(t,e){return n.apply(this,arguments)},map:function(n,e){var i,r,o,s=[];if(t(n))for(r=0;r<n.length;r++)i=e(n[r],r),null!=i&&s.push(i);else for(o in n)i=e(n[o],o),null!=i&&s.push(i);return s.length>0?g.fn.concat.apply([],s):s},grep:function(t,n){return E.call(t,n)},matches:function(t,n){if(!n||!t||1!==t.nodeType)return!1;var e=t.webkitMatchesSelector||t.mozMatchesSelector||t.oMatchesSelector||t.msMatchesSelector||t.matchesSelector;return e?e.call(t,n):void 0},unique:function(t){return E.call(t,function(n,e){return t.indexOf(n)==e})},inArray:function(t,n,e){return w.indexOf.call(n,t,e)},sibling:function(t,n){var e=[];return t.length>0&&(e=g.map(t,function(t){return(t=t[n])&&!l(t)&&e.indexOf(t)<0&&e.push(t),t})),this.unique(e)},contains:function(t,n){return t&&!n?document.documentElement.contains(t):t!==n&&t.contains(n)},camelCase:p,now:Date.now}),g.fn.extend({forEach:w.forEach,concat:w.concat,indexOf:w.indexOf,each:function(t){return g.each(this,t)},map:function(t){return g(g.map(this,function(n,e){return t.call(n,e,n)}))},get:function(t){return void 0===t?x.call(this):this[t>=0?t:t+this.length]},index:function(t){return t?"string"===e(t)?this.indexOf(this.parent().children(t)[0]):this.indexOf(t):this.parent().children().indexOf(this[0])},is:function(t){return this.length>0&&"string"!=typeof t?this.indexOf(t)>-1:this.length>0&&g.matches(this[0],t)},add:function(t){return g(g.unique(this.concat(g(t))))},eq:function(t){return g(-1===t?this.slice(t):this.slice(t,+t+1))},first:function(){var t=this[0];return t&&!r(t)?t:g(t)},slice:function(t){return g(x.apply(this,arguments))},size:function(){return this.length},filter:function(t){return i(t)?this.not(this.not(t)):g(E.call(this,function(n){return g.matches(n,t)}))},not:function(n){var e=[];if(i(n)&&void 0!==n.call)this.each(function(t){n.call(this,t)||e.push(this)});else{var r="string"==typeof n?this.filter(n):t(n)&&i(n.item)?x.call(n):g(n);this.forEach(function(t){r.indexOf(t)<0&&e.push(t)})}return g(e)},children:function(t){var n=[];return E.call(this.pluck("children"),function(t,e){g.map(t,function(t){t&&1==t.nodeType&&n.push(t)})}),g(n).filter(t||"*")},contents:function(t){return this.map(function(){return this.contentDocument||$.grep(this.childNodes,function(n){return t?$.matches(n,t):n})})},parent:function(t){return g(g.unique(this.pluck("parentNode"))).filter(t||"*")},parents:function(t){return m(this,t,"parentNode")},closest:function(t,n){var e=this[0],i=!1;for("object"==typeof t&&(i=g(t));e&&!(i?i.indexOf(e)>=0:g.matches(e,t));)e=e!==n&&!l(e)&&e.parentNode;return g(e)},prev:function(t){return g(this.pluck("previousElementSibling")).filter(t||"*")},next:function(t){return g(this.pluck("nextElementSibling")).filter(t||"*")},nextAll:function(t){return m(this,t,"nextElementSibling")},prevAll:function(t){return m(this,t,"previousElementSibling")},siblings:function(t){var n=[];return this.map(function(t,e){E.call(e.parentNode.children,function(t,i){t&&1==t.nodeType&&t!=e&&n.push(t)})}),g(n).filter(t||"*")},find:function(t){for(var n=this.children(),e=[];n.length>0;)n=g.map(n,function(t,i){return e.indexOf(t)<0&&e.push(t),(n=g(t).children())&&n.length>0?n:void 0});return g(e).filter(t||"*")},replaceWith:function(t){return this.before(t).remove()},unwrap:function(){return this.parent().each(function(){g(this).replaceWith(g(this).html())}),this},remove:function(t){var n=t?g(this.find(h(this,t))):this;return n.each(function(){null!=this.parentNode&&this.parentNode.removeChild(this)})},detach:function(){return this.remove()},empty:function(){return this.each(function(){this.innerHTML=""})},clone:function(){return this.map(function(){return this.cloneNode(!0)})},text:function(t){return void 0===t?this.length>0?this[0].textContent:null:this.each(function(){this.textContent=h(this,t)})},html:function(t){return 0 in arguments?this.each(function(n){g(this).empty().append(h(this,t))}):0 in this?this[0].innerHTML:null},hide:function(){return this.css("display","none")},show:function(){return this.each(function(){function t(t){var e,i=document.createElement(t);return g("body").append(g(i)),e=n(i).display,i.parentNode.removeChild(i),e}"none"==this.style.display&&(this.style.display="");var n=function(t){return t.currentStyle||document.defaultView.getComputedStyle(t,null)};"none"==n(this).display&&(this.style.display=t(this.nodeName))})},toggle:function(t){return this.each(function(){var n=g(this);(void 0===t?"none"==n.css("display"):t)?n.show():n.hide()})},offset:function(){if(0==this.length)return null;var t=this[0].getBoundingClientRect();return{left:t.left+window.pageXOffset,top:t.top+window.pageYOffset,width:t.width,height:t.height}},css:function(t,n){var e=this[0];if(arguments.length<2){if(!e)return[];if(!n&&"string"==typeof t)return e.style[t];if(o(t)){var i={};return $.each(t,function(t,n){i[n]=e.style[p(n)]}),i}}var r,s={};if("string"==typeof t)n||0===n?s[d(t)]=n:this.each(function(){this.style.removeProperty(d(t))});else for(r in t)t[r]||0===t[r]?s[d(r)]=t[r]:this.each(function(){this.style.removeProperty(d(r))});return this.each(function(){for(var t in s)this.style[t]=s[t]})},hasClass:function(t){return t?w.some.call(this,function(t){return(" "+t.className+" ").indexOf(this)>-1}," "+t+" "):!1},addClass:function(t){if(!t)return this;var n,e,i;return this.each(function(r){return n=[],e=this.className,i=h(this,t).trim(),i.split(/\s+/).forEach(function(t){g(this).hasClass(t)||n.push(t)},this),i?void(n.length?this.className=e+(e?" ":"")+n.join(" "):null):this})},removeClass:function(t){var n;return void 0===t?this.removeAttr("class"):this.each(function(e){n=this.className,h(this,t,e,n).split(/\s+/).forEach(function(t){n=n.replace(new RegExp("(^|\\s)"+t+"(\\s|$)")," ").trim()},this),n?this.className=n:this.className=""})},toggleClass:function(t){return t?this.each(function(n){var e=g(this),i=h(this,t);i.split(/\s+/g).forEach(function(t){e.hasClass(t)?e.removeClass(t):e.addClass(t)})}):this},pluck:function(t){return g.map(this,function(n){return n[t]})},prop:function(t,n){return t=O[t]||t,1 in arguments?this.each(function(e){this[t]=h(this,n,e,this[t])}):this[0]&&this[0][t]},removeProp:function(t){return t=O[t]||t,this.each(function(){try{this[t]=void 0,delete this[t]}catch(n){}})},attr:function(t,n){var e,i;return"string"!=typeof t||1 in arguments?this.each(function(e){if(r(t))for(i in t)this.setAttribute(i,t[i]);else this.setAttribute(t,h(this,n))}):this.length&&1===this[0].nodeType?!(e=this[0].getAttribute(t))&&t in this[0]?this[0][t]:e:void 0},removeAttr:function(t){return this.each(function(){1===this.nodeType&&this.removeAttribute(t)})},val:function(t){return 0 in arguments?this.each(function(n){this.value=h(this,t,n,this.value)}):this[0]&&(this[0].multiple?g(this[0]).find("option").filter(function(){return this.selected}).pluck("value"):this[0].value)},data:function(t,n){var e,i,r="data-"+t;if(!t)return this[0].dataset;if(t&&c(t)){for(i in t)this.attr("data-"+i,t[i]);return this}n&&(o(n)||c(n))&&(n=JSON.stringify(n)),e=1 in arguments?this.attr(r,n):this.attr(r);try{e=JSON.parse(e)}catch(s){}return e}}),g.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(t,n){var e="pageYOffset"===n;g.fn[t]=function(i){var r=u(this[0]);return void 0===i?r?window[n]:this[0][t]:r?(window.scrollTo(e?window.pageXOffset:i,e?i:window.pageYOffset),this[0]):this.each(function(){this[t]=i})}}),["after","prepend","before","append"].forEach(function(t,n){var i=n%2;g.fn[t]=function(){var t,r,o,s=g.map(arguments,function(n){return t=e(n),"function"==t&&(n=h(this,n)),"object"==t||"array"==t||null==n?n:f(n)}),a=this.length>1;return s.length<1?this:this.each(function(t,e){r=i?e:e.parentNode,e=0==n?e.nextSibling:1==n?e.firstChild:2==n?e:null;var c=g.contains(document.documentElement,r);s.forEach(function(t){var n;a&&(t=t.cloneNode(!0)),r.insertBefore(t,e),!c||null==t.nodeName||"SCRIPT"!==t.nodeName.toUpperCase()||t.type&&"text/javascript"!==t.type||t.src?c&&t.children&&t.children.length>0&&g(t)&&(o=g(t).find("script"))&&o.length>0&&o.each(function(t,e){n=e.innerHTML}):n=t.innerHTML,n?window.eval.call(window,n):void 0})})},g.fn[i?t+"To":"insert"+(n?"Before":"After")]=function(n){return g(n)[t](this),this}}),["width","height"].forEach(function(t){var n=t.replace(/./,t[0].toUpperCase());g.fn[t]=function(e){var i,r=this[0];return void 0===e?u(r)?r["inner"+n]:l(r)?r.documentElement["scroll"+n]:(i=this.offset())&&i[t]:this.each(function(n){r=$(this),r.css(t,h(this,e,n,r[t]()))})}});var T=window.JSLite,C=window.$;return g.noConflict=function(t){return window.$===g&&(window.$=C),t&&window.JSLite===g&&(window.JSLite=T),g},window.JSLite=window.$=g,function(t){t.fn.extend({serializeArray:function(){var n,e,i=[],r=this.get(0);return r&&r.elements?(t([].slice.call(this.get(0).elements)).each(function(){n=t(this),e=n.attr("type"),"fieldset"!=this.nodeName.toLowerCase()&&!this.disabled&&"submit"!=e&&"reset"!=e&&"button"!=e&&("radio"!=e&&"checkbox"!=e||this.checked)&&i.push({name:n.attr("name"),value:n.val()})}),i):i},serialize:function(t){return t=[],this.serializeArray().forEach(function(n){t.push(encodeURIComponent(n.name)+"="+encodeURIComponent(n.value))}),t.join("&")}})}(g),function(t){function n(n,e,o,a,c){var u=i(n),l=s[u]||(s[u]=[]);e.split(/\s/).forEach(function(e){var i=t.extend(r(e),{fn:o,sel:c,i:l.length}),s=i.proxy=function(e){if(c){var i=t(n).find(c),r=[].some.call(i,function(n){return n===e.target||t.contains(n,e.target)});if(!r)return!1}e.data=a;var s=o.apply(n,void 0==e._data?[e]:[e].concat(e._data));return s===!1&&(e.preventDefault(),e.stopPropagation()),s};l.push(i),n.addEventListener&&n.addEventListener(i.e,s,!1)})}function e(n,e,a,c){(e||"").split(/\s/).forEach(function(e){t.event=r(e),o(n,e,a,c).forEach(function(t){delete s[i(n)][t.i],n.removeEventListener&&n.removeEventListener(t.e,t.proxy,!1)})})}function i(t){return t._jid||(t._jid=a++)}function r(t){var n=(""+t).split(".");return{e:n[0],ns:n.slice(1).sort().join(" ")}}function o(t,n,e,o){i(t);return n=r(n),(s[i(t)]||[]).filter(function(t){return t&&(!n.e||t.e==n.e)&&(!e||t.fn.toString()===e.toString())&&(!o||t.sel==o)})}var s={},a=1;t.fn.extend({ready:function(t){return/complete|loaded|interactive/.test(document.readyState)&&document.body?t(g):document.addEventListener("DOMContentLoaded",function(){t(g)},!1),this},bind:function(t,e){return this.each(function(){n(this,t,e)})},unbind:function(t,n){return this.each(function(){e(this,t,n)})},on:function(e,i,r,o){var s=this;return e&&!t.isString(e)?(t.each(e,function(t,n){s.on(t,i,r,n)}),s):(t.isString(i)||t.isFunction(o)||o===!1||(o=r,r=i,i=void 0),(t.isFunction(r)||r===!1)&&(o=r,r=void 0),o===!1&&(o=function(){return!1}),this.each(function(){n(this,e,o,r,i)}))},off:function(n,i,r){var o=this;return n&&!t.isString(n)?(t.each(n,function(t,n){o.off(t,i,n)}),o):(t.isString(i)||t.isFunction(r)||r===!1||(r=i,i=void 0),r===!1&&(r=function(){return!1}),o.each(function(){e(this,n,r,i)}))},delegate:function(t,n,e){return this.on(n,t,e)},trigger:function(t,n){var e=t,i={};return i.click=i.mousedown=i.mouseup=i.mousemove="MouseEvents","string"==typeof e?(t=document.createEvent(i[e]||"Events"),t.initEvent(e,!0,!0),t._data=n,this.each(function(){"dispatchEvent"in this&&this.dispatchEvent(t)})):void 0}}),t.event={add:n,remove:e},"blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error paste drop dragstart dragover beforeunload".split(" ").forEach(function(n){t.fn[n]=function(t){return t?this.bind(n,t):this.trigger(n)}})}(g),function(t){function n(n,e,i,r){return t.isFunction(e)&&(r=i,i=e,e=void 0),t.isFunction(i)||(r=i,i=void 0),{url:n,data:e,success:i,dataType:r}}var r=0;t.extend({ajaxSettings:{type:"GET",success:function(){},error:function(){},xhr:function(){return new window.XMLHttpRequest},processData:!0,complete:function(){},accepts:{script:"text/javascript, application/javascript",json:"application/json",xml:"application/xml, text/xml",html:"text/html",text:"text/plain"},cache:!0},param:function(n,r,o){if("string"==t.type(n))return n;var s=[],a="";if(s.add=function(t,n){this.push(encodeURIComponent(t)+"="+encodeURIComponent(null==n?"":n))},1==o&&"object"==e(n))s.add(r,n);else for(var c in n){var u=n[c],a="",l=function(){return r?1==r?c:o&&"array"==e(n)?r:r+"["+("array"==t.type(n)?"":c)+"]":c}();"object"==typeof u?a=this.param(u,l,r):i(u)||(a=s.add(l,u)),a&&s.push(a)}return s.join("&")},get:function(e,i){return t.ajax(n.apply(null,arguments))},post:function(e,i,r,o){var s=n.apply(null,arguments);return s.type="POST",t.ajax(s)},getJSON:function(){var t=n.apply(null,arguments),e=arguments[0];return e&&e==document.location.host?t.dataType="json":t.dataType="jsonp",this.ajax(t)},ajaxJSONP:function(n){var e,i=n.jsonpCallback,o=(t.isFunction(i)?i():i)||"jsonp"+ ++r,s=document.createElement("script"),a=window[o],c={};return t(s).on("load error",function(i,r){t(s).off().remove(),"error"!=i.type&&e?n.success(e[0],c,n):n.error(i,r||"error",n),window[o]=a,e&&t.isFunction(a)&&a(e[0]),a=e=void 0}),window[o]=function(){e=arguments},s.src=n.url.replace(/\?(.+)=\?/,"?$1="+o),document.head.appendChild(s),n.xhr()},ajax:function(n){var e,i,r,o=function(t,n){g[t.toLowerCase()]=[t,n]},s=function(t,n){return""==n?t:(t+"&"+n).replace(/[&?]{1,2}/,"?")},a=function(n){n.processData&&n.data&&"string"!=t.type(n.data)&&(n.data=t.param(n.data,n.traditional)),!n.data||n.type&&"GET"!=n.type.toUpperCase()||(n.url=s(n.url,n.data),n.data=void 0)};if(n=n||{},t.isString(n))if("GET"==arguments[0]){var c=arguments[1];arguments[2]&&t.isFunction(arguments[2])?t.get(c,arguments[2]):arguments[2]&&t.isJson(arguments[2])&&t.get(c.indexOf("?")>-1?c+"&"+this.param(arguments[2]):c+"?"+this.param(arguments[2]),arguments[3])}else"POST"==arguments[0]&&t.post(arguments[1],arguments[2],arguments[3],arguments[4]);else{i=t.extend({},n||{});for(e in t.ajaxSettings)void 0===i[e]&&(i[e]=t.ajaxSettings[e]);a(i);var u=i.dataType,l=/\?.+=\?/.test(i.url);if(l&&(u="jsonp"),i.cache!==!1&&(n&&n.cache===!0||"script"!=u&&"jsonp"!=u)||(i.url=s(i.url,"_="+Date.now())),"jsonp"==u)return l||(i.url=s(i.url,i.jsonp?i.jsonp+"=?":i.jsonp===!1?"":"callback=?")),t.ajaxJSONP(i);var f=i.data,h=i.success||function(){},p=i.error||function(){},d=t.ajaxSettings.accepts[i.dataType],m=i.contentType,v=new XMLHttpRequest,y=v.setRequestHeader,g={};if(i.crossDomain||(o("X-Requested-With","XMLHttpRequest"),o("Accept",d||"*/*")),i.headers)for(r in i.headers)o(r,i.headers[r]);(i.contentType||i.contentType!==!1&&i.data&&"GET"!=i.type.toUpperCase())&&o("Content-Type",i.contentType||"application/x-www-form-urlencoded"),v.onreadystatechange=function(){if(4==v.readyState)if(v.status>=200&&v.status<300||0==v.status){var t,n=!1;t=v.responseText;try{"script"==i.dataType?(0,eval)(t):"xml"==i.dataType?t=v.responseXML:"json"==i.dataType&&(t=/^\s*$/.test(t)?null:JSON.parse(t))}catch(e){n=e}n?p(n,"parsererror",v,i):h(t,"success",v)}else i.complete(v,n?"error":"success")},f&&f instanceof Object&&"GET"==i.type&&(f?i.url=i.url.indexOf("?")>-1?i.url+"&"+f:i.url+"?"+f:null);var w="async"in i?i.async:!0;v.open(i.type,i.url,w),d&&v.setRequestHeader("Accept",d),f instanceof Object&&"application/json"==d&&(f=JSON.stringify(f),m=m||"application/json");for(r in g)y.apply(v,g[r]);v.send(f?f:null)}}});var o=t.fn.load;t.fn.extend({load:function(e,i,r){if(arguments[0]&&"string"!=typeof arguments[0]&&o)return o.apply(this,arguments);if(!this.length||0===arguments.length)return this;var s,a=this,c=arguments[0].split(/\s/),u=n(e,i,r),l=u.success;return c.length>1&&(u.url=c[0],s=c[1]),u.success=function(n){n=n.replace(/<(script)[^>]*>(|[\s\S]+?)<\/\1>/gi,""),a.html(s?t("<div>").html(n).find(s):n),l&&l.apply(a,arguments)},t.ajax(u),this}})}(g),g});</script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.9.0.min.js"></script>
<script>
	// Fallback to loading jQuery from a local path if the CDN is unavailable
	//(window.jQuery || document.write('<script src="static/js/JSLite.min.js"><\/script>'));
</script>

<script type="text/javascript">
$(function($){         
    var connect_db = function(inx){		
        $('select[name=database'+inx+']').children().slice(1).remove();
		$('#btn'+inx+'ajax').css('display','inline-block');
        $.post("?act=getDatabases&inx="+inx, $("#theForm").serialize(), function(data){			
            try{
                data = $.parseJSON(data);
                if(data.status){
                    for(var k=0; k<data.databases.length; k++){
                        $('select[name=database'+inx+']').append('<option value="'+data.databases[k]+'">'+data.databases[k]+'</option>');
                    }
                }
            }catch(err){
				console.log(err);
                alert('Cannot connect to the database '+$("#username"+inx).val()+':******@'+$("#host"+inx).val());
            }
			$('#btn'+inx+'ajax').css('display','none');
        });//,'json'
    }
    $("#btnConnList1, #btnConnList2").click(function(){
        var pos = $(this).offset();    
        var inx = $(this).attr('id').replace('btnConnList','');
        $.post("?act=getAccounts&inx="+inx, {}, function(data){
            if(data.status){
                $("#connHistoryes ul").empty();
                for(var k=0; k<data.accounts.length; k++){
                    var account = data.accounts[k];
                    $("#connHistoryes ul").append('<li k="'+k+'">'+account['username']+':******@'+account['host']+'</li>');
                }
                //$("#connHistoryes li")
                $("#connHistoryes").show().css({top:pos.top+'px', left:pos.left+'px'}).find("li").click(function(){
                    var account = data.accounts[$(this).attr("k")];
                    $("#host"+inx).val(account['host']);
                    $("#username"+inx).val(account['username']);
                    $("#password"+inx).val(account['password']);
                    $("select[name=database"+inx+"] option").slice(1).remove();
                });
                $("#clearAccounts").unbind('click').click(function(){
                    if(window.confirm('Do you want to clear the history?')){
                        $.post("?act=clearAccounts&inx="+inx, {}, function(data){});
                    }
                });
            }
        },'json');
    });
    $("#btn1, #btn2").click(function(){
        var inx = $(this).attr('id').replace('btn','');
        connect_db(inx);
    });    
//    $('select[name=database1],select[name=database2]').change(function(){
//        if($('select[name=database1]').val()!='' && $('select[name=database2]').val()!=''){
//            $("#btnStart").removeAttr('disabled');
//        } else {
//            $("#btnStart").attr('disabled',true);
//        }        
//    });
    $("#btnStart").click(function(){
        if($('select[name=database1]').val()=='' || $('select[name=database2]').val()==''){
            alert('Please choose to compare the database!');
            return;
        }
        $("#db_tables").show();
        $("#db_tables_compare").html('');
		$('#btnCompareAjax').css('display','inline-block');
        $.post("?act=dbCompare", { 'database1':$('select[name=database1]').val(), 'database2':$('select[name=database2]').val() }, function(data){
            try{
                data = $.parseJSON(data);
                if(data.status){
                    $("#db_tables_compare").html(data.strHtml);
					var aClick = function(){
						if($(this).hasClass('expand')){							
							$(this).text("[+]").attr("class","collapse").parent().parent().nextAll().hide();
						} else {
							$(this).text("[-]").attr("class","expand").parent().parent().nextAll().show();
						}
					}
					$(".expand").click(aClick);
					$(".collapse").click(aClick).parent().parent().nextAll().hide();	
					$("#db_tables_compare div.diff_field").click(function(){
						var arr_span = $(this).find("span");
						var arr_input = $(this).children("input");
						if(arr_input.length==0){
							$(arr_span[0]).hide();
							$(this).append('<input type="text" value="'+$(arr_span[0]).text()+'" class="diff_field_input" style="width:'+$(this).width()+'px;">');
						}
					});
                }
            }catch(err){
				console.log(err);
                alert('An error has occurred!');
                //location.reload();
            }
			$('#btnCompareAjax').css('display','none');
        });//,'json'
    });
    $("body").mouseup(function(){
        $("#connHistoryes").hide();
    }).mousedown(function(){
		var tag_input = $(event.target);		
		if (navigator.appName=="Microsoft Internet Explorer") { tag_input = $(event.srcElement); }
		if(tag_input.attr("class")!="diff_field_input"){
			$("#db_tables_compare div.diff_field").each(function(){
				$(this).children("input").remove();
				$(this).find("span").show();
			});
		}
	});
	$(window).resize(function(){
		$("#container").css({minHeight:($(window).height()-122)+"px"});
	});
	$(window).resize();
});
</script>

</head>

<body>
    
<div id="header">
    <h1>Mysqldiff<span>— Identify Differences Among Database Objects</span></h1>
</div>
<div id="container">	
	<div id="db_connect">
		<div style="padding: 10px;">			 
			<form id="theForm" method="post">
			<table border="0" cellspacing="1" cellpadding="1">
				<tr><th width="100"></th><th>Database#1</th><th>Database#2</th></tr>
				<tr><td>Hostname</td><td><input type="text" id="host1" name="host1" value="<?php echo($arr1['host']); ?>" tabindex="1"><input id="btnConnList1" type="button" value="..." tabindex="-1"></td><td><input type="text" id="host2" name="host2" value="<?php echo($arr2['host']); ?>" tabindex="4"><input id="btnConnList2" type="button" value="..." tabindex="-1"></td></tr>
				<tr><td>Username</td><td><input type="text" id="username1" name="username1" value="<?php echo($arr1['username']); ?>" tabindex="2"></td><td><input type="text" id="username2" name="username2" value="<?php echo($arr2['username']); ?>" tabindex="5"></td></tr>
				<tr><td>Password</td><td><input type="password" name="password1" value="<?php echo($arr1['password']); ?>" tabindex="3"></td><td><input type="password" name="password2" value="<?php echo($arr2['password']); ?>" tabindex="6"></td></tr>
				<tr><td> </td><td><input type="button" id="btn1" value="Connect"><div id="btn1ajax" class="ajaxloading"></div></td><td><input type="button" id="btn2" value="Connect"><div id="btn2ajax" class="ajaxloading"></div></td></tr>
				<tr><td></td><td><select name="database1"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;</option></select></td><td><select name="database2"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;</option></select></td></tr>
			</table>   
			</form>
			<div style="text-align: center;"><input type="button" id="btnStart" value="  Compare  "><div id="btnCompareAjax" class="ajaxloading"></div></div>
		</div>       
	</div>
	<div id="db_tables" style="display: none;">
		<div id="db_tables_compare" style="padding: 10px;">
			<!--<table border="0" cellpadding="0" cellspacing="1">
				<tr><th width="50%">table1</th><th>table2</th></tr>
				<tr><td><span style="color: #2AB44B;">field1</span></td><td><span style="color: #EE5931;">field1</span></td></tr>     
				<tr><td><span style="color: #C49923;">field2</span></td><td><span style="color: #C49923;">field2</span></td></tr>
			</table>-->
		</div>
	</div>
	<div id="connHistoryes">
		<ul>
		   <li>root:******@127.0.0.1</li>
		</ul>
		<hr style="border:none; border-top:1px solid #ddd; margin: 0px 1px;">
		<div style="margin: 0px 0px 4px 0px; padding: 4px 7px; text-align: right;"><a id="clearAccounts" href="javascript:;">Clear history</a></div>
	</div>
</div>
<div id="footer">
	<div style="font-size: 12px; line-height: 16px; width:760px; margin: 0px auto; padding:5px 0px 0px 0px;">		
		<div>Copyright (c) 2018 <a href="http://www.ampnmp.com" target="_blank">http://www.ampnmp.com</a> All rights reserved.<span style="float:right;">Version 1.0</span></div>
		<div>Licensed ( <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank">http://www.apache.org/licenses/LICENSE-2.0</a> )</div>
		<div>GitHub repository: <a href="https://github.com/ampnmp/mysqldiff" target="_blank">https://github.com/ampnmp/mysqldiff</a></div>
	</div>
</div>	
</body>
</html>

<?php

function json_output($data)
{
    header('Cache-Control:no-store, no-cache, must-revalidate');
    //header('Content-Type:application/json; charset=utf-8');
    header('Pragma:no-cache');    
    echo json_encode($data); //,JSON_UNESCAPED_UNICODE
    exit(); 
}

function getAccounts()
{
    global $arr_account;    
    $inx = $_REQUEST['inx'];
    $data = array('status'=>1, 'accounts'=>array_reverse($arr_account['db'.$inx]));
    json_output($data);          
}

function clearAccounts()
{
    global $arr_account;
    $inx = $_REQUEST['inx'];
    $arr_account['db'.$inx] = array();
    setcookie('account_history', json_encode($arr_account), strtotime('+100 year'), '/');
    $data = array('status'=>1, 'msg'=>'');
    json_output($data);    
}

function getDatabases()
{
    global $arr_account;        
    $inx = $_REQUEST['inx'];    
    $host = $_POST['host'.$inx];
    $username = $_POST['username'.$inx];
    $password = $_POST['password'.$inx];
    $dbh = new PDO('mysql:host='.$host, $username, $password);     
    $arr = array('host'=>$host, 'username'=>$username, 'password'=>$password);
    foreach($arr_account['db'.$inx] as $k=>$item){
        if(!is_array($item) || ($item['host']==$arr['host'] && $item['username']==$arr['username'])){               
            unset($arr_account['db'.$inx][$k]);
        }
    }     
    //sort($arr_account['db1']);
    //sort($arr_account['db2']);
    $arr_account['db'.$inx] = array_slice($arr_account['db'.$inx], count($arr_account['db'.$inx])-14);
    if(is_array($arr_account['db'.$inx])){
        array_push($arr_account['db'.$inx], $arr);
    } else {
        $arr_account['db'.$inx] = array(0=>$arr);
    }        
    $databases = array();
    $rs = $dbh->query('SHOW databases;');    
    foreach ($rs as $row) {
        $databases[] = $row[0];
    }
	
    //ob_clean();
    setcookie('account_history', json_encode($arr_account), strtotime('+100 year'), '/');
    $data = array('status'=>1, 'databases'=>$databases);
    json_output($data);      
}

function dbCompare()
{
    global $arr_account;
    $arr_dbh = array();
    $arr_tables = array();
    $arr_tables_ext = array();
    $arr_columns = array();
    for($k=1; $k<=2; $k++){
        $conn = array_pop($arr_account['db'.$k]);
        $database = $_POST['database'.$k];
        $arr_dbh[$k] = new PDO('mysql:host='.$conn['host'], $conn['username'], $conn['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        $arr_dbh[$k]->query('USE '.$database);
        $rs = $arr_dbh[$k]->query('SHOW TABLES;');
        foreach ($rs as $row) {
            $table = $row[0];
            $arr_tables[$k][] = $table;
            $rs_create = $arr_dbh[$k]->query('SHOW CREATE TABLE '.$table);
            $sql_create = $rs_create->fetchColumn(1);            
            preg_match('/^\).*\s+ENGINE\=(\S+).*/im', $sql_create, $matches);
            $arr_tables_ext[$k][$table]['ENGINE'] = $matches[1];
            preg_match('/^\).*\sCHARSET\=(\S+).*/im', $sql_create, $matches);
            $arr_tables_ext[$k][$table]['CHARSET'] = $matches[1];
            preg_match('/^\).*\sCOLLATE\=(\S+).*/im', $sql_create, $matches);
            $arr_tables_ext[$k][$table]['COLLATE'] = $matches[1];			
            preg_match('/^\).*\sCOMMENT\=\'(.+)\'.*/im', $sql_create, $matches);
            $arr_tables_ext[$k][$table]['COMMENT'] = $matches[1];			
            $rs2 = $arr_dbh[$k]->query('SHOW COLUMNS FROM '.$table);
            foreach ($rs2 as $row2) {
                preg_match('/^\s+`'.$row2['Field'].'`\s.*/im', $sql_create, $matches);
                $row2['sql_lang'] = rtrim(trim($matches[0]),',');
				preg_match('/^.*\sCOMMENT\s+?\'(.+)\'.*/im', $row2['sql_lang'], $matches);
				$row2['comment'] = $matches[1];
                $arr_columns[$k][$table][] = $row2;				
            }            
        }      
        sort($arr_tables[$k]);        
    }
     
    $arr_diff[1] = array_diff($arr_tables[2], $arr_tables[1]);
    $arr_diff[2] = array_diff($arr_tables[1], $arr_tables[2]);
    sort($arr_diff[1]);
    sort($arr_diff[2]);
        
    $tables1 = array();
    $tables2 = array();
    for($k=1; $k<=2; $k++){
        $n1=0; $n2=0;
        while($n1<count($arr_tables[$k]) && $n2<count($arr_diff[$k])){
            if(strcmp($arr_tables[$k][$n1], $arr_diff[$k][$n2])<0 ){                
                ${'tables'.$k}[] = $arr_tables[$k][$n1];  //['table'=>$arr_tables[$k][$n1++], 'be'=>'yes'];
                $n1++;
            } else {
                ${'tables'.$k}[] = '<span style="color:#E8A042;">[missing]</span>';  //['table'=>$arr_diff[$k][$n2++], 'be'=>''];
                $n2++;
            }            
        }
        while($n1<count($arr_tables[$k])){
            ${'tables'.$k}[] = $arr_tables[$k][$n1++];  //['table'=>$arr_tables[$k][$n1++], 'be'=>'yes'];   
        }
        while($n2<count($arr_diff[$k])){
            ${'tables'.$k}[] = '<span style="color:#E8A042;">[missing]</span>'; $n2++;  //['table'=>$arr_diff[$k][$n2++], 'be'=>'']; 
        }
    }
        
    $str = '';
    for($i=0; $i<count($tables1); $i++)
    {        		
        $str = "";
        $ret = compare_columns($arr_columns, $tables1[$i], $tables2[$i], $str);
        //if($ret) { $str = ""; }
		
		$expandA = '<a href="javascript:;" class="expand">[-]</a>';
		if($ret){ $expandA = '<a href="javascript:;" class="collapse">[+]</a>'; }
        
        $ext1 = $arr_tables_ext[1][$tables1[$i]];
        $ext2 = $arr_tables_ext[2][$tables2[$i]];
//        print_r($ext1); print_r($ext2);
//        echo '-------------------';
        if($ext1['ENGINE']!=$ext2['ENGINE'] || $ext1['CHARSET']!=$ext2['CHARSET'] || $ext1['COLLATE']!=$ext2['COLLATE'])
        {
            $tables1[$i] .= '<span class="diff_table">'.$ext1['ENGINE'].' '.$ext1['CHARSET'].' '.$ext1['COLLATE'].'</span>';
            $tables2[$i] .= '<span class="diff_table">'.$ext2['ENGINE'].' '.$ext2['CHARSET'].' '.$ext2['COLLATE'].'</span>';
        }
		$tables1[$i] .= '<span class="table_comment">'.$ext1['COMMENT'].'</span>';
		$tables2[$i] .= '<span class="table_comment">'.$ext2['COMMENT'].'</span>';
        
$strHtml .= '
        <table border="0" cellpadding="0" cellspacing="1">
            <tr><th width="50%">'.$tables1[$i].'</th><th>'.$tables2[$i].$expandA.'</th></tr>
			'.$str.'
		<!--<tr><td><span style="color: #2AB44B;">field1</span></td><td><span style="color: #EE5931;">field1</span></td></tr>
            <tr><td><span style="color: #C49923;">field2</span></td><td><span style="color: #C49923;">field2</span></td></tr>-->
        </table>';
    }
    
    //print_r($arr_tables);    
    //ob_clean();
    $data = array('status'=>1, 'strHtml'=>$strHtml);
    json_output($data);
}

function columns_gear($arr_cols1, $arr_cols2, $n1, $n2)
{
	$pre_num = 0xFFFFFFFF;
	$pair = null;
	for($k1=$n1; $k1<count($arr_cols1); $k1++){		
		for($k2=$n2; $k2<count($arr_cols2); $k2++){
			if(strcmp($arr_cols1[$k1]['Field'], $arr_cols2[$k2]['Field'])==0){				
				$curr_num = ($k2-$n2)>($k1-$n1)?($k2-$n2):($k1-$n1);
				if($curr_num<$pre_num){			
					$pre_num = $curr_num;
					$pair = array('k1'=>$k1,'k2'=>$k2);		
				}
				break;
			}
		}
	}
	return $pair;
}

function compare_columns($arr_columns, $table1, $table2, &$str)
{
    $result = true;
    
    $arr_cols1 = $arr_columns[1][$table1];
    $arr_cols2 = $arr_columns[2][$table2];
//    if(!isset($arr_cols1) || !is_array($arr_cols1) || !isset($arr_cols2) || !is_array($arr_cols2)){
//        return;
//    }
    if( !((isset($arr_cols1) && is_array($arr_cols1)) || (isset($arr_cols2) && is_array($arr_cols2))) ){
        return;
    }
	
	$pair = columns_gear($arr_cols1, $arr_cols2, 0, 0);

	for($i1=0, $i2=0; $i1<count($arr_cols1) || $i2<count($arr_cols2); )
	{
		if($i1>=count($arr_cols1)){
			while($i2<count($arr_cols2)){				
				$str .= diff_row_disp(null, $arr_cols2[$i2], $arr_cols1, $arr_cols2);
				$i2++;  $result=false;
			}
			break;
		}
		
		if($i2>=count($arr_cols2)){
			while($i1<count($arr_cols1)){				
				$str .= diff_row_disp($arr_cols1[$i1], null, $arr_cols1, $arr_cols2);
				$i1++;  $result=false;
			}
			break;
		}

		if(!empty($pair) && $i1>=$pair['k1'] && $i2<$pair['k2']){	
			$str .= diff_row_disp(null, $arr_cols2[$i2], $arr_cols1, $arr_cols2);
			$i2++;  $result=false;
		}
		
		if(!empty($pair) && $i2>=$pair['k2'] && $i1<$pair['k1']){
			$str .= diff_row_disp($arr_cols1[$i1], null, $arr_cols1, $arr_cols2);
			$i1++;  $result=false;
		}
		
		if(empty($pair) || (!empty($pair) && $i1<$pair['k1'] && $i2<$pair['k2']))
		{			
			if(strcmp($arr_cols1[$i1]['Field'], $arr_cols2[$i2]['Field'])<0){				
				$str .= diff_row_disp($arr_cols1[$i1], null, $arr_cols1, $arr_cols2);
				$i1++;  $result=false;
				continue;
			}
			
			if(strcmp($arr_cols1[$i1]['Field'], $arr_cols2[$i2]['Field'])>0){				
				$str .= diff_row_disp(null, $arr_cols2[$i2], $arr_cols1, $arr_cols2);
				$i2++;  $result=false;
				continue;
			}
		}
		
		if(empty($pair) || (!empty($pair) && $i1==$pair['k1'] && $i2==$pair['k2']))
		{						
			if($arr_cols1[$i1]['Key']===$arr_cols2[$i2]['Key']
			   && $arr_cols1[$i1]['sql_lang']===$arr_cols2[$i2]['sql_lang'])             
			{
				$str .= '<tr><td><span title="'.cols_show($arr_cols1[$i1]).'">'.$arr_cols1[$i1]['Field'].'</span><span class="field-comment">'.$arr_cols1[$i1]['comment'].'</span></td><td><span title="'.cols_show($arr_cols2[$i2]).'">'.$arr_cols2[$i2]['Field'].'</span><span class="field-comment">'.$arr_cols2[$i2]['comment'].'</span></td></tr>';
			} else {
				$str .= diff_row_disp($arr_cols1[$i1], $arr_cols2[$i2], $arr_cols1, $arr_cols2);
				$result=false;
			}
			$i1++; $i2++;			
		}
		
		if(!empty($pair) && $i1>$pair['k1'] && $i2>$pair['k2']){			
			$pair = columns_gear($arr_cols1, $arr_cols2, $i1, $i2);
		}
	}
			    
    return $result;
}

function diff_row_disp($col1, $col2, $arr_cols1, $arr_cols2)
{	
	$have = false; 
	if(empty($col1)){
		for( $i=0; $i<count($arr_cols1); $i++ ){
			if($col2['Field']===$arr_cols1[$i]['Field']){				
				$have = true;
				break;				
			}
		}
	}
	if(empty($col2)){
		for( $i=0; $i<count($arr_cols2); $i++ ){
			if($col1['Field']===$arr_cols2[$i]['Field']){
				$have = true;
				break;				
			}
		}
	}			
	
	if($have===true){
		if(empty($col1)){
			$str1 = '<span style="color:#9C9C9C; text-decoration:line-through;">'.$col2['Field'].'</span>';
		} else {
			$str1 = '<div class="diff_repeat"><nobr><span title="'.cols_show($col1).'">'.$col1['Field'].'</span><span class="field-comment">'.$col1['comment'].'</span></nobr></div>';
		}
		if(empty($col2)){
			$str2 = '<span style="color:#9C9C9C; text-decoration:line-through;">'.$col1['Field'].'</span>';
		} else {
			$str2 = '<div class="diff_repeat"><nobr><span title="'.cols_show($col2).'">'.$col2['Field'].'</span><span class="field-comment">'.$col2['comment'].'</span></nobr></div>';
		}
	} else {		
		if(empty($col1)){		
			$str1 = '<span style="color: #EE5931;">---</span>';		
		} else {
			$str1 = '<div class="diff_field"><nobr><span title="'.cols_show($col1).'">'.cols_show($col1).'</span></nobr></div>';
		}
		if(empty($col2)){
			$str2 = '<span style="color: #EE5931;">---</span>';				
		} else {
			$str2 = '<div class="diff_field"><nobr><span title="'.cols_show($col2).'">'.cols_show($col2).'</span></nobr></div>';
		}			
	}
	
    return '<tr><td>'.$str1.'</td><td>'.$str2.'</td></tr>';
}

function cols_show($col)
{       
    $str = $col['sql_lang'];
    if( !empty($col['Key']) ) { $str = '['.$col['Key'].'] '.$str; }
    return $str;
    //return $col['Field'].' '.$col['Type'].' '.$col['Null'].' '.$col['Key'].' '.$col['Default'].' '.$col['Extra'];
}

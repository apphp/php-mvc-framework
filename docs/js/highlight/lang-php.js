/**************\
 *                                                                ____   _____
 * DlHighlight -- a JavaScript-based syntax highlighting engine.  \  /_  /   /
 *                                                                 \  / /   /
 *        Author: Mihai Bazon, http://mihai.bazon.net/blog          \/ /_  /
 *     Copyright: (c) Dynarch.com 2007.  All rights reserved.        \  / /
 *                http://www.dynarch.com/                              / /
 *                                                                     \/
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
\******************************************************************************/

// Definitions for the PHP language.

(function(){

	var builtins = [
        "define",
        "defined",
        "dirname",        
        "run",
        "init",
        "A",
        "__construct",
        "CModel",
        "Accounts",
        "PagesModel",
        "CActiveRecord",
        "indexAction",
        "viewAction",
        "deleteAction",
        "loginAction",
        "editProfileAction",
        "protectedMethod",
        "privateMethod",
        "CController",
        "AccountController",
        "getInfo",
        "fetchAll",
        "customQuery",
        "customExec", 
        "_view",
        "accountsModel",
        "data",
        "CConfig",
        "get",
        "_db",
        "select",
        "insert",
        "update",
        "delete",
        "_table",
        "render",
        "redirect",
        "config",
        "CMailer",
        "CAuth",
        "CDebug",
        "model",
        "__CLASS__",
        "findByPk",
        "find",
        "findAll",
        "findByAttributes",
        "save",
        "fieldA",
        "fieldB",
        "_relations",
        "self",
        "HAS_ONE",
        "BELONGS_TO",
        "LEFT_OUTER_JOIN",
        "Pages",
        "title",
        "content",
        "records",
        "send",
        "email",
        "smtpMailer",
        "phpMailer",
        "phpMail",
        "strcasecmp",
        "getSession",
        "app",
        "set",
        "handleLogin",
        "handleLoggedIn",
        "AuthorsController",
        "AdminController",
        "firstName",
        "first_name",
        "getLoggedId",
        "isLoggedIn",
        "username",
        "password",
        "Login",
        "getRequest",
        "getPost",
        "errorField",
        "login",
        "TheSampleClass",
        "AbstractClass",
        "ClassInterface",
        "myFunctionName",
        "CWidget",
        "message",
        "menu",
        "renderContent",
        "setTemplate",
        "date_created",
        "date_updated",
        "date",
        "CComponent",
        "BlogMenu",
        "BlogHelper",
        "substr",
        "strTruncate",
        "strrpos",
        "AdminsController",
        "dashboardAction",
        "deleteAll",
        "deleteByPk",
        "ErrorController",
        "in_array",
		"create",
		"text",
		"t",
		"exists",
		"count",
        "max",
		"openForm",
		"CHtml",
		"is_array",
		"CString",
		"Settings",
		"ModulesSettings",
		"drawNewsBlock",
		"NL",
		"NewsComponent",
		"date_format",
		"strtotime",
		"strip_tags",
		"News",
		"param",
        "write",
        "drawShortcode",
        "prepareTab",
        "Component",
        "hasPrivilege",
        "manageAction",
        "addAction",
        "Admins",
        "Website",
        "setMetaTags",
        "Modules",
        "tabs",
        "Controller",
        "_activeMenu",
        "_breadCrumbs",
        "viewAll",
        "viewAllAction",
        "setFrontend",
        "setBackend",
        "prepareBackendAction",
        "_customFields",
        "getCsrfTokenValue",
        "insertAction",
        "LocalTime",
        "currentDateTime",
        "cacheOn",
        "cacheOff",
        "view",
        "updateByPk",
        "refresh",
	];

	var BUILTINS = {};
	for (var i = builtins.length; --i >= 0;)
		BUILTINS[builtins[i]] = true;

	var keywords = [
        "__FILE__",
        "DIRECTORY_SEPARATOR",
        "APPHP_PATH",
		"array",
        "require_once",
        "true",
        "false",
        "return",
        "class",
        "extends",
        "implements",
        "public",
        "protected",
        "private",
        "function",
        "protected",
        "parent",
        "new",
        "if",
        "else",
        "elseif",
        "static",
        "int",
        "NULL",
		"echo",
		"include",
		"const",
		"foreach",
		"as",
        "20",
		
	];

	var KEYWORDS = {};
	for (var i = keywords.length; --i >= 0;)
		KEYWORDS[keywords[i]] = true;

	var end_q_mark = {
		";" : true,
		"{" : true,
		"}" : true,
		"(" : true,
		")" : true,
		"," : true
	};

	var T = {

		WORD : function(txt){
			var m = /^(\$?\w+)/.exec(txt);
			if(m){
				var style = "operand";
				var tok = this.getLastToken();
				if(tok && tok.content == "function")
					style += " defun";
				var id = m[1];
				if(id in KEYWORDS){
					style += " keyword";
					if(id == "function"){
						if(tok){
							if(tok.type == "operator" && tok.content == "=" ||
							    tok.type == "hasharrow")
								tok = this.getLastToken(1);
							if(tok && tok.type == "operand")
								tok.style += " defun";
						}
					}
				} else if(id in BUILTINS){
					style += " builtin";
				}
				return {
					content : id,
					index   : m[0].length,
					type    : "operand",
					style   : style
				};
			}
		},

		REGEXP : function(txt){
			if(!this.lastTokenType(/^operand$/)){
				//var m = /^\x2f((\\.|[^\x2f\\\n])+)\x2f([gim]+)?/.exec(txt);
				//if(m) return {
				//	before	: "/",
				//	content	: m[1],
				//	after	: m[3] ? "/" + m[3] : "/",
				//	style	: "regexp",
				//	type	: "regexp",
				//	index   : m[0].length
				//};
			}
		},

		// catch some common errors
        //regexp	: /^[,+*=-]\s*[\)\}\]]/g,
		ERRORS : {
			regexp	: /^[+=-]\s*[\)\}\]]/g,
			content	: 0,
			style	: "error",
			type    : "error"
		},

		QUESTIONMARK : function(txt){
			if(txt.charAt(0) == "?")
				this.jsQuestionMark++;
		},

		ENDQMARK : function(txt){
			if(txt.charAt(0) in end_q_mark && this.jsQuestionMark > 0)
				this.jsQuestionMark--;
		},

		COMMA : function(txt){
			if(txt.charAt(0) == ',') return {
				content	: ",",
				style	: "comma",
				type	: "comma",
				index	: 1
			};
		},

		COLON : function(txt){
			//if(!this.jsQuestionMark && txt.charAt(0) == ":"){
			//	var tok = this.getLastToken();
			//	if(tok && /string|operand/.test(tok.type)){
			//		tok.style += " hashkey";
			//		return {
			//			content : ":",
			//			style   : "hasharrow",
			//			type    : "hasharrow",
			//			index   : 1
			//		};
			//	}
			//}
		}

	};

	var H = DlHighlight;
	var lang = H.registerLang("php", [ H.BASE.COMMENT_CPP,
					  H.BASE.COMMENT_C,
					  H.BASE.STRING,
					  T.WORD,
					  T.REGEXP,
					  T.ERRORS,
					  T.QUESTIONMARK,
					  T.ENDQMARK,
					  T.COMMA,
					  T.COLON,
					  H.BASE.OPERATOR,
					  H.BASE.PAREN
					]);

	lang.T = T;

	lang.start = function(){
		this.jsQuestionMark = 0;
	};

})();

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Index</title>
    <style>
        html, body              { background: #fefefe; font-family: "helvetica neue", helvetica, arial, sans-serif; font-size: 14px; padding:0px; margin:0px; }
        h1				        { font-size:22px; font-weight:bold; }
        h2				        { font-size:19px; font-weight:bold; margin:7px 0;background-color:#f1f2f3; }
        h3				        { font-size:14px; font-weight:bold; margin:5px 0; }
        a                       { color: #356AA0; }
        a:hover                 { color: #B02B2C; }
        header                  { background: #1a1a1a; color: #fff; height:40px; width:100%; }
        header nav              { margin:0 20px; padding:0px; }
        header nav ul           { margin:0; float:left; padding:0px; }
        header nav ul li        { padding:10px; float:left; display: block; margin-right:5px; }
        header nav ul li.active { }
        header nav ul li.active a { color: #fff; } 
        header nav ul li:hover  { background-color: #2a2a2a; } 
        header nav ul li:hover  a { color: #efefef; }
        header nav a            { color: #999999; font-size:15px; text-decoration: none; }
        section                 { background: #fff; padding:20px 0px 20px 10px; /*min-height:620px;*/ }
        .description            { color:#555; background-color: #f1f2f3; border-radius:6px; padding:8px; }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul class="menu">
				<li class=""><a href="../docs/">Documentation</a></li>
				<li class=""><a href="../utils/requirements/">Requirements</a></li>				
				<li class=""><a href="../utils/tests/">Tests</a></li>				
				<li class="active"><a href="../demos/">Demo</a></li>
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../index.html">Index</a></li>
			</ul>
        </nav>
    </header>
    <section>
        These examples demonstrate just a small part of ApPHP Framework abilities. 
        Select required example and click the link below:
        <br /><br /> 

        <table width="860px">
        <tr>
            <td width="80px">Example 1.</td>
            <td> - </td>
            <td><a href="hello-world/">Hello World</a></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <div class="description">
                    This is a simplest code that outputs "Hello, world" in the browser. It demonstrates a
                    basic logic of MVC framework work.
                </div>
            </td>
        </tr>
        <tr><td colspan="3" nowrap height="10px"></td></tr>
        <tr>
            <td>Example 2.</td>
            <td> - </td>
            <td><a href="static-site/">Static Site</a></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <div class="description">
                    This is a static web site, consists from the few pages. It allows page navigation by using a
                    top menu, adding/editing content of the pages, changing HTML code etc. No database used to
                    store page contents, it may be done directly via the code of appropriate controller. This script
                    uses a template feature of the framework.
                </div>
            </td>
        </tr>
        <tr><td colspan="3" nowrap height="10px"></td></tr>
        <tr>
            <td>Example 3.</td>
            <td> - </td>
            <td><a href="login-system/">Simple Login System</a></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <div class="description">
                    This is a simple login system, consists from the few pages and login module. It allows page
                    navigation by using a top menu, adding/editing content of the pages, changing HTML code etc.
                    Logged user has access to the protected area of the site. This script includes setup module.
                </div>
            </td>
        </tr>
        <tr><td colspan="3" nowrap height="10px"></td></tr>
        <tr>
            <td>Example 4.</td>
            <td> - </td>
            <td><a href="simple-blog/">Simple Blog</a></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <div class="description">
                    This simple blog site demonstrates some advanced features of the framework, it inludes: setup
                    and login modules, work with database and web forms, CRUD operations, form validation etc. In
                    administration area you could configure blog settings, author profile info, edit blog categories,
                    create and manage your posts. On the Front-End visitors can see last the posts, sorted by
                    categories or date of posting.
                </div>
            </td>
        </tr>
        <tr><td colspan="3" nowrap height="10px"></td></tr>
        <tr>
            <td>Example 5.</td>
            <td> - </td>
            <td><a href="simple-cms/">Simple CMS</a></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <div class="description">
                    This simple CMS site demonstrates some advanced features of the framework like: setup
                    and login modules, advanced widgets, work with database and web forms, CRUD operations, form
					validation etc. In administration area you could configure all major CMS settings, admin
					profile info, edit site categories, create and manage pages. On the Front-End visitors can
					see last the menu and pages. This script may be used as a basis for creating your own advanced
					application.
                </div>
            </td>
        </tr>
        </table>

    </section>
</body>
</html>


Web application development with the kalliste PHP framework

Introduction
------------

This document will make the most sense if you have access to a working application written with the kalliste PHP framework while reading through it. Additional outside reading will be required - this is merely a guidebook.

You should already know at least one programming language - if not go work through "Learn Python the Hard Way" or "The Little Schemer".

You should already know the basics of PHP - if not read through the first half of the Language Reference section of the PHP Manual on 
Basic syntax, 
Types,
Variables,
Constants,
Expressions,
Operators,
Control Structures,
Functions,
Classes and Objects.

No need to worry about Namespaces, Exceptions, Generators, References, and so on for now.

You should already know what MVC is - if not go read the Wikipedia article.

You should already know a little MySQL - if not go read about INSERT, SELECT, UPDATE, DELETE, LIMIT, and ORDER BY. 
You may also look at LEFT JOIN, DISTINCT, IN, GROUP BY, and MAX.

You should already know some basic HTML and CSS.

You should already be running some sort of Unix operating system such as Mac OS or Ubuntu. If not, go install Ubuntu now.


Edit text files
---------------

We'll be editing a lot of text, so we should use the best tools available.

I know of only 2 text editors. They are Emacs and VIM, and I use VIM. If you already know Emacs, fine. If you do not know Emacs or VIM, learn VIM. You will be able to use your VIM keys in Emacs later if down the road you decide Emacs does something you like.

To learn VIM, spend 1 hour playing through vimtutorial. Now you are as good with VIM as it is possible to be good with nano. From here, further effort will make you more powerful and efficient. Make a habit of using the letter keys for movement. Remap caps lock to another control key, and use that ctrl+c to switch modes. Learn regex replacements, and the virtues of visual select mode with ctrl+v. It's a good thing you're on Linux using the left and middle mouse buttons to copy and paste text between windows instead of using those shortcut keys for that.

If you use an IDE you can probably set it to use VIM keys or its not a good one. We probably won't use an IDE for PHP, unless you count VIM or Emacs as being one.

Concerning visual select mode: both v and ctrl+v are of note. v is more common, ctrl+v is something that other editor you used to use cannot do.

Connect to web servers
----------------------

If this section is not completely clear to you on the first reading, you might just skip it and continue to use whatever tools you would otherwise have used. For now.

We shall work on various projects on various servers. Mostly one project at a time, though we will switch often. Let's have a convenient way to set a project as being current, and to sync the current project to a developement/testing machine and to a production machine.

I cd to a project folder and run:

```
    setproj .
```

Then regardless of my current directory I can run proj to sync to the development server and prod to sync to the production server. One might even set a hotkey for this.

```
    user@laptop:~/bin$ cat proj
    #!/bin/sh
    PROJECT=/home/user/Projects/client/myapp
    cd ${PROJECT}
    make
    user@laptop:~/bin$ cat prod
    #!/bin/sh
    PROJECT=/home/user/Projects/client/myapp
    cd ${PROJECT}
    make production
    user@laptop:~/bin$ cat setproj
    #!/bin/sh
    NEWPATH=$(cd "$@"; pwd)
    perl -pi -e s?^PROJECT.*?PROJECT=${NEWPATH}? ~/bin/proj
    perl -pi -e s?^PROJECT.*?PROJECT=${NEWPATH}? ~/bin/prod
```

We run a Makefile inside the project folder which knows how to sync that project. Different tools particular to the project may be used to sync.

I organize my projects like so:
```
    ~/Projects/ORGANIZATION/PROJECT/Makefile has the sync commands
    ~/Projects/ORGANIZATION/PROJECT/design has the files to be synced
```

So a Makefile might look like this

```
    all: up
    up:
        unison ~/Projects/client/myapp/design ssh://development.example.com//home/domains/development.example.com/pages/client/myapp -ignore 'Name .*.swp' -ignore 'Path templates_c' -ignore 'Name .htaccess
    
    production:
        unison ~/Projects/client/myapp/design ssh://production.example.com//home/webuser/public_html  -ignore 'Name .*.swp' -servercmd bin/unison -ignore 'Path templates_c'
```
    
Beware the unfortunate mandatory tabs in the Makefile syntax and note the unusual syntax used by unison for specifying paths and files to be excluded from the sync.

Unison is an excellent tool for syncing to servers where someone else (perhaps a graphic designer or the client themselves or a CSS specialist...) who will not use version control may possibly change things. Rather than blow up when things have changed remotely, simply let us see the modification times and the diff, and we'll manually confirm the sync direction if the tool cannot guess correctly. Meanwhile for some projects we'll probably use git and the Makefile will be different.

All servers I have root on will have the same version of unison as I use on my workstation. Servers where I don't have root but where I do have ssh, I put it in ~/bin and specify that path in the Makefile. Servers where I don't have ssh I usually tell the potential client that they should get me ssh or probably I'm not their guy for the job.

Your solution may be totally different! But perhaps copying mine will work for you. If you do go with something different, try to solve the same problems well that this one solves well.

Configuration
-------------

Our web applications will sometimes have multiple entry points. Even if all web requests are going through the same php (which they generally will) it will still be convenient to have some actions that are called from cron. Also, there are some settings which will differ between installations of the software. Let us put these things in one place, and separate them from the application logic.

The one thing that you nearly always need to configure for a PHP application is login information for a MySQL database. This is just the sort of thing to you don't want to expose to 3rd parties so let's not screw around with things like putting it in a YAML file and then marking that as forbidden to access in an htaccess file. We could put it outside the web directory but that adds some significant practical difficulties. The middle path is to put it inside a php script and hope that no one breaks the web server config to where php files are treated as plain text.

Here is the code I use for configuration. It's stupid simple. It's just a globally defined function (as in not a class or object method) that contains a PHP array and returns the value at a particular index. Some common entries are defined.

    function config($var='') {
      $values = array(
    
        'system_mail_from' => '',
        'base_url' => '',
        'password' => '',
        'db_user' => '',
        'db_pass' => '',
        'db_name' => '',
        'db_host' => 'localhost',
        'capture_redirects' => 0,
        'show_queries' => 0,
        'show_debug' => 0,
        'send_mail' => 1,
    
      );
      if (array_key_exists($var, $values)) { return $values[$var]; }
      if ($var == '') { return $values; } else { return false; }
    }

Here follows an explanation of these keys -

<dl>
<dt>system_mail_from</dt><dd> An e-mail address to use when sending mail</dd>
<dt>base_url</dt><dd> If you are doing something that requires the application to know its own URL, put it here</dd>
<dt>db_user</dt><dd> (MySQL) database user account</dd>
<dt>db_pass</dt><dd> database password</dd>
<dt>db_name</dt><dd> name of the database</dd>
<dt>db_host</dt><dd> database host</dd>
<dt>capture_redirects</dt><dd> Usually when we submit a form that makes a change to the database we do a POST request and get back an http redirect. If you want to debug what happens in this process, turn this on to get an HTML page instead of a redirect</dd>
<dt>show_queries</dt><dd> While developing it is useful to see all the SQL queries used in generating a page</dd>
<dt>show_debug</dt><dd> We may have other debug messages displayed at the bottom of the page as well</dd>
<dt>send_mail</dt><dd> We'll do things like copy a production app's database to a test server and do some debugging. This is one scenario where we don't want any e-mails to go out to users.</dd>
</dd>

There are a few more things that we'll put in this file beyond these settings.

A template_config function appears - this is unfortunate and should be removed in a later version of the framework.

We make sure that we see errors in the development version and maybe we turn that off in production. In either case we override the server config.

```
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
```

Recent versions of PHP hate you if you don't specify a timezone setting but many servers do not. Really they should just respect /etc/localtime but that is not our battle today.

```
    date_default_timezone_set("America/Chicago");
```

Finally, regardless of the entry point we are going to assume that we should connect to the database.

```
    require_once("includes/db/base.php");
    $db = getdb(config('db_host'), config('db_user'), config('db_pass'), config('db_name'));
```

Writing Views
-------------

We'll be using a MVC framework. Possibly there are better ways to structure a web application, but this is certainly a good one, and basically most every PHP app that I've seen not doing this is much more of a pain to maintain than the ones that do.

Smarty is a mature and battle tested template system. Originally PHP was itself intended to just be a template system, but Smarty does the job well and using it will help us keep things out of the views that really don't belong there.

Here are the relevant documentation sections for the subset of smarty that I use:

Basic syntax, including the {literal} tag
<a href="http://www.smarty.net/docs/en/language.basic.syntax.tpl">http://www.smarty.net/docs/en/language.basic.syntax.tpl</a>
<a href="http://www.smarty.net/docs/en/language.escaping.tpl">http://www.smarty.net/docs/en/language.escaping.tpl</a>

The escape modifier -  we should use escape:'htmlall' when we are printing any user generated text.
<a href="http://www.smarty.net/docs/en/language.modifier.escape.tpl">http://www.smarty.net/docs/en/language.modifier.escape.tpl</a>

String format and date format
<a href="http://www.smarty.net/docs/en/language.modifier.string.format.tpl">http://www.smarty.net/docs/en/language.modifier.string.format.tpl</a>
<a href="http://www.smarty.net/docs/en/language.modifier.date.format.tpl">http://www.smarty.net/docs/en/language.modifier.date.format.tpl</a>

{foreach} and {if} / {else}
<a href="http://www.smarty.net/docs/en/language.function.foreach.tpl">http://www.smarty.net/docs/en/language.function.foreach.tpl</a>
<a href="http://www.smarty.net/docs/en/language.function.if.tpl">http://www.smarty.net/docs/en/language.function.if.tpl</a>

{include}
<a href="http://www.smarty.net/docs/en/language.function.include.tpl">http://www.smarty.net/docs/en/language.function.include.tpl</a>

{counter} has its uses sometimes and {cycle} will be relevant when we want alternating rows in a table to be different colors
<a href="http://www.smarty.net/docs/en/language.custom.functions.tpl#language.function.counter">http://www.smarty.net/docs/en/language.custom.functions.tpl#language.function.counter</a>
<a href="http://www.smarty.net/docs/en/language.function.cycle.tpl">http://www.smarty.net/docs/en/language.function.cycle.tpl</a>

Maybe we'll use {math} or {assign} but mostly the things they do should be done in controller
<a href="http://www.smarty.net/docs/en/language.function.math.tpl">http://www.smarty.net/docs/en/language.function.math.tpl</a>
<a href="http://www.smarty.net/docs/en/language.function.assign.tpl">http://www.smarty.net/docs/en/language.function.assign.tpl</a>

$smarty.request is something that I have used but which may not be necessary
<a href="http://www.smarty.net/docs/en/language.variables.smarty.tpl#language.variables.smarty.request">http://www.smarty.net/docs/en/language.variables.smarty.tpl#language.variables.smarty.request</a>


Workings of the controller system
---------------------------------

The kController class saves us from the trouble of writing repetitive code to decide what action should be taken given a particular HTTP request, and how to map the information in that request to values in the code that performs those actions.

Our system here is dead stupid simple. We use a request variable named action to decide which method of the controller gets called, and we populate the method parameters with values taken from the same names in the request. This is done using reflection in the constructor of kController and is based on primitives in includes/general/request.php

When a controller method has done its job, it typically returns an array of information to be passed on to a view. Rather than waste time in the error-prone task of listing each variable that will be needed in the view, we should note that its ok for the view to have access to the full scope of its relevant controller method, and we'll just pass the full scope. Fortunately PHP let's us do this with get_defined_vars() - unfortunately there is no way to make this prettier, shorter, or automatic.

A more sophisticated system would keep different controller classes separate, would allow for different routes to be configured to map URLs to methods, and would have the templates organized into sub-directories according to their controller. This system today shoves all the methods in all the controllers into the same list and renders them by default using the smarty template from the templates directory of the same name as the method. This works fine for apps where the users don't care about pretty URLs, and where the software is not so big that you have different coders working on different controllers.

In methods where we are handling a request to make a change to the data, we will take in a POST request, and return a redirect. This prevents badness like the user bollocksing up the database by back-buttoning to non-idempotent operations. For those methods we will do something like this at the end:
```
    return app_redirect('new_method_after_redirect', compact('vars', 'topass', 'along'));
```
This will result in an HTTP header that looks something like this:
```
    Location: ?action=new_method_after_redirect?vars=foo&topass=bar&along=baz
```

This is all made to work in includes/oo/app.php using primitives in includes/general/request.php and includes/general/template.php

The base layer for databases
----------------------------

The way most PHP programs talk to databases is a total nightmare consisting of mostly repetitive boilerplate code. We'll tackle this in layers that address increasingly high level problems.

At the level of includes/db/base.php we wrap the functions that connect us to a database. I've redone this file several times - first to switch from the old mysql php bindings to the newer mysqli (improved) php bindings, and more recently to use PDO_MySQL. This required little or no changes outside this small file. Using other databases such as sqlite and postgresql also mostly involves changing this just this file.

We will assume that all queries will be to the same database. This has been true for every app I've written so it makes no since to specify it every time we want some data. You'll see some incomplete support in the code for the possibility of talking to some other database than the default database.

We'll also want to suitably escape any data that is going into the database so we don't break our queries and get injection attacks. escape_str() and array_escape() do this.

Structured data from databases
------------------------------

The PHP array data structure is a wonderful foundation for web applications because it is well suited to representing and working with a database record, an ordered list of records such as you might get as the result of a query, a single column, a mapping of one column to another, or an HTTP request.

Usually the first thing a PHP framework does is ruin all of this by handling models using some sort of Active Record based system.

In includes/db/structures.php you will find functions that build on includes/db/base.php and handle taking SQL queries and returning structured data. For example, this function pulls a table of results:

    function query_to_assoc_list($query) {
      $ret = array();
      $result = sql_query_dbg($query);
      if (!$result) { return FALSE; }
      while ($row = sql_fetch_assoc($result)) {
        $ret[] = $row;
      }
      return $ret;
    }

Generating SQL queries
----------------------

SQL is a great way to describe data that you want out of a database. We should learn SQL well and not be afraid to write queries and put them in our code when complex queries are called for. However we shouldn't waste time (and increase our debugging overhead) writing SQL queries for simple operations that can be described cleanly and easily in PHP.

Some code for generating parts of a SQL query can be find in includes/db/generated.php

    sql_where() turns an array of key-value pairs into a WHERE clause
    array('foo' => 'bar', 'baz' => 'quux')
    becomes
    WHERE (1=1) AND (`foo`='bar') AND (`baz`='quux')

We could eliminate the (1=1) in many cases but its nice to have in some cases because we can now always mix this generated code with manually written conditions by just adding another 'AND condition' at the end.

sql_orders_limits() turns parameters for sorting data and limiting the number of records returned into ORDER and LIMIT clauses.

There is support here for having the parameters take different names, but by default we would have something like array('sort' => 'department', 'order' => 'DESC', 'sort2' => 'lastname', 'per_page' => '50') to get up to 50 records sorted by department in descending order and then by last name.

Elsewhere I have written a function that accepts the same parameters as sql_orders_limits() and uses the PHP array_multisort() function to produce the same results as if we had made database engine do it. This is useful when you find that you want to sort on columns that are calculated by the model code and not actually stored in the database.

Also in this file is some code to help build links we can use to change the sorting parameters when we are browsing data.

The functions in includes/db/fetch.php combine the operations in includes/db/structures.php and includes/db/generated.php to return structured results from structured descriptions. For example, to get all the supervisors in the sales department you might do something like this:
```
    get_records('users', array('department' => 'sales', 'type' => 'supervisor'))
```

Database model classes
----------------------

In includes/oo/orm.php you will find code that builds on includes/db/fetch.php and allows you to have PHP classes named according to your database tables. It also uses the operations in includes/db/modify.php which deal with INSERTs, UPDATEs, and DELETEs.

Now for each table in the database we'll have a class that extends kORM. All you have to do is subclass kORM, and the methods in orm.php will use PHP's get_called_class() function to determine the table name.

So, this:
```
    get_records('users')
```
becomes this:
```
    users::records()
```

For most tables we'll override the records() function to provide some additional functionality such as special handling of certain columns, or JOINing to another table. Many times we'll override some of the other methods as well. kORM provides a structure that gets us up and running doing work in the controller code fast while also allowing for complex models to be developed when we need them.

One more little bit of magic. In includes/oo/loader.php we have a function that we'll use as our class loader. If we call a class that isn't loaded, try loading a model of the same name from the models directory. If there isn't one there, assume that we want a kORM class to talk to a database table of the same name. This function actually uses eval() to make that possible:
    eval("class $class extends kORM {}");

Now we don't have to have a bunch of lines like this:
    require_once("includes/models/users.php")
And if we aren't overriding the behavior defined in kORM, we don't even have to have an includes/models/users.php file at all. To pull records from a new database table foo, we just create that table in the database and call foo::records() in our code wherever we need it.

Data Description Language
-------------------------

Any web application we build will need to store and manipulate data for multiple concurrent users. These changes should be Atomic, Consistent, Isolated, and Durable. In short, we need a database.

You should develop some notion of database normalization. This will save you some time because a normalized database will naturally lend itself to simpler, more maintainable code in your application. Don't bother trying to become an expert - just read through some reasonable description of 1NF, 2NF, and 3NF. Perhaps here <a href="http://databases.about.com/od/specificproducts/a/normalization.htm">http://databases.about.com/od/specificproducts/a/normalization.htm</a>

The best way to pick up the actual syntax for describing a database will be to create some databases using phpMyAdmin. The full range of options are available through the interface and whenever you create or modify a table it will show you the actual code. At some point you will probably transition to writing this code directly, but there is no hurry - you don't have to do it often and phpMyAdmin is fine.

Here are the particular types and conventions I use --
Column names are all lower case with underscores between words
The first column is
`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT
Any short string such as anything coming from a text box will be
VARCHAR(255) NOT NULL
Maybe you will make them shorter than 255 sometimes when you are certain of the length but don't waste time trying to work out how long each column should be. When in doubt, set it at 255 and be done.
text boxes are
TEXT NOT NULL
references to other tables will be
other_table_id INT(11) UNSIGNED NOT NULL
We may have some ENUM() fields and we may have some FLOAT() fields
Booleans are
TINYINT(1) NOT NULL
Data and time information are
DATETIME NOT NULL
Note that while VARCHAR(3) contains up to 3 characters, INT(2) and INT(7) both store a 32-bit integer. See:
<a href="http://dev.mysql.com/doc/refman/5.1/en/integer-types.html">http://dev.mysql.com/doc/refman/5.1/en/integer-types.html</a><br />
<a href="http://dev.mysql.com/doc/refman/5.0/en/numeric-type-attributes.html">http://dev.mysql.com/doc/refman/5.0/en/numeric-type-attributes.html</a><br />



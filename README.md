Memtext
=======

Memtext is an application, which allows you to learn english by reading
favourite books (or biz articles) and train memory via passing tests.
Ispired by memrise.com.

How to use
----------
1. After registration, click on "create new text" button in user profile.
1. Copy and paste a fragment of the text you want to translate. One or two pages
will be a comfortable volume to work with. Maximum length is limited by 64kb.
1. Format the text in wysiwig, if you want. Tags listed in 'purifier' config section
are acceptable. By default: p, headers, em and strong elements allowed.
1. After submitting, you may watch the vocabulary attached to the text and delete
garbage like short words, or words you already know. And finally you can examine
your language skills by repeating the words in test form.

System requirements
-------------------
1. Php ^5.4
1. Mysql ^5.5
1. Redis >=2.8
1. Webserver apache/nginx with mod_rewrite tool

How to install
--------------
1. Install via composer
    ``` sh
$ composer install
```

1. Import database schema
    ``` sh
$ mysql -uusername -ppassword memtext < schema/mysql.sql
```

1. If needed, change config like dbname etc in settings.php.

If you are a windows user, you need to manually copy some files listed in
scripts/post-update-cmd.sh from vendor dir to their destination in public dir.

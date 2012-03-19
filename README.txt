Description - generator.php

USE ::
In typical web application we need to use database to store the data. In this scenario we need to write queries to add/retrieve/remove data from
the database. We can easily take a look at the database and write the queries. But consider there are more than 500 tables in your database.
In this case it is practically not possible to remember names of all the tables and their respective columns. Looking at database every time
you need to write a query wastes significant amount of time. So here I am with easier solution.

Just run dbblueprint.php with your database credentials.

For eg.

<?php

$hostname = "";//your host name
$username = "";//your username
$password = ""//your password
$database = ""//name of your database

getDatabaseBlueprint($hostname,$username,$password,$database);

?>

Thats it. This will create a new file tables.php at the same location this script is put.
All you need to do is simply include tables.php in your code.

This will generate a new namespace. Name of the namespace is same as that of your database.

This namespace contains classes.
Number of classes in this namespace = Number of tables in your database.
There is one class for each table. Name of the class = name of the table.

Each class contains static variable with the name of columns in that table.

This is a helper module and it is designed to work properly in NetBeans IDE.

So while writing the queries,

write

database_name/table_name::column_name
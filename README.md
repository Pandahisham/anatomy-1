Question and Answer Website for Medical Anatomy Class
==========

##About
The files for this website are provided as-is and is free to use, fork, etc. I created it about two years ago, hence why the code and schema is not in the best shape. A SQL dump of all the questions and answers is included sans the original user data.

##Suggested Changes
* Change all table collations to UTF8 / utf8_general_ci.
* Change all tables to InnoDB.
* Add more indexes for module and lecture numbers.
* Add foreign keys to database.
* Change password algorithm to use `password_hash()`: http://php.net/manual/en/function.password-hash.php
* Use Boostrap for web responsiveness.
* Upgrade to CodeIgniter 3.x.

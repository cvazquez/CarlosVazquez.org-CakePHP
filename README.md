CarlosVazquez.org-CakePHP
=========================

Description
-----------
This is a version of my blog written using CakePHP, located on www.carlosvazquez.org
See http://www.carlosvazquez.org/blog/category/Portfolio for screenshots of the admin area (coming soon)

I started out by using the blog admin code examples from the cakephp blog tutorial and the access control list examples. I have added the following features to the admin area so far:

Note: The blog itself (outside of the admin), was originally written using Coldfusion using the CF WHeels framework (see this version in my github account). The database design is based on CF Wheels' standards. But when accessing the database through CakePHP, I created CakePHP tables views that directly interact with the CF WHeels tables. 

CakePHP Database Standards
* To conform with CakePHP's database standards, I created views, using CakePHP's standards, that point to database tables I originally created in CFWheels.
* The CakePHP Database views, written in MySQL, can be found in /databases/cvazquezblogcake/cvazquezblogcake.sql
* The /app/Config/database.php file is including another database file, so I don't expose my database connection information

Publish At date
* The date the blog post can display
* A null date will not allow the blog post to display

Categories Selection
* I used a multiselect JQUERY library to select multiple categories to apply to a blog post

Saving of Drafts
* Using TinyMCE's save button, I use Javascript and JQUERY to detect the save button being clicked. Then I use AJAX to save a draft of the post body to a post drafts database table.

Drafts History
* Using JQUERY and AJAX, when "View Drafts" is clicked, I retrieve a list of drafts associated with the current post being edited






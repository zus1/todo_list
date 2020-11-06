<h3>About</h3>
Project that will ist tasks (todo list) assigned to user. Each task will contain description, and also assigned to and status
For now there is no option to login user, so tasks are listed by direct url inputs
<h3>Install</h3>
There is two ways to install and run project:
<ol>
    <li>Cloning this repository and run on docker or localhost (dev mode)</li>
    <li>Cloning composer repository and run docker only (production mode) </li>
</ol>
Second way is described on its own repo <a href="https://github.com/zus1/todo_list-composer">here</a>
<br><br>
For first way to install, steps are following:<br><br>
Clone repository
<pre><code>git clone https://github.com/zus1/todo_list.git local_directory</code></pre>
Now all dependencies for symfony needs to be installed, cd to you work directory and run
<br><br>
<pre><code>composer update</code></pre>
If you dont have installed composer you can do sp by running (works on linux distributions)
<br><br>
<pre><code>curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer</code></pre>
And after installation is done, run command in step before again
<br><br>
Now you can boot up docker (if you prefer doing it docker way)
<br><br>
<pre><code>docker-compose up</code></pre>
This will build web container and db container
<br><br>
Now bash into web container and run migrations
<br><br>
<pre><code>php bin/console doctrine:migrations:migrate</code></pre>
And that it, you are ready to fly. Go to localhost:8081 and everything should be working nicely
<br><br>
If you dont wish to build with docker then first install php symfony on you local machine, instructions can be found
<a href="https://symfony.com/doc/current/setup.html">here</a>

Only thing you basically need are symfony binaries, you cna stop after done with that step

Now also run migration but on your local machine. But before that, check .env file and change this line to fir your local db credentials
<br><br>
<pre><code>DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"</code></pre>
After migrations are done you can start local built in symfony server
<br><br>
<pre><code>symfony server:start</code></pre>
This will start local server on port 8000, and if you go to localhost:8000 you should be ready to fly :) 

<h3>How to test/use</h3>
Without login system implemented, project works on direct url call. To start go to
<br><br>
<pre><code>localhost:8081 for docker version or localhost:8000 for local version</code></pre>
And you should be redirected to /list page. You wont see any task yet so you create a few. After that you can see tasks for each user
by passing his id to url like following
<br><br>
<pre><code>localhost:8081/list/1</code></pre>
This will list all tasks for user with id 1. 

When migration are run, they will add 3 users to database with ids 1, 3 and 3. User with id 1 is admin user.


And that will be all folks, have fun!
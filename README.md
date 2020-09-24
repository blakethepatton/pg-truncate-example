This is an example repository for demonstrating the danger of the 
current way that laravels' truncate method works in postgres. 

### Prerequisites

To run this locally, you will need php 7.2^ docker, docker-compose and port 5432 unbound, if you want to use your own 
terminal, add postgres to your hosts file to refer to 127.0.0.1. Otherwise you can run php commands from inside the 
container with ```docker exec -it `docker ps -f "name=pg-truncate-example_php" --format "{{.ID}}" -l\` /bin/bash```.

### Setup

<img src="https://raw.githubusercontent.com/blakethepatton/pg-truncate-example/master/diagram.png" width="400">

Once cloned, run `docker-compose up -d` to get the containers downloaded and running. Then run `php artisan migrate` 
followed by `php artisan db:seed` this will create a sample database with 500 users, 50 products and 2000 orders with 
payments. There are no product categories created yet because in this example, the product categories are a new feature 
of the application. 

### Before state

Once seeding is complete, you can run `php artisan tinker` and then execute `Orders::count()` to get the count of orders
in your application, you should see 2000. You can also get the count of Payments with `Payment::count()` `2000` and 
finally products with `Product::count()` which is `50`. Let's exist the tinker session now. `exit`

### Product categories

By now it's established that we have 2,000 orders on our 50 products with 2,000 payments. Cool, lets add some product 
categories. `php artisan db:seed --class=ProductCategorySeeder` that will create the product categories for our store.

Once again in a tinker session, let's look at the application `php artisan tinker`. Let's check that our categories are
created. `ProductCategory::get()` should show us our five categories. Excellent! Let's go apply some to some of the 
products `Product::first()`. That's weird, it returned `null`. Are our orders okay? `Order::count()` Oh. No. All of our
orders are gone. All of our products are gone! All of our payments are gone! What just happened?

#### Analysis

If we look at the ProductCategorySeeder and what it's actually doing, that'll give us a clue as to what's going on. 

```php
DB::table('product_categories')->truncate();
DB::table('product_categories')->insert([
    [
        'id' => 1,
        'name' => 'office'
    ],
    [
        'id' => 2,
        'name' => 'furniture'
    ],
    [
        'id' => 3,
        'name' => 'clothing'
    ],
    [
        'id' => 4,
        'name' => 'produce'
    ],
    [
        'id' => 5,
        'name' => 'homegoods'
    ],
]);
```

Really there's nothing super weird with this. We empty the table (maybe there were fewer categories before) and then we 
create the new categories. But what actually happened here? 

In the table structure, we have products belonging to product categories. That's all well and good, this is a relational
database. However, when you truncate with raw sql, that would throw an error and not allow the truncation of the 
`product_categories` table because it's referenced by other tables. You can drop constraints or truncate tables. 

To combat that issue, [#26389](https://github.com/laravel/framework/pull/26389) added cascade to the grammar for 
truncations. What that does is tells postgres to go ahead and truncate any tables that reference this table. 

#### Opinion

It's my opinion that if I'm going to be taking an action that's going to jeopardize my database, that I get warned about 
it. I'd rather get an error saying, there are references to this table, you need to drop those before you truncate this
table or truncate those tables too. In the meantime, just don't truncate your tables in postgres. Unless you know what 
it's going to do. Simple enough!  

#### Solution?

My proposed solution is to get rid of the cascading statement of the truncate command. I would wholeheartedly rather 
get an error warning me of what I'm about to do than to actually do it. If you check out the branch `patched` you can 
try this again with the patched version of laravel that disallows such destruction. 

`git checkout patched`
`php artisan migrate:refresh`
`php artisan db:seed`
`php artisan db:seed --class=ProductCategorySeeder` -> `Errors out warning you about foreign key constraints.`

The original issue was created because even with `Schema::disableForeignKeyConstraints();` set, you could not truncate
the table and instead got an error (which the patch restores). PG likes to ensure that constraints are always going to
be valid. I would rather drop constraints referencing that table, do what I need to do to the table, then restore the 
constraints afterward. I would never expect truncating a table to drop related tables, I would expect an error. Like you
get when you try to delete a record that has a foreign key referencing it. 

#### Cleanup

Now we're done, feel free to `docker-compose down` and clean the files off your drive. 

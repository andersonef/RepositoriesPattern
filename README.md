## Repositories Pattern on Laravel 5.1 or superior

This package allows you to implement the Repositories design pattern in your laravel application. It makes easy to use a service layer too.

##Instalation
Its simply to install, just run:

	composer require andersonef/repositories-pattern
	

##Service Provider
You must register this package service provider at you **config/app.php** file. Just add this line at your $providers array:

```php
Andersonef\Repositories\Providers\RepositoryProvider::class,
```

##Creating your Repositories and Services:
At your console, enter the following command:

	php artisan make:repository BlogNamespace/Post --entity=Models/Post
	
This will create the following file structure in your app directory:

app/ 

	Repositories/
	
		BlogNamespace/
		
			PostRepository.php
			
	Services/
	
		BlogNamespace/
		
			PostService.php


##Repository file structure
Your repository file will be created with the following code:
	 
```php
namespace Inet\Repositories\BlogNamespace;

use Andersonef\Repositories\Abstracts\RepositoryAbstract;
use \Post;

/**
* Data repository to work with entity Post.
*
* Class PostRepository
* @package Inet\Repositories\BlogNamespace
*/
class PostRepository extends RepositoryAbstract{


public function entity()
{
    return \Post::class;
}

}
```

##Service file structure
And your PostService.php file wil be created with the following code:

```php
namespace Inet\Services\BlogNamespace;

use Andersonef\Repositories\Abstracts\ServiceAbstract;
use Illuminate\Database\DatabaseManager;
use \Inet\Repositories\BlogNamespace\PostRepository;

/**
* Service layer that will applies all application rules to work with Post class.
*
* Class PostService
* @package Inet\Services\BlogNamespace
*/
class PostService extends ServiceAbstract{

  /**
   * This constructor will receive by dependency injection a instance of PostRepository and DatabaseManager.
   *
   * @param PostRepository $repository
   * @param DatabaseManager $db
   */
  public function __construct(PostRepository $repository, DatabaseManager $db)
  {
      parent::__construct($repository, $db);
  }
}
```

##Usage
Using this pattern you wil be able to separate your application rules from your data access rules and you will be able to reuse your code in a very simple way.
Imagine that you have an app that users can register using the public pages AND the admin register users using the admin panel. The rules to register an user are the same on both cases, but the admin panel must require an admin logged user AND has an option to isent the new user to pay his subscription value.
In the old way you would have to replicate the code or use a laravel command to isolate the user registration rules.
Using the service layer and the repository pattern you will write the user registration rules inside the UserService and on both controllers (public page registration and admin panel) you would call the $userService->create($request->all()).
If you must implement an api to save users from an android interface, you can reuse your service layer and just change the way your controller respond to the client.

##Repository Inherited Methods:
Your repository have some inherited methods from RepositoryAbstract class. They are:
 - **create(array $data);**: Tries to create a new instance of your specified entity. **WARNING: Your entity must declare the $fillable field**
 - **update(array $data, $id);**: Update specified entity.
 - **delete($id);**: Delete the specified entity
 - **find($id, array $columns = ['*']);**: Find an instance of specified entity by id
 - **findBy(array $fields, array $columns = ['*']);**: Find a collection of specified instances using the fields (see phpdocs).
 
 ##Service Inherited Methods:
 Your service will be similar to your repository. The main difference is that your service layer must implement application logic, so it have transaction in its methods:
  - **create(array $data)**: Open a transaction and try to create an instance using the $data array. You can override it
  - **update(array $data, $id)**: Open a transaction and try to update the instance using $data array.
  - **delete($id)**: OPen a transaction and try to delete an instance of specified entity.
  
  ##Magic Methods:
  For convenience, we can use magic methods on both service and repository classes: 
  
```php
$yourservice->repositoryMethod(); // this will be the same as: $yourservice->getRepository()->repositoryMethod();
$yourRepository->entityMethod(); // this will be the same as: $tyourRepository->getEntity()->entityMethod();
```
  
  ##Using Criterias:
  You can implement criteria to reuse your application query rules. This package brings you one default criteria, the **FindUsingLikeCriteria**.
  Lets think you must implement a search field on your blog, and must bring all your posts that have some text like $query variable.
  You can simply do:
  
```php
$result = $postService->findByCriteria(new FindUsingLikeCriteria($request->get('textQuery')))->paginate(10);
```
  
  This will returns to you a collection of posts that has title, or content, or author like the text inside 'textQuery' request attribute.
  You can create your own criterias, its really simple to do it:
  ##Creating your own criteria
  This wil be your custom criteria:
  
```php 
namespace Andersonef\Repositories\Criteria;

use Andersonef\Repositories\Abstracts\CriteriaAbstract;
use Andersonef\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Model;

class UnreadRecentPostsCriteria extends CriteriaAbstract{

public function apply(Model $model, RepositoryContract $repository)
{
    $model
    ->where('created_at','>',(new \DateTime())->sub(new \DateInterval('P3D'))->format('Y-m-d'))
    ->where('status_read', '=', 1);
    return $model;
}
}
```

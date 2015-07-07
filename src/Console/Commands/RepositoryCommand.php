<?php

namespace Andersonef\Repositories\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class RepositoryCommand extends GeneratorCommand
{
    use \Illuminate\Console\AppNamespaceDetectorTrait;

    protected $entity;
    protected $name;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository
        {name : Repository name. ex: ContactRepository (always ending with the Repository word)}
        {--entity=: Which database entity this repository will use?.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create inside app folder, the repository and service class to work with certain database entity.';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    public function getStub(){
        return __DIR__.'/stubs/RepositoryStub.stub';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try{
            $this->createRepository();
            $this->createService();
        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }
    }


    public function createRepository()
    {
        $name = 'Repositories/'.$this->argument('name');
        $n = explode('/', $this->argument('name'));
        $n = array_pop($n);
        $this->name = $n.'Repository';

        $this->entity = $this->option('entity');



        if(!file_exists($path = $this->getPath($name.'Repository')))
        {

            $this->files->makeDirectory(dirname($path), 0777, true, true);

        }

        if(file_exists($name))
        {
            return $this->error('There is a file with the same name');
        }

        $fp = fopen($path, 'w+');
        if(!fputs($fp,$this->renderRepositoryClass())) throw new \Exception('It can not create the repository file with the given name. Check if you have permission to create it.');
        return $this->info('Repository file successfully created');

    }


    public function createService()
    {
        $nname = str_replace('Repository', 'Service', $this->argument('name'));
        $name = 'Services/'.$nname;
        $n = explode('/', $nname);
        $n = array_pop($n);
        $this->name = $n.'Service';
        $this->entity = $this->option('entity');


        if(!file_exists($path = $this->getPath($name.'Service')))
        {

            $this->files->makeDirectory(dirname($path), 0777, true, true);

        }

        if(file_exists($name))
        {
            return $this->error('There is a file with the same name');
        }

        $fp = fopen($path, 'w+');
        if(!fputs($fp,$this->renderServiceClass())) throw new \Exception('It can not create the service file with the given name. Check if you have permission to create it.');
        return $this->info('Service file successfully created!');

    }


    protected function renderRepositoryClass()
    {
        $str = $this->files->get(__DIR__.'/stubs/RepositoryStub.stub');
        $namespace = $this->getAppNamespace().'Repositories/'.$this->argument('name');
        $c = explode('/', $namespace);
        $classe = array_pop($c);
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = explode('\\', $namespace);
        array_pop($namespace);
        $namespace = implode('\\', $namespace);
        $str = str_replace('DummyNamespace', $namespace, $str);
        $str = str_replace('DummyClass', $classe.'Repository', $str);

        $entity = str_replace('/', '\\', $this->option('entity'));
        $str = str_replace('DummyEntity', '\\'.$entity, $str);
        $small = explode('\\', $entity);
        $small = array_pop($small);
        $str = str_replace('DummySmallEntity', $small, $str);
        //$str = str_replace('$entity', '$'.strtolower($small), $str);
        //$str = str_replace('$this->entity', '$this->'.strtolower($small), $str);
        return $str;
    }


    protected function renderServiceClass()
    {
        $str = $this->files->get(__DIR__.'/stubs/ServicesStub.stub');
        $namespace = $this->getAppNamespace().'Services/'.$this->argument('name').'Service';
        $c = explode('/', $namespace);
        $classe = array_pop($c);
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = explode('\\', $namespace);
        array_pop($namespace);
        $namespace = implode('\\', $namespace);

        $str = str_replace('DummyNamespace', $namespace, $str);
        $str = str_replace('DummyClass', $classe, $str);
        $str = str_replace('DummyRepository', '\\'.$this->getAppNamespace().'Repositories\\'.str_replace('/', '\\', $this->argument('name').'Repository'), $str);


        $small = explode('/', $this->argument('name'));
        $small = array_pop($small).'Repository';
        $str = str_replace('DummySmallRepository', $small, $str);

        $entity = str_replace('/', '\\', $this->option('entity'));
        $str = str_replace('DummyEntity', '\\'.$this->getAppNamespace().$entity, $str);
        $small = explode('\\', $entity);
        $small = array_pop($small);
        $str = str_replace('DummySmallEntity', $small, $str);

        return $str;
    }
}

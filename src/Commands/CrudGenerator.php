<?php

namespace Ibis117\Larasar\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larasar:crud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private Filesystem $filesystem;

    /**
     * Execute the console command.
     */

    public function __construct(Filesystem  $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    public function handle(): void
    {
        $name = $this->argument('name');
        $format = $this->formats($name);
        $this->generateController($format);
        $this->generateQuasar($format);

        $controller = '\App\Http\Controllers\\'.$format['plural'].'Controller';
        $plural = $format['l-plural'];
        $route = "Route::resource('/$plural', {$controller}::class);";
        $this->components->info($route);
        $componentName = $format['singular'].'Page';
        $vueRoute = "{ path: '/$plural', component: () => import('../pages/$plural/$componentName.vue'), name: '{$componentName}' },";
        $this->components->info($vueRoute);
    }

    private function formats($name)
    {
        $singular = str($name)->lower()->singular()->studly()->value();
        $plural = str($name)->lower()->plural()->studly()->value();
        $lSingular = str($singular)->lower()->value();
        $lPlural = str($plural)->lower()->value();

        return [
            'singular' => $singular,
            'plural' => $plural,
            'l-singular' => $lSingular,
            'l-plural' => $lPlural,
        ];
    }

    private function generateController($format)
    {
        $this->call('make:model', [
            'name' => $format['singular'],
            '--migration' => true
        ]);

        $this->call('make:resource', [
            'name' => $format['singular'].'Resource'
        ]);

        $this->call('make:request', [
            'name' => $format['singular'].'StoreRequest'
        ]);
        $path = base_path('app/Http/Controllers/'.$format['plural'].'Controller.php');
        if ($this->filesystem->exists($path)) {
            $this->error('Already exists');
        }

        $content = $this->replaceStub($format, $this->getStub('controller'));
        $this->filesystem->put($path, $content);
        $path = str_replace(base_path().'/', '',  $path);
        $this->components->info(sprintf('%s [%s] created successfully.', 'Controller', $path));

    }

    private function getStub($name)
    {

        return $this->filesystem->get(__DIR__."/../../stubs/{$name}.stub");
    }

    private function replaceStub($format, string $stub)
    {
        return str_replace( [
            '{{ singular }}',
            '{{ l-singular }}',
            '{{ plural }}',
            '{{ l-plural }}',
        ],[
            $format['singular'],
            $format['l-singular'],
            $format['plural'],
            $format['l-plural'],
        ], $stub);
    }

    private function generateQuasar($format)
    {
        $this->generateStore($format);
        $this->generateForm($format);
        $this->generatePage($format);
    }

    private function generateStore($format)
    {
        $filename = "{$format['l-singular']}-store.js";
        $path = base_path('resources/js/store'.'/'.$filename);
        if ($this->filesystem->exists($path)) {
            $this->error('Already exists');
        }
        $content = $this->replaceStub($format, $this->getStub('store'));
        $this->filesystem->put($path, $content);
        $path = str_replace(base_path().'/', '',  $path);
        $this->components->info(sprintf('%s [%s] created successfully.', 'Pina Store', $path));
    }

    private function generatePage($format)
    {
        $filename = "{$format['l-singular']}/{$format['singular']}Page.vue";
        $path = base_path('resources/js/pages'.'/'.$filename);
        $folder = base_path('resources/js/pages').'/'.$format['l-singular'];
        $this->filesystem->ensureDirectoryExists($folder);
        if ($this->filesystem->exists($path)) {
            $this->error('Already exists');
        }
        $content = $this->replaceStub($format, $this->getStub('list-page'));
        $this->filesystem->put($path, $content);
        $path = str_replace(base_path().'/', '',  $path);
        $this->components->info(sprintf('%s [%s] created successfully.', 'List Page', $path));
    }

    private function generateForm($format)
    {
        $filename = "{$format['l-singular']}/{$format['singular']}Form.vue";
        $path = base_path('resources/js/pages'.'/'.$filename);
        $folder = base_path('resources/js/pages').'/'.$format['l-singular'];
        $this->filesystem->ensureDirectoryExists($folder);
        if ($this->filesystem->exists($path)) {
            $this->error('Already exists');
        }
        $content = $this->replaceStub($format, $this->getStub('form-page'));
        $this->filesystem->put($path, $content);
        $path = str_replace(base_path().'/', '',  $path);
        $this->components->info(sprintf('%s [%s] created successfully.', 'Form Page', $path));
    }
}

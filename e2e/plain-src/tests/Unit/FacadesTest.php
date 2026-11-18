<?php

use WebmanTech\LaravelConsole\Facades\Artisan;
use WebmanTech\LaravelConsole\Kernel;
use WebmanTech\LaravelFilesystem\Facades\File;
use WebmanTech\LaravelFilesystem\Facades\Storage;

test('filesystem facade', function () {
    expect(Storage::instance())->toBeInstanceOf(\WebmanTech\LaravelFilesystem\FilesystemManager::class);

    $disk = Storage::disk('local');
    expect($disk)->toBeInstanceOf(\Illuminate\Contracts\Filesystem\Filesystem::class);

    $disk->put('plain-smoke.txt', 'hello plain');
    expect($disk->get('plain-smoke.txt'))->toBe('hello plain');
    expect($disk->exists('plain-smoke.txt'))->toBeTrue();
    $disk->delete('plain-smoke.txt');
    expect($disk->exists('plain-smoke.txt'))->toBeFalse();

    $tmp = runtime_path('storage/plain-file.txt');
    File::put($tmp, 'file content');
    expect(File::exists($tmp))->toBeTrue();
    expect(File::get($tmp))->toBe('file content');
    File::delete($tmp);
    expect(File::exists($tmp))->toBeFalse();
});

test('artisan kernel', function () {
    $kernel = Artisan::instance();
    expect($kernel)->toBeInstanceOf(Kernel::class);

    $command = new class ('plain:hello') extends \Illuminate\Console\Command {
        protected $signature = 'plain:hello {name=world}';
        protected $description = 'plain host smoke command';

        public function handle(): int
        {
            $this->info('hello ' . $this->argument('name'));
            return 0;
        }
    };
    $kernel->registerCommand($command);

    expect($kernel->call('plain:hello', ['name' => 'e2e']))->toBe(0);
    expect($kernel->output())->toContain('hello e2e');

    expect($kernel->call('list'))->toBe(0);
    expect($kernel->output())->toContain('plain:hello');
});

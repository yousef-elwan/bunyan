<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class UpdateModelComments extends Command
{
    protected $signature = 'models:update-comments';

    protected $description = 'Update PHPDoc comments for all models with database table columns';

    public function handle()
    {
        $modelPath = app_path('Models');
        $modelFiles = $this->getModelFiles($modelPath);

        foreach ($modelFiles as $file) {
            $this->info("Processing model file: $file");
            $fullPath = base_path($file);
            $class = $this->getClassFullNameFromFile($fullPath);
            if (!$class) {
                $this->error("Could not find class in file $file");
                continue;
            }

            if (!class_exists($class)) {
                $this->error("Class $class does not exist or cannot be autoloaded");
                continue;
            }

            $table = $this->getTableName($class);
            if (!$table) {
                $this->error("Could not determine table name for class $class");
                continue;
            }

            if (!Schema::hasTable($table)) {
                $this->error("Table '$table' does not exist in the database");
                continue;
            }

            $columns = Schema::getColumnListing($table);
            $columnDetails = [];
            foreach ($columns as $column) {
                $type = Schema::getColumnType($table, $column);
                $phpType = $this->mapColumnTypeToPhpType($type);
                $columnDetails[] = ['name' => $column, 'type' => $phpType];
            }

            $this->updateModelFileComment($fullPath, $class, $columnDetails);
            $this->info("Updated comments for $class");
        }

        $this->info('Model comments update completed.');
        return 0;
    }

    protected function getModelFiles($directory)
    {
        $files = File::allFiles($directory);
        $modelFiles = [];
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                // Return relative path from base path, including directory prefix
                $modelFiles[] = 'app/Models/' . $file->getRelativePathname();
            }
        }
        return $modelFiles;
    }

    protected function getClassFullNameFromFile($filePath)
    {
        $content = File::get($filePath);
        $namespace = '';
        $class = '';

        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        }

        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $class = $matches[1];
        }

        if ($class) {
            return $namespace ? $namespace . '\\' . $class : $class;
        }

        return null;
    }

    protected function getTableName($class)
    {
        try {
            $reflection = new ReflectionClass($class);
            if ($reflection->hasProperty('table')) {
                $property = $reflection->getProperty('table');
                $property->setAccessible(true);
                $instance = $reflection->newInstanceWithoutConstructor();
                $table = $property->getValue($instance);
                if ($table) {
                    return $table;
                }
            }
            // If no $table property, use Laravel convention
            return Str::snake(Str::pluralStudly(class_basename($class)));
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function mapColumnTypeToPhpType($type)
    {
        // Map common database types to PHP types
        switch ($type) {
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'int';
            case 'boolean':
                return 'bool';
            case 'float':
            case 'double':
            case 'decimal':
                return 'float';
            case 'string':
            case 'char':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'string';
            case 'date':
            case 'datetime':
            case 'timestamp':
                return '\\Carbon\\Carbon';
            case 'json':
                return 'array';
            default:
                return 'mixed';
        }
    }

    protected function updateModelFileComment($filePath, $class, $columns)
    {
        $content = File::get($filePath);

        // Build the PHPDoc comment block
        $className = class_basename($class);
        $commentLines = [];
        $commentLines[] = '/**';
        $commentLines[] = " * Class $className";
        $commentLines[] = ' *';

        foreach ($columns as $col) {
            $name = $col['name'];
            $type = $col['type'];
            $extra = '';
            if ($name === 'id') {
                $extra = ' Primary';
            }
            $commentLines[] = " * @property $type \$$name$extra";
        }

        $commentLines[] = ' *';
        $commentLines[] = ' * @package App\Models';
        $commentLines[] = ' */';

        $commentBlock = implode("\n", $commentLines);

        // Replace or insert the comment block above the class declaration
        $pattern = '/\/\*\*.*?\*\/\s*(class\s+' . preg_quote($className, '/') . ')/s';

        if (preg_match($pattern, $content)) {
            // Replace existing comment block
            $content = preg_replace($pattern, $commentBlock . "\n$1", $content, 1);
        } else {
            // Insert comment block before class declaration
            $content = preg_replace('/(class\s+' . preg_quote($className, '/') . ')/', $commentBlock . "\n$1", $content, 1);
        }

        File::put($filePath, $content);
    }
}

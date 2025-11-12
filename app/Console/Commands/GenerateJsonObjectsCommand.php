<?php

namespace App\Console\Commands;

use App\Attributes\Enum\GenericEnumCaseAttribute;
use App\Attributes\Enum\GenericObjectAttribute;
use App\Enums\Attributes\EnumAttributeType;
use Binafy\LaravelStub\Facades\LaravelStub;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Command;

class GenerateJsonObjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-json-objects';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $namespaces = ['App\Enums'];

        // This will generate all of the namespaces in App\Enums into json object files, usable in front-end
        foreach ($namespaces as $namespace) {
            $enumList = ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE);

            foreach ($enumList as $enumClass) {
                try {
                    $path = \Str::chopStart(\Str::remove($namespace, $enumClass), '\\');
                    $baseClass = \Str::reverse(explode('\\', \Str::reverse($path), -1)[0] ?? '');
                    $classNamespace = implode('-', array_filter(explode('\\', \Str::remove($baseClass, $path))));
                    $fileName = implode('-', array_filter([$classNamespace, $baseClass]));

                    LaravelStub::from(__DIR__.'/../../Stubs/EnumToJsonFile.stub')
                        ->to(__DIR__.'/../../../resources/js/Composables/GeneratedEnumObjects')
                        ->name($fileName)
                        ->ext('json')
                        ->replace('object', self::enumToJsObject($enumClass))
                        ->generate();
                } catch (\Throwable $e) {
                    if (! \Str::contains($e->getMessage(), ['cases', 'is not an enum']) &&
                        ! \Str::contains($e->getMessage(), ['instantiate abstract'])) {
                        $this->info(implode(' ', [$e->getMessage(), $e->getFile(), $e->getLine()]));
                    }
                }
            }
        }
    }

    public static function enumToJsObject(string $class): string
    {
        $objects = [];

        $reflectEnum = new \ReflectionEnum($class);

        foreach ($reflectEnum->getCases() as $reflectCase) {
            $case = $reflectCase->getValue();
            $caseKey = \Str::pascal($case->name);
            $objects[$caseKey] = [
                /** @phpstan-ignore-next-line */
                'label' => $case->label(),
                /** @phpstan-ignore-next-line */
                'value' => $case->value,
                'name' => $case->name,
            ];

            // add the non-standard-case keys into object as well (ie, GenericEnumAttributes data)
            foreach ($reflectCase->getAttributes(GenericEnumCaseAttribute::class) as $attribute) {
                $args = $attribute->getArguments();
                $keyName = $args['objectKey'];

                if ($keyName instanceof \UnitEnum) {
                    /** @phpstan-ignore-next-line */
                    $keyName = $keyName->value;
                }

                $objects[$caseKey][\Str::snake($keyName)] = $args['value'];
            }

            // Check to see if there's any meta grouping attributes
            foreach ($reflectEnum->getAttributes(GenericObjectAttribute::class) as $groupingAttribute) {
                $args = $groupingAttribute->getArguments();
                switch ($args['type']) {
                    case EnumAttributeType::Value:
                        $objects[$caseKey][\Str::snake($args['objectKey'])] = $args['value'];
                        break;
                    case EnumAttributeType::Callback:
                        $callback = $case->{$args['value']}();
                        $value = $callback;

                        if ($callback instanceof \UnitEnum) {
                            /** @var \UnitEnum $value */
                            $value = $value->value;
                        }

                        $objects[$caseKey][$args['objectKey']] = $value;
                        break;
                }
            }
        }

        return json_encode($objects);
    }
}

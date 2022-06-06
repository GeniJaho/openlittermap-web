<?php

namespace Tests\Unit\Actions;

use App\Actions\ConvertDeprecatedTagsAction;
use Tests\TestCase;

class ConvertDeprecatedTagsActionTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [['alcohol' => [
                'paperCardAlcoholPackaging' => ['alcohol' => 'packaging', 'material' => 'paper'],
                'plasticAlcoholPackaging' => ['alcohol' => 'packaging', 'material' => 'plastic'],
                'alcohol_plastic_cups' => ['alcohol' => 'cup', 'material' => 'plastic'],
            ]]],

        ];
    }

    /** @dataProvider dataProvider */
    public function test_it_converts_the_deprecated_tags(array $tagMappings)
    {
        $deprecatedTags = collect($tagMappings)->mapWithKeys(function ($tagMapping, $category) {
            return [$category => collect($tagMapping)->mapWithKeys(function ($newTags, $tag) {
                return [$tag => 3];
            })];
        })->toArray();

        $action = new ConvertDeprecatedTagsAction;
        $actualConvertedTags = $action->run($deprecatedTags);

        $this->assertEquals(
            $this->getExpectedConvertedTags($deprecatedTags, $tagMappings),
            $actualConvertedTags
        );
    }

    public function test_it_does_not_affect_the_allowed_tags()
    {
        $action = new ConvertDeprecatedTagsAction;

        $result = $action->run([
            'alcohol' => ['pint' => 5]
        ]);

        $this->assertEquals(
            ['alcohol' => ['pint' => 5]],
            $result
        );
    }

    private function getExpectedConvertedTags(array $deprecatedTags, array $tagMappings): array
    {
        $expectedConvertedTags = [];
        foreach ($deprecatedTags as $category => $tags) {
            foreach ($tags as $tag => $quantity) {
                foreach ($tagMappings[$category][$tag] as $cat => $t) {
                    if (isset($expectedConvertedTags[$cat][$t])) {
                        $expectedConvertedTags[$cat][$t] += $quantity;
                    } else {
                        $expectedConvertedTags[$cat][$t] = $quantity;
                    }
                }
            }
        }
        return $expectedConvertedTags;
    }
}

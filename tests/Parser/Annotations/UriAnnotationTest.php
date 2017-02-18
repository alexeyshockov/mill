<?php
namespace Mill\Tests\Parser\Annotations;

use Mill\Parser\Annotations\UriAnnotation;

class UriAnnotationTest extends AnnotationTest
{
    /**
     * @dataProvider providerAnnotation
     */
    public function testAnnotation($param, $visible, $deprecated, $expected)
    {
        $annotation = new UriAnnotation($param, __CLASS__, __METHOD__);
        $annotation->setVisibility($visible);
        $annotation->setDeprecated($deprecated);

        $this->assertTrue($annotation->requiresVisibilityDecorator());
        $this->assertFalse($annotation->supportsVersioning());
        $this->assertTrue($annotation->supportsDeprecation());

        $this->assertSame($expected['array'], $annotation->toArray());
        $this->assertSame($expected['clean.path'], $annotation->getCleanPath());
        $this->assertFalse($annotation->getCapability());
        $this->assertFalse($annotation->getVersion());
    }

    public function testConfiguredUriSegmentTranslations()
    {
        $this->getConfig()->addUriSegmentTranslation('movie_id', 'id');

        $annotation = new UriAnnotation('{Movies\Showtimes} /movies/+movie_id/showtimes', __CLASS__, __METHOD__);

        $this->assertSame('/movies/{id}/showtimes', $annotation->getCleanPath());
        $this->assertSame('/movies/+movie_id/showtimes', $annotation->toArray()['path']);
    }

    /**
     * @return array
     */
    public function providerAnnotation()
    {
        return [
            'private' => [
                'param' => '{Movies\Showtimes} /movies/+id/showtimes',
                'visible' => false,
                'deprecated' => false,
                'expected' => [
                    'clean.path' => '/movies/{id}/showtimes',
                    'array' => [
                        'deprecated' => false,
                        'group' => 'Movies\Showtimes',
                        'path' => '/movies/+id/showtimes',
                        'visible' => false
                    ]
                ]
            ],
            'private.group_with_no_depth' => [
                'param' => '{Movies} /movies',
                'visible' => false,
                'deprecated' => false,
                'expected' => [
                    'clean.path' => '/movies',
                    'array' => [
                        'deprecated' => false,
                        'group' => 'Movies',
                        'path' => '/movies',
                        'visible' => false
                    ]
                ]
            ],
            'public' => [
                'param' => '{Movies\Showtimes} /movies/+id/showtimes',
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'clean.path' => '/movies/{id}/showtimes',
                    'array' => [
                        'deprecated' => false,
                        'group' => 'Movies\Showtimes',
                        'path' => '/movies/+id/showtimes',
                        'visible' => true
                    ]
                ]
            ],
            'public.deprecated' => [
                'param' => '{Movies\Showtimes} /movies/+id/showtimes',
                'visible' => true,
                'deprecated' => true,
                'expected' => [
                    'clean.path' => '/movies/{id}/showtimes',
                    'array' => [
                        'deprecated' => true,
                        'group' => 'Movies\Showtimes',
                        'path' => '/movies/+id/showtimes',
                        'visible' => true
                    ]
                ]
            ],
            'public.non-alphanumeric_group' => [
                'param' => '{/} /',
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'clean.path' => '/',
                    'array' => [
                        'deprecated' => false,
                        'group' => '/',
                        'path' => '/',
                        'visible' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerAnnotationFailsOnInvalidAnnotations()
    {
        return [
            'missing-group' => [
                'annotation' => '\Mill\Parser\Annotations\UriAnnotation',
                'docblock' => '',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\MissingRequiredFieldException',
                'expected.exception.regex' => [
                    '/`group`/'
                ]
            ],
            'missing-path' => [
                'annotation' => '\Mill\Parser\Annotations\UriAnnotation',
                'docblock' => '{Movies}',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\MissingRequiredFieldException',
                'expected.exception.regex' => [
                    '/`path`/'
                ]
            ]
        ];
    }
}

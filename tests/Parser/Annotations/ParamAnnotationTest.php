<?php
namespace Mill\Tests\Parser\Annotations;

use Mill\Parser\Annotations\ParamAnnotation;
use Mill\Parser\Version;

class ParamAnnotationTest extends AnnotationTest
{
    /**
     * @dataProvider providerAnnotation
     */
    public function testAnnotation($param, $version, $visible, $deprecated, $expected)
    {
        $annotation = new ParamAnnotation($param, __CLASS__, __METHOD__, $version);
        $annotation->setVisibility($visible);
        $annotation->setDeprecated($deprecated);

        $this->assertTrue($annotation->requiresVisibilityDecorator());
        $this->assertTrue($annotation->supportsVersioning());
        $this->assertTrue($annotation->supportsDeprecation());
        $this->assertFalse($annotation->supportsAliasing());

        $this->assertSame($expected, $annotation->toArray());
        $this->assertSame($expected['field'], $annotation->getField());
        $this->assertSame($expected['type'], $annotation->getType());
        $this->assertSame($expected['description'], $annotation->getDescription());
        $this->assertSame($expected['required'], $annotation->isRequired());
        $this->assertSame($expected['values'], $annotation->getValues());

        if (is_string($expected['capability'])) {
            $this->assertInstanceOf(
                '\Mill\Parser\Annotations\CapabilityAnnotation',
                $annotation->getCapability()
            );
        } else {
            $this->assertFalse($annotation->getCapability());
        }

        if ($expected['version']) {
            $this->assertInstanceOf('Mill\Parser\Version', $annotation->getVersion());
        } else {
            $this->assertFalse($annotation->getVersion());
        }

        $this->assertEmpty($annotation->getAliases());
    }

    /**
     * @return array
     */
    public function providerAnnotation()
    {
        return [
            'capability' => [
                'param' => '{string} content_rating +MOVIE_RATINGS+ MPAA rating',
                'version' => null,
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'capability' => 'MOVIE_RATINGS',
                    'deprecated' => false,
                    'description' => 'MPAA rating',
                    'field' => 'content_rating',
                    'required' => true,
                    'type' => 'string',
                    'values' => false,
                    'version' => false,
                    'visible' => true
                ]
            ],
            'deprecated' => [
                'param' => '{page}',
                'version' => null,
                'visible' => false,
                'deprecated' => true,
                'expected' => [
                    'capability' => false,
                    'deprecated' => true,
                    'description' => 'The page number to show.',
                    'field' => 'page',
                    'required' => false,
                    'type' => 'integer',
                    'values' => false,
                    'version' => false,
                    'visible' => false
                ]
            ],
            'private' => [
                'param' => '{string} __testing [true|false] Because reasons',
                'version' => null,
                'visible' => false,
                'deprecated' => false,
                'expected' => [
                    'capability' => false,
                    'deprecated' => false,
                    'description' => 'Because reasons',
                    'field' => '__testing',
                    'required' => true,
                    'type' => 'string',
                    'values' => [
                        'false',
                        'true'
                    ],
                    'version' => false,
                    'visible' => false
                ]
            ],
            'tokens' => [
                'param' => '{page}',
                'version' => null,
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'capability' => false,
                    'deprecated' => false,
                    'description' => 'The page number to show.',
                    'field' => 'page',
                    'required' => false,
                    'type' => 'integer',
                    'values' => false,
                    'version' => false,
                    'visible' => true
                ]
            ],
            'tokens.acceptable_values' => [
                'param' => '{filter} [embeddable|playable]',
                'version' => null,
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'capability' => false,
                    'deprecated' => false,
                    'description' => 'Filter to apply to the results.',
                    'field' => 'filter',
                    'required' => false,
                    'type' => 'string',
                    'values' => [
                        'embeddable',
                        'playable'
                    ],
                    'version' => false,
                    'visible' => true
                ]
            ],
            'versioned' => [
                'param' => '{page}',
                'version' => new Version('1.1 - 1.2', __CLASS__, __METHOD__),
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'capability' => false,
                    'deprecated' => false,
                    'description' => 'The page number to show.',
                    'field' => 'page',
                    'required' => false,
                    'type' => 'integer',
                    'values' => false,
                    'version' => '1.1 - 1.2',
                    'visible' => true
                ]
            ],
            '_complete' => [
                'param' => '{string} content_rating [G|PG|PG-13|R|NC-17|X|NR|UR] (optional) +MOVIE_RATINGS+ ' .
                    'MPAA rating',
                'version' => null,
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'capability' => 'MOVIE_RATINGS',
                    'deprecated' => false,
                    'description' => 'MPAA rating',
                    'field' => 'content_rating',
                    'required' => false,
                    'type' => 'string',
                    'values' => [
                        'G',
                        'NC-17',
                        'NR',
                        'PG',
                        'PG-13',
                        'R',
                        'UR',
                        'X'
                    ],
                    'version' => false,
                    'visible' => true
                ]
            ],
            '_complete.with-markdown-description' => [
                'param' => '{string} content_rating [G|PG|PG-13|R|NC-17|X|NR|UR] (optional) +MOVIE_RATINGS+ ' .
                    '[MPAA rating](http://www.mpaa.org/film-ratings/)',
                'version' => null,
                'visible' => true,
                'deprecated' => false,
                'expected' => [
                    'capability' => 'MOVIE_RATINGS',
                    'deprecated' => false,
                    'description' => '[MPAA rating](http://www.mpaa.org/film-ratings/)',
                    'field' => 'content_rating',
                    'required' => false,
                    'type' => 'string',
                    'values' => [
                        'G',
                        'NC-17',
                        'NR',
                        'PG',
                        'PG-13',
                        'R',
                        'UR',
                        'X'
                    ],
                    'version' => false,
                    'visible' => true
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
            'missing-field-name' => [
                'annotation' => '\Mill\Parser\Annotations\ParamAnnotation',
                'docblock' => '{string}',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\MissingRequiredFieldException',
                'expected.exception.asserts' => [
                    'getRequiredField' => 'field',
                    'getAnnotation' => 'param',
                    'getDocblock' => '{string}',
                    'getValues' => []
                ]
            ],
            'missing-type' => [
                'annotation' => '\Mill\Parser\Annotations\ParamAnnotation',
                'docblock' => '__testing',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\MissingRequiredFieldException',
                'expected.exception.asserts' => [
                    'getRequiredField' => 'type',
                    'getAnnotation' => 'param',
                    'getDocblock' => '__testing',
                    'getValues' => []
                ]
            ],
            'missing-field-name' => [
                'annotation' => '\Mill\Parser\Annotations\ParamAnnotation',
                'docblock' => '{int} __testing',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\UnsupportedTypeException',
                'expected.exception.asserts' => [
                    'getAnnotation' => '{int} __testing',
                    'getDocblock' => null
                ]
            ],
            'values-are-in-the-wrong-format' => [
                'annotation' => '\Mill\Parser\Annotations\ParamAnnotation',
                'docblock' => '{string} __testing [true,false] Because reasons',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\BadOptionsListException',
                'expected.exception.asserts' => [
                    'getRequiredField' => null,
                    'getAnnotation' => 'param',
                    'getDocblock' => '{string} __testing [true,false] Because reasons',
                    'getValues' => [
                        'true,false'
                    ]
                ]
            ],
            'missing-description' => [
                'annotation' => '\Mill\Parser\Annotations\ParamAnnotation',
                'docblock' => '{string} __testing [true|false]',
                'expected.exception' => '\Mill\Exceptions\Resource\Annotations\MissingRequiredFieldException',
                'expected.exception.asserts' => [
                    'getRequiredField' => 'description',
                    'getAnnotation' => 'param',
                    'getDocblock' => '{string} __testing [true|false]',
                    'getValues' => []
                ]
            ]
        ];
    }
}

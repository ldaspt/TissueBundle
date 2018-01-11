<?php

namespace CL\Bundle\TissueBundle\Tests\DependencyInjection;

use CL\Bundle\TissueBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    public function testValuesAreInvalidIfRequiredValueIsNotProvided()
    {
        $this->assertConfigurationIsInvalid(
            [
                [] // no values at all
            ],
            'adapter' // (part of) the expected exception message - optional
        );
    }

    public function testValuesAreInvalidIfRequiredValueIsNotStringOrArray()
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'adapter' => new \stdClass() // bad type for an adapter alias
                ]
            ],
            'adapter' // (part of) the expected exception message - optional
        );

        $this->assertConfigurationIsInvalid(
            [
                [
                    'adapter' => 123 // bad type for an adapter alias
                ]
            ],
            'adapter' // (part of) the expected exception message - optional
        );
    }

    public function testValuesAreValidIfAdapterIsString()
    {
        $this->assertConfigurationIsValid(
            [
                [
                    'adapter' => 'foobar'
                ]
            ]
        );
    }

    public function testValuesAreValidWithoutOptions()
    {
        $this->assertConfigurationIsValid(
            [
                [
                    'adapter' => [
                        'alias' => 'foobar'
                    ]
                ]
            ]
        );
    }

    /**
     * @dataProvider getProcessedConfigurations
     */
    public function testProcessedConfigurations(array $configurationValues, array $expectedConfigurationValues)
    {
        $this->assertProcessedConfigurationEquals(
            [$configurationValues],
            $expectedConfigurationValues
        );
    }

    /**
     * @return array
     */
    public function getProcessedConfigurations()
    {
        return [
            [
                [
                    'adapter' => 'foobar',
                ],
                [
                    'adapter' => [
                        'alias'   => 'foobar',
                        'options' => [],
                    ]
                ]
            ],
            [
                [
                    'adapter' => [
                        'alias' => 'foobar'
                    ]
                ],
                [
                    'adapter' => [
                        'alias'   => 'foobar',
                        'options' => [],
                    ]
                ]
            ],
            [
                [
                    'adapter' => [
                        'alias' => 'foobar',
                        'options' => [
                            'apple' => 'pie'
                        ]
                    ]
                ],
                [
                    'adapter' => [
                        'alias'   => 'foobar',
                        'options' => [
                            'apple' => 'pie'
                        ],
                    ]
                ]
            ]
        ];
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}

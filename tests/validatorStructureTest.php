<?php
class validatorStructureTest extends \PHPUnit\Framework\TestCase
{
    public function testOK()
    {
        $this->assertSame(true, true);
    }

    public function testSchemaStructure()
    {
        $data = [
            "formDataEmail" => "test@revenuewire.com",
            "formDataAge" => 8,
            "formDataPeople" => [
                [
                    "name" => "A",
                    "position" => "CEO"
                ],
                [
                    "name" => "Hello B",
                    "position" => "CTO"
                ],
            ],
            "formDataKeys" => [
                "secret-a",
                "secret-b",
            ],
            "formDataComplex" => [
                "status" => "OK1",
                "messages" => [
                    [
                        "k" => "k1",
                        "v" => "v1",
                    ],
                    [
                        "k" => "k2",
                        "v" => null,
                        "extra" => "this is bad"
                    ]
                ]
            ],
            "formDataNotRequired" => null,
        ];

        $schema = [
            "key" => "formData",
            "type" => \RW\Validator::TYPE_OBJECT,
            "required" => true,
            "options" => [],
            "schema" => [
                [
                    "key" => "formDataNotRequired",
                    "type" => \RW\Validator::TYPE_STRING,
                    "required" => false,
                    "options" => [],
                ],
                [
                    "key" => "formDataEmail",
                    "type" => \RW\Validator::TYPE_EMAIL,
                    "required" => true,
                    "options" => [],
                ],
                [
                    "key" => "formDataAge",
                    "type" => \RW\Validator::TYPE_AGE,
                    "required" => true,
                    "options" => [
                        "max" => 99,
                        "min" => 18,
                    ],
                ],
                [
                    "key" => "formDataPeople",
                    "type" => \RW\Validator::TYPE_ARRAY,
                    "required" => true,
                    "options" => [],
                    "schema" => [
                        "type" => \RW\Validator::TYPE_OBJECT,
                        "required" => true,
                        "schema" => [
                            [
                                "key" => "name",
                                "type" => \RW\Validator::TYPE_STRING,
                                "required" => true,
                                "options" => [
                                    "min" => 3,
                                ]
                            ],
                            [
                                "key" => "position",
                                "type" => \RW\Validator::TYPE_STRING,
                                "required" => true,
                                "options" => [
                                    "allowedValues" => ["CEO", "CTO", "COO"]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "key" => "formDataKeys",
                    "type" => \RW\Validator::TYPE_ARRAY,
                    "required" => true,
                    "options" => [],
                    "schema" => [
                        "type" => \RW\Validator::TYPE_STRING,
                        "required" => true,
                        "options" => []
                    ]
                ],
                [
                    "key" => "formDataComplex",
                    "type" => \RW\Validator::TYPE_OBJECT,
                    "required" => true,
                    "schema" => [
                        [
                            "key" => "status",
                            "type" => \RW\Validator::TYPE_STRING,
                            "required" => true,
                            "options" => [
                                "allowedValues" => ["OK", "PENDING", "FAILED"]
                            ]
                        ],
                        [
                            "key" => "messages",
                            "type" => \RW\Validator::TYPE_ARRAY,
                            "required" => false,
                            "schema" => [
                                "type" => \RW\Validator::TYPE_OBJECT,
                                "required" => false,
                                "options" => [],
                                "schema" => [
                                    [
                                        "key" => "k",
                                        "type" => \RW\Validator::TYPE_STRING,
                                        "required" => true,
                                        "options" => []
                                    ],
                                    [
                                        "key" => "v",
                                        "type" => \RW\Validator::TYPE_STRING,
                                        "required" => true,
                                        "options" => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            [
                "key" => "formData",
                "error" => "Undefined data found",
                "context" => [
                    "formDataComplex" => [
                        "messages" => [
                            1 => [
                                "extra" => "this is bad"
                            ]
                        ]
                    ]
                ]
            ],
            [
                "key" => "formData.formDataAge",
                "error" => "formData.formDataAge must be greater than 18.",
                "context" => [
                    "max" => 99,
                    "min" => 18,
                ]
            ],
            [
                "key" => "formData.formDataPeople[0].name",
                "error" => "formData.formDataPeople[0].name must be greater than 3 characters.",
                "context" => [
                    "min" => 3
                ]
            ],
            [
                "key" => "formData.formDataComplex.status",
                "error" => "OK1 is not allowed value for formData.formDataComplex.status.",
                "context" => [
                    "allowedValues" => [
                        "OK", "PENDING", "FAILED"
                    ]
                ]
            ],
            [
                "key" => "formData.formDataComplex.messages[1].v",
                "error" => "Required value missing",
                "context" => []
            ]
        ];

        $validator = new \RW\Validator($schema, [\RW\Validator::OPTION_EXCEPTION_ON_UNDEFINED_DATA => false]);
        $result = $validator->validate($data);
        $this->assertSame(false, $result);
        $this->assertEquals($expected, $validator->getValidateResult());
    }

    /**
     * Valid object
     */
    public function testSchemaValid()
    {
        $data = [
            "formDataEmail" => "test@revenuewire.com",
        ];

        $schema = [
            "key" => "formData",
            "type" => \RW\Validator::TYPE_OBJECT,
            "required" => true,
            "options" => [],
            "schema" => [
                [
                    "key" => "formDataEmail",
                    "type" => \RW\Validator::TYPE_EMAIL,
                    "required" => true,
                    "options" => [],
                ]
            ]
        ];

        $expected = [];
        $validator = new \RW\Validator($schema);
        $result = $validator->validate($data);

        $this->assertSame(true, $result);
        $this->assertEquals($expected, $validator->getValidateResult());
    }

    /**
     * Valid array
     */
    public function testSchemaValidArray()
    {
        $data = [
            "test1", "test2"
        ];

        $schema = [
            "key" => "formData",
            "type" => \RW\Validator::TYPE_ARRAY,
            "required" => true,
            "options" => [],
            "schema" => [
                "type" => \RW\Validator::TYPE_STRING,
                "required" => true,
                "options" => []
            ]
        ];

        $expected = [];
        $validator = new \RW\Validator($schema);
        $result = $validator->validate($data);

        $this->assertSame(true, $result);
        $this->assertEquals($expected, $validator->getValidateResult());

        $validator->clearValidateResult();
        $this->assertEquals([], $validator->getValidateResult());
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage  Undefined data found. ({"formDataComplex":{"messages":[{"deep":true}]}})
     */
    public function testSchemaStructureWithUndefined()
    {
        $data = [
            "formDataEmail" => "test@revenuewire.com",
            "formDataAge" => 8,
            "formDataPeople" => [
                [
                    "name" => "A",
                    "position" => "CEO"
                ],
                [
                    "name" => "Hello B",
                    "position" => "CTO"
                ],
            ],
            "formDataKeys" => [
                "secret-a",
                "secret-b",
            ],
            "formDataComplex" => [
                "status" => "OK1",
                "messages" => [
                    [
                        "k" => "k1",
                        "v" => "v1",
                        "deep" => true,
                    ],
                    [
                        "k" => "k2",
                        "v" => null,
                    ]
                ]
            ],
            "formDataNotRequired" => null,
        ];

        $schema = [
            "key" => "formData",
            "type" => \RW\Validator::TYPE_OBJECT,
            "required" => true,
            "options" => [],
            "schema" => [
                [
                    "key" => "formDataNotRequired",
                    "type" => \RW\Validator::TYPE_STRING,
                    "required" => false,
                    "options" => [],
                ],
                [
                    "key" => "formDataEmail",
                    "type" => \RW\Validator::TYPE_EMAIL,
                    "required" => true,
                    "options" => [],
                ],
                [
                    "key" => "formDataAge",
                    "type" => \RW\Validator::TYPE_AGE,
                    "required" => true,
                    "options" => [
                        "max" => 99,
                        "min" => 18,
                    ],
                ],
                [
                    "key" => "formDataPeople",
                    "type" => \RW\Validator::TYPE_ARRAY,
                    "required" => true,
                    "options" => [],
                    "schema" => [
                        "type" => \RW\Validator::TYPE_OBJECT,
                        "required" => true,
                        "schema" => [
                            [
                                "key" => "name",
                                "type" => \RW\Validator::TYPE_STRING,
                                "required" => true,
                                "options" => [
                                    "min" => 3,
                                ]
                            ],
                            [
                                "key" => "position",
                                "type" => \RW\Validator::TYPE_STRING,
                                "required" => true,
                                "options" => [
                                    "allowedValues" => ["CEO", "CTO", "COO"]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "key" => "formDataKeys",
                    "type" => \RW\Validator::TYPE_ARRAY,
                    "required" => true,
                    "options" => [],
                    "schema" => [
                        "type" => \RW\Validator::TYPE_STRING,
                        "required" => true,
                        "options" => []
                    ]
                ],
                [
                    "key" => "formDataComplex",
                    "type" => \RW\Validator::TYPE_OBJECT,
                    "required" => true,
                    "schema" => [
                        [
                            "key" => "status",
                            "type" => \RW\Validator::TYPE_STRING,
                            "required" => true,
                            "options" => [
                                "allowedValues" => ["OK", "PENDING", "FAILED"]
                            ]
                        ],
                        [
                            "key" => "messages",
                            "type" => \RW\Validator::TYPE_ARRAY,
                            "required" => false,
                            "schema" => [
                                "type" => \RW\Validator::TYPE_OBJECT,
                                "required" => false,
                                "options" => [],
                                "schema" => [
                                    [
                                        "key" => "k",
                                        "type" => \RW\Validator::TYPE_STRING,
                                        "required" => true,
                                        "options" => []
                                    ],
                                    [
                                        "key" => "v",
                                        "type" => \RW\Validator::TYPE_STRING,
                                        "required" => true,
                                        "options" => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            [
                "key" => "formData.formDataAge",
                "error" => "formData.formDataAge must be greater than 18.",
                "context" => [
                    "max" => 99,
                    "min" => 18,
                ]
            ],
            [
                "key" => "formData.formDataPeople[0].name",
                "error" => "formData.formDataPeople[0].name must be greater than 3 characters.",
                "context" => [
                    "min" => 3
                ]
            ],
            [
                "key" => "formData.formDataComplex.status",
                "error" => "OK1 is not allowed value for formData.formDataComplex.status.",
                "context" => [
                    "allowedValues" => [
                        "OK", "PENDING", "FAILED"
                    ]
                ]
            ],
            [
                "key" => "formData.formDataComplex.messages[1].v",
                "error" => "Required value missing",
                "context" => []
            ]
        ];

        $validator = new \RW\Validator($schema, [\RW\Validator::OPTION_EXCEPTION_ON_UNDEFINED_DATA => true]);
        $result = $validator->validate($data);

        $this->assertSame(false, $result);
        $this->assertEquals($expected, $validator->getValidateResult());
    }

    /**
     * @throws Exception
     */
    public function testSchemaStructureWithSkipEmpty()
    {
        $data = [
            //"formDataEmail" => "",
            "formDataAge" => 28,
        ];

        $schema = [
            "key" => "formData",
            "type" => \RW\Validator::TYPE_OBJECT,
            "required" => true,
            "options" => [],
            "schema" => [
                [
                    "key" => "formDataEmail",
                    "type" => \RW\Validator::TYPE_EMAIL,
                    "required" => true,
                    "options" => [],
                ],
                [
                    "key" => "formDataAge",
                    "type" => \RW\Validator::TYPE_AGE,
                    "required" => false,
                    "options" => [
                        "max" => 99,
                        "min" => 18,
                    ],
                ]
            ]
        ];

        $expected = [];

        $validator = new \RW\Validator($schema, [\RW\Validator::OPTION_EXCEPTION_ON_UNDEFINED_DATA => true, RW\Validator::OPTION_SKIP_EMPTY_DATA => true]);
        $result = $validator->validate($data);
        $this->assertSame(true, $result);
        $this->assertEquals($expected, $validator->getValidateResult());
    }

    public function testSchemaStructureWithoutSkipEmpty()
    {
        $data = [
            //"formDataEmail" => "",
            "formDataAge" => 28,
        ];

        $schema = [
            "key" => "formData",
            "type" => \RW\Validator::TYPE_OBJECT,
            "required" => true,
            "options" => [],
            "schema" => [
                [
                    "key" => "formDataEmail",
                    "type" => \RW\Validator::TYPE_EMAIL,
                    "required" => true,
                    "options" => [],
                ],
                [
                    "key" => "formDataAge",
                    "type" => \RW\Validator::TYPE_AGE,
                    "required" => false,
                    "options" => [
                        "max" => 99,
                        "min" => 18,
                    ],
                ]
            ]
        ];

        $expected = [
            [
                "key" => "formData.formDataEmail",
                "error" => "Required value missing",
                "context" => []
            ]
        ];

        $validator = new \RW\Validator($schema, [\RW\Validator::OPTION_EXCEPTION_ON_UNDEFINED_DATA => true]);
        $result = $validator->validate($data);
        $this->assertSame(false, $result);
        $this->assertEquals($expected, $validator->getValidateResult());
    }
}
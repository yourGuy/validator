<?php

/**
 * Class validateAgeTest
 */
class validateURLTest extends \PHPUnit\Framework\TestCase
{
    public function dataProvider()
    {
        return [
            [ "test@revenuewire.com", true ],
            [ "google.com", true ],
            [ "", false ],
            [ 32, false ],
            [ "aaa@aaa", true ],
            [ "ht", true ],
            [ "http://aa.com", true ],
            [ "http://aa.bb.com", true ],
            [ "https://aa.bb.com", true ],
            [ "https://aa.com", true ],
        ];
    }

    /**
     * @param $input
     * @param $expected
     * @dataProvider dataProvider
     */
    public function testURL($input, $expected)
    {
        $validator = new \RW\Validator();
        $this->assertSame($expected, $validator->validateURL($input, "validate-url", []));
    }
}
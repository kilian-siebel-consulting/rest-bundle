<?php
namespace Ibrows\RestBundle\Tests\Integration;

use Symfony\Component\HttpFoundation\Response;

class PatchParamConverterTest extends WebTestCase
{
    /**
     * @param string $url
     * @param array  $headers
     * @param string $data
     * @param int    $expectedStatusCode
     * @param array  $expectedSubset
     * @dataProvider getTestData
     */
    public function test($url, array $headers, $data, $expectedStatusCode, array $expectedSubset)
    {
        $client = static::createClient([], []);
        $client->request(
            'PATCH',
            $url,
            [],
            [],
            $headers,
            $data
        );

        static::assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
        static::assertEquals('application/json', $client->getResponse()->headers->get('CONTENT-TYPE'));
        $response = json_decode($client->getResponse()->getContent(), true);
        static::assertInternalType('array', $response);
        static::assertArraySubset($expectedSubset, $response);
    }

    public function testInvalidValue()
    {
        $this->markTestSkipped('This test has to be imlemented into the serializer library itself.');
        $client = static::createClient([], []);
        $client->request(
            'PATCH',
            '/v1/en_US/comments/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    [
                        'op'    => 'replace',
                        'path'  => '/article',
                        'value' => 'new subject',
                    ]
                ]
            )
        );

        static::assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function getTestData()
    {
        return [
            [
                '/v1/en_US/comments/1',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                json_encode(
                    [
                        [
                            'op'    => 'replace',
                            'path'  => '/subject',
                            'value' => 'new subject',
                        ]
                    ]
                ),
                Response::HTTP_OK,
                [
                    'subject' => 'new subject',
                ],
            ],
            [
                '/v1/en_US/comments/1',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                json_encode(
                    [
                        [
                            'op'    => 'replace',
                            'path'  => '/invalid',
                            'value' => 'new subject',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Could not resolve path "invalid" on current address.',
                ],
            ],
            [
                '/v1/en_US/comments/1',
                [],
                json_encode(
                    [
                        [
                            'op'    => 'replace',
                            'path'  => '/invalid',
                            'value' => 'new subject',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Content Type must be json.',
                ],
            ],
            [
                '/v1/en_US/comments/1',
                [
                    [
                        'CONTENT_TYPE' => 'application/something',
                    ],
                ],
                json_encode(
                    [
                        [
                            'op'    => 'replace',
                            'path'  => '/invalid',
                            'value' => 'new subject',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Content Type must be json.',
                ],
            ],
            [
                '/v1/en_US/comments/1',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                'not a json content',
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Invalid json message received',
                ],
            ],
            [
                '/v1/en_US/comments/1',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                json_encode(
                    [
                        [
                            'whatever',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'The property "op" must be provided for every operation.',
                ],
            ],
            [
                '/v1/en_US/comments/1',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                json_encode(
                    [
                        [
                            'op'    => 'test',
                            'path'  => '/subject',
                            'value' => 'wrong value',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Operation test failed. Expected: "old subject", Actual: "wrong value"',
                ],
            ],
            [
                '/v1/en_US/comments/1/groups',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                json_encode(
                    [
                        [
                            'op'    => 'replace',
                            'path'  => '/subject',
                            'value' => 'something',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Could not resolve path "subject" on current address.',
                ],
            ],
            [
                '/v1/en_US/comments/1/version',
                [
                    'CONTENT_TYPE' => 'application/json',
                ],
                json_encode(
                    [
                        [
                            'op'    => 'replace',
                            'path'  => '/subject',
                            'value' => 'something',
                        ]
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                [
                    'message' => 'Could not resolve path "subject" on current address.',
                ],
            ],
        ];
    }
}

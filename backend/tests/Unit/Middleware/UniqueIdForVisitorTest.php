<?php
test('Unique Id will generated for first request of a new visitor', function ()
{
    $response = $this->get('/api/product');

    // Assert that the response contains a uniqueId header
    $response->assertHeader('X-Unique-ID');
});
test('Unique ID stays the same after the first request', function ()
{
    // First request to get the unique ID
    $firstResponse = $this->get('/api/product');

    // Assert that the response contains the X-Unique-ID header
    $firstResponse->assertHeader('X-Unique-ID');

    // Store the unique ID from the first request
    $uniqueId = $firstResponse->headers->get('X-Unique-ID');

    // Perform another request
    $secondResponse = $this->withHeaders(['X-Unique-ID' => $uniqueId])->get('/api/product');

    // Assert that the second response contains the same unique ID
    $secondResponse->assertHeader('X-Unique-ID');
    $this->assertEquals($uniqueId, $secondResponse->headers->get('X-Unique-ID'));
});

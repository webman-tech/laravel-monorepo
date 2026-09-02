<?php

test('validator e2e', function () {
    $response = $this->get('/e2e/validator');
    $response->assertOk();

    $data = $response->json();

    // instance
    expect($data['instance'])->toBeTrue();
    expect($data['helper'])->toBeTrue();
    expect($data['make'])->toBeTrue();

    // validate ok
    expect($data['validate_ok'])->toBeTrue();

    // validate error
    expect($data['validate_error'])->toBeTrue();

    // manual validate
    expect($data['manual_ok'])->toBeTrue();
    expect($data['manual_validated'])->toBeTrue();
    expect($data['manual_fails'])->toBeTrue();
    expect($data['manual_errors'])->toBeTrue();

    // custom messages
    expect($data['custom_message'])->toBeTrue();

    // translation messages
    expect($data['trans_message'])->toBeTrue();
    expect($data['trans_message_en'])->toBeTrue();
});

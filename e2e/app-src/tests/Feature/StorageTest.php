<?php

test('storage e2e', function () {
    $response = $this->get('/e2e/storage');
    $response->assertOk();

    $data = $response->json();

    // instance
    expect($data['instance'])->toBeTrue();

    // filesystem manager
    expect($data['disk_is_filesystem'])->toBeTrue();
    expect($data['disk_is_adapter'])->toBeTrue();
    expect($data['public_disk_is_adapter'])->toBeTrue();
    expect($data['cloud_is_cloud'])->toBeTrue();

    // build
    expect($data['build'])->toBeTrue();

    // exists/missing
    expect($data['exists_false'])->toBeTrue();
    expect($data['missing_true'])->toBeTrue();

    // put/get
    expect($data['get'])->toBeTrue();
    expect($data['exists_true'])->toBeTrue();

    // response - webman Response
    expect($data['response_is_webman'])->toBeTrue();
    expect($data['response_body'])->toBeTrue();

    // download - webman Response
    expect($data['download_is_webman'])->toBeTrue();
    expect($data['download_header'])->toBeTrue();

    // delete
    expect($data['delete'])->toBeTrue();
});

test('storage extend e2e', function () {
    $response = $this->get('/e2e/storage/extend');
    $response->assertOk();

    $data = $response->json();

    // qiniu
    expect($data['qiniu_is_adapter'])->toBeTrue();
    expect($data['qiniu_adapter_type'])->toBeTrue();

    // cos
    expect($data['cos_is_adapter'])->toBeTrue();
    expect($data['cos_adapter_type'])->toBeTrue();

    // oss
    expect($data['oss_is_adapter'])->toBeTrue();
    expect($data['oss_adapter_type'])->toBeTrue();
});

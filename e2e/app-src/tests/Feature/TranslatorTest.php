<?php

beforeEach(function () {
    // 确保 locale 重置为 zh_CN
    // 注意：跨进程下 locale 状态由 server 管理，这里仅影响测试进程内的断言
});

test('translator e2e', function () {
    $response = $this->get('/e2e/translator');
    $response->assertOk();

    $data = $response->json();

    // instance
    expect($data['instance'])->toBeTrue();

    // locale
    expect($data['locale'])->toBeTrue();

    // trans no key
    expect($data['trans_no_key'])->toBeTrue();

    // basic translation
    expect($data['trans_hello'])->toBeTrue();
    expect($data['transL_hello'])->toBeTrue();
    expect($data['trans_hello2'])->toBeTrue();
    expect($data['transL_hello2'])->toBeTrue();

    // sentence as key
    expect($data['trans_sentence'])->toBeTrue();
    expect($data['transL_sentence'])->toBeTrue();

    // group file
    expect($data['trans_group'])->toBeTrue();
    expect($data['transL_group'])->toBeTrue();

    // switch locale
    expect($data['trans_en'])->toBeTrue();
    expect($data['transL_en'])->toBeTrue();

    // replacement
    expect($data['trans_replace_symfony'])->toBeTrue();
    expect($data['transL_replace'])->toBeTrue();

    // uppercase
    expect($data['transL_upper'])->toBeTrue();
    expect($data['transL_ucfirst'])->toBeTrue();

    // choice
    expect($data['trans_choice_one'])->toBeTrue();
    expect($data['trans_choice_many'])->toBeTrue();
    expect($data['trans_choice_zero'])->toBeTrue();
    expect($data['trans_choice_18'])->toBeTrue();
});

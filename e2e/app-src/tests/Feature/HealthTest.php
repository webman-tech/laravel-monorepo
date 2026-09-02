<?php

test('health check', function () {
    $this->get('/e2e/health')
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});

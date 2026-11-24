<?php

it('allows guests to view the katalog', function () {
    $this->get('/katalog')
        ->assertOk()
        ->assertViewIs('customer.katalog');
});

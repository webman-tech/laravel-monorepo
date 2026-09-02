<?php

return [
    '你好' => 'hello',
    '你好2' => 'hello',
    'welcome0' => 'hello, %name%',
    'welcome' => 'hello, :name',
    'welcome2' => 'hello, :NAME',
    'welcome3' => 'hello, :Name',
    'apple_count' => 'There is one apple|There are many apples',
    'apple_count2' => '{0} There is no apple|{1} There is one apple|[1,19] There are %count% apples|[20,Inf] There are many apples', // symfony
    'apple_count3' => '{0} There is no apple|{1} There is one apple|[1,19] There are :count apples|[20,*] There are many apples', // laravel
];

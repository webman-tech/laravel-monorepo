<?php

namespace app\controller;

use support\Request;
use WebmanTech\LaravelTranslation\Facades\Translator;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;

class TranslatorController
{
    public function index(Request $request): \support\Response
    {
        $results = [];

        // instance
        $results['instance'] = Translator::instance() instanceof TranslatorContract;

        // current locale
        $results['locale'] = locale() === 'zh_CN';

        // trans with no key
        $results['trans_no_key'] = __() === null;

        // basic translation - zh_CN
        $results['trans_hello'] = trans('你好') === '你好';
        $results['transL_hello'] = transL('messages.你好') === 'messages.你好';

        // translation from messages
        $results['trans_hello2'] = trans('你好2') === '你好';
        $results['transL_hello2'] = transL('messages.你好2') === '你好';

        // sentence as key
        $results['trans_sentence'] = trans('I love programming.') === '我爱编程 From Message';
        $results['transL_sentence'] = transL('I love programming.') === '我爱编程 From json';

        // group file
        $results['trans_group'] = trans('key_from_app', [], 'app') === 'app中的翻译';
        $results['transL_group'] = transL('app.key_from_app') === 'app中的翻译';

        // switch locale to en
        locale('en');
        $results['trans_en'] = trans('你好') === 'hello';
        $results['transL_en'] = transL('messages.你好') === 'hello';

        // replacement
        $results['trans_replace_symfony'] = trans('welcome', [':name' => 'boy']) === 'hello, boy';
        $results['transL_replace'] = transL('messages.welcome', ['name' => 'boy']) === 'hello, boy';

        // uppercase
        $results['transL_upper'] = transL('messages.welcome2', ['name' => 'dayle']) === 'hello, DAYLE';
        $results['transL_ucfirst'] = transL('messages.welcome3', ['name' => 'dayle']) === 'hello, Dayle';

        // choice
        $results['trans_choice_one'] = trans_choice('messages.apple_count', 1) === 'There is one apple';
        $results['trans_choice_many'] = trans_choice('messages.apple_count', 3) === 'There are many apples';
        $results['trans_choice_zero'] = trans_choice('messages.apple_count3', 0) === 'There is no apple';
        $results['trans_choice_18'] = trans_choice('messages.apple_count3', 18, ['count' => 18]) === 'There are 18 apples';

        // reset locale
        locale('zh_CN');

        return json($results);
    }
}

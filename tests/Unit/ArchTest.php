<?php

use App\Interfaces\HasLabel;
use App\Models\BaseModel;

arch()->preset()->php();
arch()->preset()->security();

arch('enums implement labels')
    ->expect('App')
    ->enums()
    ->toImplement(HasLabel::class);

arch()->expect('App\Exceptions')
    ->classes()
    ->toImplement('Throwable');

// arch()->expect('App\Http\Controllers\Api')
//    ->classes()
//    ->toHaveMethodsDocumented()
//    ->toHaveAttribute(\Dedoc\Scramble\Attributes\Group::class);

arch()->expect('App')
    ->not->toImplement(Throwable::class);

arch()->expect('App\Http\Middleware')
    ->classes()
    ->toHaveMethod('handle');

arch()->expect('App\Models')

    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch()->expect('App\Models')
    ->classes()
    ->not->toHaveSuffix('Model')
    ->ignoring([BaseModel::class]);

arch()->expect('App')
    ->not->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models');

arch()->expect('App\Http\Requests')
    ->classes()
    ->toHaveSuffix('Request');

arch()->expect('App\Http\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');

arch()->expect('App\Http\Requests')
    ->toHaveMethod('rules');

arch()->expect('App')
    ->not->toExtend('Illuminate\Foundation\Http\FormRequest')
    ->ignoring('App\Http\Requests');

arch()->expect('App\Console\Commands')
    ->classes()
    ->toHaveSuffix('Command');

arch()->expect('App\Console\Commands')
    ->classes()
    ->toExtend('Illuminate\Console\Command');

arch()->expect('App\Console\Commands')
    ->classes()
    ->toHaveMethod('handle');

arch()->expect('App')
    ->not->toExtend('Illuminate\Console\Command')
    ->ignoring('App\Console\Commands');

arch()->expect('App\Mail')
    ->classes()
    ->toExtend('Illuminate\Mail\Mailable');

arch()->expect('App\Mail')
    ->classes()
    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');

arch()->expect('App')
    ->not->toExtend('Illuminate\Mail\Mailable')
    ->ignoring('App\Mail');

arch()->expect('App\Jobs')
    ->classes()
    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');

arch()->expect('App\Jobs')
    ->classes()
    ->toHaveMethod('handle');

arch()->expect('App\Listeners')
    ->toHaveMethod('handle');

arch()->expect('App\Notifications')
    ->toExtend('Illuminate\Notifications\Notification');

arch()->expect('App')
    ->not->toExtend('Illuminate\Notifications\Notification')
    ->ignoring('App\Notifications');

arch()->expect('App\Providers')
    ->toHaveSuffix('ServiceProvider');

arch()->expect('App\Providers')
    ->toExtend('Illuminate\Support\ServiceProvider');

arch()->expect('App')
    ->not->toExtend('Illuminate\Support\ServiceProvider')
    ->ignoring('App\Providers');

arch()->expect('App')
    ->not->toHaveSuffix('ServiceProvider')
    ->ignoring('App\Providers');

arch()->expect('App')
    ->not->toHaveSuffix('Controller')
    ->ignoring('App\Http\Controllers');

arch()->expect('App\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch()->expect('App\Http\Controllers')
    ->not->toHavePublicMethodsBesides(['__construct', '__invoke', 'index', 'show', 'create', 'store', 'edit', 'update', 'destroy', 'restore']);

arch()->expect([
    'dd',
    'ddd',
    'dump',
    'env',
    'exit',
    'ray',
])->not->toBeUsed();

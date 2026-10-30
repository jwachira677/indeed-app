<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Routing\Controllers\Middleware;

/**
 * One-line CRUD protection for resource controllers (Laravel 11+).
 *
 *   class TeacherController extends Controller implements HasMiddleware
 *   {
 *       use AuthorizesCrud;
 *
 *       protected static string $permissionModule = 'teachers';
 *   }
 *
 * Maps resource methods to <module>.view / create / update / delete.
 * Add extra methods in the controller with `$this->authorize('teachers.export')`.
 */
trait AuthorizesCrud
{
    public static function middleware(): array
    {
        $module = static::$permissionModule;

        return [
            new Middleware("can:{$module}.view",   only: ['index', 'show']),
            new Middleware("can:{$module}.create", only: ['create', 'store']),
            new Middleware("can:{$module}.update", only: ['edit', 'update']),
            new Middleware("can:{$module}.delete", only: ['destroy']),
        ];
    }
}
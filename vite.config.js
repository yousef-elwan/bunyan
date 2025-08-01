import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';
import glob from 'fast-glob';

function getInputFiles() {
    return [
        'resources/css/app.css',
        'resources/js/app.js',
        ...glob.sync('resources/js/alpine/app/**/main.js'),
        'resources/js/alpine/dashboard/app.js',
        'resources/js/alpine/app/auth/reset-password.js',
        ...glob.sync('resources/js/alpine/dashboard/**/main.js'),
        ...glob.sync('resources/js/alpine/dashboard/floor/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/type/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/property/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/orientation/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/condition/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/user/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/category/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/report/*.js'),
        ...glob.sync('resources/js/alpine/dashboard/subscriptions/*.js'),
    ];
}

export default defineConfig({
    plugins: [
        laravel({
            input: getInputFiles(),
            refresh: true,
        }),
        tailwindcss(),
    ],
});

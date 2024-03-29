<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;
use WillPower232\CloverParserLaravel\CloverParser;

class Controller extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function parse(Request $request, string $user, string $project, string $branch): Response
    {
        // laravel could do something like this
        // $request->validate([
        //     'file' => [
        //         'file',
        //         'mimes:xml',
        //     ],
        // ]);

        // lumen needs validators to be constructed manually
        // - reference Laravel\Lumen\Routing\ProvidesConvenienceMethods
        $validator = app('validator')->make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:xml',
            ],
        ]);

        // the chance of this not being a file at this point is nil but stan won't like me
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');

        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                array_unshift($messages['file'], $file->getErrorMessage());
            }

            throw new ValidationException($validator, new JsonResponse($messages, 422));
        }

        // anyway...

        $parser = app(CloverParser::class)
            ->setPath("$user/$project", $branch);

        $parser->addFile($file->getPathName());

        $parser->storeImage();

        $parser->store($branch . '.clover', $file);

        return response('', 204);
    }
}
/*
 * curl -X POST -F "file=@coverage.clover" -H "Authorization: Bearer hello"
 * http://coverage.zz.zz/willpower232/cloverparser-laravel/main
 */

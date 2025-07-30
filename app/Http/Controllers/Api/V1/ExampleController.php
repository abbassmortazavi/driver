<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\V1\PublicUserResponse;
use App\Models\InternalUser;
use App\Models\PublicUser;
use Patoughi\Common\Orm\Repositories\ModelManager;

class ExampleController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request, ModelManager $modelManager): \Illuminate\Http\JsonResponse
    {
        $internalUserRepository = $modelManager->getRepository(InternalUser::class);
        $publicUserRepository = $modelManager->getRepository(PublicUser::class);

        return PublicUserResponse::show($publicUserRepository->find($request->get('id')));
    }
}
